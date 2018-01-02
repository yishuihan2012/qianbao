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
					 <a href="share_link.html" s="{{$v['share_thumb']}}"><div ><img src="{{$v['share_thumb']}}"></div><p class="f14 fc">{{$v['share_title']}}</p></a>
				 </li>
				@endforeach
			 </ul>
		 </div>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
	      var u = navigator.userAgent;
	      var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
	      var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
			mui.init();
			$(function(){
				mui('.exc-code-list').on('tap','a',function(){
					var src=$(this).attr('s');
					src=src.replace(/\//g,'~');
					var url="{{$url}}"+"/share_thumb/"+src;
					var title=$(this).find('p').html();
			      if(!isAndroid){
			        window.webkit.messageHandlers.shareUrl.postMessage([url,title,title]);
			      }else{
			        android.shareUrl(url,title,title);
			      }
			    });
			})
		</script>
	</body>
</html>