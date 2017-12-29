<?php
 /**
 * @authors John(1414210199@qq.com)
 * @date    2017-10-19 14:11:05
 * @version $Bill$
 */
 namespace app\api\controller;

 use think\Config;
 use think\Request;
 use think\Controller;
 use app\index\controller\Tool;

 class Upload
 {
     public $error;
     public function index()
     {
          $file = Request::instance()->file('file');
          //dump($file);
         if(!$file)
              echo json_encode(['code'=>100,'msg'=>'请选择上传图片~','data'=>'']);
        $tool=new Tool();
        $images=$tool->uploads($file, 'avatar');
        echo $images;
    }
 }
