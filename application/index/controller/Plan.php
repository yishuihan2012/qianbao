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
			$where['member_creat_time']=["between time",[request()->param('beginTime'),request()->param('endTime')]];
		}
		#身份证查询
		 if( request()->param('cert_member_idcard')){
			$where['cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
		}else{
			$r['cert_member_idcard'] = '';
		}

		#计划订单列表
		$data = Generation::with("member,creditcard")->where($where)->order("generation_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
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
			$where['member_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		#身份证查询
		 if( request()->param('cert_member_idcard')){
			$where['cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
		}else{
			$r['cert_member_idcard'] = '';
		}
		#失败条件
		#计划订单列表

		$where['generation_state'] = -1;
		$data = Generation::with("member,creditcard")->where($where)->order("generation_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
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