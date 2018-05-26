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
			'partnerOrderNo'=>$this->partnerCode,//外部订单号 字符串(1-40) 是 商户订单唯一标识 888777666 
			'openId'=>'888777666000',//外部会员唯 一标识 字符串(1-40) 是 商户用户的唯一标识 888777666000 
			'phone'=>'16605383329',//外部会员手 机号，该手机号用于注册易极付会员 
			'creditCardNo'=>"6259760291531725",//银行卡号 字符串(1-40) 是 用户用于提现的信用卡卡号 35860120111000918 
			'realName'=>"许成成",//姓名 字符串(1-16) 是 用户的真实姓名 张三/北京xxx 有限公司 
			'identityNo'=>"370983199109202832",//身份证号 字符串(1-18) 是 用户的身份证号 45022519880814928X 
			'bankPhone'=>'16605383329',//预留手机号 字符串(11) 是 用户对应的银行卡的预留手机号； 18962105588 
			'channelId'=>"61",//通道ID 字符串(20) 是 本次验证的通道 ID，需要使用【3.4 查询可用通 道】进行获取 0121212121 
		);
		$this->request('/agency/api/channelInfoCheck.html',$data);
	}
	/**
	 * 通道开通商户
	 * @return [type] [description]
	 */
	public function passway_mech(){
		$data=array(
			'partnerOrderNo'=>"8887776661",//外部订单号 字符串(1-32) 是 商户订单唯一标识 888777666 
			'openId'=>"qwertyuiop123",//外部会员唯一 字符串(20) 是  20160122000220157014 5 标识 
			'identityFrontUrl'=>"",//身份证正面照 片 字符串(1-128)  否 正面照片的URL链接 http://xxx.xxx.xxx/1.jpg 
			'identityBackUrl'=>"",// 身份证反面照 片 字符串(1-128)  否 反面照片的URL链接 http://xxx.xxx.xxx/1.jpg 
		);
		$res=$this->request('/agency/api/openPayAccount.html',$data);

	}
	/**
	 * 支付
	 * @return [type] [description]
	 */
	public function pay(){
		$html="
<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
		<style>
			div{display:none;width:500px; border:1px solid #0008ff;margin: 0px 0px 0px 20px; padding:5px 5px 5px 5px;}
			#signRepayItemListA{
				padding-left:50px;
				padding-top:20px;
			}
		</style>
		<script>
			function showdiv(targetid,dispid){   
					var disp=document.getElementById(targetid);
					//var show = document.getElementById(dispid).value;
					var show = dispid;
					if (show =='yes'){
						disp.style.display='block';
					}else{
						disp.style.display='none';
					}   
				}
			function show1(ojID,id){
				var category = document.getElementById(ojID).value;
				if(category == 'yes'){
					showdiv(id,'yes');
				}else{
					showdiv(id,'no');
				}
			}

			function toShow(ojID){
				var oCategory = document.getElementById(ojID).value;
				showdiv('dBindCreditCard','no');
				showdiv('dBindDebitCard','no');
				showdiv('dReturnUrl','no');
				showdiv('dcouponNos','no');
				switch(oCategory)
				{
					case 'BIND_CREDIT_CARD':
						showdiv('dBindCreditCard','yes');
						break;
					case 'BIND_DEBIT_CARD':
						showdiv('dBindDebitCard','yes');
						break;
					case 'OPEN_PAY_ACCOUNT':
						showdiv('dReturnUrl','yes');
						break;
					case 'WITHDRAW':
						showdiv('dcouponNos','yes');
						break;
					default:
				}
			}
		</script>
	</head>

<body>
	<form method='post' action='dopost.php' target='_blank'>
公共请求:
<br>请求网关地址：<input Type='text' Name='requestUrl' value='http://scdeercnet.yijifu.net/agency/api/merge/withdraw.html'/>
<br>代理商ID:partnerCode<input Type='text' Name='partnerCode' value='18042418580300200000'/>
<br><input Type='text' Name='pkey' value='ca3fae56ae9ac12042cf6b5b1b9d8202'/>
<br>时间戳:timestamp<input Type='text' Name='timestamp' value='1527233845213'/> 单位: 毫秒
<br>页面跳转返回URL：returnUrl<input Type='text' Name='returnUrl' value='http://honestbear.iok.la:25868/scl/return_url.php'/>
<br>异步通知URL：notifyUrl<font color='red'>*</font><input Type='text' Name='notifyUrl' value='http://honestbear.iok.la:25868/UTF-8/notify_url.php'/>
<br>外部订单号:partnerOrderNo<input Type='text' Name='partnerOrderNo' value='parNo2018052515372594'/>
<br>:signType<input Type='' Name='signType' value='MD5'/>
<br>外部会员唯一标识:openId<input Type='text' Name='openId' value='opid201805230001'/>
<br>外部会员手机号:phone<input Type='text' Name='phone' value='15998987777'/>
<br>提现银行卡号:creditCardNo<input Type='text' Name='creditCardNo' value='6259654001497001'/>
<br>到账银行卡号:debitCardNo<input Type='text' Name='debitCardNo' value='6227000783510915180'/>
<br>姓名:realName<input Type='text' Name='realName' value='司马落雅'/>
<br>身份证号:identityNo<input Type='text' Name='identityNo' value='410101199305082684'/>
<br>预留手机号:bankPhone<input Type='text' Name='bankPhone' value='15998987793'/>
<br>通道ID:channelId<input Type='text' Name='channelId' value='62'/>
<br>通道费率:channelRate<input Type='text' Name='channelRate' value='0.53'/>
<br>提现金额:amount<input Type='text' Name='amount' value='897'/>
<br>提现金额类型:amountType<input Type='text' Name='amountType' value='PAYMENT'/>
<br>是否实时到账:isPromptly<input Type='text' Name='isPromptly' value='true'/>
<br>实时到账费:serviceFee<input Type='text' Name='serviceFee' value='3'/>
<br>身份证正面照片:identityFrontUrl<input Type='text' Name='identityFrontUrl' value='https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1527075722279&di=9ae97afbda540e44e8729a9a0b5a8b19&imgtype=0&src=http%3A%2F%2Fimg.mp.itc.cn%2Fupload%2F20160428%2F06a46a30902244fbb6eccb80a61902dd_th.jpg'/>
<br>身份证反面照片:identityBackUrl<input Type='text' Name='identityBackUrl' value='https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1527075722279&di=9ae97afbda540e44e8729a9a0b5a8b19&imgtype=0&src=http%3A%2F%2Fimg.mp.itc.cn%2Fupload%2F20160428%2F06a46a30902244fbb6eccb80a61902dd_th.jpg'/>

<p><input Type='submit' Name='submit' value='提交'></p>
		</form>
	</body>
</html>
";
		return ['code'=>'200','html'=>$html];
		$data=array(
			'partnerOrderNo'=>"888777666",//外部订单号 字符串(1-40) 是 商户订单唯一标识 888777666 
			'openId'=>"16605383329",//外部会员唯一 标识 字符串(1-40) 是 商户用户的唯一标识 888777666000 
			'signType'=>"MD5",
			'phone'=>'16605383329',
			'creditCardNo'=>"6258101661675746",//提现银行卡号 字符串(1-40) 是 用户用于提现的信用卡卡号，该银行卡提现所用通 35860120111000918 道必须进行了信息验证。 
			'debitCardNo'=>"6215590200003242971",//到账银行卡号 字符串(1-40) 是 用户用于到账的储蓄卡卡号，必须为该用户实名的 身份信息名下的储蓄卡。 
			'realName'=>"许成成",
			'identityNo'=>"370983199109202832",
			'bankPhone'=>"16605383329",
			'amount'=>"299",//提现金额 Money类型 是 用户提现金额，单位元，非用户到账金额 300.00 
			'amountType'=>"PAYMENT",//提现金额类型 字符串 否 默认为实付金额。 PAYMENT：实付金额 RECEIVE：到账金额 PAYMENT 
			'channelId'=>"61",//通道ID 字符串(1-40) 是 提现所用的通道对应的 ID，提现银行卡号必须在此 通道进行了信息验证。 99990000 
			'channelRate'=>"0.3",//通道费率 数字(百分比) 是 通道费率，不可低于合同费率 0.6 
			'isPromptly'=>true,//是否实时到账 Bool 是 是：T+0到账 否：T+1到账 是 
			'serviceFee'=>"0",//实时到账费 Money 否 实时到账所需要的服务费，单位元，不可低于合同 实时到账费 2.00 
			'identityFrontUrl'=>'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1527075722279&di=9ae97afbda540e44e8729a9a0b5a8b19&imgtype=0&src=http%3A%2F%2Fimg.mp.itc.cn%2Fupload%2F20160428%2F06a46a30902244fbb6eccb80a61902dd_th.jpg',
			'identityBackUrl'=>"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1527075722279&di=9ae97afbda540e44e8729a9a0b5a8b19&imgtype=0&src=http%3A%2F%2Fimg.mp.itc.cn%2Fupload%2F20160428%2F06a46a30902244fbb6eccb80a61902dd_th.jpg",
		);
		$res=$this->request('agency/api/merge/withdraw.html',$data);
		echo $res;die;
	}
	public function getSign($data){
		$data=SortByASCII($data);
		$string=http_build_query($data).$this->secretKey;
		$sign=md5($string);
		return $sign;
	}
	public function request($url,$data){
		$reqdata=array(
			'partnerCode'=>$this->partnerCode,
			'timestamp'=>(string)time()*1000,
			'returnUrl'=>"http://wallet.test.xijiakeji.com/api/Test/notify",
			'notifyUrl'=>'http://wallet.test.xijiakeji.com/api/Test/notify',
		);
		$array=array_merge($reqdata,$data);
		$array['sign']=$this->getSign($array);
		$string=http_build_query($array);
		// echo $string;die;
		$request_url=$this->url.$url.'?'.$string;
		// echo htmlspecialchars($request_url);die;
		$res=curl_post($request_url);
		// $res=file_get_contents($request_url);
		echo $res;die;
	}
}