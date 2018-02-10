<?php
/**
 * WalletLog controller / 钱包日志管理控制器
 * @authors GongKe(755969423@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use app\index\model\WalletLog as WalletLogs;
use app\index\model\Commission;
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
		$where['log_wallet_amount'] = array("<>",0);
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['log_add_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		if(request()->param('log_wallet_type')!=''){
			$where['log_relation_type'] = array("=",request()->param('log_wallet_type'));
		}
		if(request()->param('member_nick')!=''){
			$where['member_nick'] =  array("like","%".request()->param('member_nick')."%");
		}
		$list = WalletLogs::with('wallet')->join("wt_member","member_id=wallet_member")->where($where)->order('log_id', 'desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);	
		foreach ($list as $key => $value) {
			if($value['log_relation_type']==1){
				$commission=Commission::where(['commission_id'=>$value['log_relation_id']])->find();
				#交易分润
				if($commission['commission_type']==1){
					$list[$key]['hrefurl']=$commission['commission_from']?'/index/Order/cash/order_id/'.$commission['commission_from'] : '';
				}elseif($commission['commission_type']==2){//分佣
					$list[$key]['hrefurl']=$commission['commission_from']?'/index/Order/index/upgrade_id/'.$commission['commission_from'] : '';
				}elseif($commission['commission_type']==3){//代还分润
					$list[$key]['hrefurl']=$commission['commission_from']?'/index/Plan/index/generation_id/'.$commission['commission_from'] : '';
				}else{
					$list[$key]['hrefurl']='';
				}
			}elseif($value['log_relation_type']==2){
				$list[$key]['hrefurl']=$value['log_relation_id']?'/index/Order/withdraw/withdraw_id/'.$value['log_relation_id'] : '';
			}else{
				$list[$key]['hrefurl']='';
			}
		}
		$count = WalletLogs::with('wallet')->join("wt_member","member_id=wallet_member")->where($where)->count();
		$this->assign("count",$count);
		#计算进账总额
		$entertottal = WalletLogs::with('wallet')->join("wt_member","member_id=wallet_member")->where($where)->where(['log_relation_type' => 1])->order('log_id', 'desc')->sum("log_wallet_amount");
		$this->assign("entertottal",$entertottal);
		#计算出账总额
		$leavetotal = WalletLogs::with('wallet')->join("wt_member","member_id=wallet_member")->where($where)->where(['log_relation_type' => 2])->order('log_id', 'desc')->sum("log_wallet_amount");
		$this->assign("leavetotal",$leavetotal);
		$this->assign('list', $list);
		return view('admin/walletlog/index');
	}
}
