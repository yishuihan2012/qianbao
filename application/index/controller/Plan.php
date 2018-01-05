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
		$page = empty(input('page'))?1:input('page');
		$data = GenerationOrder::list($page);
		 
            // dump($list);
		$this->assign("list",$data);
		// $this->assign("page",$data['page']);
		return view("/admin/plan/index");
	}
	#还款详情
	public function info(){
		$where['order_id'] = input('id');
		
		$info = GenerationOrder::info($where);
		 // dump($info);die;
		$this->assign("info",$info);
		return view("/admin/pLan/info");
	}
}