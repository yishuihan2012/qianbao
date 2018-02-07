 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
   <div class="row">
           <div class="col-sm-8"><h4>添加App版本号</h4></div>
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
   <form action="{{url('/index/appversion/creat')}}" method="post" class="form-horizontal" id="myform">
   <h2></h2>
   <div class="row form-group">
     <label for="announcement_title" class="col-sm-3 text-right"><b>版本名称</b></label>
     <div class="col-sm-6" id="announcement_title">
       <input type="text" class="form-control announcement_title" name="version_name" placeholder="版本名称" value="">
     </div>   
   </div>
   <div class="row form-group">
     <label for="announcement_title" class="col-sm-3 text-right"><b>版本code</b></label>
     <div class="col-sm-6" id="announcement_title">
       <input type="text" class="form-control announcement_title" name="version_code" placeholder="版本code" value="">
     </div>   
   </div>
 
   <div class="row form-group">
     <label for="bank_state" class="col-sm-3 text-right"><b>类型</b></label>
     <div id="bank_state" class="col-sm-6">
       <select name="version_type" class="form-control">
         <option value="android">android</option>
         <option value="ios">ios</option>
       </select>
     </div>   
   </div>
   <div class="row form-group">
     <label for="announcement_title" class="col-sm-3 text-right"><b>版本连接</b></label>
     <div class="col-sm-6" id="announcement_title">
       <input type="text" class="form-control announcement_title" name="version_link" placeholder="版本连接" value="">
     </div>   
   </div>
   <div class="row form-group">
     <label for="announcement_title" class="col-sm-3 text-right"><b>版本描述</b></label>
     <div class="col-sm-6" id="announcement_title">
       <input type="text" class="form-control announcement_title" name="version_desc" placeholder="版本描述" value="">
     </div>   
   </div>
  
   <div class="row form-group">
     <label for="bank_state" class="col-sm-3 text-right"><b>是否强制</b></label>
     <div id="bank_state" class="col-sm-6">
       <select name="version_force" class="form-control">
         <option value="0">否</option>
         <option value="1">是</option>
       </select>
     </div>   
   </div>
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