<?php
/**
 *  @version Financial controller / 财务管理
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
 namespace app\index\controller;
 use think\{Controller,Request,Session, Config, Loader};
 use app\index\model\{Upgrade, Commission, CashOrder, Withdraw, GenerationOrder, Passageway};
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
 	 	 $where['upgradeBetween']=(!empty($conditions) && $conditions['min_money']!='' && $conditions['max_money']!='') ? ['upgrade_money'=>['between',[$conditions['min_money'], $conditions['max_money']]]] : '';
 	 	 $where['upgradeTime']=(!empty($conditions) && $conditions['beginTime']!='' && $conditions['endTime']!='') ? ['upgrade_update_time'=>['between',[$conditions['beginTime'], $conditions['endTime']]]] : '';
 	 	 #计算会员升级总收益
 	 	 $data['level']=Upgrade::haswhere('member', $where['conditions_member'])
				 	 	 ->where(['upgrade_state'=>1])
				 	 	 ->where($where['upgradeBetween'])
				 	 	 ->where($where['upgradeTime'])
				 	 	 ->sum('upgrade_money');
		 //dump(Upgrade::getLastSql());

		 $where['drawBetween']=(!empty($conditions) && $conditions['min_money']!='' && $conditions['max_money']!='') ? ['withdraw_amount'=>['between',[$conditions['min_money'], $conditions['max_money']]]] : ''; 
		 $where['drawTime']=(!empty($conditions) && $conditions['beginTime']!='' && $conditions['endTime']!='') ? ['withdraw_update_time'=>['between',[$conditions['beginTime'], $conditions['endTime']]]] : '';
 	 	 #统计提现支出
 	 	 $data['withdraw']=Withdraw::haswhere('member', $where['conditions_member'])
					 	 	 ->where(['withdraw_state'=>12])
					 	 	 ->where($where['drawBetween'])
					 	 	 ->where($where['drawTime'])
					 	 	 ->sum('withdraw_amount');
 	 	 /**
 	 	  * @version 快捷支付的总收益计算流程 获取所有成功的快捷支付交易订单  计算该会员在该通道费率减去平台与会员直接的费率差X订单金额 + 代扣费
 	 	 **/
 	 	 $where['payBetween']=(!empty($conditions) && $conditions['min_money']!='' && $conditions['max_money']!='') ? ['order_platform'=>['between',[$conditions['min_money'], $conditions['max_money']]]] : ''; 
 	 	 $where['cashTime']=(!empty($conditions) && $conditions['beginTime']!='' && $conditions['endTime']!='') ? ['order_update_time'=>['between',[$conditions['beginTime'], $conditions['endTime']]]] : '';
 	 	 $data['quickPay']=0;
 	 	 $quickOrder=CashOrder::haswhere('member', $where['conditions_member'])
				 	 	  ->where(['order_state'=>2])
				 	 	  ->where($where['payBetween'])
				 	 	  ->where($where['cashTime'])
				 	 	 ->select();
 	 	 foreach ($quickOrder as $key => $value)
 	 	 	 $data['quickPay']+=$value['order_buckle']+$value['order_platform']; //加上代扣费 平台收益
 	 	 /**
 	 	  * @version 代还的总收益计算流程 获取所有成功的代还交易订单  计算该会员在该通道费率减去平台与会员直接的费率差X订单金额 + 代扣费
 	 	 **/
 	 	 $data['autoPay']=0;
 	 	 $where['autoBetween']=(!empty($conditions) && $conditions['min_money']!='' && $conditions['max_money']!='') ? ['order_platform'=>['between',[$conditions['min_money'], $conditions['max_money']]]] : ''; 
 	 	 $where['autoTime']=(!empty($conditions) && $conditions['beginTime']!='' && $conditions['endTime']!='') ? ['order_edit_time'=>['between',[$conditions['beginTime'], $conditions['endTime']]]] : '';
 	 	 $autoOrder=GenerationOrder::haswhere('member', $where['conditions_member'])
 	 	 				 ->where($where['autoBetween'])
				 	 	 ->where(['order_type'=>1,'order_status'=>2])
				 	 	 ->where($where['autoTime'])
				 	 	 ->select();
 	 	 foreach ($autoOrder as $key => $value)
 	 	 	 $data['autoPay']+=$value['order_buckle']+$value['order_platform']; //加上代扣费 平台收益
		 #渲染视图
		 $this->assign('data',$data);
		 $this->assign('conditions', $request->param());
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
 	 	 $where['conditions_member']=(!empty($conditions) && $conditions['member_nick']!='') ? ['member_nick|member_mobile'=>['like','%'.$conditions['member_nick'].'%']] : '';
 	 	 $where['upgradeBetween']=(!empty($conditions) && $conditions['min_money']!='' && $conditions['max_money']!='') ? ['upgrade_money'=>['between',[$conditions['min_money'], $conditions['max_money']]]] : '';
 	 	 $where['upgradeTime']=(!empty($conditions) && $conditions['beginTime']!='' && $conditions['endTime']!='') ? ['upgrade_update_time'=>['between',[$conditions['beginTime'], $conditions['endTime']]]] : '';
  	 	 #查询升级记录表 支付订单表 成功的信息
  	 	 $data=Upgrade::with('member')
		  	 	 ->where(['upgrade_state'=>1])
		  	 	 ->where($where['conditions_member'])
		  	 	 ->where($where['upgradeTime'])
		  	 	 ->where($where['upgradeBetween'])
		  	 	 ->where(['upgrade_money' => ['<>',0]])
		  	 	 ->order('upgrade_id','desc')
		  	 	 ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
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
 	 	 $where['conditions_member']=(!empty($conditions) && $conditions['member_nick']!='') ? ['member_nick|member_mobile'=>['like','%'.$conditions['member_nick'].'%']] : '';
 	 	 $where['payBetween']=(!empty($conditions) && $conditions['min_money']!='' && $conditions['max_money']!='') ? ['order_platform'=>['between',[$conditions['min_money'], $conditions['max_money']]]] : '';
 	 	 $where['cashTime']=(!empty($conditions) && $conditions['beginTime']!='' && $conditions['endTime']!='') ? ['order_update_time'=>['between',[$conditions['beginTime'], $conditions['endTime']]]] : '';
  	 	 #查询快捷支付订单
  	 	 $data=CashOrder::with('member')
		  	 	 ->where(['order_state'=>2])
		  	 	 ->where($where['conditions_member'])
		  	 	 ->where($where['cashTime'])
		  	 	 ->where($where['payBetween'])
		  	 	 ->where(['order_platform' => ['<>',0]])
		  	 	 ->order('order_id','desc')
		  	 	 ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
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
 	 	 $where['conditions_member']=(!empty($conditions) && $conditions['member_nick']!='') ? ['member_nick|member_mobile'=>['like','%'.$conditions['member_nick'].'%']] : '';
 	 	 $where['autoBetween']=(!empty($conditions) && $conditions['min_money']!='' && $conditions['max_money']!='') ? ['order_platform'=>['between',[$conditions['min_money'], $conditions['max_money']]]] : ''; 
 	 	 $where['autoTime']=(!empty($conditions) && $conditions['beginTime']!='' && $conditions['endTime']!='') ? ['order_edit_time'=>['between',[$conditions['beginTime'], $conditions['endTime']]]] : '';
  	 	 #查询快捷支付订单
  	 	 $data=GenerationOrder::with('member')
  	 	 		 ->where($where['conditions_member'])
		  	 	 ->where(['order_type'=>1,'order_status'=>2])
		  	 	 ->where($where['autoBetween'])
		  	 	 ->where(['order_platform' => ['<>' , 0]])
		  	 	 ->where($where['autoTime'])
		  	 	 ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
  	 	 //$data=Upgrade::with('member')->order('upgrade_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		 $this->assign('data', $data);
		 #渲染视图
		 return view('admin/financial/substitute');
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
 	 	 $where['conditions_member']=(!empty($conditions) && $conditions['member_nick']!='') ? ['member_nick|member_mobile'=>['like','%'.$conditions['member_nick'].'%']] : '';
 	 	 $where['drawBetween']=(!empty($conditions) && $conditions['min_money']!='' && $conditions['max_money']!='') ? ['withdraw_amount'=>['between',[$conditions['min_money'], $conditions['max_money']]]] : ''; 
 	 	 $where['drawTime']=(!empty($conditions) && $conditions['beginTime']!='' && $conditions['endTime']!='') ? ['withdraw_update_time'=>['between',[$conditions['beginTime'], $conditions['endTime']]]] : '';
  	 	 #查询快捷支付订单
  	 	 $data=withdraw::with('member')
		  	 	 ->where(['withdraw_state'=>12])
		  	 	 ->where($where['conditions_member'])
		  	 	 ->where($where['drawBetween'])
		  	 	 ->where($where['drawTime'])
		  	 	 ->where(['withdraw_amount' => ['<>' , 0]])
		  	 	 ->order('withdraw_id', 'desc')
		  	 	 ->paginate(Config::get('page_size') , false , ['query'=>Request::instance()->param()]);
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
        $r= $request->param();
  	 	 $where['conditions']=['commission_type'=>2,'commission_state'=>1];
  	 	 $where['conditions_member']=$request->param('member_nick') ? ['member_nick|member_mobile'=>['like','%'.$request->param('member_nick').'%']] : '';
  	 	 $where['whereBetween']=($request->param('min_money') && $request->param('max_money')) ? ['commission_money'=>['between',[$request->param('min_money'), $request->param('max_money')]]] : '';
         if($request->param('beginTime') && $request->param('endTime')){
            $where['timeBetween']=['commission_creat_time'=>['between time',[$request->param('beginTime'), $request->param('endTime')]]];
         }else{
            $where['timeBetween']=['commission_creat_time'=>['between time',[strtotime("-7 days"),time()]]];
            $r['beginTime']=date('Y-m-d',strtotime("-7 days"));
            $r['endTime']=date('Y-m-d',time());
         }
  	 	 // $where['timeBetween']=($request->param('beginTime') && $request->param('endTime')) ? ['commission_creat_time'=>['between',[$request->param('beginTime'), $request->param('endTime')]]] : '';
  	 	 //获取分佣列表
  	 	 $data['list']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->where(['commission_money' => ['<>',0]])
				  	 	 ->order('commission_id', 'desc')
				  	 	 ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
  	 	 //获取共多少笔分佣
  	 	 $data['count']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
 	 					 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->where(['commission_money' => ['<>' , 0]])
				  	 	 ->count();
  	 	 //获取总金额
  	 	 $data['money']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
	 					 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->where(['commission_money' => ['<>' , 0]])
				  	 	 ->sum('commission_money');
  	 	 $this->assign('data',$data);
  	 	 $this->assign('conditions', $r);
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
        $r=input();
  	 	 $count['money']=0;
  	 	 $count['order_charge']=0;
  	 	 $count['charge']=0;
  	 	 $count['yingli']=0;
  	 	 $count['fenrun']=0;
  	 	 $count['fenrun_yingli']=0;
  	 	 $where['conditions']=['commission_type'=>['<>','2'],'commission_state'=>1];
  	 	 $where['conditions_member']=$request->param('member_nick') ? ['member_nick|member_mobile'=>['like','%'.$request->param('member_nick').'%']] : '';
  	 	 $where['whereBetween']=($request->param('min_money') && $request->param('max_money')) ? ['commission_money'=>['between',[$request->param('min_money'), $request->param('max_money')]]] : '';
  	 	 $endTime=date("Y-m-d",strtotime(request()->param('endTime'))+24*3600);
         if($request->param('beginTime') && $request->param('endTime')){
            $where['timeBetween']=['commission_creat_time'=>['between time',[$request->param('beginTime'), $request->param('endTime')]]];
         }else{
            $where['timeBetween']=['commission_creat_time'=>['between time',[strtotime("-7 days"),time()]]];
            $r['beginTime']=date('Y-m-d',strtotime("-7 days"));
            $r['endTime']=date('Y-m-d',time());
         }
  	 	 // $where['timeBetween']=($request->param('beginTime') && $request->param('endTime')) ? ['commission_creat_time'=>['between time',[$request->param('beginTime'), $endTime]]] : '';
  	 	 // var_dump($where['whereBetween']);die;
  	 	 //获取分佣列表
  	 	 $list=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->where(['commission_money' => ['<>' , 0]])
				  	 	 ->order('commission_id', 'desc')
				  	 	 ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
			foreach ($list as $key => $value) {
				if($value['commission_type']==1){
					$order=CashOrder::where(['order_id'=>$value['commission_from']])->find();
					$passageway=Passageway::where(['passageway_id'=>$order['order_passway']])->find();
					//刷卡金额
					$list[$key]['order_money']=$order['order_money'];
					//刷卡手续费
					$list[$key]['order_charge']=$order['order_charge']+$order['order_buckle'];
					//成本手续费
					$list[$key]['charge']=$order['order_passway_profit'];
					//通道类型
					$list[$key]['passageway']=$passageway['passageway_name'];
					//盈利分润
					if($value['commission_member_id']<=0){
						$list[$key]['yingli']=$list[$key]['order_charge']-$list[$key]['charge'];
					}else{
						$list[$key]['yingli']=$list[$key]['order_charge']-$list[$key]['charge']-$value['commission_money'];
					}
					
					$count['money']+=$order['order_money'];
					$count['order_charge']+=$list[$key]['order_charge'];
					$count['charge']+=$list[$key]['charge'];
					$count['yingli']+=$list[$key]['order_charge']-$list[$key]['charge'];
					$count['fenrun']+=$value['commission_money'];
					$count['fenrun_yingli']+=$list[$key]['yingli'];
				}elseif($value['commission_type']==3){
					$order=GenerationOrder::where(['order_id'=>$value['commission_from']])->find();
					$passageway=Passageway::where(['passageway_id'=>$order['order_passageway']])->find();
					//刷卡金额
					$list[$key]['order_money']=$order['order_money'];
					//刷卡手续费
					$list[$key]['order_charge']=$order['order_pound']+$order['order_buckle'];
					//成本手续费
					$list[$key]['charge']=$order['order_passageway_fee'];
					//通道类型
					$list[$key]['passageway']=$passageway['passageway_name'];
					//盈利分润
					if($value['commission_member_id']<=0){
						$list[$key]['yingli']=$list[$key]['order_charge']-$list[$key]['charge'];
					}else{
						$list[$key]['yingli']=$list[$key]['order_charge']-$list[$key]['charge']-$value['commission_money'];
					}
					$count['money']+=$order['order_money'];
					$count['order_charge']+=$list[$key]['order_charge'];
					$count['charge']+=$list[$key]['charge'];
					$count['yingli']+=$list[$key]['order_charge']-$list[$key]['charge'];
					$count['fenrun']+=$value['commission_money'];
					$count['fenrun_yingli']+=$list[$key]['yingli'];
				}else{
					//刷卡金额
					$list[$key]['order_money']=0;
					//刷卡手续费
					$list[$key]['order_charge']=0;
					//成本手续费
					$list[$key]['charge']=0;
					//通道类型
					$list[$key]['passageway']=0;
					//盈利分润
					$list[$key]['yingli']=0;
				}

			}
  	 	 //获取共多少笔分佣
  	 	 $data['count']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->where(['commission_money' => ['<>' , 0]])
				  	 	 ->count();
  	 	 //获取总金额
  	 	 $data['money']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->where(['commission_money' => ['<>' , 0]])
				  	 	 ->sum('commission_money');
				  	 	 // var_dump($data['list'][0]->toArray());die;
  	 	 $this->assign('list',$list);
  	 	 $this->assign('count',$count);
  	 	 $this->assign('data',$data);
  	 	 $this->assign('conditions', $r);
		 #渲染视图
		 return view('admin/financial/fenrun');
  	 }

 }
