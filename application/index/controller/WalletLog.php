<?php
/**
 * WalletLog controller / 钱包日志管理控制器
 * @authors GongKe(755969423@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use app\index\model\WalletLog as WalletLogs;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;

class WalletLog extends Common
{

    //-------------------------------------------------------

    			#钱包日志(WalletLog/index)

    //-------------------------------------------------------
	public function index($member_nick='')
	{

		#查询出会员列表
		$list = WalletLogs::with('wallet')->order('log_id', 'desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		
		$this->assign('list', $list);

		return view('admin/walletlog/index');
	}


}
