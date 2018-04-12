<?php
/**
 * @version  CashOutCallBack 快捷支付回调 快捷支付 
 * @authors John(1160608332@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;
use think\Db;
use app\index\model\CashOrder;
use app\index\model\Passageway;
use app\index\model\PassagewayItem;
use app\index\model\Cashout;
use think\Request;
use app\api\controller\Commission;
use app\index\model\Commission as Commissions;
use app\index\model\Member;
class Cashoutcallback
{

     /**
     * @version  米刷快捷支付
     * @authors bill(755969423@qq.com)
     * @date    2017-12-23 16:25:05
     * @edit   xuchengcheng 2018-01-22 
     * @version $Bill$
     */
     public function mishuaCallBack()
     {         
         $data = file_get_contents("php://input");
         $data = trim($data);
         $data = json_decode($data, true);
         //回调详细信息 解密
         $localIV="0102030405060708";
         if($data['code']==0 && $data['mchNo']){
            $passway=Passageway::get(['passageway_mech'=>$data['mchNo']]);
         }else{
            die('找不到通道');
         }
         // 获取传过来参数
             #Open module
             $info=AESdecrypt($data['payload'],$passway->passageway_pwd_key,$localIV);
             $datas = trim($info);
             $datas = substr($datas, 0, strpos($datas, '}') + 1);
             //返回结果
             $resul = json_decode($datas, true);
             // file_put_contents('datas3.txt',$resul);
             //订单详情
             $order   = CashOrder::where(array('order_thead_no' => $resul['transNo']))->find();

             $member=Member::get($order->order_member);
             #通道费率
              $passwayitem=PassagewayItem::get(['item_group'=>$member->member_group_id,'item_passageway'=>$passway->passageway_id]);
              // var_dump($passwayitem);die;
             //00代表成功
             if ($resul['status'] == '00' && $order) {
             $order->order_state=2;
             //进行分润
             //判断之前有没有分润过
             $Commission_info=Commissions::where(['commission_from'=>$order->order_id,'commission_type'=>1])->find();
             if(!$Commission_info){
                    $fenrun= new \app\api\controller\Commission();
                    $fenrun_result=$fenrun->MemberFenRun($order->order_member,$order->order_money,$order->order_passway,1,'快捷支付手续费分润',$order->order_id);
             }else{
                $fenrun_result['code']=-1;
             }


             if($fenrun_result['code']=="200")
             {
                 $order->order_fen=$fenrun_result['leftmoney'];
                 $order->order_buckle=$passwayitem->item_charges/100;
                 $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges/100-$passway->passageway_income;
             }
            else    
            {
                 $order->order_fen=-1;
                 $order->order_buckle=$passwayitem->item_charges/100;
                 $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges/100-$passway->passageway_income;
            }
             $res = $order->save();
                 if ($resul['qfStatus'] == 'SUCCESS' || $resul['qfStatus'] == 'IN_PROCESS') {
                     //订单更新成功的时候去执行分佣
                     echo 'success';
                     die;
                 }
         } 
     }

     /**
     * @version  CashOutCallBack 快捷支付回调 快捷支付0.23回调 
     * @authors John(1160608332@qq.com)
     * @date    2017-09-29 16:03:05
     * @version $Bill$
     */
     public function quick023callback()
     {
         $data = file_get_contents("php://input");
         $data = trim($data);
         // file_put_contents('datas1.txt', $data);
             // file_put_contents('filecontent.txt',$data);
             $data = json_decode($data, true);
             // file_put_contents('success.txt',$data['state']);
             //回调详细信息 解密
             $localIV="0102030405060708";
         $request= Request::instance();
         $action=$request->controller();
         $str=$action."/mishuaCallBack";
         #去查找回调函数路径为这个的设置
         $passwayinfo=Cashout::where('cashout_callback','like','%'.$str.'%')->find();
         if(!$passwayinfo)
             die('找不到回调地址');
         $passway=Passageway::get($passwayinfo['cashout_passageway_id']);
         if(!$passway)
             die('找不到通道');
         // 获取传过来参数
             #Open module
             $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $localIV); 
             mcrypt_generic_init($module, $passway->passageway_pwd_key, $localIV);
             $encryptedData = base64_decode($data['payload']);
             $encryptedData = mdecrypt_generic($module, $encryptedData);
             $info = $encryptedData;
             // file_put_contents('datas1.txt',$info);
             $datas = trim($info);
             $datas = substr($datas, 0, strpos($datas, '}') + 1);
             //file_put_contents('datas2.txt', $datas);
             //返回结果
             $resul = json_decode($datas, true);
             //file_put_contents('datas3.txt',$resul);
             //订单详情
             $order   = CashOrder::where(array('order_thead_no' => $resul['transNo']))->find();

             #通道费率
              $passwayitem=PassagewayItem::get(['item_group'=>$order->member,'item_passageway'=>$passway->passageway_id]);
             //00代表成功
             if ($resul['status'] == '2' && $order) {
             $order->order_state=2;

              $Commission_info=Commissions::where(['commission_from'=>$order->order_id,'commission_type'=>1])->find();
             //进行分润
             if(!$Commission_info){
                 $fenrun= new \app\api\controller\Commission();
                 $fenrun_result=$fenrun->MemberFenRun($order->order_member,$order->order_money,$order->order_passway,1,'快捷支付手续费分润',$order->order_id);
             }else{
                $fenrun_result['code']=-1;
             }



             if($fenrun_result['code']=="200")
             {
                 $order->order_fen=$fenrun_result['leftmoney'];
                 $order->order_buckle=$passwayitem->item_charges/100;
                 $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges-$passway->passageway_income;
             }
            else    
            {
                 $order->order_fen=-1;
                 $order->order_buckle=$passwayitem->item_charges/100;
                 $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges-$passway->passageway_income;
            }
             $res = $order->save();
                 if ($resul['qfStatus'] == 'SUCCESS' || $resul['qfStatus'] == 'IN_PROCESS') {
                     //订单更新成功的时候去执行分佣
                     echo 'success';
                     die;
                 }
         } 
     }



      /**
     * @version  jinyifuCallBack 快捷支付回调 金易付回调 
     * @authors John(1160608332@qq.com)
     * @date    2017-09-29 16:03:05
     * @version $Bill$
     */
     public function jinyifucallback()
     {
         $data = file_get_contents("php://input");
         $data = trim($data);
         // file_put_contents('datas1.txt', $data);
             // file_put_contents('filecontent.txt',$data);
             $data = json_decode($data, true);
             // file_put_contents('success.txt',$data['state']);
             //回调详细信息 解密
             $localIV="0102030405060708";
         $request= Request::instance();
         $action=$request->controller();
         $str=$action."/mishuaCallBack";
         #去查找回调函数路径为这个的设置
         $passwayinfo=Cashout::where('cashout_callback','like','%'.$str.'%')->find();
         if(!$passwayinfo)
             die('找不到回调地址');
         $passway=Passageway::get($passwayinfo['cashout_passageway_id']);
         if(!$passway)
             die('找不到通道');
         // 获取传过来参数
             #Open module
             $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $localIV); 
             mcrypt_generic_init($module, $passway->passageway_pwd_key, $localIV);
             $encryptedData = base64_decode($data['payload']);
             $encryptedData = mdecrypt_generic($module, $encryptedData);
             $info = $encryptedData;
             // file_put_contents('datas1.txt',$info);
             $datas = trim($info);
             $datas = substr($datas, 0, strpos($datas, '}') + 1);
             //file_put_contents('datas2.txt', $datas);
             //返回结果
             $resul = json_decode($datas, true);
             //file_put_contents('datas3.txt',$resul);
             //订单详情
             $order   = CashOrder::where(array('order_thead_no' => $resul['transNo']))->find();

              #通道费率
              $passwayitem=PassagewayItem::get(['item_group'=>$order->member,'item_passageway'=>$passway->passageway_id]);
             //00代表成功
             if ($resul['status'] == '2' && $order) {
             $order->order_state=2;
             //进行分润
              $Commission_info=Commissions::where(['commission_from'=>$order->order_id,'commission_type'=>1])->find();
             //进行分润
             if(!$Commission_info){
                 $fenrun= new \app\api\controller\Commission();
                 $fenrun_result=$fenrun->MemberFenRun($order->order_member,$order->order_money,$order->order_passway,1,'快捷支付手续费分润',$order->order_id);
             }else{
                $fenrun_result['code']=-1;
             }

             if($fenrun_result['code']=="200")
             {
                 $order->order_fen=$fenrun_result['leftmoney'];
                 $order->order_buckle=$passwayitem->item_charges/100;
                 $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges-$passway->passageway_income;
             }
            else    
            {
                 $order->order_fen=-1;
                 $order->order_buckle=$passwayitem->item_charges/100;
                 $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges-$passway->passageway_income;
            }
             $res = $order->save();
                 if ($resul['qfStatus'] == 'SUCCESS' || $resul['qfStatus'] == 'IN_PROCESS') {
                     //订单更新成功的时候去执行分佣
                     echo 'success';
                     die;
                 }
         } 
     }

      /**
       * @version 易宝支付回调     @method yibaoCallBack
       * @author   Bill 755969423@qq.com    @datetime  2018-01-31 13:09
       **/
       public function yibaoCallBack()
       {
           $data = file_get_contents("php://input");
           parse_str($data,$result);
           $order=CashOrder::where(['order_thead_no' => $result['requestId']])->find();         #查询到当前订单
           if($order['order_state']==2)
                 return 'SUCCESS';
           if($result['status']!='SUCCESS')
                 return 'Fail';
           $order->order_charge=$result['fee'];
           $order->order_state=2;
           $order->order_desc=$order['order_desc']."消费成功,收款宝订单号:".$result['externalld'];
           $order->save();
           $member=Member::get($order['order_member']);
           $passwayinfo=Passageway::get($order['order_passway']);
           $order_commis=CashOrder::where(['order_thead_no' => $result['requestId']])->find();
           $amount=$order['order_money']-$result['fee']-$order['order_buckle'];
           $membernetObject=new \app\api\payment\yibaoPay($order_commis['order_passway'], $order_commis['order_member']);
           $also_result=$membernetObject->Draw($member->membernet[$passwayinfo['passageway_no']], $amount, "出款".make_order());
           $order_commis->order_outmoney=$amount;
           $order_commis->order_outstatus=$also_result['code']!=0000 ? -1 : 2 ;
           $order_commis->order_outbak=$also_result['message'].",流水号:".$also_result['serialNo'];
           if($also_result['code']!=0000)
           {
                     $order_commis->order_state=1;
                     $order_commis->save();
                     return 'Fail';
           }else{
                     $Commission_info=Commissionz::where(['commission_from'=>$order_commis->order_id,'commission_type'=>1])->find();
                     if($Commission_info)
                     {
                            $order_commis->save();
                            return 'SUCCESS';
                     }
                     $fenrun= new \app\api\controller\Commissions();
                     $fenrun_result=$fenrun->MemberFenRun($order_commis->order_member,$order_commis->order_money,$order_commis->order_passway,1,'快捷支付手续费分润',$order_commis->order_id);
                     $order_commis->order_fen=$fenrun_result['code']==200 ? (string)$fenrun_result['leftmoney'] : -1;
                     $order_commis->order_platform=$order_commis->order_charge-($order_commis->order_money*$passwayinfo->passageway_rate/100)+$passwayinfo->passageway_income;
                     $res = $order_commis->save();
                     return 'SUCCESS';
           }
       }
    /**
     *  易生支付回调
     * @return [type] [description]
     */
    public function elife_notify(){
        $params=Request::instance()->param();
        file_put_contents('elife.txt', json_encode($params));
        // $params=array(
        //     'biz_content'=>'742B70FB3F58045EDAD678CDA1DEE2EEEA9661E0870291622EA4F24639DE8F0A9FE6DFAE02F5FE0BA9603EF1D51F07308ACAF483403A1FC7E7DBC7E93EAE8578B693F6F6E0C76D86295D24D42960AD0C9078B4EC176D3B1B80EB85C55128A7564B83C922CEB7ED4BFAFF35EA9241DC673DAFD5DA20DA391828030537120449F74FC3A8A1DA97D9609A2B36844D0A957084230EE754668AD73D9311E88F7E0B161EBDF8A0EDB4AAFF624A587CF0B67E9009D62CF25BF6829A074E5384FA9FA070423385855427DABD017ACA91AF9444DB',
        //     'sign'=>'2668e090a66ee511e3530c1ac57759d080e5595a342ed21dc7028ac5dad5a9c0d231940020dc8ed6bd423aeed1f8476f437a3b70f89c210f1e29355969ee9decda38ec0792a814268df80b33117fe6625577294dfea91ab2b7e9f65255de5a3b65347380185b422d4b83c8e083d23df1a78bd3f6aedf4ae9f77d117a436f56e40',
        //     'random_key'=>'806b7bf8f348c2272d1598f69f08a09c3c79b3bf01ce26d775cfd3a3d69d77e49abab87275ab6f3840d305c08394f0ba11accd0b8b6ea84fabdcece4cbd4962961f475a3e76b762d548975511fcae6083f0e18deaa9f4cb48c7c19958622123cc6b30f9438e67f54b6098d871698d29be4c3957dec2ff45d1dd7e3cc338eaba11fbbcb2cc876a3e79658157c7df74bb7ae2ce498862eaa6aad57b42a95d5798499527c917f8c35c296e88e565db3847879e4e7c730f722e43c25cfcda9a20dc52fb265f58b735d2d052f9c21391bb0625536e8fb43fe9aeb2308c3e2722d041599bf9d5b53693553566c074e8e79c4e3b5d170c725784c96877cf844331799'
        // );
        $elifepay=new \app\api\payment\Elifepay;
        // 校验签名
        $sign = $params['sign'];
        unset($params['sign']);
        $verifyResult = $elifepay->verifySignature($params, $sign);
        if(!$verifyResult){
            return null;
        }
        // 解密数据
        $randomKey = $elifepay->merchantPrivateDecrypt($params['random_key']);
        $bizContent =$elifepay->opensslDecrypt(hex2bin(strtolower($params['biz_content'])), '16-Bytes--String', $randomKey);
        if(!$bizContent){
            return null;
        }
        // 去掉特殊字符
        $bizContent =  preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', trim($bizContent));
        // 处理业务
        $result = json_decode($bizContent, true);
        file_put_contents('elife_result.txt', json_encode($result));


        $order=CashOrder::where(['order_no' => $result['out_trade_no']])->find();  #查询到当前订单

        $member=Member::get($order->order_member);

        $passway=Passageway::get(['passageway_id'=>$order->order_passway]);

        #通道费率
        $passwayitem=PassagewayItem::get(['item_group'=>$member->member_group_id,'item_passageway'=>$passway->passageway_id]);

        $order->order_thead_no=$result['trade_no'];
         if($result['trade_status']=="TRADE_FINISHED"){//成功
             $order->order_state=2;
             $returnData = 'SUCCESS';
         }
         if($result['trade_status']=="TRADE_CLOSED_BY_SYS"){//超时
             $order->order_state=-2;
              $returnData = 'FAIL';
         }
         if($result['trade_status']=="TRADE_CLOSED"){//关闭
             $order->order_state=-1;
             $returnData='FAIL';
         }
         Db::startTrans();
         if($result['trade_status']=="TRADE_FINISHED"){//成功
             //进行分润
             //判断之前有没有分润过
             $Commission_info=Commissions::where(['commission_from'=>$order->order_id,'commission_type'=>1])->find();
             if(!$Commission_info){
                    $fenrun= new \app\api\controller\Commission();
                    $fenrun_result=$fenrun->MemberFenRun($order->order_member,$order->order_money,$order->order_passway,1,'快捷支付手续费分润',$order->order_id);
             }else{
                $fenrun_result['code']=-1;
             }
        }else{
            $fenrun_result['code']=-1;
        }

         if($fenrun_result['code']=="200")
         {
             $order->order_fen=$fenrun_result['leftmoney'];
             $order->order_buckle=$passwayitem->item_charges/100;
             $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges/100-$passway->passageway_income;
         }
        else    
        {
             $order->order_fen=-1;
             $order->order_buckle=$passwayitem->item_charges/100;
             $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges/100-$passway->passageway_income;
        }
        $res = $order->save();
        if($res){
            Db::commit();
        }else{
            Db::rollback();
            $returnData='FAIL';
        }
        echo  $returnData;die;
    }
}