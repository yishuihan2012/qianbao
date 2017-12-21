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
use app\index\controller\CashOut;
use think\Request;
class CashOutCallBack
{

	 public function mishuaCallBack()
	 {
	 	 $request= \think\Request::instance();
	 	 $action=$request->controller();
	 	 $str=$action."/mishuaCallBack";
	 	 #去查找回调函数路径为这个的设置
	 	 $passwayinfo=CashOut::where(['cashout_callback'=>['like','%'.$str.'%']])->find();
	 	 if(!$passwayinfo)
	 	 	 die('找不到回调地址');
	 	 $passway=Passageway::get($passwayinfo['cashout_passageway_id']);
	 	 if(!$passway)
	 	 	 die('找不到通道');
       	 // 获取传过来参数
        	 $data = file_get_contents("php://input");
        	 $data = trim($data);
        	 // file_put_contents('filecontent.txt',$data);
        	 $data = json_decode($data, true);
        	 // file_put_contents('success.txt',$data['state']);
        	 //回调详细信息 解密
    	 	 $localIV="0102030405060708";
        	 #Open module
        	 $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $localIV); 
        	 mcrypt_generic_init($module, $passway->passageway_pwd_key, $localIV);
        	 $encryptedData = base64_decode($data['payload']);
        	 $encryptedData = mdecrypt_generic($module, $encryptedData);
        	 $info = $encryptedData;
        	 // file_put_contents('datas1.txt',$info);
        	 $datas = trim($info);
        	 $datas = substr($datas, 0, strpos($datas, '}') + 1);
        	 file_put_contents('datas2.txt', $datas);
        	 //返回结果
        	 $resul = json_decode($datas, true);
        	 //订单详情
        	 $order                = CashOrder::where(array('order_thead_no' => $resul['transNo']))->find();
	       $pay_card             = $order->order_creditcard;
	       $get_card             = $order->order_card;
	       $user_id              = $order->order_member;
	       $where['card_number'] = $pay_card;
	       $where['user_id']     = $user_id;
	       $param['card_number'] = $get_card;
	       $param['user_id']     = $user_id;
        	 //00代表成功
        	 if ($resul['status'] == '00' && $order) {
            	 //更新订单
            	$update['order_state'] = 2;
            	 $res = CashOrder::where(array('order_thead_no' => $resul['transNo']))->save($update);
            	 if ($resul['qfStatus'] == 'SUCCESS' || $resul['qfStatus'] == 'IN_PROCESS') {
	                 /*if($res===false)
	                    	 $this->subProfit($order);*/
	                 /* $this->PayRewards($order['qp_uid']);*/
                	 echo 'success';die;
            	 }
       	 } 
	 }

}