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
 /**
 *  @version Commission controller / Api 分润
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-08 10:13:05
 *   @return 
 */
 class Commission
 {
	 /**
	 *  @version MemberCommis controller / Api 分佣
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2017-12-08 10:13:05
	 *   @return 
	 */
 	 public function MemberCommis($memberId,$price,$desction)
 	 {
 	 	 $memberInfo=Member::get($memberId);
 	 	 if(!$memberInfo)
 	 	 	 return ['code'=>466];
 	 	 $total_money=$price*(System::getName('direct_rate')/100);//分佣总金额
 	 	 $leftmoney=0;//消耗的总分佣
 	 	 #查询会员的上级信息
 	 	 $member_fater_id=MemberRelation::where('relation_member_id',$memberId)->value('relation_parent_id');
 	 	 if($member_fater_id!=0)
 	 	 {	
 	 	 	 $member_faterInfo=Member::get($member_fater_id);
 	 	 	 if($member_faterInfo) //直接上级会员信息真实存在的话 进行分佣
 	 	 	 {
 	 	 	 	 $fatherMoney=$total_money*(System::getName('direct_total')/100);
 	 	 	 	 $leftmoney+=$fatherMoney;
 	 	 	 	 if(!$this->commissionOrder($memberId,$member_fater_id,$fatherMoney,2,$desction."-直接分佣"))
 	 	 	 	 	 return ['code'=>465];
 	 	 	 	 #查询间接上级
 	 	 	 	 $member_grandFater_id=MemberRelation::where('relation_member_id',$member_fater_id)->value('relation_parent_id');
 	 	 	 	 $member_grandFaterInfo= Member::get($member_grandFater_id);
 	 	 	 	 if($member_grandFater_id=="0" || !$member_grandFaterInfo)
 	 	 	 	 	 return ['code'=>200,'leftmoney'=>$leftmoney];
 	 	 	 	 #查询间接上级信息
 	 	 	 	 if($member_grandFaterInfo)
 	 	 	 	 {
 	 	 	 	 	 $grandFatherMoney=$total_money*(System::getName('indirect_total')/100);
 	 	 	 	 	 $leftmoney+=$grandFatherMoney;
	 	 	 	 	 if(!$this->commissionOrder($memberId,$member_grandFater_id,$grandFatherMoney,2,$desction."-间接分佣"))
	 	 	 	 	 	 return ['code'=>465];
	 	 	 	 	 #查询三级上级
	 	 	 	 	 $member_endFather_id=MemberRelation::where('relation_member_id',$member_grandFater_id)->value('relation_parent_id');
	 	 	 	 	 $member_endFatherInfo= Member::get($member_endFather_id);
	 	 	 	 	 if($member_endFather_id=="0" || !$member_endFatherInfo)
	 	 	 	 	 	 return ['code'=>200,'leftmoney'=>$leftmoney];
	 	 	 	 	 if($member_endFatherInfo)
	 	 	 	 	 {
	 	 	 	 	 	 $endFatherMoney=$total_money*(System::getName('indirect_total')/100);
	 	 	 	 	 	 $leftmoney+=$endFatherMoney;
		 	 	 	 	 if(!$this->commissionOrder($memberId,$member_endFather_id,$endFatherMoney,2,$desction."-三级分佣"))
		 	 	 	 	 	 return ['code'=>465];
		 	 	 	 	 return ['code'=>200,'leftmoney'=>$leftmoney];
	 	 	 	 	 }
 	 	 	 	 }	
 	 	 	 }	
 	 	 }
 	 }

	 /**
	 *   @version FenRun controller / Api 分润
	 *   @author $bill$(755969423@qq.com)
	 *   @datetime    2017-12-08 10:13:05
	 *   @param  $memberId='刷卡会员ID'  $price="交易总额" $passwayId="使用通道ID" 
	 *   @param $type=分润类型 1=快捷支付分润 2=代还分润 $desction="简介描述" 
	 */
 	 public function MemberFenRun($memberId,$price,$passwayId, $type ,$desction="会员分润")
 	 {

 	 	 if($type=='1'){
 	 	 	 $action="快捷支付";
 	 	 	 $field="item_rate";
 	 	 }
 	 	 if($type=="2"){
 	 	 	 $action="代还";
 	 	 	 $field="item_also";
 	 	 }
 	 	 //消耗的总分润
 	 	 $leftmoney=0;
 	 	 $memberInfo=Member::get($memberId); //获取会员信息 
 	 	 //如果会员信息不存在或者找不到 返回错误码
 	 	 if(!$memberInfo)
 	 	 	 return ['code'=>466];
 	 	 //获取到用户税率
 	 	 $member_also=PassagewayItem::where(['item_passageway'=>$passwayId,'item_group'=>$memberInfo['member_group_id']])->value($field);

 	 	 //获取用户直接上级ID
 	 	 $member_faterId=MemberRelation::where('relation_member_id',$memberId)->value('relation_parent_id');
 	 	 //如果该会员是一级会员 则不进行分润
 	 	 if($member_faterId=="0")
 	 	 	 return ['code'=>200, 'leftmoney'=>$leftmoney];

 	 	 //获取直接上级会员信息	
 	 	 $member_fatherInfo= Member::get($member_faterId); 
 	 	 if(!$member_fatherInfo)
 	 	 	 return ['code'=>400, 'msg'=>'找不到直接上级信息~'];

 	 	 //查询直接上级所属用户组 和他的费率
 	 	 $member_fatherAlso=PassagewayItem::where(['item_passageway'=>$passwayId,'item_group'=>$member_fatherInfo['member_group_id']])->value($field);
 	 	 //判断上级会员用户组是否允许分润
 	 	 $member_fatherGroup=MemberGroup::where(['group_id'=>$member_fatherInfo['member_group_id']])->value('group_run');
 	 	 if($member_fatherGroup=="0"){
 	 	 	 $father_result=$this->commissionOrder($memberId,$member_faterId,0,1,$desction."-直接分润:您当前用户组不允许获得分润~");
 	 	 }else{
 	 	 	 //计算税率差 如果上级的税率和本人税率相同或者大于本人税率  则不进行分润
 	 	 	 if($member_also-$member_fatherAlso<=0){
 	 	 	 	 $father_result=$this->commissionOrder($memberId,$member_faterId,0,1,$desction."-直接分润:与操作人会员级别相同或比操作人级别低,不获得分润~");
 	 	 	 }else{
 	 	 	 	 $member_fatherAlsoMoney=$price*(($member_also-$member_fatherAlso)/100);
 	 	 	 	 $leftmoney+=$member_fatherAlsoMoney;
 	 	 	 	 $str=$desction."-直接分润:邀请的".$memberInfo['member_nick'].$action."成功,获得收益".$member_fatherAlsoMoney."元~";
 	 	 	 	 $father_result=$this->commissionOrder($memberId,$member_faterId,$member_fatherAlsoMoney,1,$str);
 	 	 	 	 jpush($member_faterId,'分润收益到账提醒~',$str);
 	 	 	 }
 	 	 }

 	 	 #查询间接上级
 	 	 $member_grandFaterId=MemberRelation::where('relation_member_id',$member_faterId)->value('relation_parent_id');
 	 	 #如果没有间接上级的话 则分润完成 
 	 	 if($member_grandFaterId=="0")
 	 	 	 return ['code'=>200, 'leftmoney'=>$leftmoney];

 	 	 #查询间接上级的会员信息
 	 	 $member_grandFatherInfo=Member::get($member_grandFaterId);
 	 	 if(!$member_grandFatherInfo)
 	 	 	 return ['code'=>200, 'leftmoney'=>$leftmoney];

 	 	 #查询间接上级税率和用户组是否允许分润
 	 	 $member_grandFatherGroup=MemberGroup::where(['group_id'=>$member_grandFatherInfo['member_group_id']])->value('group_run'); 
		 #获取间接上级的税率
		 $member_grandFatherAlso=PassagewayItem::where(['item_passageway'=>$passwayId,'item_group'=>$member_grandFatherInfo['member_group_id']])->value($field);
 	 	 if($member_grandFatherGroup=="0")
 	 	 {
 	 	 	 $grandResult=$this->commissionOrder($memberId,$member_grandFaterId,0,1,$desction."-间接分润:您的用户组不允许获得分润~");	
 	 	 }else{
 	 	 	 #查询他的上级是否允许分润
 	 	 	if($member_fatherGroup=="0")
 	 	 	{
 	 	 		$total_also_1 =$member_also;
 	 	 	}else{
 	 	 		$total_also_1 =$member_fatherAlso;
 	 	 	}
 	 	 	 #比对两级的会员费率 如果比最小的费率大 则不进行分佣
 	 	 	 if($total_also_1-$member_grandFatherAlso<=0){
 	 	 	 	 $grandResult=$this->commissionOrder($memberId,$member_grandFaterId,0,1,$desction."-间接分润:与下级会员级别相同或比下级级别低,不获得分润~");
 	 	 	 } else{
	 	 	 	 $member_grandFatherAlsoMoney=$price*(($total_also_1-$member_grandFatherAlso)/100);
	 	 	 	 $leftmoney+=$member_grandFatherAlsoMoney;
	 	 	 	 $str1=$desction."-间接分润:邀请的".$memberInfo['member_nick'].$action."成功,获得收益".$member_grandFatherAlsoMoney."元~";
	 	 	 	 $grandResult=$this->commissionOrder($memberId,$member_grandFaterId,$member_grandFatherAlsoMoney,1,$str1);
	 	 	 	  jpush($member_grandFaterId,'分润收益到账提醒~',$str1);
 	 	 	 }
 	 	 }

 	 	 #查询第三级上级
 	 	 $member_endFatherId=MemberRelation::where('relation_member_id',$member_grandFaterId)->value('relation_parent_id');
 	 	 if($member_endFatherId=="0")
 	 	 	 return ['code'=>200, 'leftmoney'=>$leftmoney];

 	 	 #查询第三级上级会员信息
 	 	 $member_endFatherInfo=Member::get($member_endFatherId);
 	 	 if(!$member_endFatherInfo)
 	 	 	 return ['code'=>200, 'leftmoney'=>$leftmoney];

 	 	 #查询第三级上级用户组是否允许分润
 	 	 $member_endFatherGroup=MemberGroup::where(['group_id'=>$member_endFatherInfo['member_group_id']])->value('group_run');
		 #获取三级上级的用户组税率
 	 	 $member_endFatherAlso=PassagewayItem::where(['item_passageway'=>$passwayId,'item_group'=>$member_endFatherInfo['member_group_id']])->value($field);

 	 	 if($member_endFatherGroup=="0"){
 	 	 	 $endFather_result=$this->commissionOrder($memberId,$member_endFatherId,0,1,$desction."-三级分润:您的用户组不允许获得分润~");
 	 	 }else{
 	 	 	 if($member_grandFatherGroup=="0")
 	 	 	 {
 	 	 	 	 if($member_fatherGroup=="0")
 	 	 	 	 	 $total_also_2=$member_also;
 	 	 	 	 else 
 	 	 	 	 	 $total_also_2=$member_fatherAlso;
 	 	 	 }else{
 	 	 	 	 $total_also_2=$member_grandFatherAlso-$member_fatherAlso>=0 ? $member_fatherAlso : $member_grandFatherAlso;
 	 	 	 }
 	 	 	 #进行税率计算 比对 如果想对税率小于0 则不进行分佣
 	 	 	 if($total_also_2-$member_endFatherAlso<=0)
 	 	 	 {
 	 	 	 	 $endFather_result=$this->commissionOrder($memberId,$member_endFatherId,0,1,$desction."-三级分润:您的会员组级别较低,不获得分润~");
 	 	 	 }else{
 	 	 	 	 $member_endFatherAlsoMoney=$price*(($total_also_2-$member_endFatherAlso)/100);
 	 	 	 	 $leftmoney+=$member_endFatherAlsoMoney;
 	 	 	 	 $str2=$desction."-三级分润:邀请的".$memberInfo['member_nick'].$action."成功,获得收益".$member_endFatherAlsoMoney."元~";
 	 	 	 	 $endFather_result=$this->commissionOrder($memberId,$member_endFatherId,$member_endFatherAlsoMoney,1,$str2);
 	 	 	 	  jpush($member_endFatherId,'分润收益到账提醒~',$str2);
 	 	 	 }
 	 	 }
 	 	 #查询第三季上级税率和用户组是否允许分润
 	 	 return ['code'=>200, 'leftmoney'=>$leftmoney];
 	 }


	 /**
	 *  @version commissionOrder controller / Api 写入分佣订单
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2017-12-08 10:13:05
	 *   @param $memberId="购买会员ID"  $fatherId="上级ID"  $comPrice="分佣金额" $type="类型1=分润 2=分佣" $desc="描述"
	 */
 	 public function commissionOrder($memberId,$fatherId,$comPrice,$type, $desc)
 	 {
 	 	 if($type=="1")
 	 	 {
 	 	 	 $action="分润";
 	 	 	 $field="wallet_fenrun";
 	 	 }
 	 	 if($type=="2")
 	 	 {
 	 	 	 $action="分佣";
 	 	 	 $field="wallet_commission";
 	 	 }
 	 	 try{ 
	 	      $commission= new Commissions([
	 	      	 'commission_member_id'=>$fatherId,
	 	      	 'commission_childen_member'	=>$memberId,
	 	      	 'commission_type'		=>$type,
	 	      	 'commission_money'	=>$comPrice,
	 	      	 'commission_state'		=>1,
	 	      	 'commission_desc'		=>$desc
	 	      ]);
	 	      if($commission->save())
	 	      {
	 	      	 #查找到会员的钱包数据
	 	      	 $wallet=Wallet::get(['wallet_member'=>$fatherId]);
	 	      	 $wallet->wallet_amount=$wallet->wallet_amount+$comPrice;
	 	      	 $wallet->wallet_total_revenue=$wallet->wallet_total_revenue+$comPrice;
	 	      	 $wallet->$field=$wallet->$field+$comPrice;
	 	      	 #写入日志表
	 	      	 $log=new WalletLog([
	 	      	 	 'log_wallet_id'		=>$wallet->wallet_id,
	 	      	 	 'log_wallet_amount'	=>$comPrice,
	 	      	 	 'log_wallet_type'		=>1,
	 	      	 	 'log_relation_id'		=>0,
	 	      	 	 'log_relation_type'	=>1,
	 	      	 	 'log_form'				=>$action.'收益~',
	 	      	 	 'log_desc'			=>$desc
	 	      	 ]);
	 	      	
	 	      	 if($wallet->save() && $log->save())
	 	      	 {
		 	 	 	 Db::commit();
		                 return true;
	 	      	 }else{
		 	 	 	 Db::rollback();
		                 return false;
		           }
 	 	 	 }
 	 	 }catch (\Exception $e) {
 	 	 	 Db::rollback();
                 return false;
           }
 	 }
 }