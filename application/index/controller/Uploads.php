<?php
/**
 *  @version Uploads controller / 图片上传控制器logo
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
 use think\Db;
use app\index\model\Withdraw;
use app\index\model\System;
use app\index\model\MemberGroup;
use app\index\model\WalletLog;
use app\index\model\CashOrder;
use app\index\model\Recomment;
use app\index\model\Member;
use app\index\model\Wallet;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Uploads extends Common{
	  public function logo()
	 {	 	
	 	$logo=System::getName('system_url').'/static/images/logo.png';
	 	$this->assign('logo',$logo);
		 #渲染视图
		return view('admin/uploads/logo');
	 }
}
