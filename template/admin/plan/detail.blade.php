 <!--dialog Title-->
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
    <h3><i class="icon-list-ul"></i> 代还订单 <small>共 <strong class="text-danger" style="font-size: 16px">{{$count['count_size']}}</strong> 条</small>
    <i class="icon icon-yen"></i>消费成功总金额 <small><strong class="text-danger" style="font-size: 16px">{{$count['order_cash_money']}}</strong>元</small>
    <i class="icon icon-yen"></i>还款成功总金额 <small><strong class="text-danger" style="font-size: 16px">{{$count['order_repay_money']}}</strong>元</small>
    </h3>
    <h3>
    <i class="icon icon-yen"></i>全部手续费 <small><strong class="text-danger" style="font-size: 16px">{{$count['order_pound']}}</strong>元</small>
      <i class="icon-list-ul"></i> 成本手续费 <small>共 <strong class="text-danger" style="font-size: 16px">{{$count['chengben']}}</strong> 元</small>
      <i class="icon-list-ul"></i> 通道结算金额 <small>共 <strong class="text-danger" style="font-size: 16px">{{$count['order_platform_fee']}}</strong> 元</small>
      <i class="icon-list-ul"></i> 分润金额 <small>共 <strong class="text-danger" style="font-size: 16px">{{$count['sanji']}}</strong> 元</small>
      <i class="icon-list-ul"></i> 分润后盈利 <small>共 <strong class="text-danger" style="font-size: 16px">{{$count['fenrunhou']}}</strong> 元</small>
    </h3>
</header>
<div class="panel">
  <div class="panel-body">
    <form action="" name="myform" class="form-group" method="get">
      <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
        <span class="input-group-addon">会员</span>
        <input type="text" class="form-control" name="member" value="{{$r['member'] or ''}}" placeholder="用户名/手机号"></div>
      <div class="input-group" style="width: 120px;float: left;margin-right: 10px;">
        <span class="input-group-addon">金额</span>
        <input type="text" class="form-control" name="order_money" value="{{$r['order_money'] or ''}}" placeholder="订单金额"></div>
      <div class="input-group" style="width: 120px;float: left;margin-right: 10px;">
        <span class="input-group-addon">计划ID</span>
        <input type="text" class="form-control" name="order_no" value="{{$r['order_no'] or ''}}" placeholder="计划ID"></div>
      <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
        <span class="input-group-addon">订单号</span>
        <input type="text" class="form-control" name="order_platform_no" value="{{$r['order_platform_no'] or ''}}" placeholder="订单号"></div>
      <div class="input-group" style="width: 120px;float: left;margin-right: 10px;">
        <span class="input-group-addon">状态</span>
        <select name="order_status" class="form-control order_status">
          <option value="">全部</option>
          <option value="1">待执行</option>
          <option value="2">成功</option>
          <option value="3">取消</option>
          <option value="-1">失败</option>
          <option value="4">待查证</option>
          <option value="5">已处理</option></select>
      </div>
      <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
        <span class="input-group-addon">订单类型</span>
        <select name="order_type" class="form-control order_type">
          <option value="">全部</option>
          <option value="1">消费</option>
          <option value="2">还款</option></select>
      </div>
      <div class="input-group" style="width: 200px;float: left;margin-right: 10px;">
          <span class="input-group-btn"><button class="btn btn-default" type="button">通道</button></span>
        <select name="passageway_id" class="form-control passageway_id">
          <option value="">全部</option>
          @foreach($passageway as $v)
            <option class="" value="{{$v['passageway_id']}}">{{$v['passageway_name']}}</option>
          @endforeach
        </select>
      </div>
      <div class="input-group" style="width: 360px;float: left;margin-right: 10px;">
        <span class="input-group-addon">执行时间</span>
        <input type="date" name="beginTime" style="width: 140px" class="form-control" value="{{$r['beginTime'] or ''}}" />
        <input type="date" name="endTime" style="width: 140px" class="form-control" value="{{$r['endTime'] or ''}}" /></div>
      <div class="input-group" style="width: 360px;float: left;margin-right: 10px;">
        <span class="input-group-addon">更新时间</span>
        <input type="date" name="updatebeginTime" style="width: 140px" class="form-control" value="{{$r['updatebeginTime'] or ''}}" />
        <input type="date" name="updateendTime" style="width: 140px" class="form-control" value="{{$r['updateendTime'] or ''}}" /></div>
      <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
        <span class="input-group-addon">失败原因</span>
        <input type="text" class="form-control" name="back_statusDesc" value="{{$r['back_statusDesc'] or ''}}" placeholder="失败原因"></div>
      <button class="btn btn-primary" type="submit">搜索</button>
      <input type="hidden" name="is_export" class="is_export" value="0">
      <button class="btn btn-primary export" type="submit">导出</button></form>
  </div>
