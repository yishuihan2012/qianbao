<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>收支明细</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/mobiscroll.css" rel="stylesheet" />
		<link href="/static/css/mobiscroll_date.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content">
			<!--收支明细头部信息-->
			<!--注：只第一个月份头部信息有id,其他不加id-->
			<div class="dis-flex-be wrap">
        		<div>
        			<p class="f15 invalid-color" id="monthContainer">{{$data['month']}}</p>
        			<p class="f15 invalid-color space-up3">共收入<span id="income">{{$data['in']}}</span>元 支出<span id="expend">{{$data['out']}}</span>元</p>
        		</div> 
        		<div class="ftr por">
        			<span class="mui-icon iconfont icon-rili normal-color f24">
        				<input type="text" name="month" id="month" readonly class="poa input-reset" placeholder="请填写你的出生日期" value=""/>
        		</div>
        	</div>
			<ul class="mui-table-view" id="particularsList">
				<!--收支明细列表月份头部信息 月份改变时出现-->
				<!--<li class="mui-table-view-cell bg-color">
			        <div class="dis-flex-be">
		        		<div>
		        			<p class="f15 invalid-color">2017-12</p>
		        			<p class="f15 invalid-color space-up3">共收入<span>856.10</span>元 支出<span>581.24</span>元</p>
		        		</div> 
		        		<div class="ftr por">
		        			<span class="mui-icon iconfont icon-rili normal-color f24">
		        				<input type="text" name="month" id="month" readonly class="poa input-reset" placeholder="请填写你的出生日期" value=""/>
		        		</div>
		        	</div>	
			    </li>-->
			    <!--收支明细列表-->
			    @foreach($list as $v)
			    <li class="mui-table-view-cell bor-bot">
			        <a href="bills_detail.html">
			        	<div class="dis-flex-be">
			        		<div>
			        			<p class="f16">{{$v['log_form']}}</p>
			        			<p class="f14 invalid-color space-up3">{{$v['log_add_time']}}</p>
			        		</div>
			        		<div class="ftr">
			        			<p class="f20">{{$v['log_wallet_type']==1 ? '' : '-'}}{{substr($v['log_wallet_amount'],0,-2)}}</p>
			        			<!-- 提现操作 此处显示状态-->
			        			@if($v['log_relation_type']==2)
			        				@if(isset($v['info']))
			        			<p class="f14 yellow-color">$v['info']</p>
			        				@endif
			        			@endif
			        		</div>
			        	</div>
			        </a>
			    </li>
			    @endforeach
			    <li class="mui-table-view-cell bor-bot">
			        <a href="bills_detail.html">
			        	<div class="dis-flex-be">
			        		<div>
			        			<p class="f16">余额提现</p>
			        			<p class="f14 invalid-color space-up3">今天10:24</p>
			        		</div>
			        		<div class="ftr">
			        			<p class="f20">-200.00</p>
			        			<p class="f14 yellow-color">申请中</p>
			        		</div>
			        	</div>
			        </a>
			    </li>
			    <li class="mui-table-view-cell bor-bot">
			        <a href="bills_detail.html">
			        	<div class="dis-flex-be">
			        		<div>
			        			<p class="f16">余额提现</p>
			        			<p class="f14 invalid-color space-up3">今天10:24</p>
			        		</div>
			        		<div class="ftr">
			        			<p class="f20">-200.00</p>
			        			<p class="f14 red-color">已驳回：驳回原因驳回原因</p>
			        		</div>
			        	</div>
			        </a>
			    </li>
			    <li class="mui-table-view-cell bor-bot">
			        <a href="bills_detail.html">
			        	<div class="dis-flex-be">
			        		<div>
			        			<p class="f16">余额提现</p>
			        			<p class="f14 invalid-color space-up3">今天10:24</p>
			        		</div>
			        		<div class="ftr">
			        			<p class="f20">-200.00</p>
			        			<p class="f14 blue-color">到账</p>
			        		</div>
			        	</div>
			        </a>
			    </li>
			    <li class="mui-table-view-cell bor-bot">
			        <a href="bills_detail.html">
			        	<div class="dis-flex-be">
			        		<div>
			        			<p class="f16">邀请注册奖励</p>
			        			<p class="f14 invalid-color space-up3">今天10:24</p>
			        		</div>
			        		<div>
			        			<p class="f20">+2.00</p>
			        		</div>
			        	</div>
			        </a>
			    </li>
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script src="/static/js/mobiscroll_date.js" charset="gb2312"></script>
		<script src="/static/js/mobiscroll.js"></script>
		<script type="text/javascript">
			mui.init();
			mui('.mui-table-view-cell').on('tap','a',function(){
		      // window.top.location.href=this.href;
		    });
			mui.ready(function(){
				var currYear = (new Date()).getFullYear();	
				var opt={};
				opt.date = {preset : 'date'};
				opt.datetime = {preset : 'datetime'};
				opt.time = {preset : 'time'};
				opt.default = {
					theme: 'android-ics light', //皮肤样式
					display: 'modal', //显示方式 
					mode: 'scroller', //日期选择模式
					dateFormat: 'yyyy-mm',
					dateOrder: 'yymm',
					lang: 'zh',
					startYear: currYear - 1, //开始年份
					endYear: currYear + 1, //结束年份
					onSelect:function(textVale,inst){ //选中时间时触发事件
					  var checkedMonth = $("#month").val();
					  console.log(checkedMonth);
					  return;
				      $("#monthContainer").text(checkedMonth);//月份赋值
				      $("#income").text(222);//收入
				      $("#expend").text(222);//支出
				      //选中月份后第一个列表更改
				      $("#particularsList").html("<li class='mui-table-view-cell bor-bot'>"+
					        "<a href='bills_detail.html'>"+
					        	"<div class='dis-flex-be'>"+
					        		"<div>"+
					        			"<p class='f16'>邀请注册奖励</p>"+
					        			"<p class='f14 invalid-color space-up3'>今天10:24</p>"+
					        		"</div>"+
					        		"<div>"+
					        			"<p class='f20'>+2.00</p>"+
					        		"</div>"+
					        	"</div>"+
					        "</a>"+
					    "</li>");
					 }
				};
			$(".input-reset").mobiscroll($.extend(opt['date'], opt['default']));
			
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
			         $("#particularsList").append("<li class='mui-table-view-cell bor-bot'>"+
					        "<a href='bills_detail.html'>"+
					        	"<div class='dis-flex-be'>"+
					        		"<div>"+
					        			"<p class='f16'>邀请注册奖励</p>"+
					        			"<p class='f14 invalid-color space-up3'>今天10:24</p>"+
					        		"</div>"+
					        		"<div>"+
					        			"<p class='f20'>+2.00</p>"+
					        		"</div>"+
					        	"</div>"+
					        "</a>"+
					    "</li>");
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
		    $(window).bind("scroll",scrollFn);//绑定滚动事件
			});
		</script>
	</body>

</html>