<!doctype html>
<html>
	<head>
		<!-- 荣邦快捷支付开通 ishtml=2 时调用本页面 填入验证码调用确认接口 -->
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
		<div class="mui-content wrap3">
			<h1 style="text-align: center;">请输入验证码</h1>
			<form class="mui-input-group bg-color" style="margin-top: 50vh">
				<div class="bg-w f-br-top">
				    <div class="mui-input-row">
				        <label>验证码:</label>
				    	<input type="text" name="authcode" class="mui-input-clear authcode" placeholder="请输入验证码">
				    </div>
			    </div>
			</form>
			<div class="space-up">
				<p><a class="my-btn-blue4" id="regBtn">立即开通</a></p>
			</div>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script src="/static/js/common.js"></script>
		<script type="text/javascript">
			$(function(){
		      var u = navigator.userAgent;
		      var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
		      var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
				mui(document).on('tap','#regBtn',function(){
					var authcode=$('.authcode').val();
					if(authcode){
						var data={
							authcode:authcode,
							treatycode:"{{$treatycode}}",
							smsseq:"{{$smsseq}}",
							memberId:"{{$memberId}}",
							passwayId:"{{$passwayId}}",
						};
						$.post('',data,function(res){
							if(res==1){
								alert('成功开通快捷支付！')
							}elseif(res==2){
								alert('验证码异常！')
							}else{
								alert('开通快捷支付失败！')
							}
					      if(!isAndroid){
					        window.webkit.messageHandlers.returnIndex.postMessage(1);
					      }else{
					        android.returnIndex();
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