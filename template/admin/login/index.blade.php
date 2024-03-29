@extends('admin/layout/layout_login')
@section('title','管理员登录~')
@section('wrapper')
 <h2>用户登录 - <small>{{$site_name}}后台管理系统</small></h2>
     <form action="{{ url('/index/Login/do_login') }}" method="post" class="login_form">
        <div class="input-group"><input type="text" class="form-control" name="login_name" placeholder="用户名" value=""></div>
        <div class="input-group"><input type="password" class="form-control" name="login_passwd" placeholder="密码" value=""></div>
        <div class="input-group"><input type="password" class="form-control" name="login_key" placeholder="用户口令" value=""></div>
        <button type="button" class="btn btn-primary">&nbsp;&nbsp;登&nbsp;&nbsp;录&nbsp;&nbsp;</button>
     </form>
<script type="text/javascript">
 $('.btn').on('click', function() {
        var message="";
        if(!$("input[name='login_key']").val())
            message="请填写正确的口令信息~";
        if(!$("input[name='login_name']").val() || !$("input[name='login_passwd']").val())
            message="请填写正确的用户名或密码信息~";
        if(message){
            new $.zui.Messager(message, {
              icon: 'bell' // 定义消息图标
            }).show();
    }else
        $('.login_form').submit();
 });
 //键盘回车事件
 $("body").keydown(function(e){
    var curKey = e.which;
    if(curKey == 13){
        $(".btn").click();
        return false;
    }
 });
</script>
@endsection
