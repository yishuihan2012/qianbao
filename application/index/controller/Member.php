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
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

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

}
