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
         #导出
         if(input('is_export')){
            $fp = fopen('php://output', 'a');
            $data=db('upgrade')->alias('u')
                ->join('member m','u.upgrade_member_id=m.member_id')
                 ->where(['upgrade_state'=>1])
                 ->where($where['conditions_member'])
                 ->where($where['upgradeTime'])
                 ->where($where['upgradeBetween'])
                 ->where(['upgrade_money' => ['<>',0]])
                 ->order('upgrade_id','desc')
                 ->field('upgrade_id,member_nick,upgrade_money,upgrade_update_time')
                 ->select();
            $head=['ID','会员','金额','时间'];
            export_csv($head,$data,$fp);
            return;
         }
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
         #导出
         if(input('is_export')){
            $fp = fopen('php://output', 'a');
            $data=db('cash_order')->alias('g')
                ->join('member m','g.order_member=m.member_id')
                 ->where(['order_state'=>2])
                 ->where($where['conditions_member'])
                 ->where($where['cashTime'])
                 ->where($where['payBetween'])
                 ->where(['order_platform' => ['<>',0]])
                 ->order('order_id','desc')
                 ->field('order_id,concat("`",order_no),member_nick,order_money,order_platform,order_buckle,order_update_time')
                 ->select();
            $head=['ID','订单号','会员','订单金额','平台收益','代扣费','时间'];
            export_csv($head,$data,$fp);
            return;
         }
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
         #导出
         if(input('is_export')){
            $fp = fopen('php://output', 'a');
            $data=db('generation_order')->alias('g')
                ->join('member m','g.order_member=m.member_id')
                 ->where($where['conditions_member'])
                 ->where(['order_type'=>1,'order_status'=>2])
                 ->where($where['autoBetween'])
                 ->where(['order_platform' => ['<>' , 0]])
                 ->where($where['autoTime'])
                 ->field('order_id,order_no,member_nick,order_money,order_platform,order_buckle,order_edit_time')
                 ->select();
            $head=['ID','订单号','会员','订单金额','平台收益','代扣费','时间'];
            export_csv($head,$data,$fp);
            return;
         }
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
         #导出
         if(input('is_export')){
            $fp = fopen('php://output', 'a');
            $data=db('withdraw')->alias('w')
                ->join('member m','w.withdraw_member=m.member_id')
                 ->where(['withdraw_state'=>12])
                 ->where($where['conditions_member'])
                 ->where($where['drawBetween'])
                 ->where($where['drawTime'])
                 ->where(['withdraw_amount' => ['<>' , 0]])
                 ->order('withdraw_id', 'desc')
                 ->field('withdraw_id,concat("`",withdraw_no),member_nick,withdraw_method,withdraw_amount,withdraw_bak,withdraw_update_time')
                 ->select();
            $head=['ID','流水号','会员','方式','金额','备注','时间'];
            export_csv($head,$data,$fp);
            return;
         }
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
            // $where['timeBetween']=['commission_creat_time'=>['between time',[strtotime("-7 days"),time()]]];
            // $r['beginTime']=date('Y-m-d',strtotime("-7 days"));
            // $r['endTime']=date('Y-m-d',time());
             $where['timeBetween']='';
         }
  	 	 // $where['timeBetween']=($request->param('beginTime') && $request->param('endTime')) ? ['commission_creat_time'=>['between',[$request->param('beginTime'), $request->param('endTime')]]] : '';
    if(input('is_export')==1){
        $fp = fopen('php://output', 'a');
        #取数据
        $data=db("commission")->alias('c')
          ->join("member m1",'c.commission_member_id=m1.member_id')
          ->join("member m2",'c.commission_childen_member=m2.member_id')
               ->where($where['conditions'])
               ->where($where['whereBetween'])
               ->where($where['timeBetween'])
               ->where(['commission_money' => ['<>',0]])
          ->order("commission_id desc")
          ->field('commission_id,m1.member_nick,m2.member_nick as nick,commission_money,commission_desc,commission_creat_time')
          ->select();
        $head=['ID','收益人','购买人','金额','备注','时间'];
        export_csv($head,$data,$fp);
        return;
    }
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
  #所有分润
  public function fenrun(){
    $where=$this->fenrun_search();
    #通道
    $passway=Passageway::column("*",'passageway_id');
    $this->assign('passway',$passway);
    $r['passageway_id']=input('passageway_id') ?? '';
    #数据
    $list=db('commission')->alias('c')
      ->join('member m1','c.commission_member_id=m1.member_id')
      ->join('member m2','c.commission_childen_member=m2.member_id')
      ->order('commission_id desc')
      ->where($where);
    #通道类型
    if(input('passway_type') || input('passageway_id')){
      #消费    类型为消费 或 (有传入通道id 且 通道id对应的为消费通道)
      if(input('passway_type')==1 || (input('passageway_id') && $passway[$r['passageway_id']]['passageway_also']==1)){
        $list=$list->join('cash_order o','o.order_id=c.commission_from')
          ->where('c.commission_type',1);
          #有传入通道id 且 通道id对应的为消费通道
        if(input('passageway_id') && $passway[input('passageway_id')]['passageway_also']==1){
          $list=$list->where('order_passway',input('passageway_id'));
        }
        #代还
      }elseif(input('passway_type')==3 || (input('passageway_id') && $passway[$r['passageway_id']]['passageway_also']==2)){
        $list=$list->join('generation_order o','o.order_id=c.commission_from')
          ->where('c.commission_type',3);
          #有传入通道id 且 通道id对应的为消费通道
        if(input('passageway_id') && $passway[input('passageway_id')]['passageway_also']==1){
          $list=$list->where('order_passageway',input('passageway_id'));
        }
      }
    }
    #导出
    if(input('is_export')==1){
        $fp = fopen('php://output', 'a');
        #取数据
        $list=$list->field("c.commission_id,c.commission_from,m1.member_nick as parent,m2.member_nick as child,case when commission_type=1 then '消费' else '代还' end as type,c.commission_money,c.commission_cash_rate,c.commission_cash_fix,c.commission_desc,c.commission_creat_time")
          ->select();
        $head=['分润ID','订单ID','收益人','触发人','类型','分润金额','收益人费率','收益人代扣费','备注','时间'];
        export_csv($head,$list,$fp);
        return;
    }
    $list_obj=clone $list;
    $list_obj->__construct();
    $data=$list_obj->field("count(*) as count,sum(commission_money) as money")->find();
    $list=$list->field("c.*,m1.member_nick as parent,m2.member_nick as child")
      ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);

    $this->assign('data',$data);
    $this->assign('list',$list);
    return view('admin/financial/fenrun');
  }
  #分润搜索条件
  private function fenrun_search(){
    $r=input();
    $where=[];
    if(input('parent'))
      $where['m1.member_nick|m1.member_mobile']=['like','%'.$r['parent'].'%'];
    if(input('child'))
      $where['m2.member_nick|m2.member_mobile']=['like','%'.$r['child'].'%'];
    if(input('commission_from'))
      $where['c.commission_from']=$r['commission_from'];
    if(input('commission_type')){
      $where['c.commission_type']=$r['commission_type'];
    }else{
      $where['c.commission_type']=['in','1,3'];
    }
    if(input('min_money') && input('max_money'))
      $where['c.commission_money']=['between',[input('min_money'),input('max_money')]];
    wheretime($where,'commission_creat_time');
    $this->assign('r',$r);
    return $where;
  }

	 /**
	 *  @version fenrun method / 财务管理--分润统计列表
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2018-1-11 14:45
	 *   @return 
	 */
  	 public function fenrun2(Request $request,$member_id=null)
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
            $where['timeBetween']=['commission_creat_time'=>['between time',[$request->param('beginTime'), $endTime]]];
         }else{
            // $where['timeBetween']=['commission_creat_time'=>['between time',[strtotime("-7 days"),time()]]];
            // $r['beginTime']=date('Y-m-d',strtotime("-7 days"));
            // $r['endTime']=date('Y-m-d',time());
            $where['timeBetween']='';
         }
         // var_dump($where['timeBetween']);die;
        if($request->param('passway')){
  	 	 	$where['passway']=['commission_type'=>$request->param('passway')];
  	 	 	$r['passway']=$request->param('passway');
  	 	 }else{
  	 	 	$r['passway']=1;
  	 	 	$where['passway']=['commission_type'=>1];
  	 	 }

       if($request->param('passway_id')){
        $r['passway_id']=$request->param('passway_id');
        $where['passageway']=['c.order_passway'=>$r['passway_id']];
        $where['passageway_order']=['g.order_passageway'=>$r['passway_id']];
       }else{
        $where['passageway']='';
        $where['passageway_order']='';
        $r['passway_id']='';
       }
  	 	 // var_dump($where['passway']);die;

  	 	 // $where['timeBetween']=($request->param('beginTime') && $request->param('endTime')) ? ['commission_creat_time'=>['between time',[$request->param('beginTime'), $endTime]]] : '';
  	 	 // var_dump($where['whereBetween']);die;
  	 	 //获取分佣列表
  	 	 if($r['passway']==1){
         // $list=Commission::haswhere('member',$where['conditions_member'])->haswhere('cashorder',$where['passageway'])->with('member,members,cashorder')
  	 	 	 $list=Commission::haswhere('member',$where['conditions_member'])
                ->join('cash_order c','c.order_id=Commission.commission_from')
                // ->haswhere('cashorder',$where['passageway'])
               ->with('member,members')
               ->where($where['passageway'])
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->where($where['passway'])

				  	 	 ->where(['commission_money' => ['<>' , 0]])
				  	 	 ->order('commission_id', 'desc')
				  	 	 ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	  	 	}else{
	  	 		 $list=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
                ->join('generation_order g','g.order_id=Commission.commission_from')
                ->where($where['passageway_order'])
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->where($where['passway'])
				  	 	 ->where(['commission_money' => ['<>' , 0]])
				  	 	 ->order('commission_id', 'desc')
				  	 	 ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	  	 	}
  	 	
				  	 	 // var_dump($list[0]);die;
		//总刷卡金额
		 $from_ids=db('commission')->alias('c')
		 	 ->where($where['conditions'])
	  	 	 ->where($where['whereBetween'])
	  	 	 ->where($where['timeBetween'])
	  	 	 ->where($where['passway'])
	  	 	 ->where(['commission_money' => ['<>' , 0]])
	  	 	 ->column('commission_from');

	  	 if($r['passway']==1){
	  	 	 //总刷卡金额
	  	 	 $count['money']=db('cash_order')->where('order_id','in',$from_ids)->sum('order_money');
		  	 //刷卡总手续费
		  	 $count['order_charge']=db('cash_order')->where('order_id','in',$from_ids)->sum('order_charge');
		  	 //成本总手续费
		  	 $count['charge']=db('cash_order')->where('order_id','in',$from_ids)->sum('order_passway_profit');
		  	}else{
		  		 $count['money']=db('generation_order')->where('order_id','in',$from_ids)->sum('order_money');
			  	 //刷卡总手续费
			  	 $count['order_charge']=db('generation_order')->where('order_id','in',$from_ids)->sum('order_pound');
			  	 //成本总手续费
			  	 $count['charge']=db('generation_order')->where('order_id','in',$from_ids)->sum('order_passageway_fee');
		  	}
  	 	
	  	 //总分润金额
	  	  $from_ids=db('commission')->alias('c')
		 	 ->where($where['conditions'])
	  	 	 ->where($where['whereBetween'])
	  	 	 ->where($where['timeBetween'])
	  	 	 ->where($where['passway'])
	  	 	 ->where(['commission_money' => ['<>' , 0]])
	  	 	 ->sum('commission_money');

	  	 $count['yingli']=$count['order_charge']-$count['charge'];
  	 	 //平台的盈利
  	 	 $count['fenrun_yingli']=$count['yingli']-$count['fenrun'];
			foreach ($list as $key => $value) {
				if($value['commission_type']==1){
					$order=CashOrder::where(['order_id'=>$value['commission_from']])->find();
          if($r['passway_id']!='' && $order['order_passway']!=$r['passway_id']){
            unset($list[$key]);
            // break;
          }else{
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
              $list[$key]['yingli']=$list[$key]['order_charge']-$list[$key]['charge'];
            }
          }
					
					
					// $count['money']+=$order['order_money'];
					// $count['order_charge']+=$list[$key]['order_charge'];
					// $count['charge']+=$list[$key]['charge'];
					// $count['yingli']+=$list[$key]['order_charge']-$list[$key]['charge'];
					// $count['fenrun']+=$value['commission_money'];
					// $count['fenrun_yingli']+=$list[$key]['yingli'];
				}elseif($value['commission_type']==3){
					$order=GenerationOrder::where(['order_id'=>$value['commission_from']])->find();
          if($r['passway_id']!='' && $order['order_passageway']!=$r['passway_id']){
            unset($list[$key]);
            // break;
          }else{
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
              $list[$key]['yingli']=$list[$key]['order_charge']-$list[$key]['charge'];
            }
          }
					
					// $count['money']+=$order['order_money'];
					// $count['order_charge']+=$list[$key]['order_charge'];
					// $count['charge']+=$list[$key]['charge'];
					// $count['yingli']+=$list[$key]['order_charge']-$list[$key]['charge'];
					// $count['fenrun']+=$value['commission_money'];
					// $count['fenrun_yingli']+=$list[$key]['yingli'];
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


      if(input('is_export')==1){
        $fp = fopen('php://output', 'a');
        #取数据
        if($r['passway']==1){
          $list=db("commission")->alias('c')
          ->join("member m1",'c.commission_member_id=m1.member_id')
          ->join("member m2",'c.commission_childen_member=m2.member_id')
          ->join('cash_order o','c.commission_from=o.order_id')
               ->where($where['conditions'])
               ->where($where['whereBetween'])
               ->where($where['timeBetween'])
               ->where($where['passway'])
               ->where(['commission_money' => ['<>',0]])
          ->order("commission_id desc")
          ->field('commission_from,m1.member_nick,m2.member_nick as nick,order_money,order_passway_profit,commission_money,commission_desc,commission_creat_time')
          ->select();
          foreach ($list as $key => $value) {
            $order=CashOrder::where(['order_id'=>$value['commission_from']])->find();
            $passageway=Passageway::where(['passageway_id'=>$order['order_passway']])->find();
            //通道类型
            $list[$key]['passageway']=$passageway['passageway_name'];
            //刷卡金额
            $list[$key]['order_money']=$order['order_money'];
            //刷卡手续费
            $list[$key]['order_charge']=$order['order_charge']+$order['order_buckle'];
            
            //盈利分润

              $list[$key]['yingli']=$list[$key]['order_charge']-$order['order_passway_profit'];
          }
          

        }else{
           $list=db("commission")->alias('c')
          ->join("member m1",'c.commission_member_id=m1.member_id')
          ->join("member m2",'c.commission_childen_member=m2.member_id')
          ->join('cash_order o','c.commission_from=o.order_id')
               ->where($where['conditions'])
               ->where($where['whereBetween'])
               ->where($where['timeBetween'])
               ->where($where['passway'])
               ->where(['commission_money' => ['<>',0]])
          ->order("commission_id desc")
          ->field('commission_from,m1.member_nick,m2.member_nick as nick,order_money,order_passway_profit,commission_money,commission_desc,commission_creat_time')
          ->select();
          foreach ($list as $key => $value) {
            $order=GenerationOrder::where(['order_id'=>$value['commission_from']])->find();
          $passageway=Passageway::where(['passageway_id'=>$order['order_passageway']])->find();
          //通道类型
          $list[$key]['passageway']=$passageway['passageway_name'];
          //刷卡金额
          $list[$key]['order_money']=$order['order_money'];
          //刷卡手续费
          $list[$key]['order_charge']=$order['order_pound']+$order['order_buckle'];

          
          //盈利分润
            $list[$key]['yingli']=$list[$key]['order_charge']-$order['order_passway_profit'];

          }
          
        }
          // var_dump($list);die;
        $head=['订单ID','受益人','触发人','刷卡金额','成本手续费','分润金额','备注','时间','通道类型','刷卡手续费','盈利分润'];
        export_csv($head,$list,$fp);
        return;
    }

			// var_dump($list[1]['charge']);die;
  	 	 //获取共多少笔分佣
  	 	 $data['count']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->where($where['passway'])
				  	 	 ->where(['commission_money' => ['<>' , 0]])
               ->where('commission_from','>',0)
				  	 	 ->count();
  	 	 //获取总金额
  	 	 $data['money']=Commission::haswhere('member',$where['conditions_member'])->with('member,members')
				  	 	 ->where($where['conditions'])
				  	 	 ->where($where['whereBetween'])
				  	 	 ->where($where['timeBetween'])
				  	 	 ->where($where['passway'])
				  	 	 ->where(['commission_money' => ['<>' , 0]])
               ->where('commission_from','>',0)
				  	 	 ->sum('commission_money');
				  	 	 // var_dump($data['list'][0]->toArray());die;
               // var_dump($r);die;
        $passageway=Passageway::where('passageway_state=1')->select();
        $this->assign('passageway',$passageway);
  	 	 $this->assign('list',$list);
  	 	 $this->assign('count',$count);
  	 	 $this->assign('data',$data);
  	 	 $this->assign('conditions', $r);
		 #渲染视图
		 return view('admin/financial/fenrun');
  	 }
 }
