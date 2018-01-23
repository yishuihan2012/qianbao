<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>盈利模式说明</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class='mui-content wrap3'>
			<div class="wrap3 bg-w bor f-br2">
				<h4>盈利模式说明</h4>
				<div class="space-up">
					@if($data)
						<img src="{{$data['article_thumb']}}">
						{{$data['content']}}
					@else
						暂无盈利模式说明
					@endif
				</div>
			</div>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init()
		</script>
	</body>

</html>