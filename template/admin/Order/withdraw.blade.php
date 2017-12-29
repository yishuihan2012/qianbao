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
    <h3><i class="icon-list-ul"></i> 套现列表 <small>共 <strong class="text-danger">{{$count['count_size']}}</strong> 条</small></h3>
  </header>
  <div class="items items-hover">
      <!-- HTML 代码 -->
          <table class="table datatable">
           <thead>
                 <tr>
                 <!-- 以下两列左侧固定 -->
                      <th>#</th>
                      <th>提现流水号</th>
                      <th>用户</th>
                      <th>收款方式</th>
                      <th>收款账号</th>
                      <th class="flex-col">总金额</th>
                      <th class="flex-col">操作全额</th> 
                      <th class="flex-col">手续费</th> 
                      <th>订单状态</th>
                      <th>备注</th>
                      <th>操作人</th>
                      <th>创建时间</th>
                      <th>操作</th>
                 </tr>
      </thead>
      <tbody>
           @foreach ($order_lists as $list)
           <tr>
                 <td>{{$list->withdraw_id}}</td>
                 <td><code>{{$list->withdraw_no}}</code></td>
                 <td>{{$list->withdraw_name}}</td>
                 <td>{{$list->withdraw_method}}</td>
                 <td>{{$list->withdraw_account}}</td>

                 <td>{{$list->withdraw_total_money}}</td>
                 <td>{{$list->withdraw_amount}}</td>
                 <td>{{$list->withdraw_charge}}</td>

                 <td>@if($list->withdraw_state==11)申请已提交@elseif($list->withdraw_state==-11)申请未提交@elseif($list->withdraw_state==12)审核通过@else 审核未通过@endif</td>
                 <td>{{$list->withdraw_bak}}</td>
                 <td>@if($list->withdraw_option==0)小额自动到账@else {{$list->adminster_login}} @endif</td>
                 <td>{{$list->withdraw_add_time}}</td>
                 <td>
                      <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/order/showwithdraw/id/'.$list->withdraw_id)}}" class="btn btn-default btn-sm">详细信息</button>
                      </div>
                      <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/order/toexminewithdraw/id/'.$list->withdraw_id)}}" class="btn btn-default btn-sm">审核</button>
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
       $('.menu .nav li.withdraw').addClass('active');
       $('.menu .nav li.order-manager').addClass('show');
 })
</script>
@endsection
