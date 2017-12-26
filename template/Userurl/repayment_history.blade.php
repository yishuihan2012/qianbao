<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>还款计划已完成列表</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
	</head>
	<body>
		<div class="muicontent repayment-history">
			<ul class="mui-table-view" id="repaymentHistory">
			   <li class="mui-table-view-cell bg-color invalid-color f16">
			        2017年12月
			    </li>
			    <!--注：不同状态显示颜色不同-->
			    <li class="mui-table-view-cell bor-bot">
			        <a href="repayment_plan_detail.html">
			        	<div class="dis-flex-be">
			        		<p class="f16">还款计划2017/12/08</p>
			        		<div class="ftr">
			        		  <p>￥3000.00</p>
			        		  <p class="f16 blue-color-th">已还完</p>
			        		</div>
			        	</div>
			        </a>
			    </li>
			    <li class="mui-table-view-cell bor-bot">
			        <a href="repayment_plan_detail.html"> 
			        	<div class="dis-flex-be">
			        		<p class="f16">还款计划2017/12/08</p>
			        		<div class="ftr">
			        		  <p>￥3000.00</p>
			        		  <p class="f16 blue-color-th">已还完</p>
			        		</div>
			        	</div>
			        </a>
			    </li>
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui('.mui-table-view-cell').on('tap','a',function(){
		      window.top.location.href=this.href;
		    });
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
			         $("#repaymentHistory").append("<li class='mui-table-view-cell bor-bot'>"+
				        "<a href='repayment_plan_detail.html'>"+
				        	"<div class='dis-flex-be'>"+
				        		"<p class='f16'>还款计划2017/12/08</p>"+
				        		"<div class='ftr'>"+
				        		  "<p>￥3000.00</p>"+
				        		  "<p class='f16 blue-color-th'>已还完</p>"+
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