 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
   <div class="row">
           <div class="col-sm-8"><h4>修改公告</h4></div>
           <div class="col-sm-4">
               <div class="text-right">
                   <span class="label label-dot"></span>
                   <span class="label label-dot label-primary"></span>
                   <span class="label label-dot label-success"></span>
                   <span class="label label-dot label-info"></span>
                   <span class="label label-dot label-warning"></span>
                   <span class="label label-dot label-danger"></span>
               </div>
           </div>
       </div>
 </div>

 <!--dialog Content-->
 <div class="modal-content animated fadeInLeft">
   <form action="{{url('/index/system/show_announcement')}}" method="post" class="form-horizontal" id="myform">
   <h2></h2>
   <div class="row form-group">
     <label for="announcement_title" class="col-sm-3 text-right"><b>标题</b></label>
     <div class="col-sm-6" id="announcement_title">
       <input type="text" class="form-control announcement_title" name="announcement_title" placeholder="标题" value="{{$data['announcement_title']}}">
     </div>   
   </div>

<div class="row form-group">
     <label for="announcement_content" class="col-sm-3 text-right"><b>内容</b></label>
     <div class="col-sm-6" id="announcement_content">
       <textarea type="text" class="form-control announcement_content" name="announcement_content" placeholder="内容">{{$data['announcement_content']}}</textarea>
     </div>   
   </div>


<input type="hidden" name="announcement_id" value="{{$data['announcement_id']}}">
  
 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
   <button type="button" class="btn btn-primary save">保存</button>
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>

 <script>
 $(".save").click(function(){ 
   $("#myform").submit()
 })
 //上传文件设置
 $('#uploaderExample3').uploader({
      url: "{{url('/index/Tool/upload_one')}}",
   file_data_name:'bank',
   filters:{ max_file_size: '10mb',},
   limitFilesCount:1,
   onFileUploaded(file, responseObject) {
         var attr=eval('('+responseObject.response+")");
         attr.code ? $("input[name=passageway_avatar]").val(attr.url) : bootbox.alert({ message: attr.msg, size: 'small' });
   }
 });
</script>