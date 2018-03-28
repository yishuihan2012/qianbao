<?php
/**
 *  @version Member controller / 会员基本信息控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;

 use app\index\model\Member as Members;
 use app\index\model\{MemberLogin, MemberGroup, MemberRelation, MemberCert, MemberCashcard, Upgrade ,Passageway,MemberTeam,MemberNet, CashOrder};
 use app\api\controller\Commission;
 use app\index\model\Commission as Commissions;
 use think\{Controller, Request, Session, Config, Loader, Db};
 use app\index\model\System;
  use app\index\model\Wallet;
 class Member extends Common{
 	/**
 	 *  会员下级列表
 	 */
 	public function children($member_id=null){
	 	//传入参数
	 	$r=request()->param();
	 	 #搜索条件
	 	#手机号昵称
	 	$where=[];
	 	if(input('member_nick'))
	 		$where['member_nick|member_mobile']=['like',"%".$r['member_nick']."%"];

	 	#实名
	 	$r['member_cert']=input('member_cert')??'';
	 	if($r['member_cert']==0 && $r['member_cert']!=''){
	 		trace($r);
	 		$where['member_cert']=0;
	 	}else{
	 		$r['member_cert']=1;
	 		$where['member_cert']=1;
	 	}
	 	#推荐关系
	 	$r['relation']=input('relation')??'';
	 	if($r['relation']){
	 		$member_ids=$this->getchildIds($member_id,$r['relation'],false);
	 	}else{
	 		$member_ids=$this->getchildIds($member_id,3);
	 	}
	 	$where['member_id']=['in',$member_ids];
	 	#用户组
	 	$r['member_group_id']=input('member_group_id')??'';
	 	if($r['member_group_id']){
	 		$where['member_group_id']=$r['member_group_id'];
	 	}else{
	 		$r['member_group_id']='';
	 	}

	 	#通道 
	 	$r['passageway_id']=input('passageway_id') ?? '';
	 	$passway=Passageway::column("*","passageway_id");
	 	$this->assign('passway',$passway);
	 	$cms_where=['commission_member_id'=>$member_id];
	 	if($r['passageway_id']){
	 		#消费
	 		if($passway[$r['passageway_id']]['passageway_also']==1){
	 			$order_ids=CashOrder::where(['order_member'=>['in',$member_ids],'order_state'=>2,'order_passway'=>$r['passageway_id']])->column('order_id');
		 		$cms_where['commission_from']=['in',$order_ids];
		 		$cms_where['commission_type']=1;
	 		}else{
	 			#代还
	 			$order_ids=db('generation_order')->where(['order_member'=>['in',$member_ids],'order_status'=>2,'order_passageway'=>$r['passageway_id']])->column('order_id');
		 		$cms_where['commission_from']=['in',$order_ids];
		 		$cms_where['commission_type']=3;
	 		}
	 	}else{
	 		$cms_where['commission_type']=['in',"1,3"];
	 	}
 		$cms=Commissions::where($cms_where)->group('commission_childen_member')->column('commission_childen_member,sum(commission_money) as sum');
 		arsort($cms);
 		// halt($cms);
	 	 //注册时间
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$r['beginTime']=input('beginTime');
			$r['endTime']=input('endTime');
			$where['member_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
		}

		$this->assign('button',['text'=>'添加新用户', 'link'=>url('/index/member/register'), 'modal'=>'modal']);
		#总数据计算
		$member_data=db('member')->where($where)->select();
		foreach ($member_data as $k => $v) {
	 		$member_data[$k]['sum']=isset($cms[$v['member_id']]) ? $cms[$v['member_id']] : 0 ;
		}
		#导出
		if(input('is_export')==1){
	 	    $fp = fopen('php://output', 'a');
 	    	#取数据
	 	    $member_list=db("member")->alias('m')
	 	    	->join('member_login l','l.login_member_id=m.member_id')
	 	    	->join('member_group g','g.group_id=m.member_group_id')
	 	    	->where($where)
	 	    	->order("member_id desc")
	 	    	->field('member_id,member_nick,member_mobile,member_cert,group_name,login_state,member_creat_time')
	 	    	->select();

	 	    $head=['ID','用户名','手机号码','是否实名','会员等级','登录状态','注册时间'];
	 	    export_csv($head,$member_list,$fp);
	 	    return;
		}
	 	 //获取会员等级
	 	 $member_group=MemberGroup::column("*","group_id");
	 	 #获取会员列表 
	 	 $member_list=Members::where($where)
	 	 	->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	 #写入每个会员的分润和
	 	foreach ($member_list as $k => $v) {
	 		$member_list[$k]['sum']=isset($cms[$v['member_id']]) ? $cms[$v['member_id']] : 0 ;
	 	}
	 	#统计
	 	$data=[
	 		'count'=>count($member_data),
	 		'fenrun'=>array_sum(array_column($member_data, 'sum')),
	 	];
	 	//  halt($member_list);
		$current_member=Members::get($member_id);
		$this->assign("current_member",$current_member);
	 	 $this->assign('r', $r);
	 	 $this->assign('member_list', $member_list);
	 	 $this->assign('member_group', $member_group);
	 	 $this->assign('data', $data);
		 #渲染视图
		 return view('admin/member/children');
 	}
 	/**
 	 * 获取下级id组
 	 * deep 获取深度
 	 * all 是否获取深度内全部
 	 */
 	private function getchildIds($ids,$deep=1,$all=true){
 		if(!$deep)
 			return $ids;
 		$deep--;
 		$ids=MemberRelation::where("relation_parent_id","in",$ids)->column("relation_member_id");
 		return $all ? array_merge($ids,$this->getchildIds($ids,$deep)) : $this->getchildIds($ids,$deep,false);
 	}

 	 /**
	 *  @version child method /  会员下级信息      @datetime    2018-1-17 13:27
	 *  @author $GongKe$ (755969423@qq.com) 会员下级信息列表    @return  返回会员的下级信息 
	 */
 	 public function child(Request $request)
 	 {
	 	 if(!$request->param('memberId'))
	 	 {
			 Session::set('jump_msg', ['type'=>'error','msg'=>'参数错误,缺少会员标识ID']);
			 $this->redirect($this->history['1']);
	 	 }
	 	 $data=$this->getChildInfo($request->param('memberId'));
	 	 $this->assign('data', $data);
	 	 return view('admin/member/child');
 	 }
 	 public function getChildInfo(int $memberId, bool $json=false, int $page=1)
 	 {
 	 	 $data=Members::getChild($memberId);
 	 	 $r=input();
 	 	 $r['page']=$page;
 	 	 // $data=Members::haswhere('memberRelation',['relation_parent_id'=>$memberId])->field('member_id,member_nick,member_mobile,member_image,member_cert,member_creat_time')->paginate(Config::get('page_size'), false, ['query'=>$r]);
           foreach($data as $key => $value)
           {
               	 $data[$key]['member_cash']=CashOrder::getMemberCash($value['member_id']); 	#统计该会员共套现多少金额
               	 $data[$key]['member_son']=MemberRelation::getSonCount($value['member_id']);   #统计该会员共邀请了多少人
           }
           if($json)
           	 echo json_encode($data);
           else
           	 return $data;
 	 }

	 #会员列表
	 public function index($member_id=null)
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
			$this->assign('beginTime',request()->param('beginTime'));
			$this->assign('endTime',request()->param('endTime'));
			$r['beginTime']=input('beginTime');
			$r['endTime']=input('endTime');
		}else{
			$r['beginTime']='';
			$r['endTime']='';
		}

		#身份证查询
		$wheres = array();
		 if( request()->param('cert_member_idcard')){
			$where['m.cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
		}else{
			$r['cert_member_idcard'] = '';
		}
		#若当前用户为运营商用户
		if($this->admin['adminster_group_id']==5){
			$where['member_id']=["in",$this->admin['children']];
			$admin_group_salt=db('member_group')->alias('g')
				->join('member m','m.member_group_id=g.group_id')
				->where('m.member_id',$this->admin['adminster_user_id'])
				->value('group_salt');
			#用于判断下级用户所在的用户组是否大于当前代理商，若大于 不显示升级选项
			$this->assign('admin_group_salt',$admin_group_salt);
		}else{
			$this->assign('button',['text'=>'添加新用户', 'link'=>url('/index/member/register'), 'modal'=>'modal']);
		}
		#查找指定会员下级
		if($member_id){
			$ids=MemberRelation::where(['relation_parent_id'=>$member_id])->column('relation_member_id');
			#运营商用户 member_id 范围合并 取交集
			if(isset($where['member_id'])){
				$where['member_id'][1]=array_intersect($where['member_id'][1],$ids);
			}else{
				$where['member_id']=['in',$ids];
			}
			$current_member=Members::get($member_id);
			$this->assign("current_member",$current_member);
		}
		if(input('is_export')==1){
	 	    $fp = fopen('php://output', 'a');
 	    	#取数据
	 	    $member_list=db("member")->alias('m')
	 	    	->join('member_login l','l.login_member_id=m.member_id')
	 	    	->join('member_group g','g.group_id=m.member_group_id')
	 	    	->join('member_cert c','c.cert_member_id=m.member_id','left')
	 	    	->where($where)
	 	    	->where($wheres)
	 	    	->order("member_id desc")
	 	    	->field('member_id,member_nick,member_mobile,member_cert,group_name,login_state,member_creat_time')
	 	    	->select();

	 	    $head=['ID','用户名','手机号码','是否实名','会员等级','登录状态','注册时间'];
	 	    export_csv($head,$member_list,$fp);
	 	    return;
		}
	 	 //获取会员等级
	 	 $member_group=MemberGroup::all();
	 	 #获取会员列表 
	 	 $member_list=Members::with('memberLogin,membergroup,membercert')
	 	 	->join("wt_member_cert m", "m.cert_member_id=member_id","left")
	 	 	->where($wheres)->where($where)
	 	 	->order('member_id','desc')
	 	 	->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	
		 #统计用户总数
	 	 $this->assign('count', $this->counts());
	 	 #统计未实名用户
	 	 $this->assign('wei_count', $this->counts(["member_cert" => 0]));
	 	 #统计已实名用户数量
	 	 $this->assign('yi_count', $this->counts(["member_cert" => 1]));
	 	 $group_user = [];
	 	 foreach($member_group as $key=>$value){
	 	 	$group_user[$key]['count'] = $this->counts(["member_group_id" => $value['group_id']]);
	 	 	$group_user[$key]['group_name'] = $value['group_name'];

	 	 }
	 	 #统计分组用户数量
	 	 $this->assign("group_user",$group_user);
	 	 $this->assign('r', $r);
	 	 $this->assign('member_list', $member_list);
	 	 $this->assign('member_group', $member_group);
		 #渲染视图
		 return view('admin/member/index');
	 }
	 /**
	 * @version counts 统计用户数量
	 * @author 杨成志（3115317085@qq.com）
	 */
	 public function counts($where = []){
	 	return  Members::with('memberLogin,membergroup,membercert')->join("wt_member_cert m", "m.cert_member_id=member_id","left")->where($where)->count();
	 }
 	 /**
	 *  @version info method /  会员详细信息
	 *  @author $bill$(755969423@qq.com) 后台优化功能
	 *   @datetime    2018-1-10 14:23
	 *   @return  为统一代码方式,增加执行效率 所有方法摒弃原DB类关联查询 使用对象的与载入查询或关联查询
	 */
	 public function info(Request $request)
	 {
	 	 #如果参数(会员ID)不存在 则返回参数出错 或者缺少参数
	 	 if(!$request->param('id'))
	 	 {
			 Session::set('jump_msg', ['type'=>'error','msg'=>'参数错误']);
			 $this->redirect($this->history['1']);
	 	 }

	 	 #查询到当前会员的基本信息
	 	 $member_info=Members::with('memberLogin,memberWallet,membercert')->find($request->param('id'));

	 	 #查询上级信息
	 	 $leadr=db('member_relation')->alias('r')
	 	 	->where(["r.relation_member_id"=>$member_info->member_id])
	 	 	->join('member m','r.relation_parent_id=m.member_id')->find();
	 	 #查询下级信息
	 	 // $team=db('member_relation')->alias('r')
	 	 // 	->where(["r.relation_parent_id"=>$member_info->member_id])
	 	 // 	->join('member m','r.relation_member_id=m.member_id')->select();
	 	 // var_dump($member_info);die;
	 	 $this->assign('member_info', $member_info);
	 	 $this->assign('leadr', $leadr);
	 	 // $this->assign('team', $team);
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
	 		$info = $Member->join("wt_member_relation","relation_member_id=member_id")->where($where)->find();
	 		#获取用户分组最大会员级别
	 		$group_salt = Db::table("wt_member_group")->field("group_salt")->order("group_salt desc")->find();
	 		$content = array();
	 		//判断用户有没有实名

	 		if($info['member_cert']==1){
	 			#判断用户是不是最大级会员级别
	 			$group_where['group_id'] = request()->param("member_group_id");
	 			#获取用户组要升级的级别id
	 			$group_id = Db::table("wt_member_group")->field("group_salt,group_id,group_level_money")->where($group_where)->find();
	 			#获取
	 			$dangqian_group = Db::table("wt_member_group")->field("group_salt,group_id,group_level_money")->where(['group_id' => $info['member_group_id']])->find();
	 			$data['member_group_id'] = $group_id['group_id'];
	 			#更新用户的分组id
	 				if($info['member_group_id'] == request()->param("member_group_id")){
		 				$content = ['type'=>'error','msg'=>'等同当前等级'];
		 			}else{
		 				$re = $Member->where($where)->update($data);
			 			if($re){
			 				$status = request()->param("status");
				 			$upgrade_data['upgrade_member_id'] = $where['member_id'];
				 			$upgrade_data['upgrade_before_group'] = $info['member_group_id'];
				 			$upgrade_data['upgrade_group_id'] = $group_id['group_id'];
				 			$upgrade_data['upgrade_type'] = "后台升级";
				 			$upgrade_data['upgrade_no'] = make_order();
				 			$upgrade_data['upgrade_money'] = 0;
				 			$upgrade_data['upgrade_commission'] = ($status==0)?0:($group_id['group_level_money']-$dangqian_group['group_level_money']);
				 			$upgrade_data['upgrade_state'] = 0;
				 			$upgrade_data['upgrade_bak'] = "后台管理员升级";
				 			$upgrade_data['upgrade_adminster_id'] = Session::get("adminster")['id'];
				 			#查询出启用的通道
				 			$passageway=Passageway::where(['passageway_state'=>1,'passageway_status'=>1])->select();
				 			#遍历通道修改用户在同道入网信息
				 			foreach ($passageway as $key => $value) {
				 				$members=Members::haswhere('membernet',$value['passageway_no']." != ''")->where(['member_id'=>$info['member_id']])->find();
				 				
				 				if(empty($members)){
				 					continue;
				 				}
				 				 $Membernetsedit=new \app\api\controller\Membernetsedit($info['member_id'],$value['passageway_id'],'M03','',$info['member_mobile']);
				 				 $method=$value['passageway_method'];
				 				 $success=$Membernetsedit->$method();
				 			}
				 			
				 			//添加用户日志
				 			$Upgrade =  new Upgrade($upgrade_data);
				 			$result = $Upgrade->allowField(true)->save();
				 			$Commission = new Commission();
				 			//判断用户有没有上级，或者是判断后台有没有设置分佣。

				 			if($info['relation_parent_id']!=0 && $status==1){
				 				if($group_id['group_salt']>$dangqian_group['group_salt']){
				 					$results = $Commission->MemberCommis(request()->param("member_id"),$upgrade_data['upgrade_commission'],"后台管理员升级"); 
				 				}
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
	 	#运营商用户
	 	if($this->admin['adminster_group_id']==5){
	 		$group_salt=db('member')->alias('m')
	 			->join('member_group g','m.member_group_id=g.group_id')
	 			->where('m.member_id',$this->admin['adminster_user_id'])
	 			->value('g.group_salt');
	 		$user_group_salt=db('member')->alias('m')
	 			->join('member_group g','m.member_group_id=g.group_id')
	 			->where('m.member_id',request()->param("id"))
	 			->value('g.group_salt');
	 		#可升级的用户组 应该是大于当前用户组，并且小于当前运营商所在算用户组
		 	$member_group_info = MemberGroup::where(['group_salt'=>['between',[$user_group_salt,$group_salt]]])
		 		->order("group_salt desc")->select();//用户分组数据
	 	}else{
		 	$member_group_info = MemberGroup::order("group_salt desc")->select();//用户分组数据
	 	}
	 	$this->assign("member_group_id", request()->param("member_group_id"));
	 	$this->assign("id",request()->param("id"));
	 	$this->assign('member_group_info',$member_group_info);
	 	return view("admin/member/upgrade");
	}

 	 /**
	 *  @version walletInfo method / 会员钱包信息基本方法
	 *  @author $bill$(755969423@qq.com) 后台优化功能
	 *   @datetime    2018-1-10 14:23
	 *   @return  为统一代码方式,增加执行效率 所有方法摒弃原DB类关联查询 使用对象的与载入查询或关联查询
	 */
	 public function walletInfo(Request $request)
	 {
	 	 if(!$request->param('memberId'))
	 	 {
			 Session::set('jump_msg', ['type'=>'error','msg'=>'参数错误']);
			 $this->redirect($this->history['1']);
	 	 }
	      #如果会员ID存在的话 则查询会员的钱包详细信息
	 	 $memberWalletInfo=Members::with('memberWallet')->where('member_id',$request->param('memberId'))->find();
	 	 $this->assign('walletInfo', $memberWalletInfo);
	 	 return view('admin/member/walletInfo');
	 	 //dump($memberWalletInfo->memberWallet->wallet_amount);
	 }
	 #审核用户信息
	 public function toexamine(){
	 	$where['member_id'] = request()->param("id");
	 	$data['member_cert'] = request()->param("member_cert"); #3是用户没有通过
	 	$result = Members::where($where)->update($data);
	 	#删除银行信息
	 	$wheres['card_member_id'] = request()->param("id");
	 	$cardresult = MemberCashcard::where($wheres)->delete();
	 	#删除身份证信息
	 	$certwhere['cert_member_id'] = request()->param("id");
	 	$certresult = MemberCert::where($certwhere)->delete();
	 	$content = ($result===false && $certresult ==false && $cardresult==false) ? ['type'=>'error','msg'=>'修改信息失败'] : ['type'=>'success','msg'=>'修改信息成功'];
	 	Session::set('jump_msg', $content);
	 	$this->redirect("member/index");
	 }
	 //会员分佣分润
	public function commiss(){
	 	$commiss = new Commission();
	 	$where['commission_member_id'] = request()->param("memberId");
	 	if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['commission_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		if(request()->param('min_money') && request()->param("max_money")){
			$where['commission_money'] = array(">=",request()->param('min_money'));
			$where['commission_money'] = array("<=",request()->param('max_money'));
		}
	 	$list = Commissions::where($where)->order("commission_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	#统计金额
	 	$sum = Commissions::where($where)->sum("commission_money");
	 	$this->assign("memberId",request()->param("memberId"));
	 	$this->assign("sum",$sum);
	 	$this->assign("list",$list);
	 	return view("admin/member/commiss");	
	}
	/**
	*@version registerForOthes 用户注册
	*@author 杨成志（3115317085@qq.com）
	*/
	public function registerForOthers()
      {
           
                 
           #验证参数是否存在
           if(!phone_check(request()->param('phone'))){
           		Session::set('jump_msg',['type'=>'warning','msg'=>'请输入正确手机号码','data'=>'']);
				$this->redirect($this->history['0']);
           }
                
           
           #检查用户(是否存在)            
           $member=MemberLogin::phone_exit(request()->param('phone'));
           if($member){
           		Session::set('jump_msg',['type'=>'warning','msg'=>'该手机号码已被注册，请直接登录！','data'=>'']);
				$this->redirect($this->history['0']);
           } 
        	Db::startTrans();  
           $parentmember=MemberLogin::phone_exit(request()->param('parent_phone'));                     
           #如果有推荐人手机号码
           if(request()->param('parent_phone')!=''){    
           		#验证parent_phone号码是否存在
	           if(!$parentmember){
	           	    Session::set('jump_msg',['type'=>'warning','msg'=>'主邀请人手机号码不存在','data'=>'']);
	           	    Db::rollback();
					$this->redirect($this->history['0']);
	           }
           }
         
           #填写注册信息             
           // try{
                 #随机密码salt                  
                 $rand_salt=make_rand_code();                  
                 #加密密码
                 // dump(substr(request()->param('phone'), -6));die;
                 $pwd=encryption(substr(request()->param('phone'), -6), $rand_salt);
                 #新增会员基本信息                  
                 $member_info = new Members([
                      'member_nick'=>request()->param('phone'),
                      'member_mobile'=>request()->param('phone'),
                      'member_group_id'=>System::getName('open_reg_membertype')]); 
                 if($member_info->save()===false)
                 {
                     Session::set('jump_msg',['type'=>'warning','msg'=>'注册失败请重试','data'=>'']);
				    $this->redirect($this->history['0']) ;                
                 } 
                 $token = get_token();                  
                 $member_login= new MemberLogin([
                      'login_member_id'=>$member_info->member_id,
                      'login_account'    =>request()->param('phone'),                       
                      'login_pass'  =>$pwd,                       
                      'login_pass_salt'  =>$rand_salt,
                      'login_token'         =>$token,                       
                      'login_attempts'   =>0,
                 ]);                  
                #用户推荐表信息处理                  
                 $meber_relation= new MemberRelation([
                      'relation_member_id'=>$member_info->member_id,
                      'relation_parent_id'  =>empty($parentmember['login_member_id'])?0:$parentmember['login_member_id'],
                      'relation_type'     =>6,//TODO 邀请方式                  
                 ]);
                 #初始化会员钱包信息                  
                 $member_wallet= new Wallet([
                      'wallet_member'=>$member_info->member_id,
                      'wallet_amount'=>0                  
                ]);  
                $member_team=new MemberTeam([
                	 'team_name'=>$member_info->member_nick,
                	 'team_member_id'=>$member_info->member_id,
                ]);     
                #初始化会员入网信息
                $MemberNet=new MemberNet([
                	 'net_member_id'=>$member_info->member_id,
                ]);
                if(!$member_login->save() || !$meber_relation->save() || !$member_wallet->save() || !$member_team->save() || !$MemberNet->save())
                {                       
                     Session::set('jump_msg',['type'=>'warning','msg'=>'注册失败请重试','data'=>'']);
                     Db::rollback();
					$this->redirect($this->history['0'])  ;               
                 }                 
                                  
                 Db::commit();
                 $data=Members::member_info($token);                  
                 Session::set('jump_msg',['type'=>'success','msg'=>'注册成功','data'=>'']);
				    $this->redirect($this->history['0']) ;
                 //请求成功             
        //    } catch (\Exception $e) {                  
        //         Session::set('jump_msg',['type'=>'warning','msg'=>'你的信息添加失败请稍后再试！~','data'=>'']);
				    // $this->redirect($this->history['0'])  ;           
        //    }       
      }
      /**
      *@version register 用户注册
      *@author 杨成志 （3115317085@qq.com）
      */
      public function register(){
      		return view("admin/member/register");
      }
}
