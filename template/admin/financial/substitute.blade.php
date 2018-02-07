 <!--dialog Content-->
  <!DOCTYPE html>
 <html lang="en">
 <head>
 <link href="/static/css/zui.min.css" rel="stylesheet">
 <link href="/static/css/animate.min.css" rel="stylesheet">
 </head>
  <body class="main-container animated fadeInLeft" style="height: 100%">
<table class="table table-hover">
   <tr>
     <th>#</th>
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
    <td>{{$key->order_edit_time}}</td>
   </tr>
   @endforeach
   <tr>
    <td colspan="10">{!! $data->render() !!}</td>
   </tr>
 </table>
</body>
</html>


