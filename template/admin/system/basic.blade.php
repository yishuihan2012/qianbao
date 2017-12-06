@extends('admin/layout/layout_main')
@section('title','注册登录配置项~')
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

 <div class="tab-pane fade active in" id="tab2Content1">
      <h3></h3>
      <form action="" method="post" class="form-horizontal" id="myform">
           @foreach($setting as $setting)
           <div class="row form-group">
                 <label for="{{$setting['system_key']}}" class="col-sm-2 text-right"><b>{{$setting['system_value']}}:</b></label>
                 <div class="col-sm-6 input-group" id="{{$setting['system_key']}}">
                      @if($setting['system_genre']=='input')
                           <input type="{{$setting['system_genre']}}" name="{{$setting['system_key']}}" class="form-control" value="{{$setting['system_val']}}" />
                           <span class="input-group-btn">&nbsp;{{$setting['system_des']}}</span>
                      @elseif($setting['system_genre']=='radio')
                           <select class="form-control" name="{{$setting['system_key']}}">
                                 @foreach(explode(",",$setting['system_default']) as $key)
                                 <option @if((strrev(strstr(strrev($key),strrev('='),true)))==$setting['system_val']) selected  @endif value="{{strrev(strstr(strrev($key),strrev('='),true))}}">{{strstr($key,"=",true)}}</option>
                                 @endforeach
                           </select>
                           <span class="text-left help-block">&nbsp;{{$setting['system_des']}}</span>
                      @endif
                 </div>
           </div>  
           <hr>
           @endforeach
           <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
           </div>
      </form>
 </div>

 <script src="/static/js/jquery-labelauty.js"></script>
 <script type="text/javascript">
 $(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.setting').addClass('active');
    $('.menu .nav li.system-setting').addClass('show');
 });
 $(function(){
    $(':input').labelauty();
 });
 </script>
 <!---->
 @endsection
