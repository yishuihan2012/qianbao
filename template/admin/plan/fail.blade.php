 <!--dialog Title-->
@extends('admin/layout/layout_main')
@section('title','挂账列表管理~')
@section('wrapper')
<style>
   h4 > a,.pull-right > a{color:#145ccd;}
</style>

<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i> 挂账列表详情 <small>共 <strong class="text-danger">{{count($list)}}</strong> 条</small></h3>
  </header>
   <div class="panel">
    <div class="panel-body" style="display: inline-block;">
      <form action="" name="myform" class="form-group" method="get">
      <div class="input-group" style="width: 360px;float: left;margin-right: 10px;">
        <span class="input-group-addon">日期</span>
        <input type="date" name="beginTime" style="width: 140px" class="form-control" value="{{$r['beginTime'] or ''}}" />
          <button class="btn btn-primary" type="submit">搜索</button>
        </div>
    </form>
    </div>
    <div style="display: inline-block;position:relative;top:-25px;left:-100px">
  <select name="passway" class="form-control passway" style="width: 180px;display: inline-block;">
      @foreach($passway as $v)
        <option value="{{$v['passageway_true_name']}}">{{$v['passageway_name']}}</option>
      @endforeach
  </select>
    <input type="text" class="form-control mobile" name="mobile" value="{{$r['mobile'] or ''}}" placeholder="手机号" style="display: inline-block;width: 120px">
    <button class="btn btn-primary query">精确查询</button>
          <span class="res"></span>
    </div>
</div>


</form>
  <div class="items items-hover">
      <!-- HTML 代码 -->
        <table class="table datatable">
           <thead>
            <tr>
              <th>计划ID</th>
              <th>通道</th>
              <th>会员名称</th>
              <th>手机号</th>
              <th>信用卡号</th>
              <th>挂账金额(参考值)</th>
          </tr>
      </thead>
     <tbody>
    @foreach($list as $key => $value)
     <tr style="">
       <td>{{$value['order_no']}}</td>
       <td>{{$value['passageway_name']}}</td>
       <td>{{$value['member_nick']}}</td>
       <td>{{$value['member_mobile']}}</td>
       <td>{{$value['order_card']}}</td>
       <td>
        <a href="/index/plan/detail?order_id={{$value['order_id']}}">
        {{$value['sums']}}
      </a>
      </td>
     </tr>
     @endforeach
      </tbody>
  </table>

  </div>
</div>
</section>
<script>
 
  $(document).ready(function(){
    $('.query').click(function(){
      var passway = $('.passway').val();
      var mobile = $('.mobile').val();
      if(passway && mobile){
        $.post('',{passway:passway,mobile:mobile},function(res){
          console.log(res);
          $('.res').html(res);
        })
      }
    })
       $('.menu .nav .active').removeClass('active');
       $('.menu .nav li.plan_fails').addClass('active');
       $('.menu .nav li.plan-manager').addClass('show');

       $(".parent li a").click(function(){
        $("input[name='article_parent']").val($(this).attr('data-id'));
        $("input[name='article_category']").val(0);
        $("#myform").submit();
       })
       $(".son li a").click(function(){
        $("input[name='article_category']").val($(this).attr('data-id'));
        $("#myform").submit();
       })
       $(".remove").click(function(){
         var url=$(this).attr('data-url');
         var ths=$(this);
        bootbox.confirm({
        title: "计划列表详情",
        message: "是否执行此操作",
        buttons: {
            cancel: {label: '<i class="fa fa-times"></i> 点错'},
            confirm: {label: '<i class="fa fa-check"></i> 确定'}
        },
        callback: function (result) {
           if(result)
            $.ajax({
                url:url,
                type : 'POST',
                dataType : 'json',
                beforeSend:function(){
                  ths.parent().html('<i class="icon icon-spin icon-spinner-indicator" style="z-index: 999;"></i>');
                },
                success:function(data){
                  data = JSON.parse(data);
                  if(data.code==200){
                    alert(data.msg);
                    window.location.reload(true);
                  }else{
                    alert(data.msg);
                  }
                }
            })
        }
     });
       })
})
</script>
<style type="text/css">
 </style>
@endsection