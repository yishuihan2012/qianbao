<?php
/**
 *  @version Financial controller / 财务管理
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
 namespace app\index\controller;
 use think\{Controller,Request,Session,Config,Loader};
 use app\index\model\{Upgrade, Commission, CashOrder, Withdraw};
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
  	 	 $data=Upgrade::with('member')->order('upgrade_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		 $this->assign('data', $data);
		 #渲染视图
		 return view('admin/financial/level');
  	 } 

	 /**
	 *  @version level method / 财务管理--快捷支付收益
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2018-1-11 14:45
	 *   @return 
	 */
  	 public function income()
  	 {	
  	 	 #查询快捷支付订单
  	 	 $data=CashOrder::with('member')->where(['order_state'=>2])->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
  	 	 //$data=Upgrade::with('member')->order('upgrade_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		 $this->assign('data', $data);
		 #渲染视图
		 return view('admin/financial/income');
  	 } 

	 /**
	 *  @version level method / 财务管理--自动代还收益
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2018-1-11 14:45
	 *   @return 
	 */
  	 public function substitute()
  	 {	
  	 	 #查询快捷支付订单
  	 	 $data=CashOrder::with('member')->where(['order_state'=>2])->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
  	 	 //$data=Upgrade::with('member')->order('upgrade_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		 $this->assign('data', $data);
		 #渲染视图
		 return view('admin/financial/income');
  	 } 

	 /**
	 *  @version withdraw method / 财务管理--提现统计
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2018-1-11 14:45
	 *   @return 
	 */
  	 public function withdraw()
  	 {	
  	 	 #查询快捷支付订单
  	 	 $data=withdraw::with('member')->where(['withdraw_state'=>12])->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
  	 	 //$data=Upgrade::with('member')->order('upgrade_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		 $this->assign('data', $data);
		 #渲染视图
		 return view('admin/financial/withdraw');
  	 } 

	 /**
	 *  @version level method / 财务管理--分佣统计列表
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2018-1-11 14:45
	 *   @return 
	 */
  	 public function commiss()
  	 {
  	 	 //获取分佣列表
  	 	 $list=Commission::with('member,members')->where(['commission_type'=>2,'commission_state'=>1])->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
  	 	 //获取共多少笔分佣
  	 	 $count=Commission::where(['commission_type'=>2,'commission_state'=>1])->count();
  	 	 //获取总金额
  	 	 $money=Commission::where(['commission_type'=>2,'commission_state'=>1])->sum('commission_money');
  	 	 $this->assign('list',$list);
  	 	 $this->assign('count',$count);
  	 	 $this->assign('money',$money);
		 #渲染视图
		 return view('admin/financial/commiss');
  	 }

	 /**
	 *  @version fenrun method / 财务管理--分润统计列表
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2018-1-11 14:45
	 *   @return 
	 */
  	 public function fenrun()
  	 {
  	 	 //获取分佣列表
  	 	 $list=Commission::with('member,members')->where(['commission_type'=>['<>','2'],'commission_state'=>1])->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
  	 	 //获取共多少笔分佣
  	 	 $count=Commission::where(['commission_type'=>['<>','2'],'commission_state'=>1])->count();
  	 	 //获取总金额
  	 	 $money=Commission::where(['commission_type'=>['<>','2'],'commission_state'=>1])->sum('commission_money');
  	 	 $this->assign('list',$list);
  	 	 $this->assign('count',$count);
  	 	 $this->assign('money',$money);
		 #渲染视图
		 return view('admin/financial/fenrun');
  	 }

 }