</div>

  <div class="items items-hover">
      <!-- HTML 代码 -->
        <table class="table datatable">
           <thead>
            <tr>
              <th>ID</th>
              <th>计划ID</th>
        <th>姓名</th>
        <th>通道</th>
        <th>银行</th>
        <th>订单类型</th>
        <th>订单金额</th>
        <th>到账金额</th>
        <th>订单手续费</th>
        <th>成本手续费</th>
        <th>结算</th>
        <th>分润</th>
        <th>盈利</th>
        <th>订单状态</th>
        <th>订单执行时间</th>
        <th>订单描述</th>
        <th>查看</th>
        <th>操作</th>
        </tr>
    </thead>
     <tbody>
    @foreach($list as $key => $value)
     <tr style="">
      <td>{{$value['order_id']}}</td>
      <td><a type="button" href="/index/Plan/index?generation_id={{$value['order_no']}}">{{$value['order_no']}}</a></td>
       <td>{{$value['member_nick']}}</td>
      <td>{{$value['passageway_name']}}</td>
      <td>{{$value['card_bankname']}}</td>
       <td>@if($value['order_type'] == 1)
        <em style="color:#00FF00;"> 消费</em>
         @else
          <em style="color:#00FFFF;">还款</em>
        @endif </td>
       <td>{{$value['order_money']}}</td>
       <td>{{$value['order_real_get']}}</td>
       <td>{{$value['order_pound']}}({{$value['order_money']}}*{{$value['user_rate']}}+{{$value['user_fix']}})</td>
       <td>{{$value['order_passageway_fee']}}({{$value['order_money']}}*{{$value['passageway_rate']}}+{{$value['passageway_fix']}})</td>
       <!-- 结算 -->
       <td>
        @if($value->order_status==2)
       {{$value['order_platform_fee']}}
        @else
        0
        @endif
        </td>
       <td>
        @if($value['order_fenrun']!=0)
        <a href="/index/Financial/fenrun?commission_from={{$value['order_id']}}&commission_type=3" target="_blank" >
        {{$value['order_fenrun']}}</a>
        @else
        0
        @endif
       </td>
       <td>
        @if($value->order_status==2)
       {{$value['order_platform_fee']-$value['order_fenrun']}}
        @else
        0
        @endif
        </td>
       <td>@if($value['order_status'] == 1)<em style="color:#FF9900;">  待执行 </em>@elseif($value['order_status'] == 2)<em style="color:#33FF33;"> 成功</em> @elseif($value['order_status'] == 3)<em style="color:#FF00FF;"> 取消</em> @elseif($value['order_status'] ==4) <em style="color:#00FFFF;">待查证</em>@elseif($value['order_status'] ==5) <em style="color:#00FFFF;">已执行</em>  @else <em style="color:red;">失败 </em>@endif </td>
       <td>{{$value['order_time']}}</td>
       @if($value['order_status'] == -1)
       <td>{{$value['back_statusDesc']}}</td>
       @else
       <td>@if($value['order_status'] == 1)<em style="color:#FF9900;">  待执行 </em>@elseif($value['order_status'] == 2)<em style="color:#33FF33;"> 成功</em> @elseif($value['order_status'] == 3)<em style="color:#FF00FF;"> 取消</em> @elseif($value['order_status'] ==4) <em style="color:#00FFFF;">待查证</em> @elseif($value['order_status'] ==5) <em style="color:#00FFFF;">已执行</em>@else <em style="color:red;">失败 </em>@endif</td>
       @endif
       <td>
        <a type="button" data-toggle="modal" data-remote="/index/Plan/info?order_id={{$value['order_id']}}" href="javascript:;">详细信息</a>
          | <a type="button" href="/index/Plan/detail?order_no={{$value['order_no']}}">该计划订单</a>
       </td>
       <td>
        @if($value['order_status'] == 3)
          <!-- <a class="remove" href="#" data-url="{{url('/index/Plan/order_status/status/1/id/'.$value['order_id'])}}"><i class="icon-pencil"></i> 继续执行 </a> -->
          @endif
          @if($value['order_status'] == 1)
          | <a class="remove" href="#" data-url="{{url('/api/Membernet/action_single_plan/id/'.$value['order_id'])}}">立即执行 </a>
          | <a class="remove" href="#" data-url="{{url('/index/Plan/order_status/status/3/id/'.$value['order_id'])}}">取消执行 </a>
          @endif
          @if($value['order_status'] == -1)
          | <a class="remove" href="#" data-url="{{url('/api/Membernet/action_single_plan/id/'.$value['order_id'].'/is_admin/1')}}">重新执行 </a>
            @if($value['order_type =']= 2)
            | <a class="modify_money" href="#" data-url="{{url('/api/Membernet/update_back_money/id/'.$value['order_id'])}}">修改还款金额 </a>
            @endif
          @endif
          | <a class="remove1" data-toggle="modal" data-remote="{{url('/index/Plan/edit_status/id/'.$value['order_id'])}}" href="#">更改状态 </a>
          @if($value['order_retry_count'] > 2)
          | <a class="remove"  data-url="{{url('/api/Membernet/edit_pay_count/id/'.$value['order_id'])}}" href="#">重置执行次数</a>
          @endif

       </td>
     </tr>
     @endforeach
      </tbody>
  </table>
  </div>
  {!!$list->render()!!}
