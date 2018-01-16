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
		 <th>流水号</th>
		 <th>会员</th>
		 <th>方式</th>
		 <th>金额</th>
		 <th>状态</th>
		 <th>备注</th>
		 <th>时间</th>
	 </tr>
	 @foreach($data as $key)
	 <tr>
	 	<td>{{$key->withdraw_id}}</td>
	 	<td>{{$key->withdraw_no}}</td>
	 	<td>{{$key->member_nick}}</td>
	 	<td>{{$key->withdraw_method}}</td>
	 	<td>{{$key->withdraw_amount}}</td>
	 	<td><i class="icon icon-check text-success"></i></td>
	 	<td>{{$key->withdraw_bak}}</td>
	 	<td>{{$key->withdraw_update_time}}</td>
	 </tr>
	 @endforeach
	 <tr>
	 	<td colspan="5">{!! $data->render() !!}</td>
	 </tr>
 </table>
</body>
</html>


