<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<link rel="stylesheet" href="/static/operate/css/mui.min.css">
		<link rel="stylesheet" href="/static/operate/css/themes.css">
		<link rel="stylesheet" href="/static/operate/css/page.css">
		<title>联云宝</title>
	</head>
	<body>
		<div class="mui-content bg-main">
			<div>
				<img src="/static/operate/images/top.jpg" class="media-pic">
			</div>
			<div class="my-content">
				<div class="my-content-container">
					<span class="red-btn space-bot">加盟注册</span>
					<input class="input-file" name="username" type="text" placeholder="姓名"/>
					<input class="input-file" name="telphone" type="text" placeholder="手机号"/>
					<a class="blue-btn" href="javascript:void(0);" id="registerBtn">注册送大礼</a>
					<p class="ftr main-color f18 f-bold space-top">联云宝是一款高效、简便、实用、安全的信用卡管理工具。专业提供金融信息服务、生活服务、银行卡收款、信用卡代还等业务</p>
				</div>
				<div class="my-content-container bg-yellow2">
					<span class="red-btn">服务商政策明细表</span>
					<div class="wrap">
						<p class="ftr main-color">A->B->C->D,D开通。除和用户发展会员一样的奖励，具体见下表。</p>
						<table>
							<tr>
								<th><div class="out">
					                <span style="float:right;">会员</span>
					                <span class="line"></span>
					                <span style="float:left;">级别</span>
					            </div></th>
								<th>VIP会员(元)</th>
								<th colspan="2">直推普通会员奖(元)</th>
								<th>奖励(元)</th>
								<th>招商权限</th>
								<th>招商奖最高</th>
							</tr>
							<tr>
								<td></td>
								<td>低价</td>
								<td>刷卡分润</td>
								<td>还款分润</td>
								<td>每百人直推激活奖励</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>城市合伙人</td>
								<td>48</td>
								<td>万12</td>
								<td>万18</td>
								<td>2000</td>
								<td>有</td>
								<td>60%</td>
							</tr>
							<tr>
								<td>代理商</td>
								<td>58</td>
								<td>万10</td>
								<td>万15</td>
								<td>1500</td>
								<td>有</td>
								<td>40%</td>
							</tr>
							<tr>
								<td>分销商</td>
								<td>78</td>
								<td>万8</td>
								<td>万12</td>
								<td>1000</td>
								<td>无</td>
								<td>无</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="my-content-container bg-yellow2">
					<span class="red-btn">服务商政策扶持</span>
					<div class="wrap">
						<div class="ftr fl">
							<p class="main-color f12">1.公司会陆推出<span class="red-color">一系列的给力政策</span>，帮助城市合伙人快速拓展市场！</p>
							<p class="main-color f12">2.公司会投入<span class="red-color">千万级</span>的市场推广宣传，不断加大联云宝的行业影响力！</p>
							<p class="main-color f12">3.公司为城市合伙人提供<span class="red-color">专业团队培训</span>支持，公司帮你做团队培训！</p>
							<p class="main-color f12">4.公司重点扶持各地区的城市合伙人召开<span class="red-color">线下招商会</span>，帮助你开拓当地市场！</p>
						</div>
						<div class="fr" style="position">
							<img src="/static/operate/images/right_001.png" class="media-pic">
						</div>
					</div>
				</div>
				
				<div class="my-content-container bg-yellow2" style="margin-bottom:50px;">
					<span class="red-btn">战略合作保障</span>
					<div class="wrap">
						<div class="ftr fl">
							<p class="main-color f12"><span class="red-color">阿里云：</span>与阿里云合作，保障服务器安全稳定。</p>
							<p class="main-color f12"><span class="red-color">微信&支付宝：</span>国内移动支付两大平台，方便用户移动支付。</p>
							<p class="main-color f12"><span class="red-color">中国银联：</span>通过银联跨行交易清算系统，实现商业银行系统间的互联互通和资源共享，保证银行卡跨行、跨地区和跨境的使用，满足用户消费场景多元化需求。</p>
						</div>
						<div class="fr">
							<img src="/static/operate/images/right_002.png" class="media-pic">
						</div>
					</div>
				</div>
				
				<div class="bg-yellow3">
					<div class="fc">
						<div class="code-pic">
							<img src="/static/operate/images/code.png" class="media-pic">
						</div>
						<p class="main-color f18 f-bold">联云宝有前景有保障有实力，期待您的加入！</p>
						<p class="main-color" style="margin-top:10px;">账户安全由中国人民财产保险承保</p>
					</div>
				</div>
				<p class="message">活动提示：本次活动真实有效并且最终解释权归联云宝所有</p>
			</div>
		</div>
		<script src="/static/operate/js/mui.min.js"></script>
		<script src="/static/operate/js/jquery-2.1.4.min.js"></script>
		<script>
			mui.init();
			mui.ready(function(){
				mui('.mui-content').on('tap','a',function(){
			      window.top.location.href=this.href;
			      var username = $("input[name='username']").val();
			      var telphone = $("input[name='telphone']").val();
			      if(username == ""){
				    	mui.toast("姓名不能为空");
				    }else if(telphone == ""){
				    	mui.toast("请手机号不能为空");
				    }else if(!(/^1[345789]\d{9}$/.test(telphone))){
				    	mui.toast("请输入正确的手机号");
				    }else{
				    	 $.ajax({
					        url:"",
					        type:"post",
					        data:{username:username,telphone:telphone},
					        dataType: "json",
					        success:function(res){
					        	console.log(res);
					        	mui.toast(res.msg);
					          },
					          error:function(){
								
					          }
					        });
				    }
			   });
			});
		</script>
	</body>
</html>
