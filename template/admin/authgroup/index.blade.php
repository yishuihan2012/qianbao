@extends('admin/layout/layout_main')
@section('title','会赚钱的都在这~')
@section('wrapper')
<!-- HTML 代码 -->
<!-- <div class="row">
  <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
    <a class="btn btn-primary" href="{{ url('/index/auth_group/add') }}">新增用户组<i class="icon icon-plus"></i></a>
  </div>
</div> -->
 <header>
    <h3><i class="icon-list-ul"></i> 订单列表 <small>共 <strong class="text-danger">{{$count}}</strong> 条</small>
     
  </header>
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>#</th>
      <th>用户组名称</th>
      <th>用户组状态</th>
      <th>相关操作</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($auth_list as $auth)
      <tr>
        <td>{{ $auth['id'] }}</td>
        <td>{{ $auth['title'] }}</td>
        <td>{{ get_status_text($auth['status']) }}</td>
        <td>
          <div class="btn-group">
              <a href="{{ url('/index/auth_group/edit','id='.$auth['id']) }}" class="btn btn-sm">编辑</a>
              <div class="btn-group">
                <button type="button" class="btn dropdown-toggle btn-sm" data-toggle="dropdown"><span class="caret"></span></button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ url('/index/auth_group/change_state','id='.$auth['id']) }}" data="" explain="用户组{{ get_status_text($auth['status']*-1) }}">{{ get_status_text($auth['status']*-1) }}</a></li>
                    <li class="divider"></li>
                    <li>
                      <a href="{{ url('/index/auth_group/remove',['group_id'=>$auth['id']]) }}">删除</a>
                    </li>
                </ul>
              </div>
          </div>
        </td>
      </tr>
    @endforeach
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
        $('.menu .nav li.auth_group').addClass('active');
        $('.menu .nav li.adminster-manager').addClass('show');
        $('.auth-group-delete').click(function(){
          var link=$(this).attr('data-link');
          var option={
                  title:"转移当前用户至新的用户组",
                  name:"triggerModal",
                  type:"ajax",
                  url:link,
                  size:'lg'
          };
          var myModalTrigger = new $.zui.ModalTrigger(option);
          myModalTrigger.show();
    });
    });
</script>
@endsection
