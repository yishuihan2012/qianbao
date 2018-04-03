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
     @if(isset($current_member))
      <h3>
        当前为  <strong class="text-danger">{{$current_member->member_nick}}</strong> 直接推荐会员列表
      </h3>
     @endif
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
    <div class="input-group" style="width: 360px;float: left;margin-right: 10px;">
      <span class="input-group-addon">注册时间</span>
      <input type="date" name="beginTime" style="width: 140px" class="form-control" value="{{$r['beginTime'] or ''}}" />
      <input type="date" name="endTime" style="width: 140px" class="form-control" value="{{$r['endTime'] or ''}}" /></div>

	<button class="btn btn-primary" type="submit">搜索</button>
  <input type="hidden" name="is_export" class="is_export" value="0">
  <button class="btn btn-primary export" type="submit">导出</button>
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
                              <!-- <li><a class="son" data-width='1440' data-toggle="modal" data-remote="{{url('/index/member/child',['memberId'=>$val->member_id])}}" href="#">下级信息</a></li> -->
                              <li><a  href="/index/member/children?member_id={{$val->member_id}}">下级列表</a></li>
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
 <script type="text/javascript">
 $(document).ready(function(){
      $('table.datatable').datatable({sortable: true});
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.member').addClass('active');
    	 $('.menu .nav li.member-manager').addClass('show');
})
$('.export').click(function(){
  $(".is_export").val(1);
  setTimeout(function(){
    $(".is_export").val(0);
  },100);
})
 </script>
 @endsection
