 <!--dialog Title-->
 <link href="/static/lib/dashboard/zui.dashboard.min.css" rel="stylesheet">
 
 <style>
 	 .list-group-items{cursor: pointer;}
 	 tr{cursor: pointer;}
 </style>
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>会员关系</h4></div>
        	 <div class="col-sm-4">
            	 <div class="text-right">
                	 <span class="label label-dot"></span>
                	 <span class="label label-dot label-primary"></span>
                	 <span class="label label-dot label-success"></span>
                	 <span class="label label-dot label-info"></span>
                	 <span class="label label-dot label-warning"></span>
                	 <span class="label label-dot label-danger"></span>
            	 </div>
        	 </div>
    	 </div>
	 <div class="help-block">(本页面显示会员的三级关系)</div>
 </div>

 <!--dialog Content-->
 <div class="modal-content animated fadeInLeft">
	 <h3></h3>
	 <div id="dashboard" class="dashboard dashboard-draggable" data-height="600">
		 <section class="row">
		 <div class="col-md-4 col-sm-4">
  			 <div class="panel" data-id="1">
    			 	 <div class="panel-heading"><i class="icon icon-list"></i><span class="title">直接下级信息</span></div>
    			 	 <div class="panel-body">
					 <table class="table datatable table_one">
						 <thead>
						  	 <th>姓名</th>
						  	 <th>下级</th>
						  	 <th>总刷卡</th>
						  	 <th>详情</th>
						 </thead>
						 <tbody>
	          				 @if(!empty($data))
		          				 @foreach($data as $list)
	          					 <tr class="list-group-items-son" id="{{$list['member_id']}}">
	          						 <td><code>{{htmlspecialchars($list['member_nick'])}}</code><i class='icon icon-{{$list["member_cert"] ? "check" : "times"}} text-{{$list["member_cert"] ? "success" : "danger"}}'></i></td>
	          						 <td><span class="label label-badge label-primary">{{$list['member_son']}} 个下级</span></td>
	          						 <td><strong class="text-danger">{{$list['member_cash']}}</strong> 元</td>
	          						 <td><button type="button" data-toggle="modal" data-size="lg"  data-dismiss='modal' data-remote="{{url('/index/member/info/id/'.$list['member_id'])}}" class="btn btn-sm">查看详情</button></td>
	          					 </tr>
		            			 @endforeach
	          				 @else
	          					 <tr>
	          						 <td colspan="6"> <div class="list-group-item text-center text-red">暂无直接下级</div></td>
	          					 </tr>
						 @endif
						 </tbody>
					 </table>    			 	 
				 </div>
  			 </div>
		 </div>

		 <div class="col-md-4 col-sm-4">
  			 <div class="panel lod-son" data-id="2">
    			 	 <div class="panel-heading"><i class="icon icon-list"></i><span class="title">会员间接下级</span></div>
    				 <div class="panel-body no-padding">
					 <table class="table table_two">
					  	 <thead>
					  		 <th>姓名</th>
					  		 <th>下级</th>
					  		 <th>总刷卡</th>
					  		 <th>详情</th>
					  	 </thead>
					  	 <tbody class="table_content_son"></tbody>
					 </table>
    				 </div>
  			 </div>
		 </div>

		 <div class="col-md-4 col-sm-4">
  			 <div class="panel lod" data-id="3">
    			 <div class="panel-heading"><i class="icon icon-table"></i><span class="title">会员三级下级</span></div>
				 <div class="panel-body no-padding">
					 <table class="table datatable datatables table_three">
					  	 <thead>
					  		 <th>姓名</th>
					  		 <th style="text-align:center">下级</th>
					  		 <th>总刷卡</th>
					  		 <th>详情</th>
					  	 </thead>
					  	 <tbody class="table_content"></tbody>
					 </table>
				 </div>
  			 </div>
		 </div>
		 </section>
	 </div>
 </div>

 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
	 <button type="button" class="btn btn-primary" data-dismiss="modal">关闭页面</button>
 </div>
 <script src="/static/lib/dashboard/zui.dashboard.min.js"></script>

 <script>
	 $('table.datatables').datatable({
	    	 checkable: true,
	    	 sortable: true,
	    	 checkedClass: "checked",
	    	 // 更多参数...
	 });
	 $('table.datatable').datatable({sortable: true});
	 $('#dashboard').dashboard({draggable: true});
	
	 $(document).on("click","table_one a",function(){
	 	console.log(this.href);
	 	return false;
	 })

	 //获取间接下级
	 $(".list-group-items-son").click(function(){
		 var id = $(this).attr('data-id');
		 $(".lod-son").addClass('panel-loading');
		 if(id){
			 $.post("{{url('/index/member/getChildInfo')}}",{memberId:id, json:true},function(result){
			 	 $(".table_content_son").empty();
			 	 $(".lod-son").removeClass('panel-loading');
				 if(jQuery.isEmptyObject(result)){
					 $(".table_content_son").append("<tr><td colspan='6' class='text-center'>暂无数据</td></tr>");
				 }else{
					 var str="";
				   	 $.each(result,function(n,value) {
				   	 	 id_state=value.member_cert ? "<span class='label label-primary'>已认证</span>" : "<span class='label label-danger'>未认证</span>";	
				   	 	 str+="<tr class='list-group-items' data-id='"+value.member_id+"'><td><code>"+value.member_nick+"</code>"+id_state+"</td><td><span class='label label-badge label-primary'>"+value.member_son+" 个</span></td><td><strong class='text-danger'>"+value.member_cash+" 元</strong></td><td><button type='button' data-toggle='modal' data-size='lg' class='btn btn-sm'  data-remote='/index/member/info/id/"+value.member_id+"' data-dismiss='modal'>查看详情</button></td></tr>"; 
				      });
			      	 $(".table_content_son").append(str);
				 }
			 },'Json');
		 }else
			 bootbox.alert({message: "未找到参数",size: 'small'});
	 })

	 //点击间接下级的时候 获取三级下级
	 $(".table_content_son").delegate("tr",'click' ,function(){
	 	 $(".lod").addClass('panel-loading');
		 var id = $(this).attr('data-id');
		 if(id){
			 $.post("{{url('/index/member/getChildInfo')}}",{memberId:id, json:true},function(result){
			 	 $(".table_content").empty();
			 	 $(".lod").removeClass('panel-loading');
				 if(jQuery.isEmptyObject(result)){
					 $(".table_content").append("<tr><td colspan='6' class='text-center'>暂无数据</td></tr>");
				 }else{
					 var str="";
				   	 $.each(result,function(n,value) {
				   	 	 var id_state="";
				   	 	 id_state=value.member_cert ? "<span class='label label-primary'>已认证</span>" : "<span class='label label-danger'>未认证</span>";	
				   	 	 str+="<tr class='list-group-items'><td><code>"+value.member_nick+"</code>"+id_state+"</td><td style='text-align:center'><span class='label label-badge label-primary'>"+value.member_son+" 个</span></td><td><strong class='text-danger'>"+value.member_cash+" 元</strong></td><td><button type='button' data-toggle='modal' data-size='lg' class='btn btn-sm'  data-remote='/index/member/info/id/"+value.member_id+"'>查看详情</button></td></tr>"; 
				      });
			      	 $(".table_content").append(str);
				 }
				 $('table.datatables').datatable('load');
			 },'Json');
		 }else
			 bootbox.alert({message: "未找到参数",size: 'small'});
	 })

	 /*避免弹出两次modal层之后关闭出现问题 隐藏遮罩层*/
	 $("table").on("click",'.btn-sm', function(){
	 	 $(".modal-backdrop").remove();
	 })
	
</script>