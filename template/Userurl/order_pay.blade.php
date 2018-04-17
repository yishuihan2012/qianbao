<!doctype html>
<html>
<head>
	<!-- 荣邦申请快捷支付 ishtml=2 时调用本页面 填入验证码调用确认接口 -->
	<meta charset="UTF-8">
	<title>订单支付</title>
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
		    	持卡人：<span class="invalid-color">{{msubstr($data['card_name'],0,1)}}*</span>
		    </li>
		   <!--  <li class="mui-table-view-cell bg-w">
		    	身份证号：<span class="invalid-color">370****2832</span>
		    </li> -->
		    <li class="mui-table-view-cell bg-w bor-bot">
		    	金额：<span class="normal-color">{{$data['price']}}</span>
		    </li>
		   <li class="mui-table-view-cell bg-w bor-bot">
		    	支付卡：<span class="normal-color">{{substrs($data['card_bankno'],4)}}</span>
		    </li>
		    <li class="mui-table-view-cell bg-w bor-bot">
		    	手机号：<span class="normal-color">{{substrs($data['card_phone'],3)}}</span>
		    </li>
		    <li class="mui-table-view-cell bg-w">
		    	验证码：
		    	<input type="text" placeholder="请输入验证码" name="smsCode" value="" class="my-code authcode"/>
		    	<input type="button" class="code-btn mui-pull-right" value="发送验证码" id="sendCode">
		    </li>
		</ul>
	</div>
	<div id="loading" class="loading-box hid-load">
    	<img src='/static/images/loading.gif'/>
  	</div>
	<a class="my-confirm" id="regBtn">确认付款</a>
  </div>
	<script src="/static/js/mui.min.js"></script>
	<script src="/static/js/mui.min.js"></script>
	<script src="/static/js/jquery-2.1.4.min.js"></script>
	<script src="/static/js/common.js"></script>
	<script type="text/javascript">
		mui.init()
	</script>
	<script>
		$(function(){
			$("#sendCode").click(function(){
				var data={
					'card_name':"{{$data['card_name']}}",
					'card_idcard':"{{$data['card_idcard']}}",
					'bankAccountNo':"{{$data['card_bankno']}}",
					'mobile':"{{$data['card_phone']}}",
					'out_trade_no':"{{$data['out_trade_no']}}",
					'card_idcard':"{{$data['card_idcard']}}",
				};
				$.post('/api/Userurl/easylife_sms',{'data':data},function(res){
						if(res.code==200){
							mui.toast('发送验证码成功');
						}else{
							mui.toast(res.msg);
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
						'sms':smsCode,
						'out_trade_no':"{{$data['out_trade_no']}}",
						'mobile':"{{$data['card_phone']}}"
					};
					// console.log(data);
					$.post('/api/Userurl/easylife_pay',data,function(res){
						if(res.code==200){
							mui.toast("交易成功");

						}else{
							mui.toast("交易失败："+res.msg);
						}
						setTimeout(function(){
							if(!isAndroid){
					      	 	 window.webkit.messageHandlers.returnIndex.postMessage(1);
					        }else{
					       		 android.returnIndex();
					        }
						}, 2000 );  
					})
				}else{
					mui.toast('请输入验证码！');
				}
			})
		})
	</script>
	</body>
</html>