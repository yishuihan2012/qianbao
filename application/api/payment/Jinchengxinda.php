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
 		$this->url='https://tx.szjcxd.cn';
 		$this->mch_no='2018062818133731120747';
 		$this->secret_key='60c65b4d5b5c4c23a71780732ae683c5';
 	}
 	public function pay($member_info,$card_info,$money,$rate,$tradeNo){
 		$data=array(
 			'creditCard'=>$card_info->card_bankno,//：信用卡卡号
 			'creditCardCvn2'=>$card_info->card_Ident,//： 信用卡cvv2
 			'creditCardExpire'=>substr($card_info->card_expireDate,2,2).substr($card_info->card_expireDate,0,2),//：信用卡有效日期（YYMM）
 			'creditPhone'=>$card_info->card_phone,//：信用卡绑定手机号（必须与银行留存的一致）
 			'debitBank'=>$member_info->MemberCashcard->card_bankname,//：入账卡开户行
 			'debitCard'=>$member_info->MemberCashcard->card_bankno,//入帐卡卡号
 			'debitPhone'=>$member_info->MemberCashcard->card_phone,//：入账卡绑定手机号（必须与银行留存的一致）
 			'identityNo'=>$member_info->MemberCert->cert_member_idcard,//：身份证号码（必须与银行留存的一致）
 			'mchno'=>$this->mch_no,//：商户号
 			'mchOrderNo'=>$tradeNo,//：商户订单号
 			'orderAmount'=>$money,//：商户订单金额（单位元）
			'orderChannelRate'=>$rate->item_rate/100,//：订单汇率（eg：0.0059）
			'orderCounterFee'=>(string)$rate->item_charges /100,//：订单单笔固定费用（单位元）
			'realName'=>$member_info->MemberCert->cert_member_name,//：开户名（必须与银行留存的一致）
			'tradeDateTime'=>date('YmdHis',time()),//：交易时间（YYYYMMDDHHMMSS）
			'version'=>'1.0.0',//：版本号（默认值1.0.0）	
 		);
 		// echo json_encode($data);die;
 		$url='/yt/synonymNamePay';
 		$res=$this->request($url,$data);
 		$res=array(
 			'status'=>'000',
 			'msg'=>"成功",
 			'data'=>array(
 				'mchOrderNo'=>$tradeNo,
 				'mchno'=>'233',
 			),
 		);
 		if($res['status']=="000"){
 			$return['code']=200;
 			$return['msg']="支付成功";
 		}else{
 			$return['code']=-1;
 			$return['msg']=$res['msg'];
 		}
 		$return['orderNo']=$res['data']['mchOrderNo'];
 		$return['mchno']=$res['data']['mchno'];
 		return $return;
 	}
 	/**
 	 * 签名
 	 * @param  [type] $data [description]
 	 * @return [type]       [description]
 	 */
 	public function sign($data){
 		$str=$this->secret_key;
 		foreach ($data as $k => $v) {
 			$str.=$k.$v;
 		}
 		$str=strtoupper(md5($str));
 		return $str;
 	}
 	public function request($url,$data){
 		$data['signValue']=$this->sign($data);
 		$url=$this->url.$url;
 		$res=curl_post($url,'post',json_encode($data));
 		return json_decode($res,true);
 	}
}