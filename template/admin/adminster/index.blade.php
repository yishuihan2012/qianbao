@extends('admin/layout/layout_main')
@section('title','会赚钱的都在这~')
@section('wrapper')
<!-- HTML 代码 -->
 <header>
    <h3><i class="icon-list-ul"></i> 订单列表 <small>共 <strong class="text-danger">{{$count}}</strong> 条</small>
     
  </header>
<div class="panel">
  	<div class="panel-body">
  <div class="row row-search">
  			<div class="col-xs-4 col-md-2">
  				<div class="input-group">
					<span class="input-group-addon">登录名称</span>
				    <input type="text" class="form-control" placeholder="登录名称" name="adminster_login" value="{{ $params['login'] }}">
				</div>
  		</div>
      <div class="col-xs-4 col-md-2">
          <div class="input-group">
          <span class="input-group-addon">联系邮箱</span>
            <input type="text" class="form-control" placeholder="联系邮箱" name="adminster_email" value="{{ $params['email'] }}">
        </div>
      </div>
<!--       <div class="col-xs-4 col-md-2">
          <div class="input-group">
          <span class="input-group-addon">城市信息</span>
          <select class="form-control" name="adminster_city">
            <option value="">不限</option>
            <option value="">山东</option>
            <option value="">北京</option>
        </select>
        </div>
      </div>
 -->    <div class="col-xs-4 col-md-2">
        <div class="input-group">
        <span class="input-group-addon">管理员状态</span>
        <select class="form-control" name="adminster_state">
            <option value="">全部</option>
            <option value="1" @if($params['state']==1) selected="" @endif>启用</option>
            <option value="-1" @if($params['state']==-1) selected="" @endif>禁用</option>
        </select>
      </div>
    </div>
    @if($admin['adminster_group_id']!=5)
    <div class="col-xs-4 col-md-2">
        <div class="input-group">
        <span class="input-group-addon">用户组</span>
        <select class="form-control" name="adminster_group">
            <option value="">不限</option>
            @foreach($groupLists as $key=>$val)
                <option value="{{ $val['id'] }}" @if($val['id']==$params['group']) selected="" @endif>{{ $val['title'] }}</option>
            @endforeach
        </select>
      </div>
    </div>
    @endif
    <div class="col-xs-4 col-md-2 text-center">
      <button class="btn btn-primary btn-search" type="button">立即搜索</button>
    </div>
  </div>
</div>
</div>
<!-- <div class="row">
  <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
    <a class="btn btn-primary" href="{{ url('/index/adminster/add') }}">新增管理员<i class="icon icon-plus"></i></a>
  </div>
</div> -->
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <!-- 以下两列左侧固定 -->
      <th>#</th>
      <th>登录名称</th>
      <th>联系邮箱</th>
      <!-- <th>所在城市</th> -->
      <th>状态信息</th>
      <th>最近登录时间</th>
      <th>相关操作</th>
    </tr>
  </thead>
  <tbody>
    @if(!empty($adminster_list['total']>0))
      @foreach ($adminster_list['data'] as $adminster)
        <tr>
          <td>{{ $adminster['adminster_id'] }}</td>
          <td>{{ $adminster['adminster_login'] }}</td>
          <td>{{ $adminster['adminster_email'] }}</td>
          <!-- <td>{{ $adminster['adminster_city'] }}</td> -->
          <td>{{  get_status_text($adminster['adminster_state']) }}</td>
          <td>{{ $adminster['adminster_update_time'] }}</td>
          <td>
          @if($admin['adminster_group_id']!=5)
            <div class="btn-group">
            <a href="{{ url('/index/adminster/edit','id='.$adminster['adminster_id']) }}" class="btn btn-sm">编辑</a>
            <div class="btn-group">
              <button type="button" class="btn dropdown-toggle btn-sm" data-toggle="dropdown"><span class="caret"></span></button>
              <ul class="dropdown-menu" role="menu">
                  <li><a href="{{ url('/index/adminster/change_state','id='.$adminster['adminster_id']) }}" data="" explain="账户状态{{ get_status_text($adminster['adminster_state']*-1) }}">{{ get_status_text($adminster['adminster_state']*-1) }}</a></li>
              </ul>
            </div>
        </div>
        @else
          无
        @endif
          </td>
        </tr>
      @endforeach
    @else
    <tr>
      <td colspan="7" class="text-center">
          查无数据~
      </td>
    </tr>
    @endif
  </tbody>
  <tfoot>
    <tr>
      <td colspan="7">{!! $show !!}</td>
    </tr>
  </tfoot>
</table>
<script type="text/javascript">
    $(document).ready(function(){
        $('.menu .nav .active').removeClass('active');
        $('.menu .nav li.adminster').addClass('active');
        $('.menu .nav li.adminster-manager').addClass('show');
        $('.btn-search').click(function(){
              var url="{{ url('index/adminster/index') }}";
              var adminster_login=$("input[name='adminster_login']");
              var adminster_email=$("input[name='adminster_email']");
              var adminster_state=$("select[name='adminster_state']");
              var adminster_group=$("select[name='adminster_group']")
              url+="?";
              if(adminster_login.val()!='')
                  url+="&login="+adminster_login.val();
              if(adminster_email.val()!='')
                  url+="&email="+adminster_email.val();
              if(adminster_state.val()!=0)
                  url+="&state="+adminster_state.val();
              if(adminster_group.val()!=0)
                  url+="&group="+adminster_group.val();
              window.location.href=url;
        });
    });
</script>
@endsection
