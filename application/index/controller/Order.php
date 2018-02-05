<?php
/**
 *  @version Order controller / 订单控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use think\Db;
use app\index\model\Order as Orders;
use app\index\model\Withdraw;
use app\index\model\WalletLog;
use app\index\model\CashOrder;
use app\index\model\Recomment;
use app\index\model\Member;
use app\index\model\MemberGroup;
use app\index\model\Wallet;
use app\index\model\Upgrade;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Order extends Common{
	 #order列表
	 public function index()
	 {
	 	$r=request()->param();
	 	 #搜索条件
	 	$data = memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
	 	 //订单创建时间
	 	$wheres = array();
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$wheres['upgrade_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		#身份证查询
		if( request()->param('cert_member_idcard')){
			$wheres['m.cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
		}else{
			$r['cert_member_idcard'] = '';
		}
		#订单支付状态
		if(request()->param('upgrade_state')!=''){
			$wheres['upgrade_state'] = request()->param('upgrade_state');
		}else{
			$r['upgrade_state'] = '';
		}
		#支付类型
		// #查询订单列表分页
	 	$order_lists = Upgrade::haswhere('member',$where)
	 		->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")
	 		->where($wheres)->field('wt_member.member_nick')->order("upgrade_id desc")
	 		->paginate(Config::get('page_size'),false, ['query'=>Request::instance()->param()]);
	 	 #统计订单条数
	 	$count['count_size']=Upgrade::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->count();
	 	 #升级总金额
	 	$count['upgrade_money'] = Upgrade::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->sum("upgrade_money");
	 	 #升级未支付金额
	 	  $count['upgrade_money_del'] = Upgrade::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->where(['upgrade_state'=>0])->sum("upgrade_money");
	 	  #升级已支付的金额
	 	   $count['upgrade_money_yes'] = Upgrade::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->where(['upgrade_state'=>1])->sum("upgrade_money");

	 	$count['upgrade_commission'] = Upgrade::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->sum("upgrade_commission");
		$this->assign('order_lists', $order_lists);
	    $this->assign('count', $count);
		 
		$this->assign('r', $r);
		 #获取用户分组
		$member_group=MemberGroup::all();
		$this->assign('member_group', $member_group);
		 #渲染视图
		return view('admin/order/index');
	 }
	 #订单详情
	 public function edit(Request $request){

	 	if(!$request->param('id'))
	 	 {
			 Session::set('jump_msg', ['type'=>'error','msg'=>'参数错误']);
			 $this->redirect($this->history['1']);
	 	 }

	 	 #查询到当前订单的基本信息
	 	 $order_info=Upgrade::with('member')->find($request->param('id'));
	 	 #升级前的用户组
	 	 $front_group = MemberGroup::get($order_info['upgrade_before_group']);
	 	 $this->assign("front_group",$front_group);
	 	 #升级后的用户组
	 	 $after_group = MemberGroup::get($order_info['upgrade_group_id']);
	 	 $this->assign("after_group",$after_group);
	 	 $this->assign('order_info', $order_info);
	 	 return view('admin/order/edit');
	 }

	 #提现订单
	 public function withdraw(){
	 	$r=request()->param();
	 	 #搜索条件
	 	$data = memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
	 	 //订单下单时间
	 	$wheres = array();
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$wheres['withdraw_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		#提现状态
		if(request()->param('withdraw_state') ){
			$wheres['withdraw_state'] = request()->param('withdraw_state');
		}else{
			$r['withdraw_state'] = request()->param('withdraw_state');
		}
		#支付类型
		if(request()->param('withdraw_method') ){
			$wheres['withdraw_method'] = request()->param('withdraw_method');
		}else{
			$r['withdraw_method'] = request()->param('withdraw_method');
		}
		#身份证查询
		if( request()->param('cert_member_idcard')){
			$wheres['m.cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
		}else{
			$r['cert_member_idcard'] = '';
		}
		//管理员列表
		$admins=db('adminster')->column('adminster_id,adminster_login');
	 	 // #查询订单列表分页
	 	$order_lists = Withdraw::haswhere('member',$where)
	 	 	->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")
	 	 	->where($wheres)
	 	 	->order('withdraw_add_time desc')
	 	 	->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	//取出审批人姓名替换
	 	foreach ($order_lists as $k => $v) {
	 		if($v['withdraw_option']!=0)
	 			$order_lists[$k]['withdraw_option']=$admins[$v['withdraw_option']];
	 	}

	 	 #统计订单条数
	 	 $countmoney=Withdraw::where('withdraw_state=12')->sum('withdraw_amount');
	 	 #提现金额
	 	 $count['withdraw_total_money'] = Withdraw::where([])->sum('withdraw_total_money');
	 	 #操作全额
	 	 $count['withdraw_amount'] = Withdraw::where([])->sum('withdraw_amount');
	 	 #操作手续费
	 	 $count['withdraw_charge'] = Withdraw::where([])->sum('withdraw_charge');
	 	 $count['count_size']=Withdraw::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->count();
		 $this->assign('order_lists', $order_lists);
		 $this->assign('countmoney', $countmoney);
		 $this->assign('count', $count);
		 #获取用户分组
		$member_group=MemberGroup::all();
		$this->assign('member_group', $member_group);
		$this->assign('r', $r);
		 #渲染视图
	 	return view('admin/order/withdraw');
	 }

	  #提现订单详情
	 public function showwithdraw(Request $request){
	 	if(!$request->param('id'))
	 	 {
			 Session::set('jump_msg', ['type'=>'error','msg'=>'参数错误']);
			 $this->redirect($this->history['1']);
	 	 }
	 	 #查询到当前订单的基本信息
	 	 $order_info=Withdraw::with('member,adminster')->find($request->param('id'));
	 	 // var_dump($order_info);die;
	 	 $this->assign('order_info', $order_info);
	 	 return view('admin/order/showwithdraw');
	 }
	 #审核提现列表
	 public function toexminewithdraw(){
	 	if(request()->isPost()){
	 		$param=request()->param();
 			$Withdraw = Withdraw::get($param['withdraw_id']);
			$result=true;
	 		//审核通过
	 		if($param['withdraw_state']==12){
	 			//支付宝仅支持小数点后2位，数据库中存储的为小数点后4位，转换
              $Withdraw->withdraw_amount=substr($Withdraw->withdraw_amount, 0,-2);
	 			//调用支付接口
              $payMethod="\app\index\controller\\".$Withdraw->withdraw_method;
              $payment=new $payMethod();
              $return=$payment->transfer($Withdraw); //转账
              if ($return['code'] != "200") {
              	trace($return);
               	$result=false;
              }else{
              	$param['withdraw_option']=session('adminster.id');
              	$Withdraw->allowField(['withdraw_state','withdraw_option'])->save($param);
	              $message="您的提现已经通过,请查收~";
	              jpush($Withdraw->withdraw_member,$message,$message,$message,4);
              }
	 		//审核不通过
	 		}else{
	           Db::startTrans();
	           try{
					$Withdraw->withdraw_state=-12;
					$Withdraw->withdraw_information=$param['withdraw_information'];
					$Withdraw->withdraw_option=session('adminster.id');

					//恢复用户钱包数据
					$Wallet=Wallet::get(['wallet_member'=>$Withdraw->withdraw_member]);
					$Wallet->wallet_total_withdraw=$Wallet['wallet_total_withdraw']-$Withdraw['withdraw_total_money'];
					$Wallet->wallet_amount=$Wallet['wallet_amount']+$Withdraw['withdraw_total_money'];
					//对钱包日志修改描述说明
					$wallet_log=WalletLog::get(['log_wallet_id'=>$Wallet->wallet_id,'log_relation_type'=>2,'log_relation_id'=>$Withdraw->withdraw_id]);
					// trace($wallet_log);
					$wallet_log->log_desc="您的提现已驳回,驳回原因：".$param['withdraw_information'];
					if($Wallet->save()===false || $Withdraw->save()===false || $wallet_log->save()===false){
                      Db::rollback();
                      $result=false;
					}else{
						Db::commit();
						jpush($Withdraw->withdraw_member,$wallet_log->log_desc,$wallet_log->log_desc,$wallet_log->log_desc,4);
					}
	           } catch (\Exception $e) {
	                 Db::rollback();
	           	trace($e->getMessage());
	                 $result=false;
	           }
	 		}
			$content = $result ? ['type'=>'success','msg'=>'审核成功'] : ['type'=>'warning','msg'=>'审核失败'];
			Session::set('jump_msg', $content);
			$this->redirect('order/withdraw');
	 	}
	 	$this->assign("id",input("id"));
	 	return view("admin/order/toexminewithdraw");
	 }

	  #快捷支付订单
	 public function cash(){
	 	$r=request()->param();
	 	 #搜索条件
	 	$data = memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
	 	 //注册时间
	 	$wheres = array();
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['order_add_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		#身份证查询
		
		if( request()->param('order_creditcard')){
			$wheres['order_creditcard'] = ['like',"%".request()->param('order_creditcard')."%"];
		}else{
			$r['order_creditcard'] = '';
		}
		#订单状态
		if( request()->param('order_state')){
			$wheres['order_state'] = ['like',"%".request()->param('order_state')."%"];
		}else{
			$r['order_state'] = '';
		}
	 	 // #查询订单列表分页
	 	 $order_lists = CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->order("order_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	
	 	 #统计订单条数
	 	 $count['count_size']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->count();
	 	 #交易总金额
	 	 $count['order_money']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->sum("order_money");
	 	  #交易成功金额
	 	  $count['order_money_yes']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->where(['order_state'=>2])->sum("order_money");
	 	 #交易未成功
	 	  $count['order_money_del']=$count['order_money'] - $count['order_money_yes'];
	 	 #交易总手续费
	 	 $count['order_charge']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->sum("order_charge");

		 $this->assign('order_lists', $order_lists);
		 $this->assign('count', $count);
		if(!Request::instance()->param('member_nick')){
		 	$where['member_nick']='';
		 }
		 if(!Request::instance()->param('member_mobile')){
		 	$where['member_mobile']='';
		 }
		 $member_group=MemberGroup::all();
		$this->assign('member_group', $member_group);
		$this->assign('r', $r);
		 #渲染视图
	 	return view('admin/order/cash');
	 }
	 #银行交易信息详情
	 public function showcash(){
	 	$where['order_id'] = request()->param("id");
	 	$info =  CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->field("member_nick,member_mobile")->where($where)->find();
	 	$this->assign("info",$info);
	 	return view("admin/order/showcash");
	 }
	  #成功交易订单
	 public function successCash(){
	 	$r=request()->param();
	 	 #搜索条件
	 	$data = memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
	 	 //注册时间
		$wheres = array();
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['order_add_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		#身份证查询
		if( request()->param('order_creditcard')){
			$wheres['order_creditcard'] = ['like',"%".request()->param('order_creditcard')."%"];
		}else{
			$r['order_creditcard'] = '';
		}
		#订单状态
		if( request()->param('order_state')){
			$wheres['order_state'] = ['like',"%".request()->param('order_state')."%"];
		}else{
			$r['order_state'] = '';
		}
		$where['order_state'] = 2;
	 	 // #查询订单列表分页
	 	 $order_lists = CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->order("order_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	
	 	 #统计订单条数
	 	 $count['count_size']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->count();
	 	  #交易总金额
	 	 $count['order_money']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->sum("order_money");
	 	 #交易总手续费
	 	 $count['order_charge']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->sum("order_charge");
			 $this->assign('order_lists', $order_lists);
			 $this->assign('count', $count);
		if(!Request::instance()->param('member_nick')){
		 	$where['member_nick']='';
		 }
		 if(!Request::instance()->param('member_mobile')){
		 	$where['member_mobile']='';
		 }
		 $member_group=MemberGroup::all();
		$this->assign('member_group', $member_group);
		$this->assign('r', $r);
		 #渲染视图
	 	return view('admin/order/successCash');
	 }
	   #实名红包订单
	 public function recomment(){
	 	 // #查询订单列表分页
	 	  #如果有查询条件
	 	 $r=request()->param();
	 	 #搜索条件
	 	$data = memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
	 	 //注册时间
	 	$wheres = array();
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$wheres['recomment_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		#身份证查询
		 if( request()->param('cert_member_idcard')){
			$wheres['m.cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
		}else{
			$r['cert_member_idcard'] = '';
		}

	 	 // #查询订单列表分页
	 	 $order_lists = Recomment::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->order("recomment_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	 foreach ($order_lists as $key => $value) {
	 	 		$order_lists[$key]['recomment_member_name']=Member::where(['member_id'=>$value['recomment_member_id']])->value('member_nick');
	 	 		$order_lists[$key]['recomment_children_name']=Member::where(['member_id'=>$value['recomment_children_member']])->value('member_nick');
	 	 }
	 	 $countmoney=Recomment::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->sum('recomment_money');
	 	 #统计订单条数
	 	 $count['count_size']=Recomment::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->count();
			 $this->assign('countmoney', $countmoney);
			 $this->assign('order_lists', $order_lists);
			 $this->assign('count', $count);
		 #获取用户分组
		$member_group=MemberGroup::all();
		$this->assign('member_group', $member_group);
	
		 $this->assign('r', $r);
		 #渲染视图
	 	return view('admin/order/recomment');
	 }
}
