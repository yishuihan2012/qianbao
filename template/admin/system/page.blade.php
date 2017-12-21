@extends('admin/layout/layout_main')
@section('title','单页管理~')
@section('wrapper')
  <!--dialog Title-->
<link rel="stylesheet" href="/static/css/jquery-labelauty.css">
<style>
input.labelauty + label > span.labelauty-unchecked-image{background-image: url( /static/images/input-unchecked.png );}
input.labelauty + label > span.labelauty-checked-image{background-image: url( /static/images/input-checked.png );}
.dowebok {padding-left: 3px;}
.dowebok ul { list-style-type: none;}
.dowebok li { display: inline-block;}
.dowebok li { margin: -3px 20px -10px 0px}
.dowebok label{margin-bottom: 0}
input.labelauty + label { font: 12px "Microsoft Yahei";}
.input-group{padding-left: 30px!important;}
.input-group input,.input-group select{width:auto!important; height: 28px; font-size: 12px; padding: 0 5px;}
.input-group-btn{font-size: 14px;}
.form-group{margin-bottom: 10px; margin-top: 10px}
hr{margin:0 5px!important;}
</style>

<div class="tab-content">
@if(isset($errormsg))
<div class="alert alert-danger">
      <div class="content">{{$errormsg}}</div>
</div>
@endif
            <blockquote>
              <div class="btn-group">
                <a class="btn" href="{{url('/index/System/page/page_type/1')}}" @if ($pageinfo['page_type']==1)style="color:green;"@endif>关于我们</a>
                <a class="btn" href="{{url('/index/System/page/page_type/2')}}" @if ($pageinfo['page_type']==2)style="color:green;"@endif>用户协议</a>
              </div>
              </blockquote>
  



                <div class="tab-pane fade active in" id="tab2Content1">
                  <form action="" method="post" class="form-horizontal" id="myform">
                    <div class="row form-group">
                      <label for="page_title" class="col-sm-2 text-right"><b>文章标题:</b></label>
                      <div id="page_title" class="col-sm-6"><input type="text" class="form-control page_title" name="page_title" placeholder="文章的标题" value="{{ $pageinfo['page_title'] or ''}}"></div>
                     </div>


                    <div class="row form-group">
                     <label for="page_desc" class="col-sm-2 text-right"><b>文章简介:</b></label>
                     <div class="col-sm-6" id="page_desc">
                       <textarea name="page_desc" cols="30" class="form-control" rows="7">{{ $pageinfo['page_desc'] or '文章的简介'}}</textarea>
                     </div>
                   </div>
                    
                    <div class="row form-group">
                     <label for="page_content" class="col-sm-2 text-right"><b>文章内容:</b></label>
                     <div class="col-sm-6" id="page_content">
                       <textarea name="page_content" cols="30" id="content" class="form-control kindeditor" rows="15">{{ $pageinfo['page_content'] or '文章的内容'}}</textarea>
                     </div>
                   </div>
                    
                  <div class="row">
                <h4></h4>
                <input type="hidden" name="page_type" value="{{$pageinfo['page_type']}}">
                <input type="hidden" name="page_id" value="{{$pageinfo['page_id']}}">
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
                </div>
</form>

















 <script src="/static/js/jquery-labelauty.js"></script>
 <script type="text/javascript">
 $(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.setting').addClass('active');
    $('.menu .nav li.system-setting').addClass('show');
 });
 $(function(){
    $(':input').labelauty();
 });
 </script>
 <!---->
 @endsection
