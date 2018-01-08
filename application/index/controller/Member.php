<?php
/**
 *  @version Member controller / 会员基本信息控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Member as Members;
use app\index\model\MemberLogin;
use app\index\model\MemberGroup;
use app\index\model\MemberRelation;
use app\index\model\Upgrade;
use app\api\controller\Commission;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;
use think\Db;

class Member extends Common{
	 #会员列表
	 public function index()
	 {
	 	//传入参数
	 	$r=request()->param();
	 	 #搜索条件
	 	$data = memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
	 	 //注册时间
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['member_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		#身份证查询
		$wheres = array();
		 if( request()->param('cert_member_idcard')){
			$where['m.cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
		}else{
			$r['cert_member_idcard'] = '';
		}
		// dump(@file_get_contents("http://huiqianbao.lianshuopay.com/uploads/avatar/20171230/92ec98cd4c5ed80b525897e4a6a44110.jpg"));
	 	 //获取会员等级
	 	 $member_group=MemberGroup::all();
	 	 #获取会员列表 
	 	 $member_list=Members::with('memberLogin,membergroup,membercert')->join("wt_member_cert m", "m.cert_member_id=member_id","left")->where($wheres)->where($where)->order('member_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	 #用户身份证号码
		 $count = Members::with('memberLogin,membergroup,membercert')->join("wt_member_cert m", "m.cert_member_id=member_id","left")->where($wheres)->where($where)->count();
	 	 $this->assign('count', $count);
	 	 $this->assign('r', $r);
	 	 $this->assign('member_list', $member_list);
	 	 $this->assign('member_group', $member_group);
		 #渲染视图
		 return view('admin/member/index');
	 }
	

	 #会员详细信息
	 public function info(Request $request)
	 {
	 	 if(!$request->param('id'))
	 	 {
			 Session::set('jump_msg', ['type'=>'error','msg'=>'参数错误']);
			 $this->redirect($this->history['1']);
	 	 }
	 	 #查询到当前会员的基本信息
	 	 $member_info=Members::with('memberLogin')->find($request->param('id'));
	 	 #查询上级信息
	 	 $leadr=db('member_relation')->alias('r')
	 	 	->where(["r.relation_member_id"=>$member_info->member_id])
	 	 	->join('member m','r.relation_parent_id=m.member_id')->find();
	 	 #查询下级信息
	 	 $team=db('member_relation')->alias('r')
	 	 	->where(["r.relation_parent_id"=>$member_info->member_id])
	 	 	->join('member m','r.relation_member_id=m.member_id')->select();
	 	 // var_dump($member_info);die;
	 	 $this->assign('member_info', $member_info);
	 	 $this->assign('leadr', $leadr);
	 	 $this->assign('team', $team);
	 	 return view('admin/member/info');
	 }
	 #封停用户
	 public function disables($id){
	 	 $MemberLogin = MemberLogin::get(['login_member_id'=>$id]);
		 $result = $MemberLogin->save(['login_state'=>-1]);
		 #数据是否提交成功
		 $content = ($result===false) ? ['type'=>'error','msg'=>'用户封停失败'] : ['type'=>'success','msg'=>'用户封停成功'];
		 Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect($this->history['1']);
	 }
	 #升级会员
	 public function upgrade(){
	 	if(Request::instance()->isPost()){
	 		$where['member_id'] = request()->param("member_id");
	 		$Member = new Members();
	 		#获取用户信息
	 		$info = $Member->join("wt_member_group","member_group_id=group_id")->join("wt_member_relation","relation_member_id=member_id")->where($where)->find();
	 		#获取用户分组最大会员级别
	 		$group_salt = Db::table("wt_member_group")->field("group_salt")->order("group_salt desc")->find();
	 		$content = array();
	 		//判断用户有没有实名
	 		if($info['member_cert']==1){
	 			#判断用户是不是最大级会员级别
	 			if($info['group_salt']>=$group_salt['group_salt']){
		 			$content =  ['type'=>'error','msg'=>'用户已是最大级别会员，不用在升级了。'] ;
		 		}else{
		 			$group_where['group_salt'] = $info['group_salt']+1;
		 			#获取用户组要升级的级别id
		 			$group_id = Db::table("wt_member_group")->field("group_salt,group_id,group_level_money")->where($group_where)->find();
		 			$data['member_group_id'] = $group_id['group_id'];
		 			#更新用户的分组id
		 			$re = $Member->where($where)->update($data);
		 			if($re){
		 				$status = request()->param("status");
			 			$upgrade_data['upgrade_member_id'] = $where['member_id'];
			 			$upgrade_data['upgrade_before_group'] = $info['member_group_id'];
			 			$upgrade_data['upgrade_group_id'] = $group_id['group_id'];
			 			$upgrade_data['upgrade_type'] = "后台升级";
			 			$upgrade_data['upgrade_no'] = make_order();
			 			$upgrade_data['upgrade_money'] = 0;
			 			$upgrade_data['upgrade_commission'] = ($status==0)?0:$group_id['group_level_money'];
			 			$upgrade_data['upgrade_state'] = 0;
			 			$upgrade_data['upgrade_bak'] = "后台管理员升级";
			 			$upgrade_data['upgrade_adminster_id'] = Session::get("adminster")['id'];
			 			//添加用户日志
			 			$Upgrade =  new Upgrade($upgrade_data);
			 			$result = $Upgrade->allowField(true)->save();
			 			$Commission = new Commission();
			 			//判断用户有没有上级，或者是判断后台有没有设置分佣。
			 			if($info['relation_parent_id']!=0 || $status==1){
			 				$results = $Commission->MemberCommis(request()->param("member_id"),$upgrade_data['upgrade_commission'],"后台管理员升级"); 
			 			}
			 			$content = ($result===false) ? ['type'=>'error','msg'=>'升级会员失败'] : ['type'=>'success','msg'=>'升级会员成功'];
		 			}else{
		 				$content = ['type'=>'error','msg'=>'升级会员失败'];
		 			}
		 			
		 		}
	 		}else{
	 			$content =  ['type'=>'error','msg'=>'该用户还没有实名认证，不可以升级。'] ;		
	 		}
	 		 Session::set('jump_msg', $content);
	 		
	 		$this->redirect("member/index");
	 		die;
	 		
	 	}
	 	
	 	$this->assign("id",request()->param("id"));
	 	return view("admin/member/upgrade");
	}
}
