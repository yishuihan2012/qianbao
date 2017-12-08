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
                 <!-- 以下三列中间可滚动 -->
                 <th class="flex-col">机构号</th> 
                 <th class="flex-col">机构Key</th>
                 <th class="flex-col">最小刷卡额</th>
                 <th class="flex-col">最大刷卡额</th>
                 <th class="flex-col">最低税率</th>
                 <!-- 以下列右侧固定 -->
                 <th>通道状态</th>
                 <th>添加时间</th>
                 <th>其他操作</th>
           </tr>
      </thead>
      <tbody>
           @foreach($passageway_list as $list)
           <tr>
                 <td>{{$list->passageway_id}}</td>
                 <td>{{$list->passageway_name}}</td>
                 <td>{{$list->passageway_mech}}</td>
                 <td>{{$list->passageway_key}}</td>
                 <td>{{$list->passageway_min}}</td>
                 <td>{{$list->passageway_max}}</td>
                 <td>{{$list->passageway_rate}}</td>
                 <td>{{$list->passageway_state}}</td>
                 <td>{{$list->passageway_add_time}}</td>
                 <td>
                      <div class="btn-group"><a  data-remote="{{ url('/index/passageway/rate','id='.$list['passageway_id']) }}" data-toggle="modal" data-size="lg" href="#" class="btn btn-sm">税率调整</a>
                           <div class="btn-group">
                                 <button type="button" class="btn dropdown-toggle btn-sm" data-toggle="dropdown"><span class="caret"></span></button>
                                 <ul class="dropdown-menu" role="menu">
                                      
                                 </ul>
                           </div>
                      </div>
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
