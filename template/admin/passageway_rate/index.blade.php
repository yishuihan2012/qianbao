@extends('admin/layout/layout_main')
@section('title','通道列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
 </style>
 <blockquote> 费率编码表管理</blockquote>
 <section>
 <hr/>

 <table class="table datatable">
      <thead>
           <tr>
                 <!-- 以下两列左侧固定 -->
                 <th>#</th>
                 <th>费率</th>
                 <th>固定附加费用</th>
                 <th>荣邦平台费率套餐代码(又叫邀请码)</th>
                 <th>通道名称</th>
                 <th>操作</th>
           </tr>
      </thead>
      <tbody>
        @foreach($list as $key => $value)
          <tr>
            <td>{{$value['rate_id']}}</td>
            <td>{{$value['rate_rate']}}</td>
            <td>{{$value['rate_charge']}}</td>
            <td>{{$value['rate_code']}}</td>
            <td>{{$value['passageway_name']}}</td>
            <td><a href="{{url('/index/passageway_rate/remove/id/'.$value['rate_id'])}}"><i class="icon-pencil"></i> 删除</a></td>
          </tr>
        @endforeach
          
      </tbody>
 </table>
{!! $list->render() !!}
 </section>
 <script type="text/javascript">
 $(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.passageway_rate').addClass('active');
    	 $('.menu .nav li.passageway-manager').addClass('show');
});
</script>
@endsection
