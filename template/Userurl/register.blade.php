<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>注册</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content wrap3">
			<form class="mui-input-group bg-color">
				<div class="bg-w f-br-top">
				    <div class="mui-input-row">
				        <label>手机号:</label>
				    	<input type="tel" name="phone" class="mui-input-clear" placeholder="请输入注册手机号">
				    </div>
				    <div class="mui-input-row auth-code">
				        <label>验证码:</label>
				        <input type="button" value="发送验证码" class="mui-pull-right send-code" id="sendCode">
				    	<input type="text" name="code" class="mui-input-clear mui-pull-left" placeholder="请输入验证码">

				    </div>
			    </div>
			    <div class="bg-w space-up f-br-bot">
				    <div class="mui-input-row">
				        <label>登录密码:</label>
				        <input type="password" name="pwd" class="mui-input-clear" placeholder="请设置登录密码">
				    </div>
				    <div class="mui-input-row">
				        <label>确认密码:</label>
				        <input type="password" name="apwd" class="mui-input-clear" placeholder="请再次输入密码">
				    </div>
				    <div class="mui-input-row">
				        <label>推荐人:</label>
				        <input type="text" name="invitePhone" value="{{$tel}}" readonly="readonly">
				    </div>
				</div>
			    <div class="mui-input-row mui-checkbox mui-left">
				  <label class="f14">同意并接受用户注册协议</label>
				  <input class="f14 is-agree" name="agree" value="Item 1" type="checkbox" checked>
				</div>
			</form>
			<div class="space-up">
				<p><a class="my-btn-blue4" id="regBtn">注册</a></p>
				<p class="space-up"><a class="my-btn-blue4 download" id="download_app" href="/api/userurl/download">下载APP</a></p>
			</div>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script src="/static/js/common.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
				//下载APP
				$("#download_app").click(function(){
					var	url=$("#download_app").attr('href');
					window.location.href=url;
				})
				//点击注册
				$('#regBtn').click(function(){
					var phone = $("input[name='phone']").val();
		            var code = $("input[name='code']").val();
		            var invitePhone = $("input[name='invitePhone']").val();
		            var pwd = $("input[name='pwd']").val();
		            var apwd = $("input[name='apwd']").val();
			        var isChecked = $("input[name='agree']").is(':checked');
			        var url = '/api/register/register';
		          if(checkRegister()){
		          	var data={
		          		action:'Register',
		          		method:'register',
		          		param:{
		          			phone:phone,
		          			pwd:pwd,
		          			parent_phone:invitePhone,
		          			smsCode:code
		          		}
		          	};
		          	//提交注册
		            $.post(url,data,function(data){
		              if(data.code==200){
		              	mui.toast(data.msg); 
		              	var	download=$('#download_app').attr('href');
		              	window.location.href=download;
		              }else{
		              	mui.toast("注册失败"); 
		              }
		            });
		          }
		          return false;
		      });
		      // 发送验证码
		      var InterValObj; //timer变量，控制时间
		      var count = 60; //间隔函数，1秒执行
		      var curCount;//当前剩余秒数
		      $("#sendCode").click(function(){
		      	var phone = $("input[name='phone']").val();
		      	var url = '/api/sms/send';
		        curCount = count;
		       if(phone != "" && (/^1[34578]\d{9}$/.test(phone))){
		       	//验证手机号是否注册
		       		$.post('/api/register/register_phone',{param:{phone:phone}},function(res){
		       			if(res.code==200){
				          //设置button效果，开始计时
				          $(".send-code").attr("disabled", "true");
				          $(".send-code").val("" + curCount + "秒");
				          InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
				          //发送验证码
				          $.post(url,{param:{phone:phone}},function(data){
				            // message=eval('(' + data + ')');
				            mui.toast(data.msg); 
				          });
		       			}else{
		       				mui.toast("该手机号已注册"); 
		       			}
		       		})
		        }else if(phone == "" ){
		        	mui.toast("手机号不能为空！"); 
		        }else{
		        	mui.toast("请输入正确的手机号！"); 
		        }
		      });
		      //timer处理函数
		      function SetRemainTime() {
		        if (curCount == 0) {
		          window.clearInterval(InterValObj);//停止计时器
		          $(".send-code").removeAttr("disabled");//启用按钮
		          $(".send-code").val("重新发送");
		        }
		        else {
		          curCount--;
		          $(".send-code").val("" + curCount + "秒");
		        }
		      }
		      //表单提交前验证
		      function checkRegister(){
		      	var phone = $("input[name='phone']").val();
	            var code = $("input[name='code']").val();
	            var invitePhone = $("input[name='invitePhone']").val();
	            var pwd = $("input[name='pwd']").val();
	            var apwd = $("input[name='apwd']").val();
		        var isChecked = $("input[name='agree']").is(':checked');
		        if(phone == ""){
		          mui.toast('手机号不能为空');
		          return false;
		        }else if(!(/^1[34578]\d{9}$/.test(phone))){
		          mui.toast('请输入正确的手机号');
		          return false;
		        }else if(code == ""){
		          mui.toast('验证码不能为空！');
		          return false;
		        }else if(pwd == ""){
		          mui.toast('密码不能为空！');
		          return false;
		        }else if(apwd == ""){
		          mui.toast('确认密码不能为空！');
		          return false;
		        }else if(apwd != pwd){
		          mui.toast('两次密码输入不一致！');
		          return false;
		        }else if(!isChecked){
		          mui.toast('请阅读并确认下方的服务协议!');
		          return false;
		        }else{
		          return true;
		        }
		      }
			});
		</script>
	</body>

</html>