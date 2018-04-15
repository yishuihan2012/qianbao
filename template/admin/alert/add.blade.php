 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
   <div class="row">
           <div class="col-sm-8"><h4>添加App弹窗广告</h4></div>
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
   <form action="{{url('/index/alert/add')}}" method="post" class="form-horizontal" id="myform">
   <div class="row form-group">
     <label for="announcement_title" class="col-sm-3 text-right"><b>图片</b></label>
     <div class="col-sm-6" id="announcement_title">
       <div id='uploaderExample3' class="uploader">
         <div class="uploader-message text-center">
               <div class="content"></div>
               <button type="button" class="close">×</button>
           </div>
           <div class="uploader-files file-list file-list-grid"></div>
           <img name="alert_img" src="{{$data['alert_img'] or ''}}" alt="">
         <div>
           <hr class="divider">
           <div class="uploader-status pull-right text-muted"></div>
           <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
           <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
         </div>
       </div>
       <input type="hidden" class="form-control " name="alert_img" value="{{$data['alert_img'] or ''}}">
     </div>   
   </div>
   <div class="row form-group">
     <label for="announcement_title" class="col-sm-3 text-right"><b>跳转链接</b></label>
     <div class="col-sm-6" id="announcement_title">
       <input type="text" class="form-control announcement_title" name="alert_url" placeholder="跳转链接" value="{{$data['alert_url'] or ''}}">
     </div>   
   </div>
 
   <div class="row form-group">
     <label for="bank_state" class="col-sm-3 text-right"><b>状态</b></label>
     <div id="bank_state" class="col-sm-6">
       <select name="alert_status" class="form-control alert_status">
         <option value=1>正常</option>
         <option value=0>禁用</option>
       </select>
     </div>   
   </div>
 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
  <input type="hidden" name="id" value="{{$data['alert_id'] or ''}}">
   <button type="button" class="btn btn-primary save">保存</button>
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>
</form>
</div>   

 <script>
$(function(){
     $('.alert_status').val({{$data['alert_status'] or 1}});

})
 $(".save").click(function(){ 
   $("#myform").submit();
 })
 //上传文件设置
 $('#uploaderExample3').uploader({
      url: "{{url('/index/Tool/upload_one')}}",
   file_data_name:'imgFile',
   filters:{ max_file_size: '10mb',},
   limitFilesCount:1,
   onFileUploaded(file, responseObject) {
         var attr=eval('('+responseObject.response+")");
         attr.code ? $("input[name=alert_img]").val(attr.url) : bootbox.alert({ message: attr.msg, size: 'small' });
   }
 });
</script>