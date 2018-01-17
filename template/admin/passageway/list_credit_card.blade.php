 <!--dialog Title-->
 @extends('admin/layout/layout_main')
@section('title','订单列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
</style>

<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i> 信用卡列表 <small>共 <strong class="text-danger">{{count($list)}}</strong> 条</small></h3>
  </header>
   


</form>
  <div class="items items-hover">
      <!-- HTML 代码 -->
          <table class="table datatable">
           <thead>
             <tr>
			 <th>ID</th>
			 <th>银行名称</th>
			 <th>单笔交易金额</th>
			 <th>单日交易金额</th>
			 <th>金额类型</th>
			 <th>操作</th>
		 </tr>
      </thead>
      <tbody>
            @foreach($list as $key => $value)
		 <tr>
			 <td>{{$value['card_id']}}</td>
			 <td>{{$value['card_name']}}</td>
			 <td>{{$value['bank_single']}}</td>
			 <td>{{$value['bank_one_day']}}</td>
			 <td>{{$value['bank_attrbute']}}</td>
			 <td><a class="remove" href="#" data-url="{{url('/index/passageway/remove_credit_card/id/'.$value['card_id'])}}"><i class="icon-remove"></i> 删除</a></td>
		 </tr>
		@endforeach
      </tbody>
</table>
  </div>
  <a  href="{{url('/index/passageway/index')}}" >返回</a>
</div>
</section>
<script>
 
  $(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.passageway').addClass('active');
    	 $('.menu .nav li.passageway-manager').addClass('show');

    	 $(".parent li a").click(function(){
    	 	$("input[name='article_parent']").val($(this).attr('data-id'));
    	 	$("input[name='article_category']").val(0);
    	 	$("#myform").submit();
    	 })
    	 $(".son li a").click(function(){
    	 	$("input[name='article_category']").val($(this).attr('data-id'));
    	 	$("#myform").submit();
    	 })
    	 $(".remove").click(function(){
    	 	 var url=$(this).attr('data-url');
		 bootbox.confirm({
		    title: "删除信用卡",
		    message: "确定删除信用卡? 删除后不可恢复!",
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
});
</script>
@endsection

 