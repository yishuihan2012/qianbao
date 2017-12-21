<?php
/**
 * @version  取现接口 套现 
 * @authors John(1160608332@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use think\Loader;
use think\Config;
use app\index\model\Member;
use app\index\model\MemberCert;
use app\index\model\MemberCashcard;
use app\index\model\Passageway;
use app\index\model\MemberCreditcard;
use app\index\model\CashOrder;
use app\index\model\PassagewayItem;

use app\index\model\Order as Orders;
use app\index\model\Wallet as Wallets;
use app\index\model\Withdraw as Withdraws;
use app\index\model\CallbackLog as CallbackLogs;

class CashOut
{
	public $error;
	private $member_infos; //会员信息
      private $member_cert;  //实名信息
      private $member_card; //计算卡信息
      private $passway_info; //通道信息
      private $card_info;		//信用卡信息
      private $also;
      function __construct($memberId,$passwayId,$cardid){
      	 try{
	      	 #根据memberId获取会员信息和会员的实名认证信息还有会员银行卡信息
	      	 $member_info=Member::get($memberId);
	      	 if(!$member_info)
	      	 	 $this->error=314;
	      	 if($member_info->member_cert!='1')
	      	 	 $this->error=356;
	      	 #验证账号状态是否异常
	      	 if($member_info->memberLogin->login_state!='1')
	      	 	 $this->error=305;
	      	 $member_cert=MemberCert::get(['cert_member_id'=>$memberId]);
	      	 if(!$member_cert)
	      	 	 $this->error=367;
	      	 #获取用户结算卡信息
	      	 $member_cashcard=MemberCashcard::get(['card_member_id'=>$memberId]);
	      	 if(!$member_cashcard)
	      	 	 $this->error=459;
	      	 #获取通道信息
	      	 $passageway=Passageway::get($passwayId);
	      	 if(!$passageway)
	      	 	 $this->error=454;
	           if($passageway->cashout->cashout_open!='1')
	                  $this->error=455;
	           #获取信息卡信息
	           $creditcard=MemberCreditcard::get($cardid);
	           if(!$creditcard)
	           	 $this->error=442;
	           if($creditcard->card_member_id!=$memberId || $creditcard->card_name!=$member_cert->cert_member_name)
	           	 $this->error=461;
	           #取得当前会员组在该通道的取现费率
	           $member_also=PassagewayItem::get(['item_passageway'=>$passwayId,'item_group'=>$member_info->member_group_id]);
	           if(!$member_also)
	                 $this->error=458;
	            $this->member_infos=$member_info;
	      	 $this->member_cert=$member_cert;
	      	 $this->member_card=$member_cashcard;
	      	 $this->passway_info=$passageway;
	      	 $this->card_info	=$creditcard;
	      	 $this->also=$member_also;
           }catch (\Exception $e) {
                 $this->error=460;
           }
      }  
	 /**
	 * @version  米刷 套现 
	 * @authors John(1160608332@qq.com)
	 * @date    2017-09-29 16:03:05
	 * @version $Bill$
	 */
	 public function mishua($tradeNo,$price,$description='米刷测试')
	 {
	 	 $versionNo='1';//米刷版本号 , 值固定为1
	 	 //return $this->passway_info->passageway_mech;
	      $arr = array(
	            'versionNo'   => $versionNo, //版本固定为1
	            'mchNo'       	=> $this->passway_info->passageway_mech, //商户号
	            'price'       	=> $price, //单位为元，精确到0.01,必须大于1元
	            'description' 	=> $description, //交易描述
	            'orderDate'   => date('YmdHis', time()), //订单日期
	            'tradeNo'     	=> $tradeNo, //商户平台内部流水号，请确保唯一 TOdo
	            'notifyUrl'   	=> $this->passway_info->cashout->cashout_callback/*HOST . "/index.php?s=/Api/Quckpayment/qucikPayCallBack"*/, //异步通知URL
	            'callbackUrl' 	=>'123'/*HOST . "/index.php?s=/Api/Quckpayment/turnurl"*/, //页面回跳地址
	            'payCardNo' => $this->card_info->card_bankno, //信用卡卡号
	            'accName'    => $this->card_info->card_name, //持卡人姓名 必填
	            'accIdCard'   => $this->card_info->card_idcard, //卡人身份证  必填
	            'bankName'   => $this->member_card->card_bankname, //  结算卡开户行  必填  结算卡开户行
	            'cardNo'      	 => $this->member_card->card_bankno, //算卡卡号 必填  结算卡卡号
	            'downPayFee'  	=> $this->also->item_rate*10, //结算费率  必填  接入机构给商户的费率，D0直清按照此费率结算，千分之X， 精确到0.01
	            'downDrawFee' => '0', // 代付费 选填  每笔扣商户额外代付费。不填为不扣。
	      );

	      //请求体参数加密 AES对称加密 然后连接加密字符串转MD5转为大写
	      $payload = $this->encrypt(json_encode($arr),$this->passway_info->passageway_pwd_key);
	      //return $payload;
	      $sign    	= strtoupper(md5($payload.$this->passway_info->passageway_key));
	      $request = array('mchNo' =>$this->passway_info->passageway_mech,'payload' => $payload, 'sign' => $sign,);
	      $ch = curl_init();
	      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8"));
	      curl_setopt($ch, CURLOPT_URL, $this->passway_info->cashout->cashout_url);
	      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper('post'));
	      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	      curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
	      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	      $res = curl_exec($ch);
	      $result = json_decode($res, true);
	      if ($result['code'] == 0) {
	      	 $datas=$this->decrypt($result['payload'],$this->passway_info->passageway_pwd_key);
	            $datas = trim($datas);
	            $datas = substr($datas, 0, strpos($datas, '}') + 1);
	            $resul = json_decode($datas, true);
	            //写入套现订单
	            $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description='米刷测试',$resul['transNo']);
	      	 if(!$order_result)
	      	 	 return ['code'=>327];
	             return $resul['tranStr'];
	      }else{
	      	return $result;
	      }

	 }

	 //AES对称加密 $locallV加密偏移量
    	 public function encrypt($encryptStr,$encryptKey='') {
    	 	 $localIV="0102030405060708";
        	 #Open module
        	 $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $localIV);
        	 mcrypt_generic_init($module, $encryptKey, $localIV);
        	 #Padding
        	 $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        	 $pad = $block - (strlen($encryptStr) % $block); //Compute how many characters need to pad
        	 $encryptStr .= str_repeat(chr($pad), $pad); // After pad, the str length must be equal to block or its integer multiples
        	 #encrypt
        	 $encrypted = mcrypt_generic($module, $encryptStr);
        	 #Close
        	 mcrypt_generic_deinit($module);
        	 mcrypt_module_close($module);
        	 return base64_encode($encrypted);
    	 }
    	 //AES对称解密 $locallV加密偏移量
    	 public function decrypt($encryptStr,$encryptKey='') {
    	 	 $localIV="0102030405060708";
        	 #Open module
        	 $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $localIV); 
        	 mcrypt_generic_init($module, $encryptKey, $localIV);
        	 $encryptedData = base64_decode($encryptStr);
        	 $encryptedData = mdecrypt_generic($module, $encryptedData);
        	 return $encryptedData;
    	 }
    	 //写入订单
    	 public function writeorder($tradeNo, $price, $charge, $desc, $order_thead='')
    	 {
	      $data=array(
	      	 'order_no'=>$tradeNo,
	      	 'order_thead_no'=>$order_thead,
	      	 'order_member' =>$this->member_infos->member_id,
	      	 'order_passway'=>$this->passway_info->passageway_id,
	      	 'order_money'	=>$price,
	      	 'order_charge'	=>$charge,//手续费
	      	 'order_also'		=>$this->also->item_rate,
	      	 'order_idcard'	=>$this->card_info->card_idcard,
	      	 'order_name'		=>$this->card_info->card_name,
	      	 'order_creditcard'=>$this->card_info->card_bankno,
	      	 'order_card'		=>$this->member_card->card_bankno,
	      	 'order_state'		=>1,
	      	 'order_desc'		=>$desc
	      );
    	 	 $data_result=new CashOrder($data);
    	 	 if($data_result->allowField(true)->save()===false)
    	 	 	 return false;
    	 	 return true;
    	 }
}