</div>
</section>
<script>

$(document).ready(function() {
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.plan_detail').addClass('active');
    $('.menu .nav li.plan-manager').addClass('show');
    $('.order_status').val({{$r['order_status'] or ''}})
    $('.order_type').val({{$r['order_type'] or ''}})
    $('.passageway_id').val({{$r['passageway_id'] or ''}});
    $('.export').click(function() {
        $(".is_export").val(1);
        setTimeout(function() {
            $(".is_export").val(0);
        },
        100);
    })

});
$(".parent li a").click(function() {
    $("input[name='article_parent']").val($(this).attr('data-id'));
    $("input[name='article_category']").val(0);
    $("#myform").submit();
});
$(".son li a").click(function() {
    $("input[name='article_category']").val($(this).attr('data-id'));
    $("#myform").submit();
});
$(".remove").click(function() {
    var url = $(this).attr('data-url');
    var ths = $(this);
    bootbox.confirm({
        title: "计划列表详情",
        message: "是否执行此操作",
        buttons: {
            cancel: {
                label: '<i class="fa fa-times"></i> 点错'
            },
            confirm: {
                label: '<i class="fa fa-check"></i> 确定'
            }
        },
        callback: function(result) {
            if (result) $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    ths.parent().html('<i class="icon icon-spin icon-spinner-indicator" style="z-index: 999;"></i>');
                },
                success: function(data) {
                    data = JSON.parse(data);
                    alert(data.msg);
                    window.location.reload(true);
                }
            })
        }
    });
})
$('.modify_money').click(function() {
    var ths = $(this);
    money = prompt("请输入修改的金额");
    if (!money || money < 0) {
        alert('金额必须大于0');
        return;
    }
    var url = $(this).attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        'data': {
            'money': money
        },
        beforeSend: function() {
            ths.parent().html('<i class="icon icon-spin icon-spinner-indicator" style="z-index: 999;"></i>');
        },
        success: function(data) {
            data = JSON.parse(data);
            if (data.code == 200) {
                alert(data.msg);
                window.location.reload(true);
            } else {
                alert(data.msg);
            }
        }
    })
})
</script>
@endsection
