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
 use app\index\model\PassagewayItem;
 use app\index\model\CashOut;


 class Passageway 
 {
      protected $param;
      public $error;
      public function __construct($param)
      {

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
        // $this->param['passageway_also']=1;
         #可用支付通道
         $passageway_lists=Passageways::with('cashout')->where('passageway_state=1 and passageway_also='.$this->param['passageway_also'])->select();
         foreach ($passageway_lists as $key => $value) {
            $passageway[$key]['item_rate']=PassagewayItem::where('item_passageway='.$value['passageway_id'])->order('item_rate asc')->value('item_rate');
            $passageway[$key]['item_rate'].="%";
            $passageway[$key]['cashout']='最大交易额度：'.$value['cashout_max'].'最小交易额度：'.$value['cashout_min'];
            $passageway[$key]['passageway_id']=$value['passageway_id'];
            $passageway[$key]['passageway_name']=$value['passageway_name'];
            $passageway[$key]['passageway_desc']=$value['passageway_desc'];
            $passageway[$key]['cashout_max']=$value['cashout_max'];
            $passageway[$key]['cashout_min']=$value['cashout_min'];
         }

         return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$passageway];


      	 // $Passageways=new Passageways;
        //  #可用支付通道
        //  $passageway_lists=$Passageways->where('passageway_state=1')->select();
        //  return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$passageway_lists];
      }
 }
