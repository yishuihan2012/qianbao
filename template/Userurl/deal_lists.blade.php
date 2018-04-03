<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>平台公告列表</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content wrap3">
			<ul class="notify-list">
				@foreach ($notice as $v)
			   <li class="space-up4">
			   	  <p class="fc"><span class="my-time-bg">{{$v['notice_createtime']}}</span></p>
			   	  <a href="/api/userurl/notify_list_detail/uid/{{$uid}}/token/{{$token}}/id/{{$v['notice_id']}}" class="wrap bg-w f-br2 space-up">
			   	  	<h4 class="black-color">{{$v['notice_status'] ? '【已读】' : '【未读】'}}{{$v['notice_title']}}</h4>
			   	  	<div class="space-up-down f-telli3 normal-color">
			   	  		{{$v['notice_content']}}
			   	  	</div>
			   	  	<p class="bor-top wrap5">
			   	  		<span>查看详情</span>
			   	  		<span class="mui-icon mui-icon-arrowright mui-pull-right"></span>
			   	  	</p>
			   	  </a>
			   </li>
				@endforeach
			   <!--li class="space-up4">
			   	  <p class="fc"><span class="my-time-bg">2017-11-28 08:00</span></p>
			   	  <a href="notify_list_detail.html" class="wrap bg-w f-br2 space-up">
			   	  	<h4 class="black-color">关于夜间平台系统升级的通知</h4>
			   	  	<div class="space-up-down f-telli3 normal-color">
			   	  		尊敬的喜家钱包用户：为保证系统稳定运营并提升服务质量，平台将于2017年12月27日（本周二）23:50-2018年1月1日00:30进行系统升级，期间将暂停新用户注册、实名认证、添加银行卡、充值、投资、提现等所有服务。建议您提前做好相关资金安排，对您造成的不便我们深感歉意，感谢您对房投网平台的关注与支持！
			   	  	</div>
			   	  	<p class="bor-top wrap5">
			   	  		<span>查看详情</span>
			   	  		<span class="mui-icon mui-icon-arrowright mui-pull-right"></span>
			   	  	</p>
			   	  </a>
			   </li-->
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui('.enotify-list').on('tap','a',function(){
		      window.top.location.href=this.href;
		    });
		</script>
	</body>

</html>