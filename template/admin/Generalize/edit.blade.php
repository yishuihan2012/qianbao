@extends('admin/layout/layout_main')
@section('title','添加服务')
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
   <div class="tab-pane fade active in" id="tab2Content1">
    <form action="{{url('/index/Generalize/edit')}}" method="post" class="form-horizontal" id="myform">
        <div class="row form-group">
          <label for="announcement_title" class="col-sm-2 text-right"><b>名称</b></label>
            <div id="announcement_title" class="col-sm-6"><input type="text" class="form-control announcement_title" name="generalize_title" placeholder="名称" value="{{$info['generalize_title']}}">
              <input type="hidden" class="form-control announcement_title" name="generalize_id" placeholder="名称" value="{{$info['generalize_id']}}">
        </div>
    </div>
    <div class="row form-group">
       <label for="generalize_thumb" class="col-sm-2 text-right"><b>素材图标</b></label>      
       <div id="generalize_thumb" class="col-sm-6">
         <div id='uploaderExample3' class="uploader">
            <div class="uploader-message text-center">
              <div class="content"></div>
                 <button type="button" class="close">×</button>
              </div>
              <div class="uploader-files file-list file-list-grid">
  						 @foreach($info['arrImg'] as $k=>$v)
                <span>
  								<img src="{{$v}}" width="200" height="200">
                  <a href="javascript:;" class="removeimg" value="{{$k}}">删除</a>
                </span>
  					    @endforeach
              <div>
             <hr class="divider">
             <div class="uploader-status pull-right text-muted"></div>
             <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
             <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
            </div>
         </div>
         <input type="hidden" class="form-control generalize_thumb" name="generalize_thumb" value="{{$info['generalize_thumb']}}">
       </div>   
		</div>
  </div>
    <div class="row form-group" >
      <label for="announcement_content" class="col-sm-2 text-right"><b>素材内容</b></label>
      <textarea name="generalize_contents" class="form-control passageway_desc" style="width:50%;">{{$info['generalize_contents']}}</textarea>
    </div>
    <div class="row">
      <h4></h4>
      <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
    </div>
  </form>
</div>
 <script src="/static/js/jquery-labelauty.js"></script>
  
 <script type="text/javascript">
  //删除用户信息
  $(".removeimg").click(function(){
    var t = $(this);
    console.log(t.parent("span"));
    var key = $(this).attr("value");
    var generalize_id = $("input[name='generalize_id']").val();
    var status = '';
    bootbox.confirm({
        title: "删除图片",
        message: "确定删除这张图片吗? 删除后不可恢复!",
        buttons: {
            cancel: {label: '<i class="fa fa-times"></i> 点错'},
            confirm: {label: '<i class="fa fa-check"></i> 确定'}
        },
        callback: function (result) {
          if(result){
            $.post("{{url('index/generalize/removeimg')}}",{key:key,generalize_id:generalize_id},function(data){
              if(data.code==200){
                t.parent("span").remove();

                  $(".generalize_thumb").val(data.data);
         
              }else{
                alert("删除失败")
              }
            },"json");
          }
        }
    });
    
  })
  $(".save").click(function(){  
  if(!$(".generalize_title").val()){
     $(".generalize_title").parent().addClass("has-error");
     return;
   }
   $("#myform").submit()
 })
$(document).ready(function(){
  //上传文件设置
 $('#uploaderExample3').uploader({
      url: "{{url('/index/Tool/upload_one')}}",
   file_data_name:'generalize',
   filters:{ max_file_size: '10mb',},
   limitFilesCount:3,
   onFileUploaded(file, responseObject) {

         attr=eval('('+responseObject.response+")");
         console.log(attr);
         // attr.code ?  bootbox.alert({ message: attr.msg, size: 'small' }): $(".generalize_thumb").val('attr.url');
         var generalize_thumb = $(".generalize_thumb").val()
         if( generalize_thumb == ''){
          $(".generalize_thumb").val(attr.url);
          bootbox.alert({ message: attr.msg, size: 'small' })
         }else{
          $(".generalize_thumb").val(generalize_thumb+"#"+attr.url);
          bootbox.alert({ message: attr.msg, size: 'small' })
         }

   }

 });
       $('.menu .nav .active').removeClass('active');
       $('.menu .nav li.generalize').addClass('active');
       $('.menu .nav li.generalize-manager').addClass('show');
 });
 $(function(){
    $(':input').labelauty();
 });
 </script>
 <!---->
 @endsection
