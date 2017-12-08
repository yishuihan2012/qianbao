<?php
/**
 * Wallet controller / 会员钱包管理控制器
 * @authors GongKe(755969423@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use app\index\model\Wallet as Wallets;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;

class Wallet extends Common
{
    	#钱包列表(member/index)
	public function index($member_nick='')
	{
		#查询出会员列表
		$list = Wallets::with('member')->order('wallet_id', 'desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		$this->assign('list', $list);
		return view('admin/wallet/index');
	}

    	#冻结/解冻钱包(freezing)
	public function freezing()
	{
		#接收封停账户的ID
		$wallet = Wallets::get(Request::instance()->param('id'));
		#获取当前的用户登录状态
		$state=$wallet->wallet_state;
		#更新登录表登录状态信息
		$wallet->wallet_state = $state=='2' ? '-2' : '2';
		$wallet->wallet_desc = Request::instance()->param('wallet_desc');
		#更新保存
		$result=$wallet->save();
		#返回状态
		$context=$result ? '1' : '0';
		#输出json
		echo json_encode($context);
	}
    	#查看单个会员钱包日志(look_log)
	public function look_log(Request $request)
	{
		$wallet = Wallets::get(Request::instance()->param('id'));
		$this->assign('wallet', $wallet);
		return view('admin/wallet/log');
	}
}
