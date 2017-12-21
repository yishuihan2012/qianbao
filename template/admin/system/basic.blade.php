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


              <ul class="nav nav-tabs">
                <li class="active"><a href="###" data-target="#tab2Content1" data-toggle="tab">基本配置</a></li>
                <li><a href="###" data-target="#tab2Content2" data-toggle="tab">登录注册</a></li>
                <li><a href="###" data-target="#tab2Content3" data-toggle="tab">短信邮件</a></li>
                <li><a href="###" data-target="#tab2Content4" data-toggle="tab">分佣信息</a></li>
                <li><a href="###" data-target="#tab2Content5" data-toggle="tab">认证配置</a></li>
                <li><a href="###" data-target="#tab2Content6" data-toggle="tab">提现配置</a></li>
                <li><a href="###" data-target="#tab2Content7" data-toggle="tab">上传配置</a></li>
              </ul>

              <div class="tab-content">



                <div class="tab-pane fade active in" id="tab2Content1">
                  <form action="" method="post" class="form-horizontal" id="myform">
                  @foreach($setting as $val)
                    @if($val['system_type']=='basic')
                   <div class="row form-group">
                         <label for="{{$val['system_key']}}" class="col-sm-2 text-right"><b>{{$val['system_value']}}:</b></label>
                         <div class="col-sm-6 input-group" id="{{$val['system_key']}}">
                              @if($val['system_genre']=='input')
                                   <input type="{{$val['system_genre']}}" name="{{$val['system_key']}}" class="form-control" value="{{$val['system_val']}}" />
                                   <span class="input-group-btn help-block" style="float: left;">&nbsp;{{$val['system_des']}}</span>
                              @elseif($val['system_genre']=='radio')
                                   <select class="form-control" name="{{$val['system_key']}}">
                                         @foreach(explode(",",$val['system_default']) as $key)
                                         <option @if((strrev(strstr(strrev($key),strrev('='),true)))==$val['system_val']) selected  @endif value="{{strrev(strstr(strrev($key),strrev('='),true))}}">{{strstr($key,"=",true)}}</option>
                                         @endforeach
                                   </select>
                                   <span class="text-left help-block">&nbsp;{{$val['system_des']}}</span>
                              @endif
                         </div>
                   </div>  
                   <hr>
                   @endif
                   @endforeach
                  <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
                </div>



                <div class="tab-pane fade" id="tab2Content2">
                  <form action="" method="post" class="form-horizontal" id="myform">
                   @foreach($setting as $val)
                    @if($val['system_type']=='register')
                   <div class="row form-group">
                         <label for="{{$val['system_key']}}" class="col-sm-2 text-right"><b>{{$val['system_value']}}:</b></label>
                         <div class="col-sm-6 input-group" id="{{$val['system_key']}}">
                              @if($val['system_genre']=='input')
                                   <input type="{{$val['system_genre']}}" name="{{$val['system_key']}}" class="form-control" value="{{$val['system_val']}}" />
                                   <span class="input-group-btn help-block" style="float: left;">&nbsp;{{$val['system_des']}}</span>
                              @elseif($val['system_genre']=='radio')
                                   <select class="form-control" name="{{$val['system_key']}}">
                                         @foreach(explode(",",$val['system_default']) as $key)
                                         <option @if((strrev(strstr(strrev($key),strrev('='),true)))==$val['system_val']) selected  @endif value="{{strrev(strstr(strrev($key),strrev('='),true))}}">{{strstr($key,"=",true)}}</option>
                                         @endforeach
                                   </select>
                                   <span class="text-left help-block">&nbsp;{{$val['system_des']}}</span>
                              @endif
                         </div>
                   </div>  
                   <hr>
                   @endif
                   @endforeach
                   <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
                </div>

                <div class="tab-pane fade" id="tab2Content3">
                  <form action="" method="post" class="form-horizontal" id="myform">
                  @foreach($setting as $val)
                    @if($val['system_type']=='send')
                   <div class="row form-group">
                         <label for="{{$val['system_key']}}" class="col-sm-2 text-right"><b>{{$val['system_value']}}:</b></label>
                         <div class="col-sm-6 input-group" id="{{$val['system_key']}}">
                              @if($val['system_genre']=='input')
                                   <input type="{{$val['system_genre']}}" name="{{$val['system_key']}}" class="form-control" value="{{$val['system_val']}}" />
                                   <span class="input-group-btn help-block" style="float: left;">&nbsp;{{$val['system_des']}}</span>
                              @elseif($val['system_genre']=='radio')
                                   <select class="form-control" name="{{$val['system_key']}}">
                                         @foreach(explode(",",$val['system_default']) as $key)
                                         <option @if((strrev(strstr(strrev($key),strrev('='),true)))==$val['system_val']) selected  @endif value="{{strrev(strstr(strrev($key),strrev('='),true))}}">{{strstr($key,"=",true)}}</option>
                                         @endforeach
                                   </select>
                                   <span class="text-left help-block">&nbsp;{{$val['system_des']}}</span>
                              @endif
                         </div>
                   </div>  
                   <hr>
                   @endif
                   @endforeach
                   <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
                </div>

                <div class="tab-pane fade" id="tab2Content4">
                  <form action="" method="post" class="form-horizontal" id="myform">
                  @foreach($setting as $val)
                    @if($val['system_type']=='commission')
                   <div class="row form-group">
                         <label for="{{$val['system_key']}}" class="col-sm-2 text-right"><b>{{$val['system_value']}}:</b></label>
                         <div class="col-sm-6 input-group" id="{{$val['system_key']}}">
                              @if($val['system_genre']=='input')
                                   <input type="{{$val['system_genre']}}" name="{{$val['system_key']}}" class="form-control" value="{{$val['system_val']}}" />
                                   <span class="input-group-btn help-block" style="float: left;">&nbsp;{{$val['system_des']}}</span>
                              @elseif($val['system_genre']=='radio')
                                   <select class="form-control" name="{{$val['system_key']}}">
                                         @foreach(explode(",",$val['system_default']) as $key)
                                         <option @if((strrev(strstr(strrev($key),strrev('='),true)))==$val['system_val']) selected  @endif value="{{strrev(strstr(strrev($key),strrev('='),true))}}">{{strstr($key,"=",true)}}</option>
                                         @endforeach
                                   </select>
                                   <span class="text-left help-block">&nbsp;{{$val['system_des']}}</span>
                              @endif
                         </div>
                   </div>  
                   <hr>
                   @endif
                   @endforeach
                   <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
                </div>


                <div class="tab-pane fade" id="tab2Content5">
                  <form action="" method="post" class="form-horizontal" id="myform">
                  @foreach($setting as $val)
                    @if($val['system_type']=='cardcert')
                   <div class="row form-group">
                         <label for="{{$val['system_key']}}" class="col-sm-2 text-right"><b>{{$val['system_value']}}:</b></label>
                         <div class="col-sm-6 input-group" id="{{$val['system_key']}}">
                              @if($val['system_genre']=='input')
                                   <input type="{{$val['system_genre']}}" name="{{$val['system_key']}}" class="form-control" value="{{$val['system_val']}}" />
                                   <span class="input-group-btn help-block" style="float: left;">&nbsp;{{$val['system_des']}}</span>
                              @elseif($val['system_genre']=='radio')
                                   <select class="form-control" name="{{$val['system_key']}}">
                                         @foreach(explode(",",$val['system_default']) as $key)
                                         <option @if((strrev(strstr(strrev($key),strrev('='),true)))==$val['system_val']) selected  @endif value="{{strrev(strstr(strrev($key),strrev('='),true))}}">{{strstr($key,"=",true)}}</option>
                                         @endforeach
                                   </select>
                                   <span class="text-left help-block">&nbsp;{{$val['system_des']}}</span>
                              @endif
                         </div>
                   </div>  
                   <hr>
                   @endif
                   @endforeach
                   <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
                </div>



                <div class="tab-pane fade" id="tab2Content6">
                  <form action="" method="post" class="form-horizontal" id="myform">
                  @foreach($setting as $val)
                    @if($val['system_type']=='withdrawals')
                   <div class="row form-group">
                         <label for="{{$val['system_key']}}" class="col-sm-2 text-right"><b>{{$val['system_value']}}:</b></label>
                         <div class="col-sm-6 input-group" id="{{$val['system_key']}}">
                              @if($val['system_genre']=='input')
                                   <input type="{{$val['system_genre']}}" name="{{$val['system_key']}}" class="form-control" value="{{$val['system_val']}}" />
                                   <span class="input-group-btn help-block" style="float: left;">&nbsp;{{$val['system_des']}}</span>
                              @elseif($val['system_genre']=='radio')
                                   <select class="form-control" name="{{$val['system_key']}}">
                                         @foreach(explode(",",$val['system_default']) as $key)
                                         <option @if((strrev(strstr(strrev($key),strrev('='),true)))==$val['system_val']) selected  @endif value="{{strrev(strstr(strrev($key),strrev('='),true))}}">{{strstr($key,"=",true)}}</option>
                                         @endforeach
                                   </select>
                                   <span class="text-left help-block">&nbsp;{{$val['system_des']}}</span>
                              @endif
                         </div>
                   </div>  
                   <hr>
                   @endif
                   @endforeach
                   <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
                </div>


                <div class="tab-pane fade" id="tab2Content7">
                  <form action="" method="post" class="form-horizontal" id="myform">
                  @foreach($setting as $val)
                    @if($val['system_type']=='filed')
                   <div class="row form-group">
                         <label for="{{$val['system_key']}}" class="col-sm-2 text-right"><b>{{$val['system_value']}}:</b></label>
                         <div class="col-sm-6 input-group" id="{{$val['system_key']}}">
                              @if($val['system_genre']=='input')
                                   <input type="{{$val['system_genre']}}" name="{{$val['system_key']}}" class="form-control" value="{{$val['system_val']}}" />
                                   <span class="input-group-btn help-block" style="float: left;">&nbsp;{{$val['system_des']}}</span>
                              @elseif($val['system_genre']=='radio')
                                   <select class="form-control" name="{{$val['system_key']}}">
                                         @foreach(explode(",",$val['system_default']) as $key)
                                         <option @if((strrev(strstr(strrev($key),strrev('='),true)))==$val['system_val']) selected  @endif value="{{strrev(strstr(strrev($key),strrev('='),true))}}">{{strstr($key,"=",true)}}</option>
                                         @endforeach
                                   </select>
                                   <span class="text-left help-block">&nbsp;{{$val['system_des']}}</span>
                              @endif
                         </div>
                   </div>  
                   <hr>
                   @endif
                   @endforeach
                   <div class="row">
                <h4></h4>
                 <div class="col-sm-7 text-center"><button type="submit" class="btn btn-primary save">保存</button></div>
                </div>
                </div>



              </div>
              
</form>

















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
