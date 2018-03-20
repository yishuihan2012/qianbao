 <!--dialog Content-->
  <!DOCTYPE html>
 <html lang="en">
 <head>
 <link href="/static/css/zui.min.css" rel="stylesheet">
 <link href="/static/css/animate.min.css" rel="stylesheet">
<script src="/static/lib/jquery/jquery.js"></script>
 </head>
  <body class="main-container animated fadeInLeft" style="height: 100%">
<table class="table table-hover">
   <tr>
     <th>#<a href="/index/financial/income/is_export/1" class="btn btn-sm btn-primary ">导出</a></th>
     <th>订单号</th>
     <th>会员</th>
     <th>订单金额</th>
     <th>平台收益</th>
     <th>代扣费</th>
     <th>状态</th>
     <th>时间</th>
   </tr>
   @foreach($data as $key)
   <tr>
    <td>{{$key->order_id}}</td>
    <td>{{$key->order_no}}</td>
    <td>{{$key->member_nick}}</td>
    <td>{{$key->order_money}}</td>
    <td>{{$key->order_platform}}</td>
    <td>{{$key->order_buckle}}</td>
    <td><i class="icon icon-check text-success"></i></td>
    <td>{{$key->order_update_time}}</td>
   </tr>
   @endforeach
   <tr>
    <td colspan="10">{!! $data->render() !!}</td>
   </tr>
 </table>
</body>
</html>

<script type="text/javascript">
$(document).on("click",".reloadhref",function(){
    var page=parseInt($('.reloadpage').val());
    if(page>0){
        search=$(this).attr('href');
        param=search.replace(/page\=\d+/,"page="+page);
        if(!search)
            param="?page="+page;
        if(!param)
            param=search+"&page="+page;
        location.href=location.origin+param;
    }
  })
</script>

