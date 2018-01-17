<?php
/**
 * @version  CashOutCallBack 套现回调 套现 
 * @authors John(1160608332@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;
use app\index\model\CashOrder;
use app\index\model\Passageway;
use app\index\model\PassagewayItem;
use app\index\model\Cashout;
use think\Request;
use app\api\controller\Commission;
class Cashoutcallback
{

	 /**
	 * @version  米刷套现
	 * @authors bill(755969423@qq.com)
	 * @date    2017-12-23 16:25:05
	 * @version $Bill$
	 */
	 public function mishuaCallBack()
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
        	 if ($resul['status'] == '00' && $order) {
		 	 $order->order_state=2;
		 	 //进行分润
		 	 $fenrun= new \app\api\controller\Commission();
		 	 $fenrun_result=$fenrun->MemberFenRun($order->order_member,$order->order_money,$order->order_passway,1,'套现手续费分润',$order->order_id);
		 	 if($fenrun_result['code']=="200")
             {
 				 $order->order_fen=$fenrun_result['leftmoney'];
                 $order->order_buckle=$passwayitem->item_charges/100;
                 $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passway->passageway_income;
             }
 			else	
            {
 				 $order->order_fen=-1;
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
	 * @version  CashOutCallBack 套现回调 快捷支付0.23回调 
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
		 	 //进行分润
		 	 $fenrun= new \app\api\controller\Commission();
		 	 $fenrun_result=$fenrun->MemberFenRun($order->order_member,$order->order_money,$order->order_passway,1,'套现手续费分润',$order->order_id);
		 	 if($fenrun_result['code']=="200")
             {
 				 $order->order_fen=$fenrun_result['leftmoney'];
                  $order->order_buckle=$passwayitem->item_charges/100;
                 $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passway->passageway_income;
             }
 			else	
            {
 				 $order->order_fen=-1;
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
     * @version  jinyifuCallBack 套现回调 金易付回调 
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
             $fenrun= new \app\api\controller\Commission();
             $fenrun_result=$fenrun->MemberFenRun($order->order_member,$order->order_money,$order->order_passway,1,'套现手续费分润',$order->order_id);
             if($fenrun_result['code']=="200")
             {
                 $order->order_fen=$fenrun_result['leftmoney'];
                 $order->order_buckle=$passwayitem->item_charges/100;
                 $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passway->passageway_income;
             }
            else    
            {
                 $order->order_fen=-1;
            }
             $res = $order->save();
                 if ($resul['qfStatus'] == 'SUCCESS' || $resul['qfStatus'] == 'IN_PROCESS') {
                     //订单更新成功的时候去执行分佣
                     echo 'success';
                     die;
                 }
         } 
     }

}