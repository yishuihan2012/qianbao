<?php
 namespace app\api\payment;
 use think\Db;
 use think\Controller;
 use app\index\model\Member;
 use app\index\model\System;
 use app\index\model\Wallet;
 use app\index\model\WalletLog;
 use app\index\model\MemberGroup;
 use app\index\model\PassagewayItem;
 use app\index\model\MemberRelation;
 use app\index\model\MemberCert;
 use app\index\model\MemberCashcard;
 use app\index\model\Passageway;
 use app\index\model\Generation;
 use app\index\model\GenerationOrder;
 use app\index\model\Reimbur;
 use app\index\model\MemberNet as MemberNets;
 use app\index\model\MemberCreditcard;
 use app\index\model\BankInfo;
 use app\index\model\MemberCreditPas;
 /**`
 *  @author 许成成(1015571416@qq.com)
 *   @datetime    2018-06-28 15:13:05
 *   @return 
 */
 class Jinchengxinda{
 	public function __construct(){
 		$this->url='http://120.77.62.216:9099';
 		$this->mch_no='2018062816010211716905';
 		$this->secret_key='1e191a4e34a84f9d87bd93ced7f5f669';
 	}
 	public function pay($member_info,$card_info,$money,$rate,$tradeNo){
 		$data=array(
 			'version'=>'1.0.0',//：版本号（默认值1.0.0）
			'mchno'=>$this->mch_no,//：商户号
			'mchOrderNo'=>$tradeNo,//：商户订单号
			'orderAmount'=>$money,//：商户订单金额（单位元）
			'orderChannelRate'=>$rate->rate/100,//：订单汇率（eg：0.0059）
			'orderCounterFee'=>$rate->fix/100,//：订单单笔固定费用（单位元）
			'tradeDateTime'=>date('YmdHis',time()),//：交易时间（YYYYMMDDHHMMSS）
			'realName'=>$member_info->MemberCert->cert_member_name,//：开户名（必须与银行留存的一致）
			'identityNo'=>$member_info->MemberCert->cert_member_idcard,//：身份证号码（必须与银行留存的一致）
			'debitCard'=>$member_info->MemberCashcard->card_bankno,//入帐卡卡号
			'debitPhone'=>$member_info->MemberCashcard->card_bankno,//：入账卡绑定手机号（必须与银行留存的一致）
			'debitBank'=>$member_info->MemberCashcard->card_bankno,//：入账卡开户行
			'creditPhone'=>$card_info->card_phone,//：信用卡绑定手机号（必须与银行留存的一致）
			'creditCard'=>$card_info->card_bankno,//：信用卡卡号
			'creditCardCvn2'=>$card_info->card_Ident,//： 信用卡cvv2
			'creditCardExpire'=>$card_info->card_expireDate,//：信用卡有效日期（YYMM）
 		);
 		echo json_encode($data);die;
 		$url='/yt/synonymNamePay';
 		$res=$this->request($url,$data);
 	}
 	/**
 	 * 签名
 	 * @param  [type] $data [description]
 	 * @return [type]       [description]
 	 */
 	public function sign($data){
 		$data=SortByASCII($data);
 		$str=$this->secret_key;
 		foreach ($data as $k => $v) {
 			$str.=$k.$v;
 		}
 		$str=mb_strtoupper(md5($str));
 		return $str;
 	}
 	public function request($url,$data){
 		$data['signValue']=$this->sign($data);
 		$url=$this->url.$url;
 		$res=curl_post($url,'post',$data);
 		print_r($res);die;
 		return $res;
 	}
}