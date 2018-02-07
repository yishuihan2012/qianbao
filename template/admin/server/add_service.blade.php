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
                  <form action="{{url('/index/server_model/add_service')}}" method="post" class="form-horizontal" id="myform"  onsubmit="return verification()">
                     <div class="row form-group">
                      <label for="announcement_title" class="col-sm-2 text-right"><b>名称</b></label>
                      <div id="announcement_title" class="col-sm-6">
                           <input type="text" class="form-control announcement_title" name="list_name" placeholder="名称" value=""></div>
                     </div>

                     <div class="row form-group">
                      <label for="announcement_content" class="col-sm-2 text-right"><b>所属模块</b></label>
                      <div id="announcement_content" class="col-sm-6">
                        <select name="list_item_id" class="form-control">
                          @foreach($service as $v)
                            <option value="{{$v->item_id}}">{{$v->item_name}}</option>
                          @endforeach
                        </select>
                      </div>
                     </div>

                     <div class="row form-group">
                      <label for="announcement_content" class="col-sm-2 text-right"><b>会员专属</b></label>
                      <div id="announcement_content" class="col-sm-6">
                           <ul class="dowebok tags">
                                <li><input type="radio" name="list_authority" checked value="0" data-labelauty="不限" /></li>
                                <li><input type="radio" name="list_authority"  value="1" data-labelauty="是" /></li>
                           </ul>  
                      </div>
                     </div>

                     <div class="row form-group">
                      <label for="generalize_thumb" class="col-sm-2 text-right"><b>图标</b></label>
                      <!-- <div id="announcement_title" class="col-sm-6"><input type="text" class="form-control announcement_title" name="list_icon" placeholder="图标url" value=""></div> -->
     <div id="generalize_thumb" class="col-sm-6">
       <div id='uploaderExample3' class="uploader">
         <div class="uploader-message text-center">
               <div class="content"></div>
               <button type="button" class="close">×</button>
           </div>
           <div class="uploader-files file-list file-list-grid"></div>
         <div>
           <hr class="divider">
           <div class="uploader-status pull-right text-muted"></div>
           <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
           <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
         </div>
       </div>
       <input type="hidden" class="form-control generalize_thumb" name="list_icon" value="">
     </div>   
                      </div>
                      
                     <div class="row form-group">
                      <label for="announcement_content" class="col-sm-2 text-right"><b>服务地址</b></label>
                      <div id="announcement_title" class="col-sm-6"><input type="text" class="form-control announcement_title" name="list_url" placeholder="服务地址" value=""></div>
                      </div>

                     <div class="row form-group">
                      <label for="announcement_content" class="col-sm-2 text-right"><b>权重</b></label>
                      <div id="announcement_title" class="col-sm-6"><input type="text" class="form-control announcement_title" name="list_weight" placeholder="权重" value=""></div>
                      </div>
                      
                     <div class="row form-group">
                      <label for="announcement_content" class="col-sm-2 text-right"><b>是否开启</b></label>
                      <div id="announcement_content" class="col-sm-6">
                       <ul class="dowebok tags">
                         <li><input type="radio" name="list_state"  value="0" data-labelauty="不显示" /></li>
                         <li><input type="radio" name="list_state" checked value="1" data-labelauty="显示" /></li>
                       </ul>                   
                      </div>
                     </div>
                    
                  <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
           </form>
                </div>

      

 <script src="/static/js/jquery-labelauty.js"></script>
  
 <script type="text/javascript">
  //验证form表单
  function verification(){
  var list_name = $("[name='list_name']").val();
  var list_icon = $("[name='list_icon']").val();
  if(list_name == ''){
    $("[name='list_name']").css("border","1px solid red");
   

    return false;
  }
 
 }
 $("input").click(function(){
    $(this).css("border","1px solid #3280fc");
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
   limitFilesCount:1,
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
       $('.menu .nav li.model-server').addClass('active');
       $('.menu .nav li.model-manager').addClass('show');
 });
 $(function(){
    $(':input').labelauty();
 });
 </script>
 <!---->
 @endsection
