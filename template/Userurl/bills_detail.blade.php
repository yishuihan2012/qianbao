<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>账单详情</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content bills-detail">
			<div class="bg-w fc bills-detail-tit">
				<!-- 分润分佣 -->
				@if($wallet_log['log_relation_type']==1)
			  <p class="f14 space-bot">
					@if(isset($commission))
						{{$commission['commission_type']==1 ? '分润' : '分佣'}}
					@endif
			  奖励</p>
			  <p class="f24 f-bold">{{$wallet_log['log_wallet_amount']}}</p>

			  	<!-- 提现 -->
				@elseif($wallet_log['log_relation_type']==2)

			  <p class="f14 space-bot">提现金额</p>
			  <p class="f24 f-bold">{{$wallet_log['log_wallet_amount']}}</p>
			  
				@elseif($wallet_log['log_relation_type']==3)
				@elseif($wallet_log['log_relation_type']==4)

				<!-- 推荐奖励 -->
				@elseif($wallet_log['log_relation_type']==5)

			  <p class="f14 space-bot">推荐奖励</p>
			  <p class="f24 f-bold">{{$wallet_log['log_wallet_amount']}}</p>

				@elseif($wallet_log['log_relation_type']==6)
				@endif
			</div>
			<ul class="mui-table-view">
				<!-- 分润分佣 -->
				@if($wallet_log['log_relation_type']==1)
					@if(isset($commission))
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">交易状态</p>
				    	<p>{{$commission['commission_state']==1 ? '正常' : '异常需审核'}}</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">下级人员</p>
				    	<p>{{$commission['member_mobile']}}</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">交易时间</p>
				    	<p>{{$commission['commission_creat_time']}}</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">交易描述</p>
				    	<p>{{$commission['commission_desc']}}</p>
				    </li>
					@endif

				<!-- 提现 -->
				@elseif($wallet_log['log_relation_type']==2)
					@if(isset($withdraw))
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">交易状态</p>
				    	<p>{{$withdraw['info']}}</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">提现方式</p>
				    	<p>
				    		{{$withdraw['withdraw_method']=='Alipay' ? '支付宝' : '微信'}}
				    	</p>
				    </li>
				    @if($withdraw['withdraw_state']==12)
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">实际到账</p>
				    	<p>{{$withdraw['withdraw_amount']}}元</p>
				    </li>
				    @elseif($withdraw['withdraw_state']==-12)
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">驳回原因</p>
				    	<p>{{$withdraw['withdraw_information']}}</p>
				    </li>
				    @else
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">预计到账</p>
				    	<p>{{$withdraw['withdraw_amount']}}元</p>
				    </li>
				    @endif
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">交易时间</p>
				    	<p>{{$withdraw['withdraw_update_time']}}</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">订单号</p>
				    	<p>{{$withdraw['withdraw_no']}}</p>
				    </li>
				    @endif
				@elseif($wallet_log['log_relation_type']==3)
				@elseif($wallet_log['log_relation_type']==4)

				<!-- 推荐红包 -->
				@elseif($wallet_log['log_relation_type']==5)
					@if(isset($recomment))
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">推荐人员</p>
				    	<p>{{$recomment['member_mobile']}}</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">交易时间</p>
				    	<p>{{$recomment['recomment_creat_time']}}</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">红包描述</p>
				    	<p>{{$recomment['recomment_desc']}}</p>
				    </li>
					@endif
				@elseif($wallet_log['log_relation_type']==6)
				@endif
<!-- 				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">付款信用卡</p>
				    	<p>工商银行(尾号2586)</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">到账储蓄卡</p>
				    	<p>建设银行（尾号7821）</p>
				    </li>
 -->			    
			    <!--对此账单有疑问？跳转至客服页面-->
			    <li class="mui-table-view-cell">
			    	<a class="mui-navigate-right" id="toConsult">对此账单有疑问?</a>
			    </li>
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
				document.getElementById('toConsult').addEventListener('tap',function(){
					mui.openWindow({
						url:'customer_service.html',
						id:'customer_service'
					});
				});
			});
		</script>
	</body>

</html>