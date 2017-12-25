 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
   <div class="row">
           <div class="col-sm-8"><h4>修改客服</h4></div>
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
   <form action="{{url('/index/system/show_service')}}" method="post" class="form-horizontal" id="myform">
   <h2></h2>
   <div class="row form-group">
     <label for="service_title" class="col-sm-3 text-right"><b>客服类型:</b></label>
     <div class="col-sm-6" id="service_title">
       <input type="text" class="form-control service_title" name="service_title" placeholder="客服类型" value="{{$services['service_title']}}">
     </div>   
   </div>

   <div class="row form-group">
     <label for="service_contact" class="col-sm-3 text-right"><b>联系方式:</b></label>
     <div class="col-sm-6" id="service_contact">
       <input type="text" class="form-control service_contact" name="service_contact" placeholder="联系方式" value="{{$services['service_contact']}}">
     </div>   
   </div>

<div class="row form-group">
     <label for="service_time" class="col-sm-3 text-right"><b>客服时间:</b></label>
     <div class="col-sm-6" id="service_time">
       <input type="text" class="form-control service_time" name="service_time" placeholder="客服时间" value="{{$services['service_time']}}">
     </div>   
   </div>

    <div class="row form-group">
                       <label for="service_type" class="col-sm-3 text-right"><b>是否是电话:</b></label>
                       <div id="service_type" class="col-sm-6">
                         <select name="service_type" class="form-control">
                           <option value="1" @if ($services['service_type']==1) selected="" @endif>是</option>
                           <option value="0" @if ($services['service_type']==0) selected="" @endif>否</option>
                         </select>
                       </div>   
                     </div>


<input type="hidden" name="service_id" value="{{$services['service_id']}}">
  
 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
   <button type="button" class="btn btn-primary save">保存</button>
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>

 <script>
 $(".save").click(function(){ 
  if(!$(".service_title").val()){
     $(".service_title").parent().addClass("has-error");
     return;
   }
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