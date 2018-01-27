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
			// $endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['generation_add_time']=["between time",[request()->param('beginTime'),request()->param('endTime')]];
		}
		#需还款信用卡
		if( request()->param('generation_card')){
			$where['generation_card'] = ['like',"%".request()->param('generation_card')."%"];
		}else{
			$r['generation_card'] = '';
		}
		#计划状态查询
		$where['generation_state'] = array("<>",1);
		#计划订单列表
		if(request()->param('generation_state')){
			$where['generation_state'] = request()->param("generation_state");
		}else{
			$r['generation_state'] = '';
		}
		$data = Generation::with("member,creditcard")->where($where)->order("generation_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		#还款总金额
		$sum = Generation::with("member,creditcard")->where($where)->sum("generation_total");
		$this->assign("sum",$sum);
		#剩余还款总额
		$surplussum = Generation::with("member,creditcard")->where($where)->sum("generation_total");
		$this->assign("surplussum",$surplussum);
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
		$where['order_no'] = input('id');
		$list = GenerationOrder::with("passageway,member")->where($where)->select();
		// dump(GenerationOrder::getLastsql());die;
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
	 	 //注册时间
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['generation_add_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		#需还款信用卡
		 if( request()->param('generation_card')){
			$where['generation_card'] = ['like',"%".request()->param('generation_card')."%"];
		}else{
			$r['generation_card'] = '';
		}
		#失败条件
		#计划订单列表

		$where['generation_state'] = -1;
		$data = Generation::with("member,creditcard")->where($where)->order("generation_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		#还款总金额
		$sum = Generation::with("member,creditcard")->where($where)->sum("generation_total");
		$this->assign("sum",$sum);
		#剩余还款总额
		$surplussum = Generation::with("member,creditcard")->where($where)->sum("generation_total");
		$this->assign("surplussum",$surplussum);
		#计算总条数
		$count = Generation::with("member,creditcard")->where($where)->count();
		//用户组
		$this->assign("member_group",MemberGroup::all());
		$this->assign("list",$data);

		$this->assign("count",$count);
		$this->assign("r",$r);
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
	
}