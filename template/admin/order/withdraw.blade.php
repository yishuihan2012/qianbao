@extends('admin/layout/layout_main')
@section('title','提现列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
</style>

<section>
<hr/>
<div class="list">
  <header>
    <h3>
      <i class="icon-list-ul"></i>提现 <small>共 <strong class="text-danger">{{$count['count_size']}}</strong> 条</small>
      <i class="icon-list-ul"></i>提现成功 <small>共 <strong class="text-danger">{{$count['success_count']}}</strong> 条</small>
      <i class="icon icon-yen"></i>提现成功金额 <small><strong class="text-danger">{{$count['withdraw_amount']}}</strong> 元</small>
      <i class="icon icon-yen"></i>待审核金额 <small><strong class="text-danger">{{$count['wait_amount']}}</strong> 元</small>
      <i class="icon icon-yen"></i>手续费 <small><strong class="text-danger">{{$count['withdraw_charge']}}</strong> 元</small>
    </h3>
  </header>
  <h3></h3>
  <blockquote>
  <form action="" method="get">
    <div class="input-group" style="width: 150px;float: left;margin-right: 20px;">
    <span class="input-group-addon">用户名</span>
    <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick']}}" placeholder="用户名">
  </div>

  <div class="input-group" style="width: 200px;float: left;margin-right: 20px;">
    <span class="input-group-addon">手机号</span>
    <input type="text" class="form-control" name="member_mobile" value="{{$r['member_mobile']}}" placeholder="手机号">
  </div>
  <div class="input-group" style="width: 170px;float: left;margin-right: 10px;">
  <span class="input-group-addon">订单状态</span>
  <select name="withdraw_state" class="form-control withdraw_state">
    <option value="" >全部</option>
    <option value="11" >待审核</option>
    <option value="12">通过</option>
    <option value="-12">驳回</option>
  </select>
  </div>
<div class="input-group" style="width: 360px;float: left; margin-right: 10px;">
  <span class="input-group-addon">创建时间</span>
    <input type="date" name="beginTime" style="width: 140px" class="form-control" value="{{$r['beginTime'] or ''}}" />
    <input type="date" name="endTime" style="width: 140px" class="form-control" value="{{$r['endTime'] or ''}}" />
</div>
<div class="input-group" style="width: 360px;float: left; margin-right: 10px;">
  <span class="input-group-addon">审批时间</span>
    <input type="date" name="beginTime2"  style="width: 140px" class="form-control" value="{{$r['beginTime2'] or ''}}" />
    <input type="date" name="endTime2"  style="width: 140px" class="form-control" value="{{$r['endTime2'] or ''}}" />
</div>
  <button class="btn btn-primary" type="submit">搜索</button>
  <input type="hidden" name="is_export" class="is_export" value="0">
  <button class="btn btn-primary export" type="submit">导出</button>
</form>
</blockquote>
  <div class="items items-hover">
      <!-- HTML 代码 -->
          <table class="table datatable">
           <thead>
                 <tr>
                 <!-- 以下两列左侧固定 -->
                      <th>#</th>
                      <th>提现流水号</th>
                      <th>用户名</th>
                      <th class="flex-col">总金额</th>
                      <th class="flex-col">操作全额</th> 
                      <th class="flex-col">手续费</th> 
                      <th>订单状态</th>
                      <th>操作人</th>
                      <th>创建时间</th>
                      <th>审批时间</th>
                      <th>操作</th>
                 </tr>
      </thead>
      <tbody>
           @foreach ($order_lists as $list)
           <tr>
                 <td>{{$list->withdraw_id}}</td>
                 <td><code>{{$list->withdraw_no}}</code></td>
                 <td>{{$list->withdraw_name}}</td>
                 <td>{{$list->withdraw_total_money}}</td>
                 <td>{{$list->withdraw_amount}}</td>
                 <td>{{$list->withdraw_charge}}</td>

                 <td>@if($list->withdraw_state==11)
                    待审核
                    @elseif($list->withdraw_state==-11)
                    未提交
                    @elseif($list->withdraw_state==12)
                    通过
                    @else
                     驳回
                     @endif
                 </td>
                 <td>@if($list->withdraw_option===0 && $list->withdraw_state==12)
                      小额自动到账
                    @elseif($list->withdraw_option!==0 && $list->withdraw_state==12)
                       {{$list->withdraw_option}}
                    @elseif($list->withdraw_option!==0 && $list->withdraw_state==-12)
                       {{$list->withdraw_option}}
                    @else
                       无
                      @endif
                    </td>
                 <td>{{$list->withdraw_add_time}}</td>
                 <td>{{$list->withdraw_update_time}}</td>
                 <td>
                      <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/order/showwithdraw/id/'.$list->withdraw_id)}}" class="btn btn-default btn-sm">详细信息</button>
                      </div>
                      @if($list->withdraw_state==11)
                      <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/order/toexminewithdraw/id/'.$list->withdraw_id)}}" class="btn btn-default btn-sm">审核</button>
                      </div>
                      @else
                      @endif
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
       $('.withdraw_state').val({{$r['withdraw_state'] or ''}})
 })

$('.export').click(function(){
  $(".is_export").val(1);
  setTimeout(function(){
    $(".is_export").val(0);
  },100);
})
</script>
@endsection
