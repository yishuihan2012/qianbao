@extends('admin/layout/layout_main')
@section('title',$passageway->passageway_name)
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
 </style>
 <blockquote> 
  {{$passageway->passageway_name}} 
  累计交易 <z>{{$passageway->sum}}</z> 
  累计手续费 <z>{{$passageway->charge}}</z> 
  累计分润<z>{{$passageway->fenrun}}</z>
  累计利润<z>{{$passageway->profit}}</z>
 </blockquote>
 <blockquote> 
  <form action="" method="post">
  <button class="btn btn-primary">搜索</button>
<div class="input-group date form-datetime" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
  <input class="form-control" name="begin" size="16" type="text" value="{{$r['begin']}}" readonly="" placeholder="选择开始时间">
  <span class="input-group-addon"><span class="icon-remove"></span></span>
  <span class="input-group-addon"><span class="icon-th"></span></span>
</div>
<div class="input-group date form-datetime" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
  <input class="form-control" name="end" size="16" type="text" value="{{$r['end']}}" readonly="" placeholder="选择结束时间">
  <span class="input-group-addon"><span class="icon-remove"></span></span>
  <span class="input-group-addon"><span class="icon-th"></span></span>
</div>
  <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
     <span class="input-group-addon">订单状态</span>
  <select name="order_state" class="form-control">
    <option value="" {{$r['order_state'] ? "" : "selected"}}>全部</option>
    @foreach($order_state as $k=>$v)
      <option value="{{$k}}" {{$r['order_state']==$k ? "selected" : ""}}>{{$v}}</option>
    @endforeach
  </select>
  </div>
  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
    <span class="input-group-addon">手机号</span>
    <input type="text" class="form-control" name="member_mobile" value="{{$r['member_mobile']}}" placeholder="手机号">
  </div>
  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
    <span class="input-group-addon">用户名</span>
    <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick']}}" placeholder="用户名">
  </div>
</form>
</blockquote>
 <section>

 <table class="table datatable">
      <thead>
           <tr>
                 <!-- 以下两列左侧固定 -->
                 <th>#</th>
                 <th>订单编号</th>
                 <th>订单用户</th>
                 <th>交易金额</th>
                 <th>手续费</th> 
                 <th>交易状态</th>
                 <th>交易时间</th>
                 <th>其他操作</th>
           </tr>
      </thead>
      <tbody>
           @foreach($list as $v)
           <tr>
             <td>{{$v['order_id']}}</td>
             <td>{{$v['order_no']}}</td>
             <td>{{$v['member_nick']}}</td>
             <td>{{$v['order_money']}}</td>
             <td>@if (isset($v['order_pound'])){{$v['order_pound']}} @else{{$v['order_charge']}} @endif</td>
             <td>@if (isset($v['order_status'])){{$v['order_status']}} @else{{$v['order_state']}} @endif</td>
             <td>@if (isset($v['order_time'])){{$v['order_time']}} @else{{$v['order_update_time']}} @endif</td>
             <td>
                <div class="btn-group"><a  data-remote="/index/passageway/passageway_details_info?id={{$v['order_id']}}&type={{$passageway->passageway_also==1 ? 1 : 3}}" data-toggle="modal" data-size="md" href="#" class="btn btn-sm">查看</a>
                </div>
             </td>
           </tr>
           @endforeach
      </tbody>
 </table>
{!! $list->render() !!}
 </section>
 <script type="text/javascript">
 $(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.passageway').addClass('active');
    	 $('.menu .nav li.passageway-manager').addClass('show');
$.fn.datetimepicker.dates['zh'] = {  
                days:       ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六","星期日"],  
                daysShort:  ["日", "一", "二", "三", "四", "五", "六","日"],  
                daysMin:    ["日", "一", "二", "三", "四", "五", "六","日"],  
                months:     ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月","十二月"],  
                monthsShort:  ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],  
                meridiem:    ["上午", "下午"],  
                //suffix:      ["st", "nd", "rd", "th"],  
                today:       "今天"  
        }; 
 $(".form-datetime").datetimepicker({
    language:  'zh',  
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    forceParse: 0,
    showMeridian: 1,
    format: "yyyy-mm-dd hh:ii"
});
  });
</script>
<style type="text/css">
  blockquote z{
    font-size: 1.8rem;
    color: red;
    font-weight: 800;
  }
  .date{
    width: 200px;
    float: left;
    margin-right: 10px;
  }
</style>
@endsection
