<?php
/**
 * @version  取现接口 套现 
 * @authors Bill(755969423@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use think\Loader;
use think\Config;
use app\index\model\Member;
use app\index\model\System;
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

use app\api\controller\Membernets; //入网
use app\index\model\MemberNet;//入网模型
use app\index\model\SmsCode;

class CashOut
{
	public $error;
	private $member_infos; //会员信息
      private $member_cert;  //实名信息
      private $member_card; //计算卡信息
      private $passway_info; //通道信息
      private $card_info;		//信用卡信息
      private $also;
      function __construct($memberId,$passwayId,$cardid)
      {
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
	 * @authors bill(755969423@qq.com)
	 * @date    2017-12-21 16:03:05
	 * @version $Bill$
	 */
	 public function mishua($tradeNo,$price,$description='银联快捷支付')
	 {
	      $arr = array(
	            'versionNo'   => '1', //版本固定为1
	            'mchNo'       	=> $this->passway_info->passageway_mech, //商户号
	            'price'       	=> $price, //单位为元，精确到0.01,必须大于1元
	            'description' 	=> $description, //交易描述
	            'orderDate'   => date('YmdHis', time()), //订单日期
	            'tradeNo'     	=> $tradeNo, //商户平台内部流水号，请确保唯一 TOdo
	            'notifyUrl'		=>'http://wallet.test.xijiakeji.com/index/Cashoutcallback/mishuaCallBack',
	            // 'notifyUrl'   	=> System::getName('system_url').$this->passway_info->cashout->cashout_callback, //异步通知URL
	            'callbackUrl' 	=> System::getName('system_url').'/api/Userurl/calllback_success',/*HOST . "/index.php?s=/Api/Quckpayment/turnurl"*/ //页面回跳地址
	            'payCardNo' => $this->card_info->card_bankno, //信用卡卡号
	            'accName'    => $this->card_info->card_name, //持卡人姓名 必填
	            'accIdCard'   => $this->card_info->card_idcard, //卡人身份证  必填
	            'bankName'   => $this->member_card->card_bankname, //  结算卡开户行  必填  结算卡开户行
	            'cardNo'      	 => $this->member_card->card_bankno, //算卡卡号 必填  结算卡卡号
	            'downPayFee'  	=> $this->also->item_rate*10, //结算费率  必填  接入机构给商户的费率，D0直清按照此费率结算，千分之X， 精确到0.01
	            'downDrawFee' => '0'//$this->passway_info->cashout->cashout_charges, // 代付费 选填  每笔扣商户额外代付费。不填为不扣。
	      );
	      var_dump($arr);die;
	      //请求体参数加密 AES对称加密 然后连接加密字符串转MD5转为大写
	      $payload =AESencode(json_encode($arr),$this->passway_info->passageway_pwd_key);
	      // var_dump($payload);die;
	      //return $payload;
	      $sign    	= strtoupper(md5($payload.$this->passway_info->passageway_key));
	      $request = array('mchNo' =>$this->passway_info->passageway_mech,'payload' => $payload, 'sign' => $sign);
	      // $res=
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
	      // var_dump($result);die;
	      if ($result['code'] == 0) {
	      	 $datas=AESdecrypt($result['payload'],$this->passway_info->passageway_pwd_key);
	            $datas = trim($datas);
	            $datas = substr($datas, 0, strpos($datas, '}') + 1);
	            $resul = json_decode($datas, true);
	            //写入套现订单
	            $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description,$resul['transNo']);
	      	 if(!$order_result)
	      	 	 return ['code'=>327];
	            return ['code'=>200,'msg'=>'订单获取成功~', 'data'=>['url'=>$resul['tranStr'],'type'=>1, ]];
	      }else{
	      	 if(isset($result['message']))
	      	 return ['msg'=>$result['message'].',下单失败~', 'code'=>400];

	      	 return ['msg'=>'通道维护中,下单失败~', 'code'=>400];

	      }
	 }

	 /**
	 * @version  快捷支付 0.23费率套现 
	 * @authors bill(755969423@qq.com)
	 * @date    2017-12-23 16:25:05
	 * @version $Bill$
	 */
	 public function quickPay023($tradeNo,$price,$description='快捷支付 0.23费率套现')
	 {
	 	 #检测通道是否需要入网
	 	 if($this->passway_info->passageway_status=="1")
	 	 {
	 	 	 #检测用户是否已经入网
		 	 $member_net=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->find();
		 	 #如果该通道需要用户入网 去检查入网信息 如果用户还没有入网 则先进行入网
		 	 if(!$member_net || $member_net[$this->passway_info->passageway_no]=="")
		 	 {
		 	 	 $method=$this->passway_info->passageway_method;
		 	 	 $membernetObject=new Membernets($this->member_infos->member_id, $this->passway_info->passageway_id);
		 	 	 $member_net_result=$membernetObject->$method();
		 	 	 if($member_net_result['respCode']!="00" || $member_net_result['merchno']=="")
		 	 	 	 return ['code'=>462, 'msg'=>$member_net_result['message']];
		 	 }	 
	 	 }
	 	 #获取用户入网信息
	 	 $member_net=MemberNet::where('net_member_id',$this->member_infos->member_id)->find();
	 	 $version="v1.2";//接口版本号  目前固定
		 $arr = array(
	            'version'		=> $version, //版本固定为
	            'merchno'	=> $member_net[$this->passway_info->passageway_no], //商户号
	            'traceno'		=> $tradeNo,//网站订单号 确保在网站的唯一
	            'amount'       	=> $price, //单位为元，精确到0.01,必须大于1元
	            'accountno'	=> $this->card_info->card_bankno,//结算卡号
	            'accountName'	=> $this->card_info->card_name,//结算户名 URLEncode编码
	            'cardType'	=> 1, //1：信用卡，2：储蓄卡
	            'validDate'	=> $this->card_info->card_expireDate,//使用信用卡时必填，需要信用卡4位有效日期(格式：MMYY)。
	            'safeCode'	=> $this->card_info->card_Ident,//使用信用卡时必填，需要卡背面的3位安全码
	            'mobile'		=> $this->card_info->card_phone,//银行卡对应的手机号
	            'certno'		=> $this->card_info->card_idcard,//银行卡对应的身份证号
	            'bankCode'	=> '123',//银行卡对应的银行编码
	            'bankName'	=> $this->card_info->card_bankname,//银行卡对应的银行名称。采用URLEncode编码
	            'settleType'	=> 3,//固定值2-T+1结算
	            'notifyUrl'		=> System::getName('system_url').$this->passway_info->cashout->cashout_callback,//支付完成后将支付结果回调至该链接
	            'returnUrl'		=> System::getName('system_url').'/api/Userurl/calllback_success',//支付完成后前端跳转地址
	            //'signature'	=> ,//对签名数据进行MD5加密的结果。参见3.1
	      );
 	      $param=get_signature($arr,$this->passway_info->passageway_key);
           $result=curl_post($this->passway_info->cashout->cashout_url,'post',$param,'Content-Type: application/x-www-form-urlencoded; charset=gbk');
           $data=json_decode(mb_convert_encoding($result, 'utf-8', 'GBK,UTF-8,ASCII'),true);
 		 if ($data['respCode'] == 00) {
	           $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description,$data['traceno']);//写入套现订单
	      	 if(!$order_result)
	      	 	 return ['code'=>327];
	           return ['code'=>200,'msg'=>'订单获取成功~' , 'data'=>['url'=>$data['barCode'],'type'=>2]];
	      }else{
	      	 return ['code'=>400, 'msg'=>$data['message'].',套现失败~'];
	      }
	 }

	 /**
	 * @version  快捷支付 5万封顶 
	 * @authors bill(755969423@qq.com)
	 * @date    2017-12-23 16:25:05
	 * @version $Bill$
	 */
	 public function quickPay5()
	 {

	 }


	 /**
	 * @version  荣邦快捷支付
	 * @authors bill(755969423@qq.com)
	 * @date    2017-12-23 16:25:05
	 * @version $Bill$
	 */
	 public function rongbangcash($tradeNo,$price,$description='快捷支付')
	 {
	 	// 初始化类
 	 	 $membernetObject=new Membernets($this->member_infos->member_id, $this->passway_info->passageway_id);
	 	 #检测通道是否需要入网
	 	 if($this->passway_info->passageway_status=="1")
	 	 {
	 	 	 #检测用户是否已经入网
		 	 $member_net=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->find();
		 	 #如果该通道需要用户入网 去检查入网信息 如果用户还没有入网 则先进行入网
		 	 if(!$member_net || $member_net[$this->passway_info->passageway_no]=="")
		 	 {
		 	 	 $method=$this->passway_info->passageway_method;
		 	 	 $res=$membernetObject->$method();
		 	 	 if($res!==true)
		 	 	 	 return  ['code'=>462,'msg'=>$res]; //入网失败
		 	 }else{
		 	 	// $userinfo=$member_net[$this->passway_info->passageway_no];
		 	 }
	 	 }
	 	 //快捷支付 调用开通快捷支付接口
	 	 if($this->passway_info->passageway_mech==402512992){
		 	 //复用查询条件
		 	 $pas_where=['member_credit_pas_pasid'=>$this->passway_info->passageway_id,'member_credit_pas_creditid'=>$this->card_info->card_id];
		 	 #查询用户是否开通快捷支付
		 	 $member_credit_pas=db('member_credit_pas')->where($pas_where)->find();
		 	 //是否需要调用开通快捷支付变量
		 	 $needToOpen=false;
		 	 //从数据库检查是否开通
		 	 if(!$member_credit_pas || !$member_credit_pas['member_credit_pas_info'] || $member_credit_pas['member_credit_pas_status']==0){
		 	 	//通用数据
	 	 		$data=[
	 	 			'member_credit_pas_creditid'=>$this->card_info->card_id,
	 	 			'member_credit_pas_pasid'=>$this->passway_info->passageway_id,
	 	 		];
		 	 	//如果有 treatycode 调用查询接口
		 	 	if(isset($member_credit_pas['member_credit_pas_info']) && $member_credit_pas['member_credit_pas_info']!=1){
			 	 	//调用接口检查是否开通
			 	 	$result=$membernetObject->rongbang_check($member_credit_pas['member_credit_pas_info']);
			 	 	if(is_array($result)){
			 	 		//接口有数据，更新本地数据库 这个用户已经开通了快捷支付
			 	 		$data['member_credit_pas_info']=$result['treatycode'];
			 	 		$data['member_credit_pas_status']=1;
			 	 		if($member_credit_pas){
			 	 			db('member_credit_pas')->where($pas_where)->update($data);
			 	 		}else{
			 	 			db('member_credit_pas')->insert($data);
			 	 		}
			 	 	}else{
					 	 $needToOpen=true;
			 	 	}
		 	 	}else{
				 	 $needToOpen=true;
		 	 	}
		 	 	if($needToOpen){
		 	 		//没有数据，调用开通快捷支付接口
			 	 	$result=$membernetObject->rongbang_openpay($this->card_info->card_id);
			 	 	if(is_string($result))
			 	 		return  ['code'=>500,'msg'=>$result]; 
		 	 		$data['member_credit_pas_info']=$result['treatycode'];
		 	 		//将返回的数据，更新本地数据库
		 	 		if($member_credit_pas){
		 	 			db('member_credit_pas')->where($pas_where)->update($data);
		 	 		}else{
		 	 			db('member_credit_pas')->insert($data);
		 	 		}
	                $res= [
		              	'code'=>200,
		              	'msg'=>'荣邦开通快捷支付接口调用成功',
	                ];
		            //返回了html代码
		            if($result['ishtml']==1){
		            	$res['data']=[
		            		'type'=>2,
		            		'url'=>base64_decode($result['html']),
		            	];
		            }else{
		              //返回我们自己建的html
			            $res['data']=[
	            			'type'=>2,
	            			'url'=>request()->domain() . "/api/Userurl/passway_rongbang_openpay/treatycode/".$result['treatycode']."/smsseq/".$result['smsseq']."/memberId/" . $this->member_infos->member_id . "/passwayId/" . $this->passway_info->passageway_id,
		            	];
		            }
		            return $res;
		 	 	}
		 	 }
		 	 #封顶 调用银行签约接口
	 	 }elseif($this->passway_info->passageway_mech==402573747){
	 	 	#商户入驻
	 	 	$isSign=$membernetObject->rongbang_signquery_card($this->card_info->card_id);
	 	 	#未签约 或签约状态不是 成功
	 	 	if(is_string($isSign) || $isSign['status']!=2){
	 	 		$result=$membernetObject->rongbang_sign_card($this->card_info->card_id);
	 	 		#签约接口成功返回html 是字符串
	 	 		if(is_string($result)){
	 	 			$res=[
		              	'code'=>200,
		              	'msg'=>'荣邦银行签约接口调用成功',
		              	'data'=>[
		            		'type'=>2,
		            		'url'=>$result,
		              	]
	 	 			];
	 	 		}else{
	            	$res=[
		              	'code'=>500,
		              	'msg'=>$result['message'],
	            	];
	 	 		}
	 	 		return $res;
	 	 	}
	 	 	$result=$membernetObject->rongbang_in();
	 	 	if(is_string($result)){
	 	 		return ['code'=>500,'msg'=>$result];
	 	 	}
	 	 }
	 	 //开始调用支付接口
	 	 $result=$membernetObject->rongbang_pay($this->card_info->card_id,$tradeNo,$price,$description);
        // var_dump($result);die;
	 	 if(is_array($result)){
            $res= [
              	'code'=>200,
              	'data'=>'快捷支付订单调用成功',
            ];
	 	 	if($result['ishtml']==2){
	 	 		if($result['sendmessage']===2){
	 	 			//无需短信验证的情况 返回一个成功提示页
	            	$res['data']=[
	            		'type'=>1,
	            		'url'=>request()->domain() . "/api/Userurl/passway_success",
	            	];
	 	 		}else{
	 	 			//需要短信验证的情况 返
	            	$res['data']=[
	            		'type'=>1,
	            		'url'=>request()->domain() . "/api/Userurl/passway_rongbang_pay/ordercode/".$result['ordercode']."/card_id/".$this->card_info->card_id."/memberId/" . $this->member_infos->member_id . "/passwayId/" . $this->passway_info->passageway_id,
	            	];
	 	 		}
	 	 	}else{
	            //返回网页
            	$res['data']=[
            		'type'=>2,
            		'url'=>base64_decode($result['html']),
            	];
	 	 	}
            //写入套现订单
            $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description,$result['ordercode']);
	      	if(!$order_result)
	      	 	return ['code'=>327,'data'=>'无此订单'];
	        return $res;
	 	 }else{
	 	 	return ['code'=>501,'msg'=>$result,'data'=>$result];
	 	 }
	 }


	 /**
	 * @version 金易付取现 
	 * @authors Mr.gao(928791694@qq.com)
	 * @date    2017-12-23 16:25:05
	 * @version $Bill$
	 */
	 public function jinyifu($tradeNo,$price,$description='金易付取现')
	 {
	 	 #检测通道是否需要入网
	 	 if($this->passway_info->passageway_status=="1")
	 	 {
	 	 	 #检测用户是否已经入网
		 	 $member_net=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->find();
		 	 #如果该通道需要用户入网 去检查入网信息 如果用户还没有入网 则先进行入网
		 	 if(!$member_net || $member_net[$this->passway_info->passageway_no]=="")
		 	 {
		 	 	 $method=$this->passway_info->passageway_method;
		 	 	 // var_dump($method);die;
		 	 	 $membernetObject=new Membernets($this->member_infos->member_id, $this->passway_info->passageway_id);
		 	 	 $member_net_result=$membernetObject->$method();
		 	 	 if(!$member_net_result['merchId'])
		 	 	 	return ['code'=>462, 'msg'=>$member_net_result['msg']];
		 	 }	 
	 	 }
	 	 $url=request()->domain() . "/api/Userurl/jinyifu/memberId/".$this->member_infos->member_id."/passagewayId/".$this->passway_info->passageway_id."/cardId/".$this->card_info->card_id."/price/".$price;
	 	 return ['code'=>200,'msg'=>'订单获取成功~', 'data'=>['url'=>$url,'type'=>1, ]];



	 	 // var_dump($this->passway_info->passageway_pwd_key);die;
	 	 // return ['code'=>200,'msg'=>'订单获取成功~' , 'data'=>$this->passway_info];
	 	
	 }

	  #金易付付款界面
	 public function jinyifu_pay($param,$description='金易付取现'){

	 	 #验证码验证规则 读取本手机号最后一条没有使用的验证码 并且在系统设置的有效时间内
           $code_info=SmsCode::where(['sms_send'=>$param['phone'],'sms_log_state'=>1])->whereTime('sms_log_add_time', "-".System::getName('code_timeout').' minutes')->order('sms_log_id','desc')->find();
           if(!$code_info || ($code_info['sms_log_content']!=$param['smsCode']))
                 return ['code'=>404];
           #改变验证码使用状态
           $code_info->sms_log_state=2;
           $result=$code_info->save();
           #验证是否成功
           if(!$result)
                 return ['code'=>404];
	 	 $member_net=MemberNet::where(['net_member_id'=>$param['memberId']])->find();
	 	 $jinyifu=new \app\api\controller\Jinyifu($this->passway_info->passageway_pwd_key);
	 	 $cvn2=$jinyifu->encrypt($this->card_info->card_Ident);
	 	 $expDate=$jinyifu->encrypt($this->card_info->card_expireDate);
		 $arr = array(
	            'branchId'=>$this->passway_info->passageway_mech,// 机构号
	            'jinepay_mid'=>$member_net[$this->passway_info->passageway_no], // 商户号
	            'payamt'=>$param['price'], //交易金额
	            'clientType'=>'web',  //客户端类型
	            'bizType'=>'4301',//业务类型
	            'randomStr'=>make_order(),// 随机串
	            'orderId'=>make_order() ,//商户订单号
	            'notifyUrl'=>System::getName('system_url').$this->passway_info->cashout->cashout_callback, //异步通知URL,  //后台异步通知地址
	            'frontNotifyUrl'=>System::getName('system_url').'/api/Userurl/calllback_success',
	            'lpCertNo'=>$this->card_info->card_idcard, // 持卡人身份证号
	            'accNo'=> $this->card_info->card_bankno, // 银行卡号
	            'phoneNo'=>$this->card_info->card_phone, // 银行预留手机号
	            'lpName'=>$this->card_info->card_name, //持卡人姓名
	            'CVN2'=>$cvn2,
	            'expDate'=>$expDate,
	      );
 	        $arr=SortByASCII($arr);
	        #2签名
	        $sign=jinyifu_getSign($arr,$this->passway_info->passageway_key);
	        // var_dump($sign);die;
	        $arr['sign']=$sign;
	        // echo $sign;die;
	        // var_dump($arr);die;
	        #3参数
	        $params=base64_encode(json_encode($arr));
	        #4请求字符串
	        $urls='https://hydra.scjinepay.com/jk/QpayAction_getQpOrder?params='.urlencode($params);
	        #请求
	        $res=curl_post($urls);
	        // $res=curl_post($urls, 'get', '', $type="Content-Type: application/json; charset=utf-8");

	        $res=json_decode($res,true);
	        	
	        $result=base64_decode($res['params']);
			// return ['code'=>200,'msg'=>'订单获取成功~11' , 'data'=>$result];
	        $data=json_decode($result,true);
	        // return ['code'=>200,'msg'=>'订单获取成功~11' , 'data'=>$data];
	        var_dump($data);die;
 		 if ($data['resCode'] == 00) {
	           $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description,$data['traceno']);//写入套现订单
	      	 if(!$order_result)
	      	 	 return ['code'=>327];
	           return ['code'=>200,'msg'=>'订单获取成功~' , 'data'=>['url'=>$data['barCode'],'type'=>2]];
	      }else{
	      	 return ['code'=>400, 'msg'=>$data['resMsg'].',套现失败~'];
	      }
	 }




	 /**
	 * @version  获取订单成功的时候写入订单数据
	 * @authors bill(755969423@qq.com)
	 * @date    2017-12-23 16:25:05
	 * @version $Bill$
	 */
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
	      	 'order_desc'		=>$desc,
	      	 'order_root'		=>find_root($this->member_infos->member_id)
	      );
	      #1记录为 shangji 有效推荐人
	      $Plan_cation=new \app\api\controller\Planaction();
           $Plan_cation=$Plan_cation->recommend_record($this->member_infos->member_id);
    	 	 $data_result=new CashOrder($data);
    	 	 if($data_result->allowField(true)->save()===false)
    	 	 	 return false;
    	 	 return true;
    	 }
}