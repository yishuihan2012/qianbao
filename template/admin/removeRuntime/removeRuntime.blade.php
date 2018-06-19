
<style>
	.text-ellipsis{cursor: pointer;}
 
</style>
<div class="panel">
  	<div class="panel-body">
  		<form action="" name="myform" class="form-group" method="post">

   <form action="" method="post">
      <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
      
  </div>
      <div class="input-group" style="width: 360px;float: left;margin-right: 10px;">
        <span class="input-group-addon">请选择时间删除</span>
        <!-- <input type="date" name="beginTime" style="width: 140px" class="form-control" value="" /> -->

        <input type="date" name="endTime" style="width: 140px" class="form-control" value="" /></div>
  <button class="btn btn-primary export" type="submit">删除log日志</button>
</form>


		</form>
  	</div>
</div>
<div class="list">
  <header>
  
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.plan').addClass('active');
    $('.menu .nav li.plan-manager').addClass('show');
    $('.member_group_id').val({{$r['member_group_id'] or ''}})
    $(".freezing").click(function(){
    	var id = $(this).attr('data-id');
    	var explain = $(this).attr('explain');
		bootbox.prompt({
		    title: "请输入要"+explain+"的原因",
		    inputType: 'text',
		    callback: function (result) {
		        if(result!=null){
		        	$.ajax({
		        		url : "{{url('/index/wallet/freezing')}}",
		        		data : {id:id,wallet_desc:result},
		        		type : 'POST',
		        		dataType : 'Json',
		        		success:function(data){
		    				explain+=data ? '成功' : '失败';
		    				type= data ? 'success' : 'error';
							new $.zui.Messager(explain, { type: type, close: true, }).show();
							window.location.reload();
		        		}
		        	})
		        }
		    }
  		});
    })
  })
$('.export').click(function(){
  $(".is_export").val(1);
  setTimeout(function(){
    $(".is_export").val(0);
  },100);
})
</script>
<style type="text/css">
  

.btn {
    color: #353535;
    text-shadow: 0 1px 0 #fff;
    background-color: #f2f2f2;
    border-color: #bfbfbf;
}
.btn {
    display: inline-block;
    padding: 5px 12px;
    margin-bottom: 0;
    font-size: 13px;
    font-weight: 400;
    line-height: 1.53846154;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    border-radius: 4px;
    -webkit-transition: all .4s cubic-bezier(.175,.885,.32,1);
    -o-transition: all .4s cubic-bezier(.175,.885,.32,1);
    transition: all .4s cubic-bezier(.175,.885,.32,1);
}
button, input, select, textarea {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
}
button, html input[type=button], input[type=reset], input[type=submit] {
    -webkit-appearance: button;
    cursor: pointer;
}
button, select {
    text-transform: none;
}
button, input {
    line-height: normal;
}
button, input, select, textarea {
    margin: 0;
    font-family: inherit;
    font-size: 100%;
}
*, :after, :before {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
user agent stylesheet
input[type="button" i], input[type="submit" i], input[type="reset" i], input[type="file" i]::-webkit-file-upload-button, button {
    padding: 1px 6px;
}
user agent stylesheet
input[type="button" i], input[type="submit" i], input[type="reset" i], input[type="file" i]::-webkit-file-upload-button, button {
    align-items: flex-start;
    text-align: center;
    cursor: default;
    color: buttontext;
    background-color: buttonface;
    box-sizing: border-box;
    padding: 2px 6px 3px;
    border-width: 2px;
    border-style: outset;
    border-color: buttonface;
    border-image: initial;
}
user agent stylesheet
input, textarea, select, button {
    text-rendering: auto;
    color: initial;
    letter-spacing: normal;
    word-spacing: normal;
    text-transform: none;
    text-indent: 0px;
    text-shadow: none;
    display: inline-block;
    text-align: start;
    margin: 0em;
    font: 400 13.3333px Arial;
}
user agent stylesheet
input, textarea, select, button, meter, progress {
    -webkit-writing-mode: horizontal-tb !important;
}
user agent stylesheet
button {
    -webkit-appearance: button;
}
body {
    font-family: "Helvetica Neue",Helvetica,Tahoma,Arial,'Microsoft Yahei','PingFang SC','Hiragino Sans GB','WenQuanYi Micro Hei',sans-serif;
    font-size: 13px;
    line-height: 1.53846154;
    color: #353535;
    background-color: #fff;
}
html {
    font-size: 10px;
    -webkit-tap-highlight-color: rgba(0,0,0,0);
}
html {
    font-family: sans-serif;
    -ms-text-size-adjust: 100%;
    -webkit-text-size-adjust: 100%;
}
*, :after, :before {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
*, :after, :before {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
@media (min-width: 768px)
::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}
@media (min-width: 768px)
::-webkit-scrollbar-button {
    width: 0;
    height: 0;
}
@media (min-width: 768px)
::-webkit-scrollbar-thumb {
    min-height: 28px;
    padding-top: 100;
    background-color: rgba(0,0,0,.2);
    -webkit-background-clip: padding-box;
    background-clip: padding-box;
    border-radius: 5px;
    -webkit-box-shadow: inset 1px 1px 0 rgba(0,0,0,.1), inset 0 -1px 0 rgba(0,0,0,.07);
}
::selection {
    text-shadow: none;
    background: #b3d4fc;
}
</style>
<!---->

