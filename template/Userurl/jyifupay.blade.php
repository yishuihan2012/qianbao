
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
