 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
   <div class="row">
           <div class="col-sm-8"><h4>修改服务</h4></div>
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
   <form action="{{url('/index/server_model/service_list')}}" method="post" class="form-horizontal" id="myform">
   <h2></h2>
   <div class="row form-group">
     <label for="announcement_title" class="col-sm-3 text-right"><b>名称</b></label>
     <div class="col-sm-6" id="announcement_title">
       <input type="text" class="form-control announcement_title" name="announcement_title" placeholder="名称" value="{{$data['list_name']}}">
     </div>   
   </div>

    <div class="row form-group">
      <label for="announcement_content" class="col-sm-2 text-right">
        <b>所属模块</b>
      </label>
      <div id="announcement_content" class="col-sm-6">
        <select name="list_item_id" class="list_item_id">@foreach($service as $v)
          <option value="{{$v->item_id}}">{{$v->item_name}}</option>@endforeach
        </select>
        </div>
    </div>

    <div class="row form-group">
      <label for="announcement_content" class="col-sm-2 text-right">
        <b>会员专属</b>
      </label>
      <div id="announcement_content" class="col-sm-6">
        <input type="checkbox" name="list_authority" {{$data['list_authority'] ? 'checked=""' :''}}></div>
    </div>

    <div class="row form-group">
      <label for="announcement_content" class="col-sm-2 text-right">
        <b>图标</b>
      </label>
     <div id="generalize_thumb" class="col-sm-6">
       <div id='uploaderExample3' class="uploader">
         <div class="uploader-message text-center">
               <div class="content"></div>
               <button type="button" class="close">×</button>
           </div>
           <div class="uploader-files file-list file-list-grid">
          <img src="{{$data['list_icon']}}">
           </div>
         <div>
           <hr class="divider">
           <div class="uploader-status pull-right text-muted"></div>
           <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
           <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
         </div>
       </div>
       <input type="hidden" class="form-control generalize_thumb" name="list_icon" value="{{$data['list_icon']}}">
     </div>   
    </div>


    <div class="row form-group">
      <label for="announcement_content" class="col-sm-2 text-right">
        <b>服务地址</b>
      </label>
      <div id="announcement_title" class="col-sm-6">
        <input type="text" class="form-control announcement_title" name="list_url" placeholder="服务地址" value="{{$data['list_url']}}"></div>
    </div>

    <div class="row form-group">
      <label for="announcement_content" class="col-sm-2 text-right">
        <b>权重</b>
      </label>
      <div id="announcement_title" class="col-sm-6">
        <input type="text" class="form-control announcement_title" name="list_weight" placeholder="权重" value="{{$data['list_weight']}}"></div>
    </div>

    <div class="row form-group">
      <label for="announcement_content" class="col-sm-2 text-right">
        <b>是否开启</b>
      </label>
      <div id="announcement_content" class="col-sm-6">
        <input type="checkbox" name="list_state" {{$data['list_state'] ? 'checked=""' :''}}></div>
    </div>

<input type="hidden" name="list_id" value="{{$data['list_id']}}">
  
 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
   <button type="button" class="btn btn-primary save">保存</button>
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>

 <script>
  $(function(){
    $('.list_item_id').val({{$data['list_item_id']}});
  })
 $(".save").click(function(){ 
   $("#myform").submit();
 })
 //上传文件设置
 $('#uploaderExample3').uploader({
      url: "{{url('/index/Tool/upload_one')}}",
   file_data_name:'generalize',
   filters:{ max_file_size: '10mb',},
   limitFilesCount:1,
   onFileUploaded(file, responseObject) {
         attr=eval('('+responseObject.response+")");
         console.log(attr);
         // attr.code ?  bootbox.alert({ message: attr.msg, size: 'small' }): $(".generalize_thumb").val('attr.url');
         var generalize_thumb = $(".generalize_thumb").val()
          $(".generalize_thumb").val(attr.url);
          bootbox.alert({ message: attr.msg, size: 'small' })
   }
 });
</script>