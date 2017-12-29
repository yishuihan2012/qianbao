@extends('admin/layout/layout_main')
@section('title','套现列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
</style>

<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i> 套现订单 <small>共 <strong class="text-danger">{{$count['count_size']}}</strong> 条</small></h3>
  </header>
  <div class="items items-hover">
      <!-- HTML 代码 -->
          <table class="table datatable">
           <thead>
                 <tr>
                 <!-- 以下两列左侧固定 -->
                      <th>#</th>
                      <th>套现流水号</th>
                      <th>用户</th>
                      <th>结算卡</th>
                      <th>信用卡</th>
                      <th class="flex-col">总金额</th>
                      <th class="flex-col">分润消耗</th> 
                      <th class="flex-col">手续费</th> 
                      <th class="flex-col">费率</th> 
                      <th>订单状态</th>
                      <th>备注</th>
                      <th>创建时间</th>
                      <th>操作</th>
                 </tr>
      </thead>
      <tbody>
           @foreach ($order_lists as $list)
           <tr>
                 <td>{{$list->order_id}}</td>
                 <td><code>{{$list->order_no}}</code></td>
                 <td>{{$list->order_name}}</td>
                 <td>{{$list->order_card}}</td>
                 <td>{{$list->order_creditcard}}</td>

                 <td>{{$list->order_money}}</td>
                 <td>{{$list->order_fen}}</td>
                 <td>{{$list->order_charge}}</td>
                 <td>{{$list->order_also}}%</td>

                 <td>@if($list->order_state==1)待支付@elseif($list->order_state==2)成功@elseif($list->order_state==-1)失败@else 超时@endif</td>
                 <td>{{$list->order_desc}}</td>
                 <td>{{$list->order_add_time}}</td>
                 <td>
                      <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/order/showcash/id/'.$list->order_id)}}" class="btn btn-default btn-sm">详细信息</button>
                      </div>
                      <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/order/showcash/id/'.$list->order_id)}}" class="btn btn-default btn-sm">审核</button>
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
       $('.menu .nav li.cash').addClass('active');
       $('.menu .nav li.order-manager').addClass('show');
 })
</script>
@endsection
