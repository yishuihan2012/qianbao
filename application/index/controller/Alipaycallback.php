<?php
/**
 * @version  AlipayCallBack 支付宝付费升级回调 
 * @authors John(1160608332@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;
use app\index\model\CashOrder;
use app\index\model\Passageway;
use app\index\model\PassagewayItem;
use app\index\model\Cashout;
use app\index\model\Member;
use app\index\model\MemberNet;
use app\index\model\MemberRelation;
use app\index\model\MemberGroup;
use app\index\model\MemberCert;
use app\index\model\Upgrade;
use app\index\model\Commission;
use think\Request;
use think\Exception;
class Alipaycallback
{

     public function callback()
     {
        // return "SUCCESS";
         $data=$_POST;
         $Alipay=new \app\index\controller\Alipay();
         $success=$Alipay->callback($data);
         if($success!="SUCCESS"){
            echo "FAIL";
            die();
         }
         $order=Upgrade::get(['upgrade_no'=>$data['out_trade_no']]);
         $post['upgrade_member_id']=$order->upgrade_member_id;
         $post['upgrade_money']=$order->upgrade_money;
         $post['upgrade_group_id']=$order->upgrade_group_id;
         #修改会员等级
         $member=Member::where('member_id='.$post['upgrade_member_id'])->update(['member_group_id'=>$post['upgrade_group_id']]);

         $member_info=Member::where('member_id='.$post['upgrade_member_id'])->find();

         $Commission_info=Commission::where(['commission_from'=>$order->upgrade_id,'commission_type'=>2])->find();
          if(!$Commission_info){
             #执行分佣
             $commission=new \app\api\controller\Commission();
             $commission->MemberCommis($post['upgrade_member_id'],$post['upgrade_money'],'会员付费升级',$order->upgrade_id);
         }

          #修改会员通道费率
         #查询出必须入网的通道 
         $passageway=Passageway::where(['passageway_status'=>1,'passageway_state'=>1])->select();
         foreach ($passageway as $key => $value) {
             $membernet=MemberNet::where(['net_member_id'=>$order->upgrade_member_id])->find();
             if(empty($membernet[$value['passageway_no']]))continue;

             $Membernetsedit=new \app\api\controller\Membernetsedit($member_info['member_id'],$value['passageway_id'],'M03','',$member_info['member_mobile']);
             $method=$value['passageway_method'];
             $success=$Membernetsedit->$method();
         }
         echo "SUCCESS";die;
     }

}