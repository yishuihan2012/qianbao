<!--dialog Title-->
<link rel="stylesheet" href="/static/css/jquery-labelauty.css">
<style>
input.labelauty + label > span.labelauty-unchecked-image
{
	background-image: url( /static/images/input-unchecked.png );
}

input.labelauty + label > span.labelauty-checked-image
{
	background-image: url( /static/images/input-checked.png );
}
.dowebok {padding-left: 3px;}
.dowebok ul { list-style-type: none;}
.dowebok li { display: inline-block;}
.dowebok li { margin: 5px 3px 3px 0px;}
.dowebok label{margin-bottom: 0}
input.labelauty + label { font: 12px "Microsoft Yahei";}
</style>
<div class="modal-header animated fadeInLeft">
	<div class="row">
        <div class="col-sm-8"><h4>更改会员用户组</h4></div>
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
</div>

<!--dialog Content-->
<div class="modal-content animated fadeInLeft">

	<h2></h2>
	<form action="{{ url('/index/member_group/group_edit/id/'.$group->group_id) }} " method="post" class="form-horizontal" id="myform">
		<input type="hidden" name='id' value="{{ $group->group_id }}">
		<div class="row form-group">
			<label for="group_name" class="col-sm-2 text-right"><b>用户组名:</b></label>
			<div id="group_name" class="col-sm-6">
				<input type="text" class="form-control group_name" name="group_name" placeholder="请填写用户组名称" value="{{ $group->group_name }}">
			</div>
		</div>

		<div class="row form-group">
		 <label for="" class="col-sm-3 text-right"><b>用户组图标:</b></label>
		 <div id="" class="col-sm-6">
			 <div id='uploaderExample3' class="uploader">
			 	 <div class="uploader-message text-center">
			    	 	 <div class="content"></div>
			    		 <button type="button" class="close">×</button>
			  	 </div>
			  	 <div class="uploader-files file-list file-list-grid"></div>
			  	 <img name="group_thumb" src="{{$group->group_thumb}}" alt="">
			 	 <div>
			 	 	 <hr class="divider">
			 	 	 <div class="uploader-status pull-right text-muted"></div>
			 	 	 <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
			 	 	 <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
			 	 </div>
			 </div>
			 <input type="hidden" class="form-control " name="group_thumb" value="">
		 </div>		
	 </div>


		<div class="row form-group">
			<label for="group_name" class="col-sm-2 text-right"><b>组 级 别:</b></label>
			<div id="group_salt" class="col-sm-6">
				<input type="text" class="form-control group_salt" name="group_salt" placeholder="请填写用户组名称" value="{{ $group->group_salt }}">
			</div>
		</div>
		<div class="row form-group">
			<label for="group_name" class="col-sm-2 text-right"><b>升级条件-推荐人数量:</b></label>
			<div id="group_level_value" class="col-sm-6">
				<input type="text" class="form-control group_level_value" name="group_level_invite" placeholder="" value="{{ $group->group_level_invite }}">
			</div>
		</div>
		<div class="row form-group">
			<label for="group_name" class="col-sm-2 text-right"><b>升级条件-刷卡量:</b></label>
			<div id="group_level_value" class="col-sm-6">
				<input type="text" class="form-control group_level_value" name="group_level_transact" placeholder="" value="{{ $group->group_level_transact }}">
			</div>
		</div>
		<div class="row form-group">
			<label for="group_name" class="col-sm-2 text-right"><b>升级条件-付费金额:</b></label>
			<div id="group_level_value" class="col-sm-6">
				<input type="text" class="form-control group_level_value" name="group_level_money" placeholder="" value="{{ $group->group_level_money }}">
			</div>
		</div>
		<div class="row">
			<label for="group_type" class="col-sm-2 text-right"><b>是否能推荐升级:</b></label>
			<div id="group_name" class="col-sm-6">
				<select class="form-control" name="group_level_type">
				  	<!-- <option value="-1" {{ $group->group_level_type=='-1' ? 'selected' : '' }}>不限</option> -->
				  	<option value="0" {{ $group->group_level_type=='0' ? 'selected' : '' }}>否</option>
				  	<option value="1" {{ $group->group_level_type=='1' ? 'selected' : '' }}>是</option>
				  	<!-- <option value="2" {{ $group->group_level_type=='3' ? 'selected' : '' }}>刷卡量</option>
				  	<option value="3" {{ $group->group_level_type=='3' ? 'selected' : '' }}>付费升级</option>-->
				</select>
			</div>
		</div>
		<br/>
		<div class="row form-group">
			<label for="group_name" class="col-sm-2 text-right"><b>直接推荐人分佣金额:</b></label>
			<div id="group_level_value" class="col-sm-6">
				<input type="text" class="form-control group_level_value" name="group_direct_cent" placeholder="" value="{{ $group->group_direct_cent }}">
			</div>
		</div>
		<div class="row form-group">
			<label for="group_name" class="col-sm-2 text-right"><b>二级推荐人分佣金额:</b></label>
			<div id="group_level_value" class="col-sm-6">
				<input type="text" class="form-control group_level_value" name="group_second_level_cent" placeholder="" value="{{ $group->group_second_level_cent }}">
			</div>
		</div>
		<div class="row form-group">
			<label for="group_name" class="col-sm-2 text-right"><b>三级推荐人分佣金额:</b></label>
			<div id="group_level_value" class="col-sm-6">
				<input type="text" class="form-control group_level_value" name="group_three_cent" placeholder="" value="{{ $group->group_three_cent }}">
			</div>
		</div>
		<h5></h5>


	</form>
	<h2></h2>
</div>

<!--dialog Button-->
<div class="modal-footer animated fadeInLeft">
    <button type="button" class="btn" data-dismiss="modal">关闭</button>
    <button type="button" class="btn btn-primary save">保存</button>
</div>
<script src="/static/js/jquery-labelauty.js"></script>
<script>
$(".save").click(function(){
	if(!$(".group_name").val()){
		$(".group_name").parent().addClass("has-error");
		return;
	}
	$("#myform").submit()
})
$(function(){
	$(':input').labelauty();
});

//上传文件设置
 $('#uploaderExample3').uploader({
     url: "{{url('/index/Tool/upload_one')}}",
	 file_data_name:'avatar',
	 filters:{ max_file_size: '10mb',},
	 limitFilesCount:1,
	 onFileUploaded(file, responseObject) {
	    	 var attr=eval('('+responseObject.response+")");
	    	 attr.code ? $("input[name=group_thumb]").val(attr.url) : bootbox.alert({ message: attr.msg, size: 'small' });
	    	 attr.code ? $("img[name=group_thumb]").attr('src',attr.url) : bootbox.alert({ message: attr.msg, size: 'small' });
	 }
 });
</script>