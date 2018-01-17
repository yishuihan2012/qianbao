<?php
 namespace app\api\controller;
 use think\Db;
 use app\index\model\Member;
 use app\index\model\System;
 use app\index\model\Wallet;
 use app\index\model\WalletLog;
 use app\index\model\MemberGroup;
 use app\index\model\PassagewayItem;
 use app\index\model\MemberRelation;
 use app\index\model\Commission as Commissions;
 use app\index\model\Upgrade as Upgrades;
 /**
 *  @version Upgrage controller / 升级
 *  @author $xuchengcheng(1015571416@qq.com)
 *   @return 
 */
 class Upgrade
 {
	 //升级
 	 // arr->upgrade_member_id 会员id  必填
 	 // arr->upgrade_group_id  升级到的会员组id  必填 
 	 // arr->upgrade_type  升级方式  Alipay   backstage  auto  必填 
 	 // arr->upgrade_bak  备注  必填 
 	 // arr->upgrade_no  订单号
 	 // arr->upgrade_money 升级金额
 	 // arr->upgrade_commission   分佣金额
 	 // arr->upgrade_state  支付状态
 	 // arr->upgrade_adminster_id  后台升级用户id
 	 // is_commission  是否有分佣 0无分佣 1有分佣
 	 // commission_des  分佣描述
	 public function memberUpgrade($arr,$is_commission='0',$commission_des=''){
	 	Db::startTrans();   
	 	#1升级会员      
	 	$arr['upgrade_before_group']=Member::where(['member_id'=>$arr['upgrade_member_id']])->value('member_group_id');
	 	// print_r($arr);die;
	 	if(!$arr['upgrade_before_group']){
	 		return false;
	 	}
	 	$member=Member::where(['member_id'=>$arr['upgrade_member_id']])->update(['member_group_id'=>$arr['upgrade_group_id']]);
	 	#2分佣
	 	if($is_commission){
	 		$commission=new \app\api\controller\Commission();
        	$commissions=$commission->MemberCommis($arr['upgrade_member_id'],$arr['upgrade_commission'],$commission_des);
        	if($commissions['code']==200){
        		$commissions=1;
        	}else{
        		$commissions=0;
        	}
	 	}else{
	 		$commissions=1;
	 	}
	 	#2记录日志
	 	$log=$this->upgrade_log($arr);
	 	if($member && $log && $commissions){
	 		Db::commit();
	 		return true;
	 	}else{
	 		Db::rollback();  
	 		return false;  
	 	}
	 }	
	 //
	 public function upgrade_log($arr){
	 	 $log=array(
	 	 	'upgrade_member_id'=>$arr['upgrade_member_id'],
	 	 	'upgrade_before_group'=>$arr['upgrade_before_group'],
	 	 	'upgrade_group_id'=>$arr['upgrade_group_id'],
	 	 	'upgrade_type'=>$arr['upgrade_type'],
	 	 	'upgrade_no'=>isset($arr['upgrade_no'])?$arr['upgrade_no']:make_order(),
	 	 	'upgrade_money'=>isset($arr['upgrade_money'])?$arr['upgrade_money']:0,
	 	 	'upgrade_commission'=>isset($arr['upgrade_commission'])?$arr['upgrade_commission']:0,
	 	 	'upgrade_state'=>isset($arr['upgrade_state'])?$arr['upgrade_state']:0,
	 	 	'upgrade_bak'=>isset($arr['upgrade_bak'])?$arr['upgrade_bak']:'',//备注
	 	 	'upgrade_adminster_id'=>isset($arr['upgrade_adminster_id'])?$arr['upgrade_adminster_id']:0,
	 	 );
	 	 $Upgrade = Upgrades::insert($log);
		 return $Upgrade;

	 }
 }