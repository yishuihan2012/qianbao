<?php
/**
 *  @version Order controller / 订单控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Order as Orders;
use app\index\model\Withdraw;
use app\index\model\CashOrder;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Order extends Common{
	 #order列表
	 public function index()
	 {
	 	 // #查询订单列表分页
	 	 $order_lists=Orders::with('member')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	 #统计订单条数
	 	 $count['count_size']=Orders::count();
			 $this->assign('order_lists', $order_lists);
			 $this->assign('count', $count);
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
	 	 $order_info=Orders::with('member')->find($request->param('id'));
	 	 $this->assign('order_info', $order_info);
	 	 return view('admin/order/edit');
	 }

	 #提现订单
	 public function withdraw(){
	 	 // #查询订单列表分页
	 	 $order_lists = Withdraw::with('member,adminster')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	 #统计订单条数
	 	 $count['count_size']=Withdraw::count();
			 $this->assign('order_lists', $order_lists);
			 $this->assign('count', $count);
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
	 	if($_POST){
	 		
	 	}
	 	$this->assign("id",input("id"));
	 	return view("admin/order/toexminewithdraw");
	 }

	  #套现订单
	 public function cash(){
	 	 // #查询订单列表分页
	 	 $order_lists = CashOrder::with('passageway')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	 #统计订单条数
	 	 $count['count_size']=CashOrder::count();
			 $this->assign('order_lists', $order_lists);
			 $this->assign('count', $count);
		 #渲染视图
	 	return view('admin/order/cash');
	 }
}
