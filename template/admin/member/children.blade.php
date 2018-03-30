@extends('admin/layout/layout_main')
@section('title',$current_member->member_nick.'下级用户列表')  
@section('wrapper')
 <style>
  .content td{vertical-align: middle;}
 </style>
 <header>
     @if(isset($current_member))
      <h3>
          <strong class="text-danger">{{$current_member->member_nick}}</strong> 下级会员列表
          @if(isset($parent))
            <a href="?member_id={{$parent->relation_parent_id}}" class="btn btn-primary">跳转上级</a>
          @endif
      </h3>
     @endif
     <h3>
      <i class="icon-list-ul"></i> 会员数 <small>共 <strong class="text-danger">{{$data['count']}}</strong> 人</small>
      <i class="icon-list-ul"></i> 分润 <small>共 <strong class="text-danger">{{$data['fenrun']}}</strong> 元</small>
     </h3>
  </header>
<blockquote>
   
	<form action="" method="get">
  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
    <span class="input-group-addon">用户名/手机号</span>
    <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick'] or ''}}" placeholder="用户名/手机号">
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
  <div class="input-group" style="width: 140px;float: left;margin-right: 10px;">
     <span class="input-group-addon">是否实名</span>
    <select name="member_cert" class="form-control">
        <option value="1" >是</option>
        <option value="0" {{$r['member_cert']==0 ? 'selected' : ''}}>否</option>
        <option value="all" {{$r['member_cert']=='all' ? 'selected' : ''}}>全部</option>
    </select>
  </div>

  <div class="input-group" style="width: 220px;float: left;margin-right: 10px;">
     <span class="input-group-addon">统计通道</span>
  <select name="passageway_id" class="form-control">
      <option value="" @if ($r['passageway_id']=='') selected="" @endif>全部</option>
    @foreach($passway as $v)
      <?php if($v['passageway_state']!=1)continue; ?>
      <option value="{{$v['passageway_id']}}" @if ($r['passageway_id']==$v['passageway_id']) selected @endif>({{$v['passageway_also']==1 ? '消费' : '代还'}}){{$v['passageway_name']}}</option>
    @endforeach
  </select>
  </div>

  <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
     <span class="input-group-addon">推荐关系</span>
  <select name="relation" class="form-control">
      <option value="" @if ($r['relation']=='') selected="" @endif>全部</option>
      <option value="1" {{$r['relation']==1 ? 'selected' : ''}} >直接</option>
      <option value="2" {{$r['relation']==2 ? 'selected' : ''}} >间接</option>
      <option value="3" {{$r['relation']==3 ? 'selected' : ''}} >三级</option>
  </select>
  </div>

<div class="input-group" style="width: 380px;float: left; margin-right: 10px;">
  <span class="input-group-addon">注册时间</span>
    <input type="date" name="beginTime" id="beginTime" value="{{$r['beginTime'] or ''}}" />
    <input type="date" name="endTime" id="endTime" value="{{$r['beginTime'] or ''}}" />
</div>

  <input type="hidden" name="member_id" value="{{$r['member_id']}}">
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
          <th>分润总计</th>
          <th>会员等级</th>
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
          <td>
            @if($val->sum!=0)
            <!-- 快捷支付 -->
            <a href="/index/Financial/fenrun?parent={{$current_member->member_mobile}}&child={{$val->member_mobile}}&passageway_id={{$r['passageway_id']}}"  target="_blank" >
              {{$val->sum}}
            </a>
            @else
              {{$val->sum}}
            @endif
          </td>
          <td>{{$member_group[$val->member_group_id]['group_name']}}</td>
          <td>{{$val->member_creat_time}}</td>
          <td>
<!--                      <button type="button" data-toggle="modal" data-size="lg" data-remote="{{url('/index/member/info/id/'.$val->member_id)}}" class="btn btn-sm">查看详情</button>
 -->                <div class="btn-group">
                     <button type="button" data-toggle="modal" data-size="lg" data-remote="{{url('/index/member/info/id/'.$val->member_id)}}" class="btn btn-sm">查看详情</button>
                @if(!isset($admin_group_salt) || $admin_group_salt>$val->group_salt)
                     <div class="btn-group">
                           <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                           <ul class="dropdown-menu" role="menu">
                 @if($admin['adminster_group_id']!=5)
                                <!-- <li><a data-remote="{{url('/index/financial/fenrun',['memberId'=>$val->member_id])}}" href="{{url('/index/member/commiss',['memberId'=>$val->member_id])}}">分润明细</a></li> -->
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
   
  $('.export').click(function(){
    $(".is_export").val(1);
    setTimeout(function(){
      $(".is_export").val(0);
    },100);
  })
})
 </script>
 @endsection
