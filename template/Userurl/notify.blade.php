<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>消息</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div id="pullrefresh" class="mui-content mui-scroll-wrapper">
		<div class="mui-content notify">
			<ul class="mui-table-view">
			    <li class="mui-table-view-cell mui-media bor-bot">
			        <a class="mui-navigate-right" id="toNotifyList" href="/api/userurl/notify_list/uid/{{$uid}}/token/{{$token}}">
			        	<img class="mui-media-object mui-pull-left" src="/static/images/message_notice.png">
			        	<div class="mui-media-body">系统通知</div>
			        	<!--有消息通知时显示-->
			        	@if($count!=0)
			        	<span class="mui-badge  mui-badge-red">{{$count}}</span>
			        	@endif
			        </a>
			    </li>
			    <li class="mui-table-view-cell mui-media bor-bot">
			        <a class="mui-navigate-right" id="toDealList"  href="/api/userurl/deal_list/uid/{{$uid}}/token/{{$token}}">
			        	<img class="mui-media-object mui-pull-left" src="/static/images/message_transaction.png">
			        	 <div class="mui-media-body">动账交易</div>
			        </a>
			    </li>
			    <li class="mui-table-view-cell mui-media bor-bot" >
			        <a class="mui-navigate-right" id="toWelfareList"  href="/api/userurl/welfare_list/uid/{{$uid}}/token/{{$token}}">
			        	<img class="mui-media-object mui-pull-left" src="/static/images/message_welfare.png">
			        	 <div class="mui-media-body">平台福利</div>
			        </a>
			    </li>
			</ul>
		</div>
		<script src="js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init({
				pullRefresh: {
					container: '#pullrefresh',
					down: {
						style:'circle',
						callback: function(){
							setTimeout(function() {
							window.location.reload();
							mui('#pullrefresh').pullRefresh().endPulldownToRefresh();
						    }, 1500);
						 }
					}
				}
			});
			mui.ready(function(){
				//跳转到平台公告列表页
				document.getElementById('toNotifyList').addEventListener('tap',function(){
					mui.openWindow({
						url:'notify_list.html',
						id:'notify_list'
					});
				});
				//跳转到动账交易列表
				document.getElementById('toDealList').addEventListener('tap',function(){
					mui.openWindow({
						url:'deal_list.html',
						id:'deal_list'
					});
				});
				//跳转到平台福利列表
				document.getElementById('toWelfareList').addEventListener('tap',function(){
					mui.openWindow({
						url:'welfare_list.html',
						id:'welfare_list'
					});
				});
			});
		</script>
	</body>

</html>