@extends('admin/layout/layout_main')
@section('title','会员列表管理~')  
@section('wrapper')
<blockquote>
	<form action="" method="post">
  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
    <span class="input-group-addon">手机号</span>
    <input type="text" class="form-control" name="member_mobile" value="{{$r['member_mobile']}}" placeholder="手机号">
  </div>
  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
    <span class="input-group-addon">昵称</span>
    <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick']}}" placeholder="昵称">
  </div>
  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
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

<div class="input-group" style="width: 200px;float: left; margin-right: 10px;">
    <input type="text" class="form-control date-picker" id="dateTimeRange" placeholder="注册时间查询" />
    <input type="hidden" name="beginTime" id="beginTime" value="" />
    <input type="hidden" name="endTime" id="endTime" value="" />
    <z class='clearTime'>X</z>
</div>

	<button class="btn btn-primary" type="submit">搜索</button>
</form>
</blockquote>
 <hr/>

 <div class="row">
      @foreach ($member_list as $list)
      <div class="col-sm-2">
           <a class="card" href="###" class="btn btn-default btn-sm">
              <img src="{{$list->member_image}}" data-toggle="lightbox"  class="img-circle" style="max-width: 30%">
              <div class="card-heading"><strong>{{$list->member_nick}}<br/>({{$list->member_mobile}}){{state_preg($list->member_cert,1,'实名')}}</strong></div>


              <div class="card-actions">
                <span style="font-size: 12px;">会员等级:</span> <code>{{$list->group_name}}</code>
              </div>


              <div class="card-actions">
                <span style="font-size: 12px;">登录状态:</span> <code>@if($list->login_state==1)正常@else封停@endif</code>
                <div class="pull-right text-gray"><span style="font-size: 12px;">注册时间:</span> <code>{{$list->member_creat_time}}</code></div>
              </div>
              <div class="text-gray">
                <button class="btn btn-sm" data-toggle="modal" data-remote="{{url('/index/member/info/id/'.$list->member_id)}}"  type="button">查看详情</button>
                <button class="btn btn-sm" type="button">升级会员</button>
              </div>

           </a>
      </div>
      @endforeach
</div>

{!! $member_list->render() !!}
 <script type="text/javascript">
 $(document).ready(function(){
      $('table.datatable').datatable({sortable: true});
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.member').addClass('active');
    	 $('.menu .nav li.member-manager').addClass('show');

    	 $(".remove").click(function(){
    	 	 var url=$(this).attr('data-url');
		 bootbox.confirm({
		    title: "删除文章确认",
		    message: "确定删除这篇文章吗? 删除后不可恢复!",
		    buttons: {
		        cancel: {label: '<i class="fa fa-times"></i> 点错了'},
		        confirm: {label: '<i class="fa fa-check"></i> 确定'}
		    },
		    callback: function (result) {
		    	 if(result)
		    	 	window.location.href=url;
		    }
		 });
    	 })

$('#dateTimeRange').daterangepicker({
        applyClass : 'btn-sm btn-success',
        cancelClass : 'btn-sm btn-default',
        locale: {
            applyLabel: '确认',
            cancelLabel: '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            customRangeLabel : '自定义',
            firstDay : 1
        },
        ranges : {
            //'最近1小时': [moment().subtract('hours',1), moment()],
            '今日': [moment().startOf('day'), moment()],
            '昨日': [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],
            '最近7日': [moment().subtract('days', 6), moment()],
            '最近30日': [moment().subtract('days', 29), moment()],
            '本月': [moment().startOf("month"),moment().endOf("month")],
            '上个月': [moment().subtract(1,"month").startOf("month"),moment().subtract(1,"month").endOf("month")]
        },
        opens : 'left',    // 日期选择框的弹出位置
        separator : ' 至 ',
        showWeekNumbers : true,     // 是否显示第几周

 
        //timePicker: true,
        //timePickerIncrement : 10, // 时间的增量，单位为分钟
        //timePicker12Hour : false, // 是否使用12小时制来显示时间
 
         
        //maxDate : moment(),           // 最大时间
        format: 'YYYY-MM-DD'
 
    }, function(start, end, label) { // 格式化日期显示框
        $('#beginTime').val(start.format('YYYY-MM-DD'));
        $('#endTime').val(end.format('YYYY-MM-DD'));
    });
begin_end_time_clear();
$('.clearTime').click(begin_end_time_clear);
  //清除时间
    function begin_end_time_clear() {
        $('#dateTimeRange').val('');
        $('#beginTime').val('');
        $('#endTime').val('');
    }
 });
 </script>
 <style type="text/css">
   .clearTime{
    position: absolute;
    right: 5px;
    top: 5px;
    z-index: 99;
    border: 1px solid;
    color: red;
    font-size: .6rem;
    padding: 0 5px;
   }
 </style>
 @endsection
