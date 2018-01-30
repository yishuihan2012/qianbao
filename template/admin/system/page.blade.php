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
                <a class="btn" href="{{url('/index/System/page/page_type/3')}}" @if ($pageinfo['page_type']==3)style="color:green;"@endif>资质证书</a>
              </div>  
              </blockquote>
                <div class="tab-pane fade active in" id="tab2Content1">
                  <form action="" method="post" class="form-horizontal" id="myform">
                    <div class="row form-group">
                      <label for="page_title" class="col-sm-2 text-right"><b>标题:</b></label>
                      <div id="page_title" class="col-sm-6"><input type="text" class="form-control page_title" name="page_title" placeholder="文章的标题" value="{{ $pageinfo['page_title'] or ''}}"></div>
                     </div>
                    <div class="row form-group">
                     <label for="page_desc" class="col-sm-2 text-right"><b>简介:</b></label>
                     <div class="col-sm-6" id="page_desc">
                       <textarea name="page_desc" cols="30" class="form-control" rows="7">{{ $pageinfo['page_desc'] or '简介'}}</textarea>
                     </div>
                   </div> 
                    <div class="row form-group">
                     <label for="page_content" class="col-sm-2 text-right"><b>内容:</b></label>
                     <div class="col-sm-6" id="page_content">
                       <textarea name="page_content" cols="30" id="content" class="form-control kindeditor" rows="15">{{ $pageinfo['page_content'] or '内容'}}</textarea>
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
    $('.menu .nav li.setting-page').addClass('active');
    $('.menu .nav li.system-setting').addClass('show');

    $(".save").click(function(){
    // if(!$(".article_title").val()){
    //   $(".article_title").parent().addClass("has-error");
    //   return false;
    // }
    html = editor.html();
    // 同步数据后可以直接取得textarea的value
    editor.sync();
    html = document.getElementById('content').value; // 原生API
    html = KindEditor('#content').val(); // KindEditor Node API
    html = $('#content').val(); // jQuery
    editor.html(html);
    $("#myform").submit()
  });
  //上传文件设置
   $('#uploaderExample3').uploader({
         url: "{{url('/index/Tool/upload_one')}}",
        file_data_name:'article',
     filters:{
           // 最大上传文件为 10MB
           max_file_size: '10mb',
     },
     limitFilesCount:1,
        onFileUploaded(file, responseObject) {
         var attr=eval('('+responseObject.response+")");
           attr.code ? $("input[name=article_thumb]").val(attr.url) : bootbox.alert({ message: attr.msg, size: 'small' });
        }

   });
   //编辑器初始化
      KindEditor.ready(function(K) {
            window.editor = K.create('#editor_id');
      });
  
    var options = {
        filterMode : true,
           uploadJson : "{{url('/index/Tool/KindEditor_upload')}}",
           fileManagerJson : "{{url('/index/Tool/KindEditor_upload')}}",
           allowFileManager : true
   };
   var editor = KindEditor.create('textarea[id="content"]', options);
   $(".goHistory").click(function(){
     window.history.go(-1);
   })
 });
 $(function(){
    $(':input').labelauty();
 });
 </script>
 <!---->
 @endsection
