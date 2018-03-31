@extends('admin/layout/layout_main')
@section('title','钱包日志管理~')
@section('wrapper')
<style>
  .text-ellipsis{cursor: pointer;}
</style>
<header>
  <h3>
    <i class="icon-list-ul"></i> 日志条数 <small>共 <strong class="text-danger">{{$count}}</strong> 条</small>
    <i class="icon icon-yen"></i> 钱包收入 <small>共 <strong class="text-danger">{{$entertottal}}</strong> 元</small>
    <i class="icon icon-yen"></i> 钱包支出 <small>共 <strong class="text-danger">{{$leavetotal}}</strong> 元</small>
  </h3>
  </header>
<div class="panel">
  <div class="panel-body">
    <form action="" name="myform" class="form-group" method="get">
      <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
        <span class="input-group-addon">会员</span>
        <input type="text" class="form-control" name="member" value="{{$r['member'] or ''}}" placeholder="用户名/手机号"></div>
      <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
        <span class="input-group-addon">收入支出</span>
        <select name="log_wallet_type" class="form-control log_wallet_type">
          <option value="">全部</option>
          <option value="1">收入</option>
          <option value="2">支出</option></select>
      </div>
      <div class="input-group" style="width: 360px;float: left; margin-right: 10px;">
        <span class="input-group-addon">添加时间</span>
        <input type="date" name="beginTime" style="width: 140px" class="form-control" value="{{$r['beginTime'] or ''}}" />
        <input type="date" name="endTime" style="width: 140px" class="form-control" value="{{$r['endTime'] or ''}}" /></div>
      <div class="input-group" style="width: 50px;float: left;margin-right: 10px;">
        <button class="btn btn-primary" type="submit">搜索</button></div>
      <input type="hidden" name="is_export" class="is_export" value="0">
      <div class="input-group" style="width: 50px;float: left; margin-right: 10px;">
        <button class="btn btn-primary export" type="submit">导出</button></div>
    </form>
  </div>
</div>
<table class="table table-striped table-hover">
    <thead>
      <tr>
          <th>#</th>
          <th>用户名</th>
          <!-- <th>订单号</th> -->
          <th>操作金额</th>
          <th>实时余额</th>
          <th>描述</th>
          <th>添加时间</th>
      </tr>
  </thead>
    <tbody>
    @foreach($list as $log)
      <tr>
          <td>{{$log['log_id']}}</td>
          <td>{{$log->wallet->member->member_nick}}</td>
          <!-- <td><code></code></td> -->
          <td><i class="icon icon-{{$log->log_wallet_type=='1' ? 'plus' : 'minus' }}">{{$log['log_wallet_amount']}}</td>
          <td><i class="">{{$log['log_balance']}}</td>
          <td class="text-ellipsis" title="{{$log->log_desc}}"><a class="Listen" href="{{$log['hrefurl']}}">{{$log['log_desc']}}</a></td>
          <td>{{$log['log_add_time']}}</td>
      </tr>
  @endforeach
    </tbody>
    <tfoot>
      <tr>
          <td colspan="15">{!! $list->render() !!}</td>
      </tr>
    </tfoot>
</table>
<script type="text/javascript">
$(document).ready(function() {
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.walletlog').addClass('active');
    $('.menu .nav li.wallet-manager').addClass('show');
    $('.log_wallet_type').val({{$r['log_wallet_type'] or ''}});
    $('.export').click(function() {
        $(".is_export").val(1);
        setTimeout(function() {
            $(".is_export").val(0);
        },
        100);
    })
})
 </script>
@endsection
