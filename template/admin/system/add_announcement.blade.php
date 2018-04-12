@extends('admin/layout/layout_main')
@section('title','添加公告')
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
                  <form action="" method="post" class="form-horizontal" id="myform"  onsubmit="return verification()">

                     <div class="row form-group">
                      <label for="announcement_title" class="col-sm-2 text-right"><b>标题</b></label>
                      <div id="announcement_title" class="col-sm-6"><input type="text" class="form-control announcement_title" name="announcement_title" placeholder="标题" value=""></div>
                     </div>

                     <div class="row form-group">
                      <label for="announcement_content" class="col-sm-2 text-right"><b>内容</b></label>
                      <div id="announcement_content" class="col-sm-6"><textarea type="text" class="form-control announcement_content" name="announcement_content" placeholder="内容" value=""></textarea>
                      </div>
                     </div>
                    
                  <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
                </div>
</form>
<div class="bodys"></div>

 <script type="text/javascript">
  //验证form表单
  function verification(){
  if($("[name='announcement_title']").val() == ''){
   $("[name='announcement_title']").css("border","1px solid red");

    return false;
  }
  if($("[name='announcement_content']").val()==''){
     $("[name='announcement_content']").css("border","1px solid red");
    return false;
  }
 }
  $("input").click(function(){
    $(this).css("border","1px solid #3280fc");
 })
 $(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.setting-announcement').addClass('active');
    $('.menu .nav li.system-setting').addClass('show');
 });
 // $(function(){
 //    $(':input').labelauty();
 // });
 </script>
 
 <!---->
 @endsection
