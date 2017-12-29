<?php
/**
 * Upload controller / 上传管理控制器
 * @authors GongKe(755969423@qq.com)
 * @date    2017-10-11 18:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use app\index\model\System;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use OSS\OssClient;

#use OSS\Core\OssException;
class Tool
{
    //-------------------------------------------------------
    //文件上传 图片上传(index)
    //-------------------------------------------------------
	public function upload_one($path='other')
	{
		#定义返回消息
		$data = array('code'=>'0', 'msg'=>'没有选择上传文件', 'url'=>'');
		#如果上传有图片
		if($_FILES){
			#循环图片上传属性
			foreach ($_FILES as $key => $value)
				#获取到上传到服务器的路径
				$path=$key;
			#打开这个目录
			$file = Request::instance()->file($path);
			#移动到目录
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . $path);
			#图片上传成功与否返回对应的参数
			$data = $info ? array('code'=>'200', 'msg'=>'上传成功!', 'url'=>"/uploads/".$path.DS.$info->getSaveName()) : array('code'=>'0', 'msg'=>$file->getError(), 'url'=>'');
			#如果开启OSS
			// if(Setting::getName('ossopen'))
			// {
			// 	#初始化OSS 并且传入配置参数
			// 	try {
			// 	    $ossClient = new OssClient(Config::get('OSS.accessKeyId'),Config::get('OSS.accessKeySecret'),Config::get('OSS.endpoint'));
			// 	} catch (OssException $e) {
			// 		#OSS链接失败的时候 返回错误信息
			// 		$data = array('code'=>'0', 'msg'=>$e->getMessage(), 'url'=>'');
			// 	}
			// 	#OSS项目目录
			// 	$bucket = Config::get('OSS.bucket');
			// 	#OSS保存地址
			// 	$object = 'images/'.$path.DS.$info->getSaveName();
			// 	#文件在服务器上的绝对地址
			// 	$file =$info->getRealPath();
			// 	#开始处理文件上传
			// 	try {
			// 		#上传OSS
			// 		$result=$ossClient->uploadFile($bucket, $object, $file);
			// 		$data = array('code'=>'200', 'msg'=>'上传成功', 'url'=>$result['info']['url']);
			// 		#是否开启删除服务器文件开关
			// 		if(Setting::getName('delpic'))
			// 		{
			// 			#移除本地文件
			// 			@unlink($file);
			// 		}
			// 	} catch (OssException $e) {
			// 		#上传失败后返回上传失败的错误信息
			// 		$data = array('code'=>'0', 'msg'=>$e->getMessage(), 'url'=>'');
			// 	}
			// }
			#返回地址
			echo json_encode($data);
		}
	}

	 /**
	 * app上传头像使用
	 * @param  [type] $file [description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	 public function uploads($file,$path="other"){
		 #TODO 后台可配置 上传大小以及各式 和 目录
		 try { 
		 	 #开始上传
	    		 $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS . $path);
	    		 //return $info;
	    		 if($info){ 
	    		 	 #上传成功 上传oss
				 $data = json_encode(['code'=>'200', 'msg'=>'上传成功', 'data'=>['link'=>Request::instance()->domain(). DS . 'uploads'. DS . $path. DS .$info->getSaveName()]]);
				 if(System::getName('ossopen')){
					 $result = $this->upload_oss($info,$path);
					 $data = json_encode(['code'=>'200', 'msg'=>'上传成功', 'data'=>['link'=>$result]]);
				 }
	    		 }else{
	        		 return json_encode(['code'=>'100', 'msg'=>$file->getError(), 'data'=>'']); //上传失败 ;
	    		 }
			 return $data;
		} catch (OssException $e) {
			 #上传失败
			 return json_encode(['code'=>'100', 'msg'=>$e->getMessage(), 'data'=>'']); 
		}
	}

	 /**
	 * 上传至OSS
	 * @return [type] [description]
	 */
	 private function upload_oss($info,$path){
		 $ossClient = new OssClient(System::getName('ossKeyId'),System::getName('ossKeySecret'),System::getName('ossendpoint'));
		 #OSS保存地址
		 $object = 'images/'.$path.DS.$info->getSaveName();							
		 #文件在服务器上的绝对地址
		 $file =$info->getRealPath();							
		 #上传OSS
		 $result=$ossClient->uploadFile(System::getName('ossbucket'), $object, $file); 			
		 #是否开启删除服务器文件开关  #移除本地文件
		 if(Setting::getName('delpic'))
		      @unlink($file);			
		 return $result['info']['url'];
	 }
}
