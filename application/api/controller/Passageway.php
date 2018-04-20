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
 use app\index\model\Member;

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
         #可用支付通道
         #获取会员等级
         $member_group=Member::where(['member_id'=>$this->param['uid']])->value('member_group_id');
         // $passageway_lists=Passageways::with('cashout')->where(['passageway_state'=>1,'passageway_also'=>$this->param['passageway_also']])->order('passageway_sort desc')->select();
         $passageway_lists=db('passageway')->alias('p')
            ->join('cashout c','p.passageway_id=c.cashout_passageway_id')
            ->where('passageway_state',1)
            ->where('passageway_also',$this->param['passageway_also'])
            ->order('passageway_sort desc')
            ->select();
         if($this->param['passageway_also']==2){ 
            foreach ($passageway_lists as $key => $value) {
              $rate=PassagewayItem::where(['item_passageway'=>$value['passageway_id'],'item_group'=>$member_group])->find();
              $passageway[$key]['item_rate']='消费'.$rate->item_also.'%';
              if($rate->item_charges){
                $passageway[$key]['item_rate'].="+".$rate->item_charges/100;
              }
               $passageway[$key]['item_rate'].=' 代付'.$rate->item_qfalso.'%';
              if($rate->item_qffix){
                $passageway[$key]['item_rate'].="+".$rate->item_qffix/100;
              }
              $passageway[$key]['cashout']='最大交易额度：'.$value['cashout_max'].'最小交易额度：'.$value['cashout_min'];
              $passageway[$key]['passageway_id']=$value['passageway_id'];
              $passageway[$key]['passageway_name']=$value['passageway_name'];
              $passageway[$key]['passageway_desc']=$value['passageway_desc'];
              $passageway[$key]['cashout_max']=$value['cashout_max'];
              $passageway[$key]['cashout_min']=$value['cashout_min'];
           }


         }else{
            foreach ($passageway_lists as $key => $value) {
              $rate=PassagewayItem::where(['item_passageway'=>$value['passageway_id'],'item_group'=>$member_group])->find();
              if(empty($rate))continue;
              $passageway[$key]['item_rate']=$rate->item_rate.'%';
              if($rate->item_charges){
                $passageway[$key]['item_rate'].="+".$rate->item_charges/100;
              }
              $passageway[$key]['cashout']='最大交易额度：'.$value['cashout_max'].'最小交易额度：'.$value['cashout_min'];
              $passageway[$key]['passageway_id']=$value['passageway_id'];
              $passageway[$key]['passageway_name']=$value['passageway_name'];
              $passageway[$key]['passageway_desc']=$value['passageway_desc'];
              $passageway[$key]['cashout_max']=$value['cashout_max'];
              $passageway[$key]['cashout_min']=$value['cashout_min'];
           }
         }
         return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$passageway];


      	 // $Passageways=new Passageways;
        //  #可用支付通道
        //  $passageway_lists=$Passageways->where('passageway_state=1')->select();
        //  return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$passageway_lists];
      }
 }
