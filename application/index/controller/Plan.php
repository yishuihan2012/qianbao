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
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;
use think\Db;

class Plan extends Common{
	public function index(){
		return view("/admin/Plan/index");
	}
}