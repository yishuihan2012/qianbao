<?php
/**
 *  @version Financial controller / 财务管理
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
 namespace app\index\controller;
 use think\{Controller,Request,Session, Config, Loader};
 use app\index\model\{Upgrade, Commission, CashOrder, Withdraw, GenerationOrder};
 class Financial extends Common{
	 /**
	 *  @version index method / 财务管理--对账中心
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2018-1-11 11:13
	 *   @return 
	 */
 	 public function index(Request $request)
 	 {
 	 	 $conditions=$request->param();
 	 	 if($request->isGet())
 	 	 	 Session::delete('name');
 	 	 if($request->isPost())
 	 	 	 Session::set('name',$conditions);
 	 	 if(Session::has('name'))
 	 	 	 $conditions=Session::get('name');

 	 	 $where['conditions_member']=(!empty($conditions) && $conditions['member_nick']!='') ? ['member_nick|member_mobile'=>['like','%'.$conditions['member_nick'].'%']] : '';
 	 	 #计算会员升级总收益
 	 	 $data['level']=Upgrade::where(['upgrade_state'=>1])->sum('upgrade_money');

 	 	 #统计提现支出
 	 	 $data['withdraw']=Withdraw::where(['withdraw_state'=>12])->sum('withdraw_amount');
 	 	 /**
 	 	  * @version 快捷支付的总收益计算流程 获取所有成功的快捷支付交易订单  计算该会员在该通道费率减去平台与会员直接的费率差X订单金额 + 代扣费
 	 	 **/
 	 	 $data['quickPay']=0;
 	 	 $quickOrder=CashOrder::where(['order_state'=>2])->select();
 	 	 foreach ($quickOrder as $key => $value)
 	 	 	 $data['quickPay']+=$value['order_buckle']+$value['order_platform']; //加上代扣费 平台收益
 	 	 /**
 	 	  * @version 代还的总收益计算流程 获取所有成功的代还交易订单  计算该会员在该通道费率减去平台与会员直接的费率差X订单金额 + 代扣费
 	 	 **/
 	 	 $data['autoPay']=0;
 	 	 $autoOrder=GenerationOrder::where(['order_type'=>1,'order_status'=>2])->select();
		 #渲染视图
		 $this->assign('data',$data);
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
 	 	 if(Session::has('name'))
 	 	 	 $conditions=Session::get('name');
  	 	 #查询升级记录表 支付订单表 成功的信息
  	 	 $data=Upgrade::with('member')->where(['upgrade_state'=>1])->order('upgrade_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
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
 	 	 if(Session::has('name'))
 	 	 	 $conditions=Session::get('name');
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
 	 	 if(Session::has('name'))
 	 	 	 $conditions=Session::get('name');
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
 	 	 if(Session::has('name'))
 	 	 	 $conditions=Session::get('name');
  	 	 #查询快捷支付订单
  	 	 $data=withdraw::with('member')->where(['withdraw_state'=>12])->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
  	 	 #计算总提现多少钱

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
  	 public function commiss(Request $request)
  	 {
  	 	 $where['conditions']=['commission_type'=>2,'commission_state'=>1];
  	 	 $where['conditions_member']=$request->param('member_nick') ? ['member_nick|member_mobile'=>['like','%'.$request->param('member_nick').'%']] : '';
  	 	 $where['whereBetween']=($request->param('min_money') && $request->param('max_money')) ? ['commission_money'=>['between',[$request->param('min_money'), $request->param('max_money')]]] : '';
  	 	 $where['timeBetween']=($request->param('beginTime') && $request->param('endTime')) ? ['commission_creat_time'=>['between',[$request->param('beginTime'), $request->param('endTime')]]] : '';
  	 	 //获取分佣列表
  	 	 $data['list']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
  	 	 //获取共多少笔分佣
  	 	 $data['count']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
 	 					 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->count();
  	 	 //获取总金额
  	 	 $data['money']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
	 					 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->sum('commission_money');
  	 	 $this->assign('data',$data);
  	 	 $this->assign('conditions', $request->param());
		 #渲染视图
		 return view('admin/financial/commiss');
  	 }

	 /**
	 *  @version fenrun method / 财务管理--分润统计列表
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2018-1-11 14:45
	 *   @return 
	 */
  	 public function fenrun(Request $request)
  	 {
  	 	 $where['conditions']=['commission_type'=>['<>','2'],'commission_state'=>1];
  	 	 $where['conditions_member']=$request->param('member_nick') ? ['member_nick|member_mobile'=>['like','%'.$request->param('member_nick').'%']] : '';
  	 	 $where['whereBetween']=($request->param('min_money') && $request->param('max_money')) ? ['commission_money'=>['between',[$request->param('min_money'), $request->param('max_money')]]] : '';
  	 	 $where['timeBetween']=($request->param('beginTime') && $request->param('endTime')) ? ['commission_creat_time'=>['between',[$request->param('beginTime'), $request->param('endTime')]]] : '';
  	 	 //获取分佣列表
  	 	 $data['list']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
  	 	 //获取共多少笔分佣
  	 	 $data['count']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->count();
  	 	 //获取总金额
  	 	 $data['money']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->sum('commission_money');
  	 	 $this->assign('data',$data);
  	 	 $this->assign('conditions', $request->param());
		 #渲染视图
		 return view('admin/financial/fenrun');
  	 }

 }
