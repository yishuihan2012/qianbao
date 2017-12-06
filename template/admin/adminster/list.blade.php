<!--dialog Content-->
<form class="form-group" action="{{ url('/index/adminster/change_group') }}" method="post">
	<div class="modal-content animated fadeInLeft">
	  <div class="row" style="padding:10px;">
	  @foreach($adminster as $kev=>$val)
	    <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3" style="margin:5px 0;">
	      <input type="checkbox" id="adminster-{{ $val['adminster_id'] }}" name="adminster" value="{{ $val['adminster_id'] }}"> <label for="adminster-{{ $val['adminster_id'] }}">{{ $val['adminster_login'] }}</label>
	    </div>
	  @endforeach
	  </div>
	</div>
	<!--dialog Button-->
	<div class="modal-footer animated fadeInLeft">
		<button type="button" class="btn btn-primary btn-save">确认更改</button>
	    <button type="button" class="btn btn-cancel" data-dismiss="modal">取消更改</button>
	</div>
<script type="text/javascript">
	$(document).ready(function(){
				$('.btn-save').click(function(){
						var input=$("input[name='adminster']:checked");
						var data_checked=new Array();
						input.each(function(){
								data_checked.push($(this).attr('value'));
						});
				$.ajax({
						type: 'POST',
						url: "{{ url('/index/adminster/change_group') }}" ,
						 data: {
							 'adminster_id':data_checked,
							 'group_id':"{{ $group_id }}"
						 } ,
					beforeSend:function(){
							$('button.btn-save').prop('disabled','true').html('<i class="icon icon-spin icon-spinner"></i> 正在添加用户到用户组~');
					},
					success: function(data){
						 if(data.code == 200){
								$('button.btn-save').prop('disabled','false').html('<i class="icon icon-check"></i> 用户组用户添加完成~');
								$(".btn-cancel").trigger("click");
						 }else{
								$('button.btn-save').prop('disabled','true').html('<i class="icon icon-times"></i> 用户组用户添加失败，请重试~');
						 }
					 },
					 dataType: 'json'
				});
			});
	});
</script>
</form>
