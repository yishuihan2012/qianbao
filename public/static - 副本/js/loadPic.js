//图片大小自适应容器
function loadPIc(obj){
  imgWidth=obj.width;  
  imgHeight=obj.height; 
  var maxWidth=$(".material-pic-container").width();
  var maxHeight=$(".material-pic-container").height(); 
  AutoResizeImage(imgWidth,imgHeight,maxWidth,maxHeight,obj);  
}  
function AutoResizeImage(imgWidth,imgHeight,maxWidth,maxHeight,objImg){  
  var hRatio;  
  var wRatio;  
  var Ratio = 1;  
  wRatio = maxWidth / imgWidth;  
  hRatio = maxHeight / imgHeight; 
  Ratio = (wRatio>=hRatio?wRatio:hRatio);  
  imgWidth = imgWidth * Ratio;  
  imgHeight = imgHeight * Ratio;  
  if(imgWidth > imgHeight){  
    var pad=(imgWidth-maxWidth)/2;  
    $(objImg).css("margin-left",-pad+"px");  
  }else if(imgHeight > maxHeight){  
    var pad=(imgHeight-maxHeight)/2;  
    $(objImg).css("margin-top",-pad+"px");  
  }    
	$(objImg).css('width',imgWidth);  
	$(objImg).css('height',imgHeight);  
}
 window.onload=function(){  
  var imgs = document.getElementsByTagName("img");
  for(var j=0;j<imgs.length;j++){ 
    if(imgs[j].className=='small'){  
      loadPIc(imgs[j]);  
    }else if(imgs[j].className=='middle'){  
      loadPIc(imgs[j]);  
    }else if(imgs[j].className=='big'){        
      loadPIc(imgs[j]);  
    }  
  }  
}   