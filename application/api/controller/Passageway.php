<?php
 /**
 *  @version Passageway controller / Api 获取支付通道
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-15 9:01:05
 *   @return 
 */
 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use app\index\model\Passageway as Passageways;


 class Passageway 
 {
      protected $param;
      public $error;
      public function __construct($param)
      {
         var_dump(123);die;
        	 $this->param=$param;
      }

      /**
 	 *  @version passageway_lists method / Api 支付通道 通用接口
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-15 09:03:05
 	 *  @param 
      **/ 
      public function passageway_lists()
      {
         var_dump(123);die;
      	 // $Passageways=new Passageways;
        //  #可用支付通道
        //  $passageway_lists=$Passageways->where('passageway_state=1')->select();
        //  return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$passageway_lists];
      }


 }
