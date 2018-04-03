@extends('admin/layout/layout_main')
@section('title','订单列表管理~')
@section('wrapper')
<style>
   h4 > a,.pull-right > a{color:#145ccd;}
</style>

<section>
<hr/>
<div class="list">
  <header>
    <h3>
      <i class="icon-list-ul"></i> 订单列表 <small>共 <strong class="text-danger">{{$count['count_size']}}</strong> 条</small>
        <i class="icon-list-ul"></i> 已支付订单 <small>共 <strong class="text-danger">{{$count['upgrade_money_yes']}}</strong> 元</small>
      <i class="icon-list-ul"></i> 未支付订单 <small>共 <strong class="text-danger">{{$count['upgrade_money_del']}}</strong> 元</small>
      <i class="icon icon-yen"></i> 升级总金额 <small>共 <strong class="text-danger">{{$count['upgrade_money']}}</strong> 元</small>
      <i class="icon icon-yen"></i> 分佣总金额 <small>共 <strong class="text-danger">{{$count['upgrade_commission']}}</strong> 条</small>
    </h3>
  </header>
   <form action="" method="post">
    <div class="input-group" style="width: 150px;float: left;margin-right: 20px;">
    <span class="input-group-addon">用户名</span>
    <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick']}}" placeholder="用户名">
  </div>

  <div class="input-group" style="width: 200px;float: left;margin-right: 20px;">
    <span class="input-group-addon">手机号</span>
    <input type="text" class="form-control" name="member_mobile" value="{{$r['member_mobile']}}" placeholder="手机号">
  </div>
  <!-- <div class="input-group" style="width: 240px;float: left;margin-right: 10px;">
    <span class="input-group-addon">身份号</span>
    <input type="text" class="form-control" name="cert_member_idcard" value="{{$r['cert_member_idcard']}}" placeholder="身份号">
  </div> -->
  <div class="input-group" style="width: 160px;float: left;margin-right: 10px;">
     <span class="input-group-addon">订单状态</span>
  <select name="upgrade_state" class="form-control">
    <option value="" >全部</option>
    <option value="1" @if($r['upgrade_state']==1) selected @endif>已支付</option>
    <option value="0" @if($r['upgrade_state']==="0") selected @endif>未支付</option>
  </select>
 
  </div>
  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
     <span class="input-group-addon">会员级别</span>
  <select name="member_group_id" class="form-control">
      <option value="" @if ($r['member_group_id']=='') selected="" @endif>全部</option>
    @foreach($member_group as $v)
      <option value="{{$v['group_id']}}" @if ($r['member_group_id']==$v['group_id']) selected @endif>{{$v['group_name']}}</option>
    @endforeach
  </select>
  </div>
    <div class="input-group" style="width: 360px;float: left;margin-right: 10px;">
      <span class="input-group-addon">升级时间</span>
      <input type="date" name="beginTime" style="width: 140px" class="form-control" value="{{$r['beginTime'] or ''}}" />
      <input type="date" name="endTime" style="width: 140px" class="form-control" value="{{$r['endTime'] or ''}}" /></div>
      
  <button class="btn btn-primary" type="submit">搜索</button>

  <input type="hidden" name="is_export" class="is_export" value="0">
  <div class="input-group" style="width: 180px;float: left; margin-right: 10px;">
    <span class="input-group-addon">导出页码,10万/页</span>
    <input type="text" name="start_p" class="form-control start_p" value="">
  </div>
  <button class="btn btn-primary export" type="submit">导出</button>
</form>
  <div class="items items-hover">
      <!-- HTML 代码 -->
          <table class="table datatable">
           <thead>
                 <tr>
                 <!-- 以下两列左侧固定 -->
                      <th>#</th>
                      <!-- <th>订单号</th> -->
                      <th>用户名</th>
                      <th>升级方式</th>
                      <th class="flex-col">升级流水号</th>
                      <th class="flex-col">升级金额</th> 
                      <th class="flex-col">分佣金额</th> 
                      <th>支付状态</th>
                      <th>订单备注</th>
                      <th>订单时间</th>
                      <th>操作</th>
                 </tr>
      </thead>
      <tbody>
           @foreach ($order_lists as $list)
           <tr>
                 <td>{{$list->upgrade_id}}</td>                
                 <td>{{$list->member_nick}}</td>
                 <td>{{$list->upgrade_type}}</td>
                 <td>{{$list->upgrade_no}}</td>
                 <td>{{$list->upgrade_money}}</td>
                 <td>{{$list->upgrade_commission}}</td>
                 <td>@if($list->upgrade_state == 0) 未支付 @else 已支付 @endif</td>
                 <td>{{$list->upgrade_bak}}</td>
                 <td>{{$list->upgrade_creat_time}}</td>
                 <td>
                  <div class="btn-group">
                       <button type="button" data-toggle="modal" data-remote="{{url('/index/order/edit/id/'.$list->upgrade_id)}}" class="btn btn-default btn-sm">详细信息</button>
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
  $('.export').click(function(){
  $(".is_export").val(1);
  setTimeout(function(){
    $(".is_export").val(0);
  },100);
  var start_p=$('.start_p').val();
  var end_p=$('.end_p').val();
  if(start_p){
    var re=/^\d+$/;
    if(!re.test(start_p)){
      alert('导出页码请输入数字');
      return false;
    }
  }
  alert("数据量大的话请耐心等待不要重复点击导出\n单次最大10万条数据\n点击确定开始导出");
})

  $(document).ready(function(){
       $('.menu .nav .active').removeClass('active');
       $('.menu .nav li.order').addClass('active');
       $('.menu .nav li.order-manager').addClass('show');
 })
</script>
@endsection
