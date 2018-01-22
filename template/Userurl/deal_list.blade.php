<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>交易账单</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content bills">
			<div id="billsList">
			<ul class="mui-table-view">
				<!--月账单统计表头-->
			   <!--  <li class="mui-table-view-cell bg-color invalid-color f15">
			        2017年11月 9000.00元 共3笔
			    </li> -->
			    <!--注：不同状态显示颜色不同-->
			    @foreach($data as $k => $v)
			    <li class="mui-table-view-cell bills-list">
			        <a href="javascript:void(0)">
			        	<div class="dis-flex-be space-bot">
			        		<p>银行卡尾号({{$v->bank_ons}})</p>
			        		<p class="blue-color f20">{{$v->order_money}}元</p>
			        	</div>
			        	<div class="dis-flex-be">
			        		<p class="f14 invalid-color dis-flex">{{$v['add_time']}}<br/>{{$v->passageway_name}}</p>
			        		
			        		<p class="f14 yellow-color dis-flex ftr">@if($v->order_state == 1) 待支付 @elseif($v->order_state==2) 成功 @elseif($v->order_state==-1) 失败 @else 超时 @endif</p>
			        	</div>
			        </a>
			    </li>
			   @endforeach
			</ul>
			</div>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript">
			mui.init();
			//滚动加载更多
			var allpage={{$pages}};//全部页面
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
		        	var data = JSON.parse(data);
		        	page = data.page;
		        	console.log(page);
		        //要执行的内容
		        //isEnd =  ;
		        for(var i=0;i<data.data.length;i++){
		        	state = '';
		        	if(data.data[i].order_state == 1){
		        		state = "待支付";
		        	}else if(data.data[i].order_state == 2){
		        		state = "成功";
		        	}else if(data.data[i].order_state == -1){
		        		state = "失败";
		        	}else{
		        		state = "超时";
		        	}
		         $(".mui-table-view").append("<li class='mui-table-view-cell bills-list'>"+
				        "<a href='javascript:void(0)'>"+
				        	"<div class='dis-flex-be space-bot'>"+
				        		"<p>银行卡尾号("+data.data[i].bank_ons+")</p>"+
				        		"<p class='blue-color f20'>"+data.data[i].order_money+"元</p>"+
				        	"</div>"+
				        	"<div class='dis-flex-be'>"+
				        		"<p class='f14 invalid-color dis-flex'>"+data.data[i].add_time+"<br/>"+data.data[i].passageway_name+"</p>"+
				        		// "<p class='f14 invalid-color dis-flex fc'>"++"</p>"+
				        		"<p class='f14 yellow-color dis-flex ftr'>"+state+"</p>"+
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
			mui('.mui-table-view-cell').on('tap','a',function(){
		      window.top.location.href=this.href;
		    });
		</script>
	</body>

</html>