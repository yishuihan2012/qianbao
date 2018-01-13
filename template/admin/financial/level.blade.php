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
		 <th>会员</th>
		 <th>金额</th>
		 <th>状态</th>
		 <th>时间</th>
	 </tr>
	 @foreach($data as $key)
	 <tr>
	 	<td>{{$key->upgrade_id}}</td>
	 	<td>{{$key->member_nick}}</td>
	 	<td>{{$key->upgrade_money}}</td>
	 	<td><i class="icon icon-check text-success"></i></td>
	 	<td>{{$key->upgrade_update_time}}</td>
	 </tr>
	 @endforeach
	 <tr>
	 	<td colspan="5">{!! $data->render() !!}</td>
	 </tr>
 </table>
</body>
</html>


