@extends('admin/layout/layout_main')
@section('title','控制面板~')
@section('wrapper')
<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i>控制面板 <small>详情</small></h3>
  </header>
  <div class="items items-hover">
    <div class="z-card">
      当前用户总数:
      <span>{{$data['count']}}</span>
    </div>
    <div class="z-card">
      今日用户增量:
      <span>{{$data['Todaycount']}}</span>
    </div>
  </div>
  <div class="items items-hover">
    <div class="z-card">
      当前套现总量:
      <span>{{$data['CashOrdercount']}}</span>
    </div>
    <div class="z-card">
      当前还款总量:
      <span>{{$GenerationOrdercount}}</span>
    </div>
    
  </div>
  <div class="items items-hover">
    @foreach($membergrouplist as $key => $value)
    <div class="z-card">
      {{$value['group_name']}}总量:
      <span>{{$value['membergroupcount']}}</span>
    </div>
    @endforeach
  </div>
</div>
</div>
</section>
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.dashboard').addClass('active');
});
</script>
@endsection
<style type="text/css">
  .z-card{
    width: 200px;
    height: 80px;
    border-radius: 10px;
    background-color: #3280fc;
    text-align: center;
    line-height: 70px;
    font-size: 1.6rem;
    color: white;
    /*float: left;*/
    margin: 15px;
    display: inline-block;
  }
  .z-card span{
    font-size: 2rem;
    font-weight: 800;
  }
</style>