<?php 
/**
*输出指定时间的log日志
*/
namespace app\index\controller;
use think\Controller;
use think\Request;
class RemoveRuntime extends Controller{
	 public function removeRuntime()
	{
		
	 	 if($request = Request::instance()->post()){
	 	 	$re = $this->delfiles("../runtime/log",$request);
	 	 	echo "<script>alert('共删除".$re."个文件')</script>";
	 	 }
	 	 return view("admin/removeRuntime/removeRuntime");
	}
	 public function delfiles($dir,$n="") //删除DIR路径下N天前创建的所有文件;  
	{  
		if(is_dir($dir))  
		{  
			$num = 0;
			if($dh=opendir($dir))  
			{  
			    while (false !== ($file = readdir($dh)))   
			    {  
				    if($file!="." && $file!="..")   
				    {  
				       $fullpath=$dir."/".$file;  
				       if(is_dir($fullpath))   
				       {              
				        $filedate=date("Y-m-d", filemtime($fullpath));  
				      	
				        $d1=strtotime($n['endTime']);  
				        $d2=strtotime($filedate);  
	
					        if($d2<=$d1) { 
					        	$dh2 = opendir($fullpath);
					        	while(false !== ($file2 = readdir($dh2))){
					        		if($file2 != '.' && $file2 != '..'){
					        			$fullpath2 = $fullpath."/".$file2;
					        			if(unlink($fullpath2)){
					        				$num += 1;
					        			}
						        		
					        		}	
					        	}
					        	rmdir($fullpath);
				       		}
				        }  
				    }        
			    }  
			}  
	 		return $num;
	 	}  
	}
}