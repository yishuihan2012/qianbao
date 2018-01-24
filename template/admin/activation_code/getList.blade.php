@extends('admin/layout/layout_main')
@section('title','激活码管理')  
@section('wrapper')
 <header>
    <h3><i class="icon-list-ul"></i> 激活码列表 <small>共 <strong class="text-danger">{{$count}}</strong> 条</small></h3>
  </header>
<table class="table table-striped table-hover">
    <thead>
      <tr>
          <th>###</th>
          <th>使用状况</th>
          <th>激活码KEY</th>
          <th>激活码PWD</th>
          <th>添加时间</th>
          <th>操作</th>
          
      </tr>
  </thead>
    <tbody>
    @foreach($list as $key=>$activation_code)
        <tr>
            <td>{{ $activation_code->activation_code_id  }}</td>
            <td>{{ $activation_code->activation_states==1?"未使用":"已使用" }}</td>
            <td>{{ $activation_code->activation_code_key  }}</td>
            <td>{{ $activation_code->activation_code_pwd  }}</td>
            <td>{{ $activation_code->activation_add_time  }}</td>
            <td>
                <div class="btn-group">
                <a href="javascript:;" class="btn btn-sm">相关操作</a>
                <div class="btn-group">
                    <button type="button" class="btn dropdown-toggle btn-sm" data-toggle="dropdown"><span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{ url('ActivationCode/delete','id='.$activation_code->activation_code_id) }}" data="" explain="">删除</a></li>
                        
                    </ul>
                </div>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="8">
            {!! $list->render()  !!}
        </td>
    </tr>
    </tfoot>
</table>
<script type="text/javascript">
    $(document).ready(function(){
        $('.menu .nav .active').removeClass('active');
        $('.menu .nav li.activation_code').addClass('active');
        $('.menu .nav li.member-activation-code').addClass('show');
    });
</script> 
 @endsection
