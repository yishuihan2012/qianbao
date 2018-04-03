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
    <h3><i class="icon-list-ul"></i> 实名红包列表 <small>共 <strong class="text-danger">{{$count['count_size']}}</strong> 条</small>
    <i class="icon icon-yen"></i>全部总金额 <small><strong class="text-danger">{{$countmoney}} 元</small></strong></h3>
  </header>
  <form action="" method="post">
    <div class="input-group" style="width: 150px;float: left;margin-right: 20px;">
    <span class="input-group-addon">收益用户</span>
    <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick']}}" placeholder="收益用户">
  </div>

  <div class="input-group" style="width: 200px;float: left;margin-right: 20px;">
    <span class="input-group-addon">手机号</span>
    <input type="text" class="form-control" name="member_mobile" value="{{$r['member_mobile']}}" placeholder="手机号">
  </div>
  <div class="input-group" style="width: 240px;float: left;margin-right: 10px;">
    <span class="input-group-addon">身份号</span>
    <input type="text" class="form-control" name="cert_member_idcard" value="{{$r['cert_member_idcard']}}" placeholder="身份号">
  </div>
  <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
     <span class="input-group-addon">实名状态</span>
  <select name="member_cert" class="form-control">
    <option value="" >全部</option>
    <option value="1" @if($r['member_cert']==1) selected @endif>已认证</option>
    <option value="2" @if($r['member_cert']==2) selected @endif>未认证</option>
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
      <span class="input-group-addon">创建时间</span>
      <input type="date" name="beginTime" style="width: 140px" class="form-control" value="{{$r['beginTime'] or ''}}" />
      <input type="date" name="endTime" style="width: 140px" class="form-control" value="{{$r['endTime'] or ''}}" /></div>

  <button class="btn btn-primary" type="submit">搜索</button>
</form>
   
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
