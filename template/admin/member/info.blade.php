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
	 <table class="table table-bordered table-hover table-striped" style="width:90%; margin:0 auto;">
		 <tr>
			 <th>昵称</th>	
			 <td>{{$member_info->member_nick}}</td>	
			 <th>手机号</th>	
			 <td>{{$member_info->member_mobile}}</td>	
			 <th>头像</th>	
			 <th><img src="{{$member_info->member_image}}"  data-toggle="lightbox"></th>	
		 </tr>	
	 </table>

	 <div class="help-block"><code>(登录信息)</code></div>
	 <table class="table table-bordered table-hover table-striped" style="width:90%; margin:0 auto;">
		 <tr>
			 <th>账号</th>
			 <td></td>
			 <th>token</th>
			 <td></td>
		 </tr>
		 <tr>
			 <th>尝试次数</th>
			 <td></td>
			 <th>登录状态</th>
			 <td></td>
		 </tr>
		 <tr>
			 <th>更新时间</th>
			 <td></td>
			 <th>注册时间</th>
			 <td></td>
		 </tr>
	 </table>

	 <div class="help-block"><code>(其他信息)</code></div>
	 <table class="table table-bordered table-hover table-striped" style="width:90%; margin:0 auto;">
		 <tr>
			 <th>更新时间</th>
			 <td></td>
			 <th>注册时间</th>
			 <td></td>
		 </tr>
	 </table>

	 </form>
 </div>

 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
	 <button type="button" class="btn btn-primary save">保存</button>
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>
 <script>
	 $(".save").click(function(){
		 $("#myform").submit()
	 })
 </script>