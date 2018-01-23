<?php
 namespace app\api\controller;
 use think\Db;
 use app\index\model\Member;
 // use app\index\model\MemberGroup;
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
 	#上线集合
 	private $family;
 	#剩余分配利率
 	private $last_also;
 	#订单号
 	private $order_id;
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

 	 	 	 	$group1 = MemberGroup::get($memberInfo['member_group_id']);
 	 	 	 	$fatherMoney = 0;
 	 	 	 	if(System::getName('commission_type')==1){
 	 	 	 		$fatherMoney = $group1['group_direct_cent'];
 	 	 	 	}else{
 	 	 	 		$fatherMoney=$total_money*(System::getName('direct_total')/100);
 	 	 	 	}
 	 	 	 	 
 	 	 	 	  $leftmoney+=$fatherMoney;
 	 	 	 	 if(!$this->commissionOrder($memberId,$member_fater_id,$fatherMoney,2,$desction."-直接分佣")){
 	 	 	 	 	 return ['code'=>465];
 	 	 	 	  }
 	 	 	 	 
 	 	 	 	 //j极光推送分佣提醒
 	 	 	 	 $str="-直接分佣:邀请的".$memberInfo['member_nick']."付费升级成功,获得收益".$fatherMoney."元~";
 	 	 	 	 jpush($member_fater_id,'直接分佣收益到账提醒~',$str,$str);

 	 	 	 	 #查询间接上级
 	 	 	 	 $member_grandFater_id=MemberRelation::where('relation_member_id',$member_fater_id)->value('relation_parent_id');
 	 	 	 	 $member_grandFaterInfo= Member::get($member_grandFater_id);

 	 	 	 	 if($member_grandFater_id=="0" || !$member_grandFaterInfo)
 	 	 	 	 	 return ['code'=>200,'leftmoney'=>$leftmoney];
 	 	 	 	 #查询间接上级信息

 	 	 	 	 if($member_grandFaterInfo)

 	 	 	 	 {   
 	 	 	 	 	$grandFatherMoney = 0;
 	 	 	 	 	if(System::getName('commission_type')==1){
 	 	 	 			$grandFatherMoney = $group1['group_second_level_cent'];
 	 	 	 	 	 }else{
 	 	 	 	 	 	 $grandFatherMoney=$total_money*(System::getName('indirect_total')/100);
 	 	 	 	 	 }
 	 	 	 	 
 	 	 	 	 	 $leftmoney+=$grandFatherMoney;
	 	 	 	 	 if(!$this->commissionOrder($memberId,$member_grandFater_id,$grandFatherMoney,2,$desction."-间接分佣")){
	 	 	 	 	 	 return ['code'=>465];
	 	 	 	 	 }
	 	 	 	 	  //j极光推送分佣提醒
	 	 	 	 	 $str="-间接分佣:邀请的".$memberInfo['member_nick']."付费升级成功,获得收益".$grandFatherMoney."元~";
	 	 	 	 	 jpush($member_grandFater_id,'间接分佣收益到账提醒~',$str,$str);
	 	 	 	 	 #查询三级上级
	 	 	 	 	 $member_endFather_id=MemberRelation::where('relation_member_id',$member_grandFater_id)->value('relation_parent_id');
	 	 	 	 	 $member_endFatherInfo= Member::get($member_endFather_id);
	 	 	 	 	 if($member_endFather_id=="0" || !$member_endFatherInfo)
	 	 	 	 	 	 return ['code'=>200,'leftmoney'=>$leftmoney];
	 	 	 	 	 if($member_endFatherInfo)
	 	 	 	 	 {
	 	 	 	 	 	$endFatherMoney = 0;
	 	 	 	 	 	if(System::getName('commission_type')==1){
 	 	 	 				$endFatherMoney = $group1['group_three_cent'];
	 	 	 	 	 	}else{
	 	 	 	 	 		$endFatherMoney=$total_money*(System::getName('indirect_3rd_total')/100);
	 	 	 	 	 	}
	 	 	 	 	 	    
	 	 	 	 	 	 $leftmoney+=$endFatherMoney;
		 	 	 	 	 if(!$this->commissionOrder($memberId,$member_endFather_id,$endFatherMoney,2,$desction."-三级分佣")){
		 	 	 	 	 	 return ['code'=>465];
		 	 	 	 	 }
		 	 	 	 	 //j极光推送分佣提醒
		 	 	 	 	 $str="-三级分佣:邀请的".$memberInfo['member_nick']."付费升级成功,获得收益".$endFatherMoney."元~";
		 	 	 	 	 jpush($member_endFather_id,'三级分佣收益到账提醒~',$str,$str);
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
	 *   @param $type=分润类型 1=快捷支付分润 2=分佣 3=代还分润 $desction="简介描述"  $order_id 对应订单的id
	 */
 	 public function MemberFenRun($memberId,$price,$passwayId, $type ,$desction="会员分润",$order_id){
 	 	global $leftmoney;
 	 	$this->order_id=$order_id;
 	 	 if($type=='1'){
 	 	 	 $action="快捷支付分润";
 	 	 	 $field="item_rate";
 	 	 }
 	 	 if($type=="3"){
 	 	 	 $action="代还分润";
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

 	 	 #存储消费者费率
 	 	$this->last_also=$member_also;
 		$passway_also=db('passageway')->where('passageway_id',$passwayId)->value('passageway_rate');
 		//基础利润
 		$profit=$price*($member_also-$passway_also)/100;

	 	# 【代理商机制】
	 	# 判断顶级是否存在代理商
	 	$this->family=find_relation($memberId);
	 	#剔除memberId本身
	 	array_shift($this->family);

 	 	 //获取用户直接上级ID
 	 	 $member_faterId=MemberRelation::where('relation_member_id',$memberId)->value('relation_parent_id');
 	 	 //如果该会员是一级会员 则不进行分润
 	 	 if($member_faterId=="0")
 	 	 	goto end;

 	 	 //获取直接上级会员信息	
 	 	 $member_fatherInfo= Member::get($member_faterId); 
 	 	 if(!$member_fatherInfo)
 	 	 	goto end;

 	 	 //查询直接上级所属用户组 和他的费率
 	 	 $member_fatherAlso=PassagewayItem::where(['item_passageway'=>$passwayId,'item_group'=>$member_fatherInfo['member_group_id']])->value($field);
 	 	 //判断上级会员用户组是否允许分润
 	 	 $member_fatherGroup=MemberGroup::where(['group_id'=>$member_fatherInfo['member_group_id']])->value('group_run');
 	 	 if($member_fatherGroup=="0" || $father_is_agent){
 	 	 	#非代理用户组 不调用分润方法
 	 	 	 // $father_result=$this->commissionOrder($memberId,$member_faterId,0,$type,$desction."-直接分润:您当前用户组不允许获得分润~",$order_id);
 	 	 }else{
 	 	 	 //计算税率差 如果上级的税率和本人税率相同或者大于本人税率  则不进行分润
 	 	 	 if($member_also-$member_fatherAlso<=0){

 	 	 	 	// 【无忧钱管家】 推荐A级代理 平台补贴0.01%给推荐人
 	 	 	 	// 取最高级用户组 判断是否A级代理
 	 	 	 	$maxGroup=db('member_group')->max('group_salt');
 	 	 	 	if($memberInfo['member_group_id']==$maxGroup){
 	 	 	 		$member_fatherMoney=$price/10000;
	 	 	 	 	$str=$desction."-直接分润:邀请的".$memberInfo['member_nick'].$action."成功,获得平台补贴收益".$member_fatherMoney."元~";
	 	 	 	 	$father_result=$this->commissionOrder($memberId,$member_faterId,$member_fatherMoney,$type,$str,$order_id);
	 	 	 	 	$leftmoney+=$member_fatherMoney;
	 	 	 	 	#同级推荐同级或上级，平台获取的分润结算差额的50%给推荐者
 	 	 	 	}elseif($member_also<=$member_fatherAlso ){
 	 	 	 		#占位符 在运营商分配时使用
 	 	 	 		$has_recommend_nb=true;
 	 	 	 	}else{
	 	 	 	 	$father_result=$this->commissionOrder($memberId,$member_faterId,0,$type,$desction."-直接分润:与操作人会员级别相同或比操作人级别低,不获得分润~",$order_id);
 	 	 	 	}
 	 	 	 }else{
 	 	 	 	 $member_fatherAlsoMoney=$price*(($member_also-$member_fatherAlso)/100);
 	 	 	 	 $leftmoney+=$member_fatherAlsoMoney;
 	 	 	 	 $str=$desction."-直接分润:邀请的".$memberInfo['member_nick'].$action."成功,获得收益".$member_fatherAlsoMoney."元~";
 	 	 	 	 $father_result=$this->commissionOrder($memberId,$member_faterId,$member_fatherAlsoMoney,$type,$str,$order_id);
 	 	 	 	 // jpush($member_faterId,'分润收益到账提醒~',$str,$str);
 	 	 	 }
 	 	 }
	 	#代理商利润分配
	 	end:
	 	foreach ($this->family as $k => $v) {
	 	 	#不可见用户组 即为代理商用户组
	 	 	if($v['group_visible']==0){
	 	 		$rate=db('passageway_item')->where(['item_passageway'=>$passwayId,'item_group'=>$v['member_group_id']])->value($field);
	 	 		#通过费率差计算代理商的差价利润
	 	 			$also=$this->last_also-$rate;
	 	 			// w_log($this->last_also . '---' . $rate);
	 	 		if($also>0){
		 	 		$agent_money=$also*$price/100;
		 	 		$leftmoney+=$agent_money;
		 	 		#针对同级推荐 和推荐上级的情况 遇到的第一个分润的 利润50%给推荐人
		 	 		if(isset($has_recommend_nb) && $has_recommend_nb==true){
		 	 			$agent_money/=2;
		 	 	 	 	$str=$desction."-直接分润:邀请的".$memberInfo['member_nick'].$action."成功,获得分润结算差额收益".$agent_money."元~";
		 	 	 	 	$father_result=$this->commissionOrder($memberId,$member_faterId,$agent_money,$type,$str,$order_id);
		 	 		}
		 	 		$this->commissionOrder($memberId,$v['member_id'],$agent_money,4,'代理商利润',$this->order_id);
		 	 		$this->last_also=$rate;
	 	 		}
	 	 	}
	 	}
		#系统利润
	 	$system_Money=$profit-$leftmoney;
	      $commission= new Commissions([
	      	 'commission_member_id'=>-1,// -1 代表是平台的利润
	      	 'commission_childen_member'	=>$memberId,
	      	 'commission_type'		=>4,
	      	 'commission_money'		=>$system_Money,
	      	 'commission_state'		=>1,
	      	 'commission_desc'		=>'平台利润',
	      	 'commission_from'		=>$order_id,
	      ]);
	      $commission->save();
 	}


	 /**
	 *  @version commissionOrder controller / Api 写入分佣订单
	 *  @author $bill$(755969423@qq.com)
	 *   @datetime    2017-12-08 10:13:05
	 *   @param $memberId="购买会员ID"  $fatherId="上级ID"  $comPrice="分佣金额" $type="类型1=分润 2=分佣 3代还分润 4 代理商的子用户分润产生费率差利润" $desc="描述"
	 */
 	 public function commissionOrder($memberId,$fatherId,$comPrice,$type, $desc,$order_id=null)
 	 {
 	 	 if($type=="1")
 	 	 {
 	 	 	 $action="快捷支付分润";
 	 	 	 $field="wallet_fenrun";
 	 	 }
 	 	 if($type=="2")
 	 	 {
 	 	 	 $action="分佣";
 	 	 	 $field="wallet_commission";
 	 	 }
 	 	 if($type=="3")
 	 	 {
 	 	 	 $action="代还分润";
 	 	 	 $field="wallet_fenrun";
 	 	 }
 	 	 if($type=='4'){
 	 	 	 $action="代理利润";
 	 	 	 $field="wallet_fenrun";
 	 	 }

 	 	 try{ 
	 	      $commission= new Commissions([
	 	      	 'commission_member_id'=>$fatherId,
	 	      	 'commission_childen_member'	=>$memberId,
	 	      	 'commission_type'		=>$type,
	 	      	 'commission_money'	=>$comPrice,
	 	      	 'commission_state'		=>1,
	 	      	 'commission_desc'		=>$desc,
	 	      	 'commission_from'		=>$order_id,
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
	 	      	 	 'log_relation_id'		=>$commission->commission_id,
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
 	 	 	 }else{
 	 	 	 	Db::rollback();
                 return false;
 	 	 	 }
 	 	 }catch (\Exception $e) {
 	 	 	 Db::rollback();
                 return false;
           }
 	 }
}