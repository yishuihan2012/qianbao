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
class YiJiFu{
	public function __construct(){
		$this->url='http://scdeercnet.yijifu.net/';
		$this->partnerCode='18042418580300200000';
		$this->secretKey='ca3fae56ae9ac12042cf6b5b1b9d8202';
	}
	/**
	 * 根据银行卡搜索可用通道
	 * @return [type] [description]
	 */
	public function passway_search($creditCardNo,$mech_id){
		$data=array(
			'creditCardNo'=>$creditCardNo,//银行卡号 字符串(1-40) 是 用户用于提现的信用卡卡号 500050505050505 
 			'openId'=>$mech_id,//外部会员唯一 标识 字符串(1-40) 否 商户用户的唯一标识 2222222222 
		);
		$res=$this->request('/agency/api/queryChannelList.json',$data,'','',1);
		return $res;
	}
	/**
	 *  通道信息验证
	 * @return [type] [description]
	 */
	public function passway_validate(){
		$data=array(
			'partnerOrderNo'=>"41452174214",//外部订单号 字符串(1-40) 是 商户订单唯一标识 888777666 
			'partnerOrderNo'=>$this->partnerCode,//外部订单号 字符串(1-40) 是 商户订单唯一标识 888777666 
			'openId'=>'2222222222',//外部会员唯 一标识 字符串(1-40) 是 商户用户的唯一标识 888777666000 
			'phone'=>'16605383329',//外部会员手 机号，该手机号用于注册易极付会员 
			'creditCardNo'=>"6259760291531725",//银行卡号 字符串(1-40) 是 用户用于提现的信用卡卡号 35860120111000918 
			'realName'=>"许成成",//姓名 字符串(1-16) 是 用户的真实姓名 张三/北京xxx 有限公司 
			'identityNo'=>"370983199109202832",//身份证号 字符串(1-18) 是 用户的身份证号 45022519880814928X 
			'bankPhone'=>'16605383329',//预留手机号 字符串(11) 是 用户对应的银行卡的预留手机号； 18962105588 
			'channelId'=>"61",//通道ID 字符串(20) 是 本次验证的通道 ID，需要使用【3.4 查询可用通 道】进行获取 0121212121 
		);
		// $this->assign('data',$data);
		// return view("Userurl/passway_validate");
		$res=$this->request('/agency/api/channelInfoCheck.html',$data);
		echo $res;die;
	}
	/**
	 * 通道开通商户
	 * @return [type] [description]
	 */
	public function passway_mech(){
		$data=array(
			'partnerOrderNo'=>generate_password(16),//外部订单号 字符串(1-32) 是 商户订单唯一标识 888777666 
			'openId'=>"",//外部会员唯一 字符串(20) 是  20160122000220157014 5 标识 
			'identityFrontUrl'=>"",//身份证正面照 片 字符串(1-128)  否 正面照片的URL链接 http://xxx.xxx.xxx/1.jpg 
			'identityBackUrl'=>"",// 身份证反面照 片 字符串(1-128)  否 反面照片的URL链接 http://xxx.xxx.xxx/1.jpg 
		);
		$res=$this->request('/agency/api/openPayAccount.html',$data);

	}
	/**
	 * 支付
	 * @return [type] [description]
	 */
	public function pay($member_infos,$member_cert,$member_card,$card_info,$also,$price,$tradeNo,$channelId,$material_id){
		$data=array(
			'partnerOrderNo'=>$tradeNo,//外部订单号 字符串(1-40) 是 商户订单唯一标识 888777666 
			'openId'=>$material_id,//外部会员唯一 标识 字符串(1-40) 是 商户用户的唯一标识 888777666000 
			'phone'=>$member_infos['member_mobile'],
			'creditCardNo'=>$card_info['card_bankno'],//提现银行卡号 字符串(1-40) 是 用户用于提现的信用卡卡号，该银行卡提现所用通 35860120111000918 道必须进行了信息验证。 
			'debitCardNo'=>$member_card['card_bankno'],//到账银行卡号 字符串(1-40) 是 用户用于到账的储蓄卡卡号，必须为该用户实名的 身份信息名下的储蓄卡。 
			'realName'=>$member_card['card_name'],
			'identityNo'=>$member_card['card_idcard'],
			'bankPhone'=>$card_info['card_phone'],
			'amount'=>$price,//提现金额 Money类型 是 用户提现金额，单位元，非用户到账金额 300.00 
			'amountType'=>"PAYMENT",//提现金额类型 字符串 否 默认为实付金额。 PAYMENT：实付金额 RECEIVE：到账金额 PAYMENT 
			'channelId'=>$channelId,//通道ID 字符串(1-40) 是 提现所用的通道对应的 ID，提现银行卡号必须在此 通道进行了信息验证。 99990000 
			'channelRate'=>$also->item_rate,//通道费率 数字(百分比) 是 通道费率，不可低于合同费率 0.6 
			'isPromptly'=>true,//是否实时到账 Bool 是 是：T+0到账 否：T+1到账 是 
			'serviceFee'=>$also->item_charges/100,//实时到账费 Money 否 实时到账所需要的服务费，单位元，不可低于合同 实时到账费 2.00 
			// 'identityFrontUrl'=>'1',
			// 'identityBackUrl'=>"2",
		);
		$returnUrl=System::getName('system_url').'/Api/Userurl/jiyifuturnback';
		$notifyUrl=System::getName('system_url').'/Api/Cashoutcallback/jiyifucallback';
		$res=$this->request('agency/api/merge/withdraw.html',$data,$returnUrl,$notifyUrl,0);
		return $res;
	}
	public function order_query($order){
		$data=array(
			'partnerOrderNo'=>$order['order_no'],///外部订单号 字符串(1-32) 否 商户订单唯一标识，同内部订单号必须保证传入其一 888777666 
			// 'orderNo'=>"",//内部订单号  字符串(1-32) 否 我方订单唯一标识，同外部订单号必须保证传入其一 20160122000220157014 
		);
		$res=$this->request('/agency/api/queryOrder.json',$data,$returnUrl,$notifyUrl,1);
		if($res['status']=='SUCCESS'){
			$res['pay_status']=2;
			$res['qf_status']=2;
		}
		if($res['status']=='FAIL'){
			$res['pay_status']=-1;
			$res['qf_status']=-1;
		}
		return $res;
	}
	public function getSign($data){
		$data=SortByASCII($data);
		$string=http_build_query($data).$this->secretKey;
		$sign=md5($string);
		return $sign;
	}
	public function request($url,$data,$returnUrl='',$notifyUrl='',$is_api=0){
		$reqdata=array(
			'partnerCode'=>$this->partnerCode,
			'timestamp'=>(string)time()*1000,
			'returnUrl'=>$returnUrl,
			'notifyUrl'=>$notifyUrl,
		);
		$array=array_merge($reqdata,$data);
		$array['sign']=$this->getSign($array);
		$string=http_build_query($array);
		$request_url=$this->url.$url.'?'.$string;
		if($is_api){
			$res=curl_post($request_url);
			return json_decode($res,true);
		}else{
			$html='<!DOCTYPE html><html><head><title></title></head><body><form id="myForm" action="http://scdeercnet.yijifu.net/agency/api/merge/withdraw.html" method="get">';
						foreach ($array as $k => $v) {
							$html.='<input type="hidden" name="'.$k.'" value="'.$v.'">';
						}
				$html.='</form></body><script type="text/javascript">window.onload= function(){ document.getElementById("myForm").submit(); }</script></html>';
				$data['url']=urlencode($request_url);
				$data['html']=$html;
			return $data;
		}
	}
}