<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>分享下载链接列表</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content">
			<ul class="bg-w exc-code-list">
				@foreach($list as $k => $v)
					<li class="fl">
					  <a href="share_link.html">
						<div ><img src="{{$v['share_thumb']}}"></div>
						<p class="f14 fc">{{$v['share_title']}}</p>
					  </a>
					</li>
				@endforeach
				
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui('.exc-code-list').on('tap','a',function(){
		      window.top.location.href=this.href;
		    });
		</script>
	</body>

</html>