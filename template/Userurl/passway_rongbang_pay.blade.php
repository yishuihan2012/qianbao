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
	    	结算卡号：<span class="orange-color">{{substrs($member_info['card_bankno'],4)}}</span>
	    </li>
	    <li class="mui-table-view-cell bg-w">
	    	结算卡持卡人：<span class="invalid-color">{{msubstr($member_info['member_nick'],0,1)}}*</span>
	    </li>
	    <li class="mui-table-view-cell bg-w">
	    	身份证号：<span class="invalid-color">{{substrs($member_info['card_idcard'],3)}}</span>
	    </li>
	    <li class="mui-table-view-cell bg-w bor-bot">
	    	金额：<span class="normal-color">{{$money}}</span>
	    </li>
	    <li class="mui-table-view-cell bg-w bor-bot">
	    	支付卡：<span class="normal-color">{{substrs($creditcard['card_bankno'],4)}}</span>
	    </li>
	    <li class="mui-table-view-cell bg-w bor-bot">
	    	手机号：<span class="normal-color">{{substrs($member_info['member_mobile'],3)}}</span>
	    </li>
	    <li class="mui-table-view-cell bg-w">
	    	验证码：
	    	<input type="text" placeholder="请输入验证码" name="authcode" value="" class="my-code authcode"/>
	    	<!--<input type="button" class="code-btn mui-pull-right" value="发送验证码" id="sendCode">-->
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
		<script type="text/javascript">
			$(function(){
		      var u = navigator.userAgent;
		      var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
		      var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
		      var isClick=false;
				mui(document).on('tap','#regBtn',function(){
					var authcode=$('.authcode').val();
					if(authcode){
						if(isClick)return;
						isClick=true;
						$("#regBtn").html('请稍后......');
						var data={
							authcode:authcode,
							order_no:"{{$order_no}}",
							card_id:"{{$card_id}}",
							memberId:"{{$memberId}}",
							passwayId:"{{$passwayId}}",
						};
						$.post('',data,function(res){
							if(res==1){
								window.top.location.href='/api/userurl/passway_success/uid/{{$uid}}/token/{{$token}}/order_no/{{$order_no}}';
							}else if(res==2){
								alert('验证码异常！');
							}else{
								alert('申请快捷支付失败！err:'+res);
								if(!isAndroid){
									window.webkit.messageHandlers.returnIndex.postMessage(1);
								}else{
									android.returnIndex();
								}
							}
						})
					}else{
						alert('请输入验证码！');
					}
				})
			})
		</script>
	</body>

</html>