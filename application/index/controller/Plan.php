<?php
/**
 *  @version Passageway controller / 还款计划列表
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Passageway as Passageways;
use app\index\model\PassagewayItem;
use app\index\model\MemberGroup;
use app\index\model\Cashout;
use app\index\model\CreditCard;
use app\index\model\Generation;
use app\index\model\GenerationOrder;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;
use think\Db;

class Plan extends Common{
	#还款计划列表
	public function index(){
		$r=request()->param();
	 	 #搜索条件
	 	$data = memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
	 	 //注册时间
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['generation_add_time']=["between time",[request()->param('beginTime'),$endTime]];
		}else{
			$where['generation_add_time']=["between time",[strtotime("-7 days"),time()]];
			$r['beginTime']=date('Y-m-d',strtotime("-7 days"));
			$r['endTime']=date('Y-m-d',time());
		}
		#身份证号码
		if( request()->param('generation_card')){
			$where['generation_card'] = ['like',"%".request()->param('generation_card')."%"];
		}else{
			$r['generation_card'] = '';
		}
		#需还信用卡号
		if( request()->param('card_bankno')){
			$where['card_bankno'] = ['eq',request()->param('card_bankno')];
		}else{
			$r['card_bankno'] = '';
		}
		#代还计划号码
		if( request()->param('generation_no')){
			$where['generation_no'] = ['eq',request()->param('generation_no')];
		}else{
			$r['generation_no'] = '';
		}
		#计划状态查询
		$where['generation_state'] = array("<>",1);
		#计划订单列表
		if(request()->param('generation_state')){
			$where['generation_state'] = request()->param("generation_state");
		}else{
			$r['generation_state'] = '';
		}

		if(request()->param('generation_id')){
			$where['generation_id'] = request()->param("generation_id");
		}
		if(input('is_export')==1){
	 	    $fp = fopen('php://output', 'a');
 	    	#取数据
	 	    $generation_list=db("generation")->alias('g')
	 	    	->join('member m','m.member_id=g.generation_member')
	 	    	->join('member_creditcard c','c.card_bankno=g.generation_card')
	 	    	->where($where)
	 	    	->order("generation_id desc")
	 	    	->field('generation_id,member_nick,member_mobile,generation_no,card_bankno,generation_total,generation_count,generation_has,generation_left,generation_pound,generation_start,generation_end,generation_state,generation_desc')
	 	    	->select();

	 	    $head=['ID','还款会员','手机号码','计划代号','需还信用卡','需还款总额','还款次数','已还款总额','剩余总额','手续费','开始还款日期','最后还款日期','计划状态','还款失败原因'];
	 	    export_csv($head,$generation_list,$fp);
	 	    return;
		}
		$data = Generation::with("member,creditcard")->where($where)->order("generation_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		#还款总金额
		$sum = Generation::with("member,creditcard")->where($where)->sum("generation_total");
		$this->assign("sum",$sum);
		#剩余还款总额
		$surplussum = Generation::with("member,creditcard")->where($where)->sum("generation_total");
		$this->assign("surplussum",$surplussum);
		#还款总笔数
		$count_plan = Generation::with("member,creditcard")->where($where)->sum("generation_count");
		$this->assign("count_plan",$count_plan);
		#计算总条数
		$count = Generation::with("member,creditcard")->where($where)->count();
		//用户组
		$this->assign("member_group",MemberGroup::all());
		$this->assign("list",$data);
		$this->assign("count",$count);
		$this->assign("r",$r);
		return view("admin/plan/index");
	}
	#还款详情
	public function info(){
		#从钱包日志跳转来 单条详情
		if(input('order_id')){
			$where['order_id'] = input('order_id');
		}else{
			#从菜单点击来 计划所有详情
			$where['order_no'] = input('id');
		}
		$list = GenerationOrder::with("passageway,member,memberCreditcard")->where($where)->order('order_time','asc')->select();
		$this->assign("list",$list);
		return view("/admin/plan/info");
	}
	#失败还款计划
	public function fail(){
		$r=request()->param();
	 	 #搜索条件
	 	$data = memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
		$where['order_status'] = -1;
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['order_time']=["between time",[request()->param('beginTime'),$endTime]];
		}else{
			$where['order_time']=["between time",[strtotime("-7 days"),time()]];
			$r['beginTime']=date('Y-m-d',strtotime("-7 days"));
			$r['endTime']=date('Y-m-d',time());
		}
		if(request()->param('order_money')!=''){
			$where['order_money'] = request()->param('order_money');
		}else{
			$r['order_money'] = ''; 
		}
		if(request()->param('order_no')!=''){
			$where['order_no'] = request()->param('order_no');
		}else{
			$r['order_no'] = '';
		}
		$list = GenerationOrder::with("passageway,member")->join("wt_generation","generation_id=order_no")->where($where)->where([])->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		$this->assign('r',$r);
		$this->assign("list",$list);
		return view("admin/plan/fail");
	}
	#取消执行|继续执行还款计划
	public function order_status(){
		$where['order_id'] = request()->param("id");
		$data['order_status'] = request()->param("status");

		$result	= GenerationOrder::where($where)->update($data);
		// $result	= GenerationOrder::where($where)->update($data);
		// if(!$result)
		// 	die;
		 #数据是否提交成功
		 $content = ($result===false) ? ['type'=>'error','msg'=>'操作失败'] : ['type'=>'success','msg'=>'操作成功'];
		 Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect("Plan/index");
	}

	/**
	 *  @version detail controller / 总还款列表详情
	 *  @author $Mr.gao$(928791694@qq.com)
	 *   @datetime    2017-02-27 09:34:05
	 *   @return 
	 */

	 public function detail(){

	 	$r=request()->param();
	 	 #搜索条件
	 	$data = memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
		$where['order_status'] = -1;
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=date("Y-m-d",strtotime(request()->param('endTime'))+24*3600);
			$where['order_time']=["between",[request()->param('beginTime'),$endTime]];
			$this->assign('beginTime',request()->param('beginTime'));
			$this->assign('endTime',request()->param('endTime'));
		}else{
			$where['order_time']=["between time",[strtotime("-7 days"),time()]];
			$r['beginTime']=date('Y-m-d',strtotime("-7 days"));
			$r['endTime']=date('Y-m-d',time());
		}
		if(request()->param('order_money')!=''){
			$where['order_money'] = request()->param('order_money');
		}else{
			$r['order_money'] = ''; 
		}

		if(request()->param('order_type')!=''){
			$where['order_type'] = request()->param('order_type');
		}else{
			$r['order_type'] = ''; 
		}

		if(request()->param('id')!=''){
			$where['order_id'] = request()->param('id');
			$r['order_id']=request()->param('id');
		}else{
			$r['order_id'] = '';
		}

		// if(request()->param('order_no')!=''){
		// 	$where['order_no'] = request()->param('order_no');
		// }else{
		// 	$r['order_no'] = '';
		// }

		#计划状态查询
		$where['order_status'] = array("<>",1);
		#计划订单列表
		if(request()->param('order_status')){
			$where['order_status'] = request()->param("order_status");
		}else{
			$r['order_status'] = '';
		}

		// $generation = db("Generation")->alias('w')->where('generation_state=1')->field('generation_id')->select();
		// $generation_id=array();
		// foreach ($generation as $key => $value) {
		// 	$generation_id[]=$value['generation_id'];
		// }

	 	// $list = GenerationOrder::with("passageway,member,memberCreditcard")->where($where)->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	// foreach ($list as $key => $value) {
	 	// 	if(in_array($value->order_no, $generation_id)){
	 	// 		unset($list[$key]);
	 	// 	}
	 	// }

	 	$generation = Generation::where('generation_state!=1')->select();
	 	$generation_id=array();
	 	foreach ($generation as $key => $value) {
			$generation_id[]=$value['generation_id'];
		}
	 	$generation_id=implode(',', $generation_id);

		if(input('is_export')==1){
	 	    $fp = fopen('php://output', 'a');
	 	    $type=['1'=>'消费','2'=>'还款'];
	 	    $status=['1'=>'待执行','-1'=>'失败','2'=>'成功','3'=>'取消','4'=>'待查证处理中'];
 	    	#取数据
	 	    $order_list=db("generation_order")->alias('o')
	 	    	->join('passageway p','p.passageway_id=o.order_passageway')
	 	    	->join('member m','m.member_id=o.order_member')
	 	    	->join('member_creditcard c','c.card_bankno=o.order_card')
	 	    	->where($where)
	 	    	->where('order_no in ('.$generation_id.')')
	 	    	->order("order_id desc")
	 	    	->field('order_id,passageway_name,member_nick,member_mobile,order_type,concat("`",order_card),card_bankname,order_money,order_pound,order_status,order_retry_count,back_statusDesc,order_desc,order_time,order_add_time')
	 	    	->select();
	 	    foreach ($order_list as $k => $v) {
	 	    	$order_list[$k]['order_type']=$type[$v['order_type']];
	 	    	$order_list[$k]['order_status']=$status[$v['order_status']];
	 	    }
	 	    $head=['ID','通道','姓名','手机号','订单类型','信用卡号','银行名称','订单金额','订单手续费','订单状态','重新执行次数','执行结果','订单描述','订单执行时间','订单创建时间'];
	 	    export_csv($head,$order_list,$fp);
	 	    return;
		}

		//消费总金额
		$order['money']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=1')->where($where)->sum('order_money');
		//全部手续费
		$order['change']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=1')->where($where)->sum('order_pound');
		//成本手续费
		$order['chengben']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=1')->where($where)->sum('order_passageway_fee');
		//盈利分润
		$order['yingli']=$order['change']-$order['chengben'];
		//三级分润消耗
		$order['fen']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=1')->where($where)->sum('order_fen');
		//分润后平台盈利
		$order['fenrunhou']=$order['yingli']-$order['fen'];
		//消费笔数
		$order['consumption']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=1')->where($where)->count();
		//还款笔数
		$order['repayment']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=2')->where($where)->count();


	 	$list=GenerationOrder::with("passageway,member,memberCreditcard")->where($where)->where('order_no in ('.$generation_id.')')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	$count = GenerationOrder::with("passageway,member,memberCreditcard")->where($where)->where('order_no in ('.$generation_id.')')->count();

	 	$this->assign('r',$r);
		$this->assign("list",$list);
		$this->assign("count",$count);
		$this->assign("order",$order);

	 	return view("admin/plan/detail");
	 }

	  /**
	 *  @version edit_status controller / 修改订单状态
	 *  @author $Mr.gao$(928791694@qq.com)
	 *   @datetime    2017-02-27 09:34:05
	 *   @return 
	 */

	  public function edit_status(){
	  	$order_status = GenerationOrder::where(['order_id'=>request()->param('id')])->value('order_status');

	  	$this->assign("order_status",$order_status);
	  	$this->assign("id",request()->param('id'));
	  	if($_POST){
	  		$data=array(
	  			'order_status'=>request()->param('order_status')
	  		);
	  		$status=GenerationOrder::where(['order_id'=>request()->param('id')])->update($data);

	  		$content = ($status===false) ? ['type'=>'error','msg'=>'修改状态失败'] : ['type'=>'success','msg'=>'修改状态成功'];

	  		Session::set('jump_msg', $content);
	 		$this->redirect("plan/detail");
	  	}

	 	return view("admin/plan/edit_status");
	  }
	
}