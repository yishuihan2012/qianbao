@extends('admin/layout/layout_main')
@section('title','会赚钱的都在这~')
@section('wrapper')
<!-- HTML 代码 -->
<form class="form-group" action="{{ $information['action_link'] }}" method="post">
    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
      <h4>基本信息</h4>
      <hr>
      <div class="form-group">
          <label for="login_name">登录名称</label>
          <input id="login_name" name="login_name" type="text" class="form-control" placeholder="登录名称" value="{{ $data['login_name'] or '' }}">
          <input type="text" style="display:none" name="login_id" value="{{ $data['login_id'] or '' }}">
      </div>
      <div class="form-group">
          <label for="login_passwd">登录密码</label>
          <input id="login_passwd" name="login_passwd" type="password" class="form-control" placeholder="登录密码" value="{{ $data['login_passwd'] or '' }}">
      </div>
      <div class="form-group">
          <label for="login_email">联系邮箱</label>
          <input id="login_email" name="login_email" type="email" class="form-control" placeholder="联系邮箱" value="{{ $data['login_email'] or '' }}">
      </div>
    @if($admin['adminster_group_id']!=5)
      <div class="form-group">
          <label for="login_group">用户组信息</label>
          <select class="form-control" id="login_group" name="login_group" multiple>
            @foreach($auth_groups as $key=>$val)
              @if($val['id'] == $data['login_group'])
                  <option value="{{ $val['id'] }}" selected="selected">{{ $val['title'] }}</option>
              @else
                  <option value="{{ $val['id'] }}">{{ $val['title'] }}</option>
              @endif
            @endforeach
          </select>
      </div>
    @endif
      <div class="form-group">
          <label for="user_id">运营商绑定用户-非运营商用户组忽略此项</label>
          <select class="form-control" name="adminster_user_id">
            <option value="">无</option>
            @foreach($users as $v)
              <option value="{{ $v['member_id'] }}" {{$v['member_id']==$data['adminster_user_id'] ? 'selected' : ''}}>{{$v['member_nick']}} {{$v['member_mobile']}}</option>
            @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="login_name">账户添加时间</label>
          <input class="form-control" type="text" placeholder="账户添加时间" disabled value="{{ $data['adminster_add_time'] or '' }}">
      </div>
  <button type="submit" class="btn btn-primary">{{ $information['action_text'] }}</button>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $('.menu .nav .active').removeClass('active');
        $('.menu .nav li.adminster').addClass('active');
        $('.menu .nav li.adminster-manager').addClass('show');
        $('table.datatable').datatable();
    });
</script>
@endsection
