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
	public function passway_search(){
		$data=array(
			'creditCardNo'=>'6259760291531725',//银行卡号 字符串(1-40) 是 用户用于提现的信用卡卡号 500050505050505 
 			'openId'=>'2222222222',//外部会员唯一 标识 字符串(1-40) 否 商户用户的唯一标识 2222222222 
		);
		$res=$this->request('/agency/api/queryChannelList.json',$data);
		echo $res;die;
	}
	/**
	 *  通道信息验证
	 * @return [type] [description]
	 */
	public function passway_validate(){
		$data=array(
			'partnerOrderNo'=>"888777666",//外部订单号 字符串(1-40) 是 商户订单唯一标识 888777666 
			'openId'=>'888777666000',//外部会员唯 一标识 字符串(1-40) 是 商户用户的唯一标识 888777666000 
			'phone'=>'16605383329',//外部会员手 机号，该手机号用于注册易极付会员 
			'creditCardNo'=>"6259760291531725",//银行卡号 字符串(1-40) 是 用户用于提现的信用卡卡号 35860120111000918 
			'realName'=>"许成成",//姓名 字符串(1-16) 是 用户的真实姓名 张三/北京xxx 有限公司 
			'identityNo'=>"370983199109202832",//身份证号 字符串(1-18) 是 用户的身份证号 45022519880814928X 
			'bankPhone'=>'16605383329',//预留手机号 字符串(11) 是 用户对应的银行卡的预留手机号； 18962105588 
			'channelId'=>"",//通道ID 字符串(20) 是 本次验证的通道 ID，需要使用【3.4 查询可用通 道】进行获取 0121212121 
		);
		$this->request('/agency/api/channelInfoCheck.html',$data);
	}
	/**
	 * 通道开通商户
	 * @return [type] [description]
	 */
	public function passway_mech(){
		$data=array(
			'partnerOrderNo'=>"",//外部订单号 字符串(1-32) 是 商户订单唯一标识 888777666 
			'openId'=>"",//外部会员唯一 字符串(20) 是  20160122000220157014 5 标识 
			'identityFrontUrl'=>"",//身份证正面照 片 字符串(1-128)  否 正面照片的URL链接 http://xxx.xxx.xxx/1.jpg 
			'identityBackUrl'=>"",// 身份证反面照 片 字符串(1-128)  否 反面照片的URL链接 http://xxx.xxx.xxx/1.jpg 
		);
		$res=$this->request('/agency/api/openPayAccount.html',$data);

	}
	public function pay(){

	}
	public function getSign($data){
		$data=SortByASCII($data);
		$string=http_build_query($data).$this->secretKey;
		$sign=md5($string);
		return $sign;
	}
	public function request($url,$data){
		$sign=$this->getSign($data);
		$reqdata=array(
			'partnerCode'=>$this->partnerCode,
			'sign'=>$sign,
			'timestamp'=>time(),
			'returnUrl'=>"",
			'notifyUrl'=>'',
		);
		$array=array_merge($data,$reqdata);
		$request_url=$this->url.'?'.$url.http_build_query($array);
		$res=curl_post($request_url,'post',$array);
		echo($res);die;
		echo $res;die;
	}
}