<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>分享二维码列表</title>
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
					  <a h="{{$v['exclusive_id']}}">
						<div ><img src="{{$v['exclusive_thumb']}}"></div>
						<p class="f14 fc">{{$v['exclusive_name']}}</p>
					  </a>
					</li>
				@endforeach
				
			</ul>
		</div>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui('.exc-code-list').on('tap','a',function(){
		      window.top.location.href=location.href.replace('exclusive_code','exclusive_code_detail')+'&exclusive_id='+$(this).attr('h');
		    });
		</script>
	</body>

</html>