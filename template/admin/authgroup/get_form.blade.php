@extends('admin/layout/layout_main')
@section('title','会赚钱的都在这~')
@section('wrapper')
<!-- HTML 代码 -->
<form class="form-group" action="{{ $information['action_link'] }}" method="post">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      <h4>基本信息</h4>
      <hr>
      <div class="form-group">
          <label for="group_name">用户组名称</label>
          <input id="group_name" name="group_name" type="text" class="form-control" placeholder="用户组名称" value="{{ $info['group_name'] }}">
          <input type="text" style="display:none" name="group_id" value="{{ $info['id'] }}">
      </div>
      <h4>组成员</h4>
      <hr>
      <table class="table table-hover table-adminster">
        <thead>
          <tr>
            <th>成员姓名</th>
            <th>相关操作</th>
          </tr>
        </thead>
        <tbody>
          @foreach($adminster as $key=>$val)
            <tr>
                <td><a href="javascript:;">{{ $val['profile']['adminster_login'] }}</a></td>
                <td><a href="javascript:;" class="remove" data-id="{{ $val['profile']['adminster_id'] }}">移除</a></td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td></td>
            <td style="text-align:right">
              @if($information['add_link']==="#")
                  <button type="button" class="btn btn-primary btn-adminster-add" disabled="">新增用户</button>
              @else
                  <button type="button" class="btn btn-primary btn-adminster-add" data-url="{{ $information['add_link'] }}" >新增用户</button>
              @endif
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="height: 500px;overflow: auto;">
      <h4>组权限管理</h4>
      <hr>
      <!-- HTML 代码 -->
      <table class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>权限</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rules as $key=>$val)
          <tr>
            <td>
              @if(!empty($info['group_rules']) && in_array($val['id'],$info['group_rules']))
                  <input type="checkbox" name="group_rules[]" checked value="{{ $val['id'] }}">
              @else
                  <input type="checkbox" name="group_rules[]" value="{{ $val['id'] }}">
              @endif
            </td>
            <td>{{ $val['title'] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <button type="submit" class="btn btn-primary">{{ $information['action_text'] }}</button>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $('.menu .nav .active').removeClass('active');
        $('.menu .nav li.auth_group').addClass('active');
        $('.menu .nav li.adminster-manager').addClass('show');
        //
        $('.btn-adminster-add').click(function(){
              var url=$(this).attr('data-url');
              var option={
                      name:"triggerModal",
                      type:"ajax",
                      url:url,
                      size:'lg'
              };
              var myModalTrigger = new $.zui.ModalTrigger(option);
              myModalTrigger.show({onHide: function() {
                  window.location.reload();
              }});
        });
        //移除操作
        $('table.table-adminster a.remove').click(function(){
          var id=$(this).attr('data-id');
          var row=$(this).parents('tr');
          $.ajax({
             type: 'POST',
             url: "{{ url('/index/auth_group/remove_adminster') }}" ,
            data: {
              'id':id,
              'group_id':"{{ $info['id'] }}"
            } ,
            success: function(data){
              if(data.code == 200){
                row.remove();
                new $.zui.Messager('账户信息已经移除~', {
                    type: 'success'
                }).show();
              }else{
                new $.zui.Messager('账户信息移除失败~', {
                    type: 'warning'
                }).show();
              }
            },
            dataType: 'json'
            });
        });
        $('input[type="checkbox"]').click(function(){
          this.checked=this.checked ? 0 : 1;
        })
    });
</script>
@endsection
