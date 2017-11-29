@extends('admin/layout/layout_main')
@section('title','会员列表管理~')
@section('wrapper')

<div class="row">
	@foreach ($member as $list)
          <div class="col-sm-4" style="height: 230px;">
                <div class="contact-box">
                    <a href="">
                        <div class="col-sm-4">
                            <div class="text-center">
                                <img alt="image" class="img-circle m-t-xs img-responsive" src="http://xijia.oss-cn-shanghai.aliyuncs.com/images/appavatar/uc_defaultavatar%402x.png" width="128" height="256">
                                <div class="m-t-xs font-bold"></div>
                            </div>
                        </div>
                        </a>
                        <div class="col-sm-8">
                        	<a href="/index.php?s=/Mrpiadmin/UserManage/UserCon/id/44.html">
                            <h3><strong>{{$list['member_mobile']}}&nbsp;&nbsp;&nbsp;@if($list['member_cert']==1)已认证@else未认证@endif</strong></h3>
                            </a>
                            <p><a href="">普通商户
                            </a><a style="color:green;font-size:16px;" user_id="44" u_member_id="1" class="uplevel"><button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" style="font-size:12px;">升级</button></a>                            
                        </p>
                        <address>
                            电话：15253394752<br>

                            推荐码：wrwigotw<br>

                            注册时间：2017-11-24 09:09:15
                        </address>
                        </div>
                        <div class="clearfix">
                            <p class="project-actions" style="float:right;">
                                 <a href="" class="btn btn-white btn-sm"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>分成信息</a>
                             </p>
                            <p class="project-actions" style="float:right;">
                                 <a onclick="UserDel(44)" class="btn btn-white btn-sm"><span class="glyphicon glyphicon-list-alt login_forbid" aria-hidden="true"></span>账号禁用</a>
                            </p>
                            <p class="project-actions" style="float:right;">
                                <a href="" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                            </p>
                        </div>
                    
                </div>
            </div>
            @endforeach
        </div>
<script type="text/javascript">
$(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.member').addClass('active');
    	 $('.menu .nav li.member-manager').addClass('show');

    	 $(".parent li a").click(function(){
    	 	$("input[name='article_parent']").val($(this).attr('data-id'));
    	 	$("input[name='article_category']").val(0);
    	 	$("#myform").submit();
    	 })
    	 $(".son li a").click(function(){
    	 	$("input[name='article_category']").val($(this).attr('data-id'));
    	 	$("#myform").submit();
    	 })
    	 $(".remove").click(function(){
    	 	 var url=$(this).attr('data-url');
		 bootbox.confirm({
		    title: "删除文章确认",
		    message: "确定删除这篇文章吗? 删除后不可恢复!",
		    buttons: {
		        cancel: {label: '<i class="fa fa-times"></i> 点错了'},
		        confirm: {label: '<i class="fa fa-check"></i> 确定'}
		    },
		    callback: function (result) {
		    	 if(result)
		    	 	window.location.href=url;
		    }
		 });
    	 })
});
</script>
@endsection
