@extends('admin/layout/layout_main')
@section('title','添加客服~')
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
                  <form action="" method="post" class="form-horizontal" id="myform" onsubmit="return verification()">
                    <div class="row form-group">
                      <label for="service_title" class="col-sm-2 text-right"><b>客服类型:</b></label>
                      <div id="service_title" class="col-sm-6"><input type="text" class="form-control service_title" name="service_title" placeholder="客服类型" value=""></div>
                     </div>

                     <div class="row form-group">
                      <label for="service_contact" class="col-sm-2 text-right"><b>联系方式:</b></label>
                      <div id="service_contact" class="col-sm-6"><input type="text" class="form-control service_contact" name="service_contact" placeholder="联系方式" value=""></div>
                     </div>

                     <div class="row form-group">
                      <label for="service_time" class="col-sm-2 text-right"><b>客服时间:</b></label>
                      <div id="service_time" class="col-sm-6"><input type="text" class="form-control service_time" name="service_time" placeholder="客服时间" value=""></div>
                     </div>

                     <div class="row form-group">
                       <label for="passageway_state" class="col-sm-2 text-right"><b>是否是电话:</b></label>
                       <div id="passageway_state" class="col-sm-6">
                         <select name="passageway_state" class="form-control">
                           <option value="1">是</option>
                           <option value="0">否</option>
                         </select>
                       </div>   
                     </div>
                    
                  <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
                </div>
</form>



<div class="bodys"></div>
<div class="alert">
    <span class="hint">ppppppp</span><br>
    <button class="determine">确定</button>
</div>


 <script src="/static/js/jquery-labelauty.js"></script>
 <script type="text/javascript">
  //验证form表单
  function verification(){
  var service_title = $("[name='service_title']").val();
  var service_contact = $("[name='service_contact']").val();
  var service_time = $("[name='service_time']").val();
  if(service_title == ''){
    $("[name='service_title']").css("border","1px solid red");

    return false;
  }
  if(service_contact==''){
      $("[name='service_contact']").css("border","1px solid red");
    return false;
  }
  if(service_time==''){
     $("[name='service_time']").css("border","1px solid red");
    return false;
  }
 }
 $("input").click(function(){
    $(this).css("border","1px solid #3280fc");
 })
 $(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.service').addClass('active');
    $('.menu .nav li.service').addClass('show');
 });
 $(function(){
    $(':input').labelauty();
 });
 </script>
 <!---->
 @endsection
