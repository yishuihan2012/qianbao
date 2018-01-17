@extends('admin/layout/layout_main')
@section('title','通道列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
 </style>
 <blockquote> 通道列表管理</blockquote>
 <section>
 <hr/>

 <table class="table datatable">
      <thead>
           <tr>
                 <!-- 以下两列左侧固定 -->
                 <th>#</th>
                 <th>通道名</th>
                 <th>真实通道</th>
                 <!-- 以下三列中间可滚动 -->
                 <th class="flex-col">机构号</th> 
                 <th class="flex-col">机构Key</th>
                 <!-- 以下列右侧固定 -->
                 <th>通道状态</th>
                 <th>是否入网</th>
                 <th>添加时间</th>
                 <th>其他操作</th>
           </tr>
      </thead>
      <tbody>
           @foreach($passageway_list as $list)
           <tr>
                 <td>{{$list->passageway_id}}</td>
                 <td>{{$list->passageway_name}}</td>
                 <td>{{$list->passageway_true_name}}</td>
                 <td>{{$list->passageway_mech}}</td>
                 <td>{{$list->passageway_key}}</td>
                 <td>@if($list->passageway_state==1) 启用 @else 禁用 @endif</td>
                 <td>@if($list->passageway_status==1) 是 @else 否 @endif</td>
                 <td>{{$list->passageway_add_time}}</td>
                 <td>
                 @if($admin['adminster_group_id']==5)
                      <a class="btn btn-sm" href="{{url('/index/passageway/passageway_details','id='.$list['passageway_id'])}}">交易详情</a> 
                      @else
                      <div class="btn-group"><a  data-remote="{{url('/index/passageway/rate','id='.$list['passageway_id'])}}" data-toggle="modal" data-size="lg" href="#" class="btn btn-sm">税率调整</a>
                           <div class="btn-group">
                                 <button type="button" class="btn dropdown-toggle btn-sm" data-toggle="dropdown"><span class="caret"></span></button>
                                 <ul class="dropdown-menu" role="menu">
                                  <li><a  data-remote="{{url('/index/passageway/edit','id='.$list['passageway_id'])}}" data-toggle="modal" data-size="lg" href="#">修改</a></li>
                                   <li><a  data-remote="{{url('/index/passageway/add_credit_card','id='.$list['passageway_id'])}}" data-toggle="modal" data-size="lg" href="#">添加信用卡</a></li>
                                   <li><a  href="{{url('/index/passageway/list_credit_card','id='.$list['passageway_id'])}}" >查看信用卡列表</a></li>
                                      <li><a  data-remote="{{url('/index/passageway/cashout','id='.$list['passageway_id'])}}" data-toggle="modal" data-size="lg" href="#">提现设置</a></li>
                                      <li><a  data-remote="{{url('/index/passageway/also','id='.$list['passageway_id'])}}" data-toggle="modal" data-size="lg" href="#">代还设置</a> </li>
                                      <li><a href="{{url('/index/passageway/passageway_details','id='.$list['passageway_id'])}}">交易详情</a> </li>
                                 </ul>
                           </div>
                      </div>
                      @endif
                 </td>
           </tr>
           @endforeach
      </tbody>
 </table>
 {!! $passageway_list->render() !!}
 </section>
 <script type="text/javascript">
 $(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.passageway').addClass('active');
    	 $('.menu .nav li.passageway-manager').addClass('show');
});
</script>
@endsection
