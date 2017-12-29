$(document).ready(function(){
  $("table tr").click(function(){
          var input = $(this).find("input[type=checkbox]");//获取checkbox
          if(input.is(":checked")){
              input.prop("checked",false);
          }else{
              input.prop("checked",true);
          }
  });
  $('a.print').on('click',function(){
      $('#print_area').printArea();
  });
});
$(function(){
  //分享素材页面点击显示更多 或收起
  $(".show-more").click(function(){
  var isHide = $(this).parent().parent().find(".material-info").hasClass("block-hide");
  if(isHide){
    $(this).parent().parent().find(".material-info").removeClass("block-hide");
    $(this).parent().parent().find(".material-info").addClass("block-show");
    $(this).text("收起");
  }else{
    $(this).parent().parent().find(".material-info").removeClass("block-show");
    $(this).parent().parent().find(".material-info").addClass("block-hide");
    $(this).text("显示更多");
  }
  $(this).parent().parent().siblings().find(".material-info").removeClass("block-show");
  $(this).parent().parent().siblings().find(".material-info").addClass("block-hide");
  $(this).parent().parent().siblings().find(".show-more").text("显示更多");
  });
  //分享素材页面保存图片
  $(".saveImg").click(function(){
    var imgarr=new Array();
    $(this).parent().parent().parent().find(".img-list").each(function(k,v){
            var img=$(this).attr('src');
            if(img.substring(0,7)=="Uploads" || img.substring(0,7)=="/Upload"){
              var http = window.location.protocol;
              var host = window.location.host;
              realimg=http+"//"+host+"/"+img;
            }else{
              var http = window.location.protocol;
              var host = window.location.host;
              realimg=http+"//"+host+"/"+img
            }
            imgarr[k]=realimg;
        })
      var id = $(this).attr("value");
      var yuansu = $(this);
      //保存图片增加下载数量
      $.post("save_generalizenum",{id:id},function(data){
        yuansu.parent('.f13').prev(".f13").find(".generalize_download_num").text(parseInt(yuansu.parent('.f13').prev(".f13").find(".generalize_download_num").text())+parseInt(data)); 
      },"json");
      console.log(imgarr);
      var u = navigator.userAgent;
      var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
      var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
      if(!isAndroid){
        window.webkit.messageHandlers.savePicture.postMessage(imgarr);
      }else{
        android.savePicture(imgarr);
      }
  });
  //分享素材页面复制文本
  $(".copyArticle").click(function(){
    var id = $(this).attr("value");
    var yuansu = $(this);
    //复制文本增加下载数量
    $.post("save_generalizenum",{id:id},function(data){
      yuansu.parent('.f13').prev(".f13").find(".generalize_download_num").text(parseInt(yuansu.parent('.f13').prev(".f13").find(".generalize_download_num").text())+parseInt(data)); 
    },"json");
    var articleInfo = $(this).parent().parent().parent().find(".material-info").text();
    var u = navigator.userAgent;
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    if(!isAndroid){
      window.webkit.messageHandlers.copyArticle.postMessage(articleInfo);
    }else{
      android.copyArticle(articleInfo);
    }
  });
})
