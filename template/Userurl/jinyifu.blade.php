<!doctype html>
<html>
	<head>
		<!-- 荣邦申请快捷支付 ishtml=2 时调用本页面 填入验证码调用确认接口 -->
		<meta charset="UTF-8">
		<title>请输入验证码</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>

	<body>
		<header class="wrap bg-blue dis-flex-be white-color">
	  	<span></span>
	  	<span><strong>订单支付</strong></span>
	  	<img src="/static/images/order_pay.png" class="media-pic">
	  </header>
  <div class="mui-content order-payment">
  	<ul class="mui-table-view bg-color">
	    <li class="mui-table-view-cell bg-w">
	    	结算卡号：<span class="orange-color">{{substrs($info->memberCashcard->card_bankno,4)}}</span>
	    </li>
	    <li class="mui-table-view-cell bg-w">
	    	结算卡持卡人：<span class="invalid-color">{{msubstr($info->member_nick,0,1)}}*</span>
	    </li>
	    <li class="mui-table-view-cell bg-w">
	    	身份证号：<span class="invalid-color">{{substrs($info->memberCashcard->card_idcard,3)}}</span>
	    </li>
	    <li class="mui-table-view-cell bg-w bor-bot">
	    	金额：<span class="normal-color">{{$price}}</span>
	    </li>
	    <li class="mui-table-view-cell bg-w bor-bot">
	    	支付卡：<span class="normal-color">{{substrs($info->memberCreditcard->card_bankno,4)}}</span>
	    </li>
	    <li class="mui-table-view-cell bg-w bor-bot">
	    	手机号：<span class="normal-color">{{substrs($info->memberCreditcard->card_phone,3)}}</span>
	    </li>
	    <li class="mui-table-view-cell bg-w">
	    	验证码：
	    	<input type="text" placeholder="请输入验证码" name="smsCode" value="" class="my-code authcode"/>
	    	<input type="button" class="code-btn mui-pull-right" value="发送验证码" id="sendCode">
	    </li>
	</ul>

		<!-- <div class="mui-content wrap3">
			<h1 style="text-align: center;">请输入验证码</h1>
			<form class="mui-input-group bg-color" style="margin-top: 50vh">
				<div class="bg-w f-br-top">
				    <div class="mui-input-row">
				        <label>验证码:</label>
				    	<input type="text" name="authcode" class="mui-input-clear authcode" placeholder="请输入验证码">
				    </div>
			    </div>
			</form> -->
			<div class="space-up">
				<!-- <p><a class="my-btn-blue4 authcode" id="regBtn">立即申请</a></p> -->
			</div>
		</div>
		 <div id="loading" class="loading-box hid-load">
    <img src='/static/images/loading.gif'/>
  </div>
  <!-- <input type="button" value="确认付款" class="my-confirm" id="myConfirm"> -->
	<a class="my-confirm" id="regBtn">确认付款</a>
  </div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init()
		</script>
	
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script src="/static/js/common.js"></script>

		<script>
			$(function(){
				$("#sendCode").click(function(){
					var phone="{{$info->memberCreditcard->card_phone}}";
					$.post('/api/Userurl/jinyifu_sms',{phone:phone},function(res){
							if(res['code']==303){
								alert('验证码发送失败');
							}else if(res['code']==200){
								alert('发送验证码成功');
							}else if(res['code']==401){
								alert('手机号格式不正确');
							}

						})
					function invokeSettime(obj){
					    var countdown=60;
					    settime(obj);
					    function settime(obj) {
					        if (countdown == 0) {
					            $(obj).attr("disabled",false);
					            $(obj).text("获取验证码");
					            countdown = 60;
					            return;
					        } else {
					            $(obj).attr("disabled",true);
					            $(obj).val("(" + countdown + ") s 重新发送");
					            countdown--;
					        }
					        setTimeout(function() {
					                    settime(obj) }
					                ,1000)
					    }
					}

					  new invokeSettime("#sendCode");
					 
				})
			})

		</script>
		<script type="text/javascript">
			$(function(){
		      var u = navigator.userAgent;
		      var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
		      var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
		      var isClick=false;
					$("#regBtn").click(function(){
					var smsCode=$('.authcode').val();
					if(smsCode){
						if(isClick)return;
						isClick=true;
						$("#regBtn").html('请稍后......');
						var data={
							smsCode:smsCode,
							cardId:"{{$info->memberCreditcard->card_id}}",
							memberId:"{{$info->member_id}}",
							passwayId:"{{$passagewayId}}",
							phone:"{{$info->memberCreditcard->card_phone}}",
							price:"{{$price}}",
						};
						console.log(data);
						$.post('',data,function(res){
							alert(res)
							if(res['code']==404){
								alert("验证码错误")
							}else if(res['code']==327){
								alert("插入订单失败");
							}else if(res['code']==400){
								alert(res['msg']);
							}
					      // if(!isAndroid){
					      //   window.webkit.messageHandlers.returnIndex.postMessage(1);
					      // }else{
					      //   android.returnIndex();
					      // }
						})
					}else{
						alert('请输入验证码！');
					}
				})
			})
		</script>
	</body>

</html>