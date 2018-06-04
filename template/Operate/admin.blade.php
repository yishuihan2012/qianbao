<link href="/static/css/zui.min.css" rel="stylesheet">
<link href="/static/css/animate.min.css" rel="stylesheet">
<link href="/static/css/common.css" rel="stylesheet">
<link href="/static/lib/datatable/zui.datatable.min.css" rel="stylesheet">
<link href="/static/lib/bootbox/bootbox.min.css" rel="stylesheet">
<link href="/static/lib/datetimepicker/datetimepicker.min.css" rel="stylesheet">
<link href="/static/lib/chosen/chosen.min.css" rel="stylesheet">
<link href="/static/lib/uploader/zui.uploader.min.css" rel="stylesheet">
<link href="/static/css/daterangepicker.css" rel="stylesheet">
<link href="/static/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
 <style>
  .content td{vertical-align: middle;}
 </style>

<blockquote>
   
	
</blockquote>
 <hr/>
<div class="items items-hover">
     
<table class="table table-striped table-hover">
    <thead>
      <tr>
          <th>ID</th>
          <th>用户名</th>
          <th>手机号码</th>
          <th>添加时间</th>
          
          
      </tr>
  </thead>
    <tbody>
    @foreach($list as $val)
      <tr class="content">
          <td>{{$val['operate_id']}}</td>
          <td>{{$val['operate_nick']}}</td>
          <td>{{$val['operate_mobile']}}</td>
          <td>{{$val['operate_add_time']}}</td>
          
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
          <td colspan="7">{!! $list->render() !!}</td>
          
      </tr>
    </tfoot>
</table>
 <script type="text/javascript">
 $(document).ready(function(){
      $('table.datatable').datatable({sortable: true});
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.member').addClass('active');
    	 $('.menu .nav li.member-manager').addClass('show');
})
$('.export').click(function(){
  $(".is_export").val(1);
  setTimeout(function(){
    $(".is_export").val(0);
  },100);
})
 </script>


