<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>我的费率</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
	</head>
	
	<body>
		<div class="mui-content my-rates">
			<div id="myRatesList">
				@foreach($also as $v)
			<table class="f13">
			  <caption>【{{$v['passageway_also']==1 ? '交易' : '代还'}}类】{{$v['passageway_name']}}</caption>
			  <thead class="bg-table" align="center">
			    <tr>
			      <th>&nbsp;</th>
			      @foreach($v['details'] as $d)
			      	<th>{{$d['group_name']}}</th>
			      @endforeach
<!-- 			      <th>普通商户</th>
			      <th>银牌商户</th>
			      <th>金牌商户</th>
			      <th>代理商</th>
 -->			    </tr>
			  </thead>
			  <tbody  class="bg-w" align="center">
			    <tr>
			      <td>费率</td>

			      @foreach($v['details'] as $d)
			      	@if($v['passageway_also'] == 1)
			      		 @if($d['item_charges'])
			      			 <td>{{$d['item_rate']}}+{{$d['item_charges']/100}}</td>
			      		 @else
			      		 	 <td>{{$d['item_rate']}}</td>
			      		 @endif
			      	@else
			      		 @if($d['item_charges'])
			      		 
			      		  	<td>{{$d['item_also']}}+{{$d['item_charges']/100}}</td>
			      		 @else
			      			<td>{{$d['item_also']}}</td>
			      		 @endif
			      	@endif
			      @endforeach
<!-- 			      <td>0.49%</td>
			      <td>0.45%</td>
			      <td>0.42%</td>
			      <td>0.35%</td>
 -->			    </tr>
			    <tr>
			      <td>额度</td>
			      <td colspan="{{count($v['details'])}}" align="left"><span class="space-left">{{$v['passageway_limit']}}</span></td>
			    </tr>
			    <tr>
			      <td>提示</td>
			      <td colspan="{{count($v['details'])}}" align="left"><span class="space-left">{{$v['passageway_desc']}}</span></td>
			    </tr>
			  </tbody>
			</table>
			@endforeach
			</div>
		</div>
<!-- 		<nav class="mui-bar mui-bar-tab">
		  <a class="my-btn-blue f18">我要升级</a>
		</nav>
 -->		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
		    	//滚动加载提示    
		    var allpage=2;//全部页面
		    var page=1; //当前页的页码
		    function showAjax(page){
		      $.ajax({
		        url:"",
		        type:"post",
		        data:{page:page},
		        dateType:"json",
		        beforeSend:function(XMLHttpRequest){ 
		          $(".load-more").text("加载中..."); 
		        }, 
		        success:function(data){
		        //要执行的内容
		        //isEnd =  ;
		        for(var i=0;i<data.length;i++){
		         $("#myRatesList").append("<table class='f13'>"+
					  "<caption>快捷支付一</caption>"+
					  "<thead class='bg-table' align='center'>"+
					    "<tr>"+
					      "<th>&nbsp;</th>"+
					      "<th>普通商户</th>"+
					      "<th>银牌商户</th>"+
					      "<th>金牌商户</th>"+
					      "<th>代理商</th>"+
					    "</tr>"+
					  "</thead>"+
					  "<tbody  class='bg-w' align='center'>"+
					    "<tr>"+
					      "<td>费率</td>"+
					      "<td>0.49%</td>"+
					      "<td>0.45%</td>"+
					      "<td>0.42%</td>"+
					      "<td>0.35%</td>"+
					    "</tr>"+
					    "<tr>"+
					      "<td>额度</td>"+
					      "<td colspan='4' align='left'><span class='space-left'>100-50000元/笔</span></td>"+
					    "</tr>"+
					    "<tr>"+
					      "<td>提示</td>"+
					      "<td colspan='4' align='left'><span class='space-left'>终消费率固定0.35%，差额会以分润形式补偿</span></td>"+
					    "</tr>"+
					  "</tbody>"+
					"</table>");
		            }
		          },
		          error:function(){
	
		          }
		        });
		        }
		      function scrollFn(){
		        //真实内容的高度
		        var pageHeight = Math.max(document.body.scrollHeight,document.body.offsetHeight);
		        //视窗的高度
		        var viewportHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight || 0;
		        //隐藏的高度
		        var scrollHeight = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
		        if(pageHeight - viewportHeight - scrollHeight < 60){
		          page++;
		          if(page<=allpage){
		            showAjax(page);
		          }else{
		            $(".load-more").text("已无数据");
		          }
		         }
		        }
		    // $(window).bind("scroll",scrollFn);//绑定滚动事件
		    });
		</script>
	</body>

</html>