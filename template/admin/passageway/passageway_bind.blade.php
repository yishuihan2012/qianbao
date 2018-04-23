 <!--dialog Title-->
 @extends('admin/layout/layout_main')
@section('title','信用卡签约日志~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
</style>

<section>
<hr/>
<div class="list">
  <header>
    <h3>
      总计<strong class="text-danger">{{$data['count']}}</strong>条数据,
      金额<strong class="text-danger">{{$data['money']}}</strong>元
    </h3>
  </header>
<div class="panel">
  <div class="panel-body">
    <form action="" name="myform" class="form-group" method="get">
      <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
        <span class="input-group-addon">会员</span>
        <input type="text" class="form-control" name="member" value="{{$r['member'] or ''}}" placeholder="用户名/手机号"></div>
      <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
        <span class="input-group-addon">通道</span>
        <select name="bind_passway_id" class="form-control bind_passway_id">
          <option value="">全部</option>
          @foreach($passageway as $v)
            <option value="{{$v['passageway_id']}}">{{$v['passageway_name']}}</option>
          @endforeach
        </select>
      </div>
      <div class="input-group" style="width: 360px;float: left; margin-right: 10px;">
        <span class="input-group-addon">添加时间</span>
        <input type="date" name="beginTime" style="width: 140px" class="form-control" value="{{$r['beginTime'] or ''}}" />
        <input type="date" name="endTime" style="width: 140px" class="form-control" value="{{$r['endTime'] or ''}}" /></div>
      <div class="input-group" style="width: 50px;float: left;margin-right: 10px;">
        <button class="btn btn-primary" type="submit">搜索</button></div>
    </form>
  </div>
</div>
  <div class="items items-hover">
      <!-- HTML 代码 -->
  <table class="table datatable">
   <thead>
     <tr>
			 <th>ID</th>
			 <th>用户名</th>
			 <th>银行卡号</th>
			 <th>金额</th>
       <th>通道</th>
			 <th>添加时间</th>
		 </tr>
      </thead>
      <tbody>
    @foreach($list as $key => $value)
		 <tr>
			 <td>{{$value['bind_id']}}</td>
			 <td>{{$value['member_nick']}}</td>
			 <td>{{$value['bind_card']}}</td>
       <td>{{$value['bind_money']}}</td>
			 <td>{{$passageway[$value['bind_passway_id']]['passageway_name']}}</td>
       <td>{{$value['bind_createtime']}}</td>
		 </tr>
		@endforeach
      </tbody>
</table>
 {!! $list->render() !!}
  </div>
</div>
</section>
<script>
$(document).ready(function(){
 	 $('.menu .nav .active').removeClass('active');
	 $('.menu .nav li.passageway_bind').addClass('active');
	 $('.menu .nav li.passageway-manager').addClass('show');
    $('.bind_passway_id').val({{$r['bind_passway_id'] or ''}});
});
</script>
@endsection

 