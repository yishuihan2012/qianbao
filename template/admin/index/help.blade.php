@extends('admin/layout/layout_main')
@section('title','控制面板~')
@section('wrapper')
<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i>使用说明 </h3>
  </header>
  <div class="items items-hover">
    <article class="article">
      <!-- 文章头部 -->
      <header>
        <!-- <h1>文章标题</h1> -->
        <!-- 文章属性列表 -->
        <div class="abstract">
          <p>自定义模块</p>
        </div>
      </header>
      <!-- 文章正文部分 -->
      <section class="content">
        
      </section>
    </article>
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
</style>