@extends('admin/layout/layout_main')
@section('title','实名红包列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
</style>

<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i> 红包列表 <small>共 <strong class="text-danger">{{$count['count_size']}}</strong> 条</small></h3>
  </header>
  <form action="" method="post">
    <div class="input-group" style="width: 200px;float: left;margin-right: 20px;">
    <span class="input-group-addon">名称</span>
    <input type="text" class="form-control" name="member_nick" value="{{$where['member_nick']}}" placeholder="名称">
  </div>

  <div class="input-group" style="width: 200px;float: left;margin-right: 20px;">
    <span class="input-group-addon">手机号</span>
    <input type="text" class="form-control" name="member_mobile" value="{{$where['member_mobile']}}" placeholder="手机号">
  </div>

  <button class="btn btn-primary" type="submit">搜索</button>
</form>
    <h3>红包总金额  <strong class="text-danger">{{$countmoney}}</strong> 元</h3>
  <div class="items items-hover">
      <!-- HTML 代码 -->
          <table class="table datatable">
           <thead>
                 <tr>
                 <!-- 以下两列左侧固定 -->
                      <th>#</th>
                      <th>触发用户</th>
                      <th>收益用户</th>
                      <th class="flex-col">红包金额</th>
                      <th>备注</th>
                      <th>创建时间</th>
                      <th>操作</th>
                 </tr>
      </thead>
      <tbody>
           @foreach ($order_lists as $list)
           <tr>
                 <td>{{$list->recomment_id}}</td>
                 <td>{{$list->recomment_children_name}}</td>
                 <td>{{$list->recomment_member_name}}</td>
                 <td>{{$list->recomment_money}}</td>
                 <td>{{$list->recomment_desc}}</td>
                 <td>{{$list->recomment_creat_time}}</td>
                 <td>
                      <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/order/showwithdraw/id/'.$list->recomment_id)}}" class="btn btn-default btn-sm">详细信息</button>
                      </div>
                      <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/order/toexminewithdraw/id/'.$list->recomment_id)}}" class="btn btn-default btn-sm">审核</button>
                      </div>
                 </td>
           </tr>
           @endforeach
      </tbody>
</table>
  </div>
{!! $order_lists->render() !!}
</div>
</section>
<script>
  $(document).ready(function(){
       $('.menu .nav .active').removeClass('active');
       $('.menu .nav li.recomment').addClass('active');
       $('.menu .nav li.order-manager').addClass('show');
 })
</script>
@endsection
