<!--dialog Content-->
<form class="form-group" action="{{ url('/index/adminster/change_group') }}" method="post">
	<div class="modal-content animated fadeInLeft">

	  <div class="row" style="padding:10px;">
	  @foreach($authGroups as $kev=>$val)
	    <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3" style="margin:5px 0;">
	      <input type="radio" id="adminster-{{ $val['id'] }}" name="auth-group" value="{{ $val['id'] }}"> <label for="adminster-{{ $val['id'] }}">{{ $val['title'] }}</label>
	    </div>
	  @endforeach
	  </div>
	</div>
	<!--dialog Button-->
	<div class="modal-footer animated fadeInLeft">
		<button type="button" class="btn btn-primary btn-save">确认转移，并删除当前用户组</button>
	    <button type="button" class="btn btn-cancel" data-dismiss="modal">取消转移</button>
	</div>
<script type="text/javascript">
	$(document).ready(function(){
				$('.btn-save').click(function(){
						var id=$("input[name='auth-group']:checked").prop('value');
            $.ajax({
                type: 'POST',
                url: "{{ url('/index/adminster/change_group') }}" ,
                 data: {
                   group_id:id
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
