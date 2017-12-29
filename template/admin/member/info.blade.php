 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>会员信息展示</h4></div>
        	 <div class="col-sm-4">
            	 <div class="text-right">
	                 <span class="label label-dot label-primary"></span>
	                 <span class="label label-dot label-success"></span>
	                 <span class="label label-dot label-info"></span>
	                 <span class="label label-dot label-warning"></span>
	                 <span class="label label-dot label-danger"></span>
            	 </div>
        	 </div>
    	 </div>
    	 <div class="help-block"><code>(本页面只能修改部分会员信息,具体请会员自行修改)</code></div>
 </div>

 <!--dialog Content-->
 <div class="modal-content animated fadeInLeft">
	 <form action="{{ url('/index/member/member_edit/id/'.$member_info->member_id) }} " method="post" class="form-group" id="myform">
	 <input type="hidden" name="id" value="{{$member_info->member_id }}">
	 <div class="help-block"><code>(基本信息)</code></div>
	 <div style="margin-bottom: 5px">
	 <table class="table table-bordered table-hover table-striped" style="width:60%;float: left;margin-bottom: 0;margin-left: 30px">
		 <tr>
			 <td>昵称</td>
			 <td>{{$member_info->member_nick}}</td>
		 </tr>
		 <tr>
			 <td>手机号</td>
			 <td>{{$member_info->member_mobile}}</td>
		 </tr>
		 <tr>
			 <td>更新时间</td>
			 <td>{{$member_info->member_update_time}}</td>
		 </tr>
		 <tr>
			 <td>注册时间</td>
			 <td>{{$member_info->member_creat_time}}</td>
		 </tr>
	 </table>
	 <img src="{{$member_info->member_image}}"  data-toggle="lightbox" style="width: 25%">
	 </div>
	 <div class="help-block"><code>(登录信息)</code></div>
	 <table class="table table-bordered table-hover table-striped" style="width:90%; margin:0 auto;">
		 <tr>
			 <th>账号</th>
			 <td>{{$member_info->login_account}}</td>
			 <th>token</th>
			 <td>{{$member_info->login_token}}</td>
		 </tr>
		 <tr>
			 <th>尝试次数</th>
			 <td>{{$member_info->login_attempts}}</td>
			 <th>登录状态</th>
			 <td>@if($member_info->login_state==1)正常@elseif($member_info->login_state==-1)禁止@else异常@endif</td>
		 </tr>
	 </table>

	 </form>
 </div>

 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
 	<button type="button" class="btn disables"  data-url="{{url('/index/Member/disables/id/'.$member_info->member_id)}}">封停用户</button>
	 <button type="button" class="btn btn-primary save">保存</button>
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>
 <script>
	 $(".save").click(function(){
		 $("#myform").submit()
	 })
	 $(function(){
	 	$('.disables').click(function(){
 		var url=$(this).attr('data-url');
		 bootbox.confirm({
		    title: "封停用户",
		    message: "确定封停{{$member_info->member_nick}}吗？",
		    buttons: {
		        cancel: {label: '<i class="fa fa-times"></i> 点错'},
		        confirm: {label: '<i class="fa fa-check"></i> 确定'}
		    },
		    callback: function (result) {
		    	 if(result)
		    	 	window.location.href=url;
		    }
		 });
	 	})
	 })
 </script>