<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>银生宝首次交易页面</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content wrap3">
			<div class="space-up">
				<h1 style="text-align: center;">银生宝首次交易验证</h1>
				<p>该通道单张信用卡首次交易需要进行一次小额交易验证</p>
				<form action="{{$url}}" method="post">
					<input type="hidden" name="accountId" value="{{$arr['accountId']}}" />
					<input type="hidden" name="repayVersion" value="{{$arr['repayVersion']}}" />
					<input type="hidden" name="orderNo" value="{{$arr['orderNo']}}" />
					<input type="hidden" name="amount" value="{{$arr['amount']}}" />
					<input type="hidden" name="repayInfo" value="{{$arr['repayInfo']}}" />
					<input type="hidden" name="memberId" value="{{$arr['memberId']}}" />
					<input type="hidden" name="merchantNo" value="{{$arr['merchantNo']}}" />
					<input type="hidden" name="deductCardToken" value="{{$arr['deductCardToken']}}" />
					<input type="hidden" name="repayCardToken" value="{{$arr['repayCardToken']}}" />
					<input type="hidden" name="purpose" value="{{$arr['purpose']}}" />
					<input type="hidden" name="quickPayResponseUrl" value="{{$arr['quickPayResponseUrl']}}" />
					<input type="hidden" name="delegatePayResponseUrl" value="{{$arr['delegatePayResponseUrl']}}" />
					<input type="hidden" name="pageResponseUrl" value="{{$arr['pageResponseUrl']}}" />
					<input type="hidden" name="mac" value="{{$arr['mac']}}" />
					<input type="submit" class="my-btn-blue4" style="margin-top: 200px" value="下一步"/>
				</form>
				<!-- <p style="margin-top: 200px"><a class="my-btn-blue4" id="regBtn">下一步</a></p> -->
				<!-- <p style="margin-top: 200px"><a class="my-btn-blue4" id="regBtn">返回首页</a></p> -->
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
			      if(!isAndroid){
			        window.webkit.messageHandlers.returnIndex.postMessage(1);
			      }else{
			        android.returnIndex();
			      }
				})
			})
		</script>
	</body>

</html>