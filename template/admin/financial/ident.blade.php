@extends('admin/layout/layout_main')
@section('title','可识别银行列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
 </style>
 <blockquote> 可识别银行列表管理</blockquote>
 <section>
 <hr/>

 <table class="table datatable">
      <thead>
           <tr>
                 <!-- 以下两列左侧固定 -->
                 <th>#</th>
                 <th>银行</th>
                 <!-- 以下三列中间可滚动 -->
                 <th class="flex-col">详细信息</th> 
                 <th class="flex-col">类型</th>
                 <th class="flex-col">识别码</th>
                 <th class="flex-col">识别次数</th>
                 <!-- 以下列右侧固定 -->
                 <th>添加时间</th>
           </tr>
      </thead>
      <tbody>
           @foreach($bank_list as $list)
           <tr>
                 <td>{{$list->ident_id}}</td>
                 <td>{{$list->ident_name}}</td>
                 <td>{{$list->ident_desc}}</td>
                 <td>
                 @if($list->ident_type=='1' or $list->ident_type=='3')
                  信用卡/贷记卡
                 @elseif($list->ident_type=='2')
                 借记卡
                 @elseif($list->ident_type=='4')
                 准贷记卡
                 @elseif($list->ident_type=='5')
                 预付费卡
                 @endif
                 </td>
                 <td>{{$list->ident_code}}</td>
                 <td>{{$list->ident_count}}</td>
                 <td>{{$list->ident_add_time}}</td>
           </tr>
           @endforeach
      </tbody>
 </table>
 {!! $bank_list->render() !!}
 </section>
 <script type="text/javascript">
 $(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.bank_ident').addClass('active');
    	 $('.menu .nav li.bank-manager').addClass('show');
});
</script>
@endsection
