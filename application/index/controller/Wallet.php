<?php
/**
 * Wallet controller / 会员钱包管理控制器
 * @authors GongKe(755969423@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use app\index\model\Wallet as Wallets;
use app\index\model\WalletLog;
use think\Controller;
use think\Request;
use app\index\model\MemberGroup;
use think\Session;
use think\Config;

class Wallet extends Common
{
    	#钱包列表(member/index)
	public function index($member_nick='')
	{
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
		$wheres = array();
		 if( request()->param('cert_member_idcard')){
			$wheres['m.cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
		}else{
			$r['cert_member_idcard'] = '';
		}
		#查询出会员列表
		$list = Wallets::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->field("member_nick")->where($wheres)->order('wallet_id', 'desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		$this->assign('list', $list);
		#统计数据总条数
		$count =  Wallets::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->count();
		#余额总数
		$wallet_amount =  Wallets::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->sum("wallet_amount");
		$this->assign("wallet_amount",$wallet_amount);
		#用户总收益总数
		$wallet_total_revenue =  Wallets::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->sum("wallet_total_revenue");
		$this->assign("wallet_total_revenue",$wallet_total_revenue);
		#分佣收益总数
		$wallet_commission =  Wallets::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->sum("wallet_commission");
		$this->assign("wallet_commission",$wallet_commission);
		#分润收益总数
		$wallet_fenrun =  Wallets::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->sum("wallet_fenrun");
		$this->assign("wallet_fenrun",$wallet_fenrun);
		$this->assign("count",$count);
		$this->assign('r', $r);
		#获取用户分组
		$member_group=MemberGroup::all();
		$this->assign('member_group', $member_group);
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
		$where['log_wallet_id'] = Request::instance()->param('id');
		$where['log_wallet_amount'] = array("<>",0);
	 	if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['log_add_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		if(request()->param('log_wallet_type')!=''){
			$where['log_relation_type'] = array("=",request()->param('log_wallet_type'));
		}
		$wallet = Wallets::get(Request::instance()->param('id'));
		
		$WalletLog =WalletLog::where($where)->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		$count = WalletLog::where($where)->count();
		$this->assign("count",$count);
		$this->assign("log_wallet_id",Request::instance()->param('id'));
		#计算进账
		$entertottal = WalletLog::where($where)->where(['log_relation_type' => 1])->sum("log_wallet_amount");
		$this->assign("entertottal",$entertottal);
		#计算出账
		$leavetotal = WalletLog::where($where)->where(['log_relation_type' => 2])->sum("log_wallet_amount");
		$this->assign("leavetotal",$leavetotal);
		$this->assign('wallet', $wallet);
		$this->assign('WalletLog', $WalletLog);
		return view('admin/wallet/log');
	}
}
