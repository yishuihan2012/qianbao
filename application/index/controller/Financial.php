<?php
/**
 *  @version Financial controller / 财务管理
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
 namespace app\index\controller;
 use think\Controller;
 use think\Request;
 use think\Session;
 use think\Config;
 use think\Loader;
 use app\index\model\Member;

 class Financial extends Common{
	 /**
	 *  @version index method / 财务管理--对账中心
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2018-1-11 11:13
	 *   @return 
	 */
 	 public function index()
 	 {

		 #渲染视图
		 return view('admin/financial/index');
 	 }

	 /**
	 *  @version level method / 财务管理--会员升级收益统计
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2018-1-11 14:45
	 *   @return 
	 */
  	 public function level()
  	 {	
  	 	 #查询升级记录表 支付订单表 成功的信息
  	 	 $data=Member::haswhere('memberUpgrade',['upgrade_state'=>0])->select();
  	 	 dump($data);

		 #渲染视图
		 return view('admin/financial/level');
  	 } 

 }
