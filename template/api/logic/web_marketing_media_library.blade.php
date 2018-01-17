<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>推广素材</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/previewimage.css" rel="stylesheet" />
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script src="/static/js/loadPic.js"></script>
	</head>
	<body>
		<div class="mui-content">
			<ul class="bg-w wrap material-list">
				@foreach($generalizelist as $k=>$v)
				<li>
					<div class="material-info block-hide space-up f16" id="biao1">【{{$name}}】{{$v['generalize_contents']}}</div>
					<div class="btn-con f14">
						<a class="show-more bth-show">显示更多</a>
					</div>
					<div class="material-pic">
						@foreach($v['thumbarr'] as $k2 => $v2)
						<div class="material-pic-container">
						    <img src="{{$v2}}" data-preview-src="" data-preview-group="{{$k}}" class="img-list small" onLoad="loadPIc(this)"/>
						</div>
						@endforeach
					</div>
					<div class="dis-flex-be btn-con">
						<p class="f13">
							<span class="invalid-color">{{$v['generalize_time']}}</span>
							<span class="invalid-color ">下载量:<em class="generalize_download_num">{{$v['generalize_download_num']}}</em></span>
						</p>
						<p class="f13">
							<a class="invalid-color saveImg" value="{{$v['generalize_id']}}"><span class="mui-icon iconfont icon-baocun space-right f16"></span>保存图片</a>
							<a class="invalid-color copyArticle"  value="{{$v['generalize_id']}}"><span class="mui-icon iconfont icon-fuzhi space-right f16"></span copyUrl2()>复制文案</a>
						</p>
					</div>
				</li>
				@endforeach
				
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/mui.zoom.js"></script>
		<script src="/static/js/mui.previewimage.js"></script>
		<script src="/static/js/common.js"></script>
		<script type="text/javascript">
			mui.init(); 
			mui.ready(function(){
				mui.previewImage();
			});
			</script>
			
		
	</body>

</html>