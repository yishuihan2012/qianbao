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
use app\index\model\MemberRelation;
use app\index\model\MemberGroup;
use app\index\model\MemberNet;
use app\index\model\MemberCert;
use app\index\model\Upgrade;
use think\Request;
use think\Exception;
class Alipaycallback
{

	 public function callback()
	 {
         $data=$_POST;
         // $str = var_export($data,TRUE);
         // file_put_contents('ceshi.txt',$str);
        // var_dump($data);die;
         $Alipay=new \app\index\controller\Alipay();
         $success=$Alipay->callback($data);
         if($success!="SUCCESS")
            echo "FAIL777";

        $order=Upgrade::get(['upgrade_no'=>$data['out_trade_no']]);
        $post['upgrade_member_id']=$order->upgrade_member_id;
        $post['upgrade_money']=$order->upgrade_money;
        $post['upgrade_group_id']=$order->upgrade_group_id;
         // $Alipay=new \app\index\controller\Alipay();total_amount
         // $data['signedStr']=$Alipay->callback($params);

    	 #修改会员等级
    	 $member=Member::where('member_id='.$post['upgrade_member_id'])->update(['member_group_id'=>$post['upgrade_group_id']]);

         $member_info=Member::where('member_id='.$post['upgrade_member_id'])->find();

    	 #修改入网
      //    $member_net=MemberNet::where('net_member_id='.$post['upgrade_member_id'])->find();

    	 // $member=Member::where('member_id='.$post['upgrade_member_id'])->find();

    	 // $passageway=Passageway::where('passageway_status=1 and passageway_also=2')->find();

    	 // #查询费率
    	 // $rate=PassagewayItem::where('item_passageway='.$passageway['passageway_id'].' and item_group='.$post['upgrade_group_id'])->find();

    	 // $member_info=MemberCert::where('cert_member_id='.$post['upgrade_member_id'])->find();

    	 #执行修改入网信息
    	 // $arr=mishuaedit($passageway, $rate, $member_info, $member['member_mobile'], $member_net[$passageway['passageway_no']]);
         // var_dump($arr);die;
    	 // $add_net=MemberNet::where('net_member_id='.$post['upgrade_member_id'])->update($arr);

         
                        
       

         $commission=new \app\api\controller\Commission();
         $commission->MemberCommis($post['upgrade_member_id'],$post['upgrade_money'],'会员付费升级');

         $passageway=Passageway::where(['passageway_state'=>1,'passageway_id'=>['neq','1']])->select();

        foreach ($passageway as $key => $value) {
             $Membernetsedit=new \app\api\controller\Membernetsedit($member_info['member_id'],$value['passageway_id'],'M03','',$member_info['member_mobile']);
             $method=$value['passageway_method'];
             $success=$Membernetsedit->$method();
        }
         echo 111;
	 }

}