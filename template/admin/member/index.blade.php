@extends('admin/layout/layout_main')
@section('title','会员列表管理~')  
@section('wrapper')
 <style>
  .content td{vertical-align: middle;}
 </style>
 <header>
    <h3><i class="icon-list-ul"></i> 用户总人数 <small>共 <strong class="text-danger">{{$count}}</strong> 人</small>
      <i class="icon-list-ul"></i> 未实名总人数 <small>共 <strong class="text-danger">{{$wei_count}}</strong> 人</small>
      <i class="icon-list-ul"></i> 已实名总人数 <small>共 <strong class="text-danger">{{$yi_count}}</strong> 人</small>
    </h3>
     <h3>
    @foreach($group_user as $key => $val)
      <i class="icon-list-ul"></i> {{$val['group_name']}} <small>共 <strong class="text-danger">{{$val['count']}}</strong> 人</small>
    @endforeach
     </h3>
  </header>
<blockquote>
   
	<form action="" method="post">
    <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
    <span class="input-group-addon">用户名</span>
    <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick']}}" placeholder="用户名">
  </div>
  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
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

<div class="input-group" style="width: 200px;float: left; margin-right: 10px;">
    <input type="text" class="form-control date-picker" id="dateTimeRange" placeholder="注册时间" />
    <input type="hidden" name="beginTime" id="beginTime" value="" />
    <input type="hidden" name="endTime" id="endTime" value="" />
    <z class='clearTime'>X</z>
</div>

	<button class="btn btn-primary" type="submit">搜索</button>
</form>
</blockquote>
 <hr/>
<div class="items items-hover">
     
<table class="table table-striped table-hover">
    <thead>
      <tr>
          <th>ID</th>
          <th>用户名</th>
          <th>手机号码</th>
          <th>用户头像</th>
          <th>是否实名</th>
          <th>会员等级</th>
          <th>登录状态</th>
          <th>注册时间</th>
          <th>操作</th>
          
      </tr>
  </thead>
    <tbody>
    @foreach($member_list as $val)
      <tr class="content">
          <td>{{$val->member_id}}</td>
          <td>{{$val->member_nick}}</td>
          <td>{{$val->member_mobile}}</td>
          <td><img src="{{$val->member_image}}" data-toggle="lightbox"  class="img-circle" style="max-width: 40px;"></td>
          <td>@if($val->member_cert == 2) 审核未通过 @else {{state_preg($val->member_cert,1,'实名')}} @endif</td>
          <td>{{$val->group_name}}</td>
          <td>{{$val->login_state==1 ? '正常' : '封停'}}</td>
          <td>{{$val->member_creat_time}}</td>
          <td>
<!--                      <button type="button" data-toggle="modal" data-size="lg" data-remote="{{url('/index/member/info/id/'.$val->member_id)}}" class="btn btn-sm">查看详情</button>
 -->                <div class="btn-group">
                     <button type="button" data-toggle="modal" data-size="lg" data-remote="{{url('/index/member/info/id/'.$val->member_id)}}" class="btn btn-sm">查看详情</button>
                @if(!isset($admin_group_salt) || $admin_group_salt>$val->group_salt)
                     <div class="btn-group">
                           <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                           <ul class="dropdown-menu" role="menu">
                                <li><a data-toggle="modal" data-remote="{{url('/index/member/upgrade/id/'.$val->member_id.'/member_group_id/'.$val->member_group_id)}}" href="#">升级会员</a></li>
                 @if($admin['adminster_group_id']!=5)
                                <li><a data-remote="{{url('/index/member/commiss',['memberId'=>$val->member_id])}}" href="{{url('/index/member/commiss',['memberId'=>$val->member_id])}}">分佣分润</a></li>
                               <!--  <li><a data-size='lg' data-toggle="modal" data-remote="{{url('/index/member/child',['memberId'=>$val->member_id])}}" href="#">下级信息</a></li> -->
                     @endif
                           </ul>
                     </div>
                  @endif
                </div>
          </td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
          <td colspan="7">{!! $member_list->render() !!}</td>
          
      </tr>
    </tfoot>
</table>
<!--  <div class="row">
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
</div> -->

<!-- {!! $member_list->render() !!}<em>当前共{{$count}}条</em> -->
 <script type="text/javascript">
 $(document).ready(function(){
  var start="{{(isset($beginTime))?$beginTime : ''}}";
  var end="{{(isset($endTime))?$endTime : ''}}";

      $('table.datatable').datatable({sortable: true});
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.member').addClass('active');
    	 $('.menu .nav li.member-manager').addClass('show');

$('#dateTimeRange span').html();
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
        $('#dateTimeRange').val(start+'-'+end);
    });
  setTimeout(function(){
        $('#beginTime').val(start.format('YYYY-MM-DD'));
        $('#endTime').val(end.format('YYYY-MM-DD'));
        $('#dateTimeRange').val();
        console.log(start);
      },100);
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
