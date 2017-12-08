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
          <label for="login_email">联系方式</label>
          <input id="login_email" name="login_email" type="email" class="form-control" placeholder="联系方式" value="{{ $data['login_email'] or '' }}">
      </div>
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
      <div class="form-group">
          <label for="login_city">所属城市</label>
          <input id="login_city" name="login_city" type="email" class="form-control" placeholder="所在城市">
      </div>
      <div class="form-group">
          <label for="login_name">账户添加时间</label>
          <input class="form-control" type="text" placeholder="账户添加时间" disabled value="{{ $data['adminster_add_time'] or '' }}">
      </div>
  <button type="submit" class="btn btn-primary">{{ $information['action_text'] }}</button>
    </div>
    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
      <h4>操作记录</h4>
      <hr>
      <!-- HTML 代码 -->
      <table class="table datatable">
        <thead>
          <tr>
            <th class="flex-col">#</th>
            <th class="flex-col">时间</th>
            <!-- 以下三列中间可滚动 -->
            <th class="flex-col">操作</th>
            <th class="flex-col">IP</th>
            <th class="flex-col">浏览器信息</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>#</td><td>2017-10-10 11:15:18</td><td>账户信息添加</td><td>192.168.1.1</td><td>火狐 chrome</td></tr>
          <tr><td>#</td><td>2017-10-10 11:15:18</td><td>账户信息需改</td><td>192.168.1.1</td><td>火狐 chrome</td></tr>
          <tr><td>#</td><td>2017-10-10 11:15:18</td><td>用户红包审核</td><td>192.168.1.1</td><td>火狐 chrome</td></tr>
          <tr><td>#</td><td>2017-10-10 11:15:18</td><td>用户信息删除</td><td>192.168.1.1</td><td>火狐 chrome</td></tr>
          <tr><td>#</td><td>2017-10-10 11:15:18</td><td>项目信息发布</td><td>192.168.1.1</td><td>火狐 chrome</td></tr>
          <tr><td>#</td><td>2017-10-10 11:15:18</td><td>项目信息修改</td><td>192.168.1.1</td><td>火狐 chrome</td></tr>
          <tr><td>#</td><td>2017-10-10 11:15:18</td><td>测试西悉尼</td><td>192.168.1.1</td><td>火狐 chrome</td></tr>
          <tr><td>#</td><td>2017-10-10 11:15:18</td><td>账户信息添加</td><td>192.168.1.1</td><td>火狐 chrome</td></tr>
          <tr><td>#</td><td>2017-10-10 11:15:18</td><td>账户信息添加</td><td>192.168.1.1</td><td>火狐 chrome</td></tr>
        </tbody>
        <tfoot>
          <tr>
            <td comspan="3"></td>
            <td comspan="2"><a href="#">更多记录</a></td>
          </tr>
        </tfoot>
      </table>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <h4>其他信息</h4>
      <hr>
      <div class="panel">
          <div class="panel-heading">
            主要IP
          </div>
          <div class="panel-body">
            127.0.0.1
          </div>
        </div>
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
