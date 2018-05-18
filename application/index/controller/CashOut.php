<?php
/**
 * @version  取现接口 快捷支付 
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
use app\index\model\MemberCreditPas;
use app\api\controller\Membernets; //入网
use app\index\model\MemberNet;//入网模型
use app\index\model\SmsCode;
use app\api\controller\Helibao; //入网
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
	 * @version  米刷 快捷支付 
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
	            'description' 	=> System::getName('sitename').'-'.$this->member_infos->member_mobile, //交易描述
	            'orderDate'   => date('YmdHis', time()), //订单日期
	            'tradeNo'     	=> $tradeNo, //商户平台内部流水号，请确保唯一 TOdo
	            'notifyUrl'   	=> System::getName('system_url').$this->passway_info->cashout->cashout_callback, //异步通知URL
	            'callbackUrl' 	=> System::getName('system_url').'/api/Userurl/calllback_success',/*HOST . "/index.php?s=/Api/Quckpayment/turnurl"*/ //页面回跳地址
	            'payCardNo' => $this->card_info->card_bankno, //信用卡卡号
	            'accName'    => $this->card_info->card_name, //持卡人姓名 必填
	            'accIdCard'   => $this->card_info->card_idcard, //卡人身份证  必填
	            'bankName'   => $this->member_card->card_bankname, //  结算卡开户行  必填  结算卡开户行
	            'cardNo'      	 => $this->member_card->card_bankno, //算卡卡号 必填  结算卡卡号
	            'downPayFee'  	=> $this->also->item_rate*10, //结算费率  必填  接入机构给商户的费率，D0直清按照此费率结算，千分之X， 精确到0.01
	            'downDrawFee' => $this->also->item_charges/100//$this->passway_info->cashout->cashout_charges, // 代付费 选填  每笔扣商户额外代付费。不填为不扣。
	      );
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
	      if ($result['code'] == 0 && $result['payload']) {
	      	 $datas=AESdecrypt($result['payload'],$this->passway_info->passageway_pwd_key);
	            $datas = trim($datas);
	            $datas = substr($datas, 0, strpos($datas, '}') + 1);
	            $resul = json_decode($datas, true);
	            //写入快捷支付订单
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
	 * @version  快捷支付 0.23费率快捷支付 
	 * @authors bill(755969423@qq.com)
	 * @date    2017-12-23 16:25:05
	 * @version $Bill$
	 */
	 public function quickPay023($tradeNo,$price,$description='快捷支付 0.23费率快捷支付')
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
	           $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description,$data['traceno']);//写入快捷支付订单
	      	 if(!$order_result)
	      	 	 return ['code'=>327];
	           return ['code'=>200,'msg'=>'订单获取成功~' , 'data'=>['url'=>$data['barCode'],'type'=>2]];
	      }else{
	      	 return ['code'=>400, 'msg'=>$data['message'].',快捷支付失败~'];
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
	 	#判断是否支持银行
	 	if(in_array($this->card_info->card_bankname, ['招商银行','建设银行']))
	 		return ['code'=>500,'msg'=>'当前通道暂不支持该银行'];
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
	 	 	 	#入驻
		 	 	$result=$membernetObject->rongbang_in();
		 	 	if(is_string($result)){
		 	 		return ['code'=>500,'msg'=>$result];
		 	 	}
		 	 }else{
		 	 	// $userinfo=$member_net[$this->passway_info->passageway_no];
		 	 }
	 	 }
	 	 //快捷支付 调用开通快捷支付接口
	 	 if(in_array($this->passway_info->passageway_mech, [402512992,402512936])){
	 	 // if($this->passway_info->passageway_mech==402512992){
		 	 //复用查询条件
		 	 $pas_where=['member_credit_pas_pasid'=>$this->passway_info->passageway_id,'member_credit_pas_creditid'=>$this->card_info->card_id];
		 	 #查询用户是否开通快捷支付
		 	 $member_credit_pas=db('member_credit_pas')->where($pas_where)->find();
		 	 //是否需要调用开通快捷支付变量
		 	 $needToOpen=false;
		 	 //从数据库检查是否开通 不满足条件调用查询接口
		 	 if(!$member_credit_pas || !$member_credit_pas['member_credit_pas_info'] || $member_credit_pas['member_credit_pas_status']==0){
		 	 	//通用数据
	 	 		$data=[
	 	 			'member_credit_pas_creditid'=>$this->card_info->card_id,
	 	 			'member_credit_pas_pasid'=>$this->passway_info->passageway_id,
	 	 		];
                if($member_credit_pas && $member_credit_pas['member_credit_pas_info']){
                    //调用接口检查是否开通
                    $result=$membernetObject->rongbang_check($member_credit_pas['member_credit_pas_info']);
                    if(is_array($result)){
                        //接口有数据，更新本地数据库 这个用户已经开通了快捷支付
                        $data['member_credit_pas_info']=$result['treatycode'];
                        $data['member_credit_pas_status']=1;
                        db('member_credit_pas')->where($pas_where)->update($data);
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
	                #已开通过协议
	                #根据treatycode 修改对应的信用卡id 若没有 则新增
	                if(isset($result['isopen'])){
	                	#留空 上面会插入一条新的信息
		            //返回了html代码
	                }elseif($result['ishtml']==1){
		            	$res['data']=[
		            		'type'=>2,
		            		'url'=>base64_decode($result['html']),
		            	];
			            return $res;
		            }else{
		              //返回我们自己建的html
			            $res['data']=[
	            			'type'=>1,
	            			'url'=>request()->domain() . "/api/Userurl/passway_rongbang_openpay/treatycode/".$result['treatycode']."/smsseq/".$result['smsseq']."/memberId/" . $this->member_infos->member_id . "/passwayId/" . $this->passway_info->passageway_id,
		            	];
			            return $res;
		            }
		 	 	}
		 	 }
		 	 #修改费率接口 封顶的限制1天1次 并且还没有套餐 先不启用
		 	 $Membernetsedit = new \app\api\controller\Membernetsedit($this->member_infos->member_id, $this->passway_info->passageway_id);
		 	 $res=$Membernetsedit->rongbangnet();
		 	 if(!$res)
		 	 	return ['code'=>500,'data'=>'该费率无对应套餐编码，请联系客服'];

		 	 #封顶 调用银行签约接口
	 	 }elseif($this->passway_info->passageway_mech==402573747){
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
            //写入快捷支付订单
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
	           $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description,$data['traceno']);//写入快捷支付订单
	      	 if(!$order_result)
	      	 	 return ['code'=>327];
	           return ['code'=>200,'msg'=>'订单获取成功~' , 'data'=>['url'=>$data['barCode'],'type'=>2]];
	      }else{
	      	 return ['code'=>400, 'msg'=>$data['resMsg'].',快捷支付失败~'];
	      }
	 }




	  /**
	 * @version H5有积分取现 
	 * @authors Mr.gao(928791694@qq.com)
	 * @date    2017-12-23 16:25:05
	 * @version $Bill$
	 */
	 public function h5youjifen($tradeNo,$price,$description='H5youjifen取现')
	 {
	 	$item_rate=$this->also->item_rate/100;
	 	$item_charges=$this->also->item_charges;
	 	$url= System::getName('system_url').'/api/Userurl/H5youjifen/tradeNo/'.$tradeNo;
	 	$price_po=$price*100;
	 	 $arr= $price_po."|".$this->card_info->card_name."|".$this->card_info->card_idcard."|".$this->member_card->card_bankno."|".$this->card_info->card_phone."|".$this->card_info->card_bankname."|".$this->card_info->card_bankno."|".$this->card_info->card_phone."|".$this->card_info->card_bankname."| |".$url."|".$tradeNo."|".$item_rate."|".$item_charges;
	 	 // echo $arr;die;
	 	 $params['data']=H5encrypt($arr,$this->passway_info->passageway_key);
	 	 $params['channel']=$this->passway_info->passageway_mech;

	 	 $url="http://kjnq.jct8.com/quickpay/Integral";

	 	 $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//设置为POST
		curl_setopt($ch, CURLOPT_POST, 1);
		//把POST的变量加上
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$output = curl_exec($ch);
		curl_close($ch);
		$output=preg_replace('/\/quickpay\//', 'http://kjnq.jct8.com/quickpay/', $output);
		preg_match_all ("/<p class=\"result\">(.*)<\/p>/", $output, $error);
		if(empty($error[1][0])){
			$res=[
            		'type'=>2,
            		'url'=>$output,
            	];
            $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description,$tradeNo);//写入快捷支付订单
            if(!$order_result)
	      	 	 return ['code'=>327];
	           return ['code'=>200,'msg'=>'订单获取成功~' , 'data'=>$res];
		}else{
			return ['code'=>400, 'msg'=>$error[1][0].',快捷支付失败~'];
		}	 	
	 }



	  /**
	 * @version H5无积分通道2 
	 * @authors Mr.gao(928791694@qq.com)
	 * @date    2017-12-23 16:25:05
	 * @version $Bill$
	 */
	 public function h5wujifen2($tradeNo,$price,$description='H5无积分通道2取现')
	 {
	 	$item_rate=$this->also->item_rate/100;
	 	$item_charges=$this->also->item_charges;
	 	$url= System::getName('system_url').'/api/Userurl/H5youjifen/tradeNo/'.$tradeNo;
	 	$price_po=$price*100;
	 	 $arr= $price_po."|".$this->card_info->card_name."|".$this->card_info->card_idcard."|".$this->member_card->card_bankno."|".$this->card_info->card_phone."|".$this->card_info->card_bankno."|".$this->card_info->card_phone."| |".$url."|".$tradeNo."|".$item_rate."|".$item_charges;
	 	 // echo $arr;die;
	 	 $params['data']=H5encrypt($arr,$this->passway_info->passageway_key);
	 	 $params['channel']=$this->passway_info->passageway_mech;

	 	 $url="http://kjnq.jct8.com/quickpay/tmplaceOrder";

	 	 $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//设置为POST
		curl_setopt($ch, CURLOPT_POST, 1);
		//把POST的变量加上
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$output = curl_exec($ch);
		curl_close($ch);
			$res=[
            		'type'=>2,
            		'url'=>$output,
            	];
            $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description,$tradeNo);//写入快捷支付订单
            if(!$order_result)
	      	 	 return ['code'=>327];
	           return ['code'=>200,'msg'=>'订单获取成功~' , 'data'=>$res];
			
	 }




	   /**
	 * @version H5有积分通道2 
	 * @authors Mr.gao(928791694@qq.com)
	 * @date    2017-12-23 16:25:05
	 * @version $Bill$
	 */
	 public function h5youjifen2($tradeNo,$price,$description='H5有积分通道2取现')
	 {
	 	$item_rate=$this->also->item_rate/100;
	 	$item_charges=$this->also->item_charges;
	 	$url= System::getName('system_url').'/api/Userurl/H5youjifen/tradeNo/'.$tradeNo;
	 	$price_po=$price*100;
	 	 $arr= $price_po."|".$this->card_info->card_name."|".$this->card_info->card_idcard."|".$this->member_card->card_bankno."|".$this->card_info->card_phone."|".$this->card_info->card_bankno."|".$this->card_info->card_phone."| |".$url."|".$tradeNo."|".$item_rate."|".$item_charges;
	 	 // echo $arr;die;
	 	 $params['data']=H5encrypt($arr,$this->passway_info->passageway_key);
	 	 $params['channel']=$this->passway_info->passageway_mech;

	 	 $url="http://kjnq.jct8.com/quickpay/travelOrder";

	 	 $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//设置为POST
		curl_setopt($ch, CURLOPT_POST, 1);
		//把POST的变量加上
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$output = curl_exec($ch);
		curl_close($ch);
			$res=[
            		'type'=>2,
            		'url'=>$output,
            	];
            $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description,$tradeNo);//写入快捷支付订单
            if(!$order_result)
	      	 	 return ['code'=>327];
	           return ['code'=>200,'msg'=>'订单获取成功~' , 'data'=>$res];
			
	 }


	 /**
	 * @version  易宝快捷支付   @date    2018-01-29 09:28 AM
	 * @author bill(755969423@qq.com)   @version $Bill$
	 */ 	 
	 public  function yibao($tradeNo,$price,$description='易宝快捷支付')
	 {
	 	 $membernetObject=new \app\api\payment\yibaoPay($this->passway_info->passageway_id, $this->member_infos->member_id);
	 	 #检测通道是否需要入网
	 	 if($this->passway_info->passageway_status=="1")
	 	 {
	 	 	 #检测用户是否已经入网
		 	 $member_net=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->find();
		 	 #如果该通道需要用户入网 去检查入网信息 如果用户还没有入网 则先进行入网 并且设置费率
		 	 if(!$member_net || $member_net[$this->passway_info->passageway_no]=="")
		 	 {
		 	 	 $method=$this->passway_info->passageway_method;
		 	 	 $member_net_result=$membernetObject->$method();
		 	 }
	 	 }
	 	 #查询子商户是否审核 如果未审核 则进行审核
 	 	 $membernetAudit_result=$membernetObject->info($this->member_infos->member_mobile);
 	 	 if($membernetAudit_result['code']!=0000)
 	 	 	 return ['code'=>202, 'msg'=>'审核查询失败', 'data'=>''];
 	 	 //dump($membernetAudit_result['retList'][0]['auditStatus']);die();
 	 	 if($membernetAudit_result['retList'][0]['auditStatus']!=2 && $membernetAudit_result['retList'][0]['auditStatus']!=3) //如果不等于这两个值 则去审核子商户
 	 	 {
 	 	 	 $memberAudit=$membernetObject->usreAudit($this->member_infos->membernet[$this->passway_info->passageway_no]);
 	 	 	 if($memberAudit['code']!=0000)
 	 	 	 	 return ['code'=>203, 'msg'=>'商户审核失败', 'data'=>''];
 	 	 }
 	 	 #查询费率与入网费率是否一致
 	 	 $also=PassagewayItem::where(['item_passageway'=>$this->passway_info->passageway_id, 'item_group'=>$this->member_infos->member_group_id])->value('item_rate');
 	 	 $also=$also/100;
 	 	 $netAlao=$membernetObject->queryFee($this->member_infos->membernet[$this->passway_info->passageway_no]);
 	 	 if($netAlao['code']!=0000)
 	 	 	 return ['code'=>204, 'msg'=>'费率查询失败', 'data'=>''];
 	 	 if($also!=$netAlao['rate'])  //费率不一致则重新设置费率
 	 	 {
 	 	 	 $also_result=$membernetObject->fee($this->member_infos->membernet[$this->passway_info->passageway_no], 1, $also);
 	 	 	 $YourSister=true;
 	 	 	 for ($i=2; $i<=5 ; $i++)
 	 	 	 { 
 	 	 	 	 $MyReayThinkCaoYourMather=$membernetObject->fee($this->member_infos->membernet[$this->passway_info->passageway_no], $i, 0);
 	 	 	 	 if($MyReayThinkCaoYourMather['code']!=0000)
 	 	 	 	 {
 	 	 	 	 	 $YourSister=false;
 	 	 	 	 	 break;
 	 	 	 	 }
 	 	 	 }
 	 	 	 if($also_result['code']!=0000 or $YourSister!=true)
 	 	 	  	 return ['code'=>205,  'msg'=>'费率设置失败', 'data'=>''];	
 	 	 }
 	 	 //前置工作完成 则进行调用收款接口 
 	 	 $memberTrade=$membernetObject->trade($this->member_infos->membernet[$this->passway_info->passageway_no], $price, $tradeNo, $this->card_info->card_bankno);
 	 	 if($memberTrade['code']!=200)
 	 	 	 return ['code'=>$memberTrade['code'], 'msg'=>$memberTrade['msg'], 'data'=>''];
 	 	 $order_result=$this->writeorder($tradeNo, $price, $price*$also ,$description, $memberTrade['data']['data']['requestId']);//写入套现订单
 	 	 return  !$order_result ? ['code'=>327] : ['code'=>200,'msg'=>'订单获取成功~', 'data'=>['url'=>$memberTrade['data']['url'],'type'=>1]];
	 }
	/**
	 * 合利宝扫码付
	 * @return [type] [description]
	 */
	public function hlbsacn(){
		//检测是否入网
		$helibao= new Helibao();
		$res=$helibao->scan_pay();
		
	}
	/**
	 * 易生支付-纯api模式
	 * @return [type] [description]
	 */
	public function elife_pay($tradeNo,$price,$description='银联快捷支付1'){
		$elifepay=new \app\api\payment\Elifepay($this->passway_info->passageway_mech);
		#1判断是否上传资料,看有没有存取子商户号
		$MemberNet=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->find();
		$MemberNet_value=$MemberNet[$this->passway_info->passageway_no];
		$explode=explode (',',$MemberNet_value);
		$product_id='3006';
		// print_r($explode);die;
		if(!$explode || $MemberNet[$this->passway_info->passageway_no]==""){ //商户没有上传资料没生成商户号
			$MemberNet_value=$material_id=generate_password();
			$img=$this->member_infos->memberCert->IdPositiveImgUrl;//身份证正面
			$res=$elifepay->merch_upload_material($material_id,$img);
			if($res['epaypp_merchant_material_upload_response'] && $res['epaypp_merchant_material_upload_response']['result_code']=='00'){
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$material_id]);
				if(!$update){
					return ['code'=>'101','msg'=>'上传资料失败'];
				}
			}else{
				$msg=isset($res['epaypp_merchant_material_upload_response']['sub_msg'])?$res['epaypp_merchant_material_upload_response']['sub_msg']:$res['epaypp_merchant_material_upload_response']['result_code_msg'];
				return ['code'=>'102','msg'=>$msg];
			}
		}else{
			$material_id=$explode[0];
		}
		#2判断是否入网
		if(!isset($explode[1]) || $explode[1]!=1){
			$res=$elifepay->merch_income($material_id,$this->member_infos);
			if($res['epaypp_merchant_register_response'] && $res['epaypp_merchant_register_response']['result_code']=='00'){
				$MemberNet_value=$MemberNet_value.',1';
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$MemberNet_value]);
				if(!$update){
					return ['code'=>'101','msg'=>'商户入网失败'];
				}
			}else{
				$msg=isset($res['epaypp_merchant_register_response']['sub_msg'])?$res['epaypp_merchant_register_response']['sub_msg']:$res['epaypp_merchant_register_response']['result_code_msg'];
				return ['code'=>'102','msg'=>$msg];
			}
		}
		#3判断是否设置结算商户
		if(!isset($explode[2]) || $explode[2]!=1){
			$res=$elifepay->merch_Settlement_setting($material_id,$this->member_infos);
			if($res['epaypp_merchant_settle_account_set_response'] && $res['epaypp_merchant_settle_account_set_response']['result_code']=='00'){
				$MemberNet_value=$MemberNet_value.',1';
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$MemberNet_value]);
				if(!$update){
					return ['code'=>'101','msg'=>'商户设置结算卡失败'];
				}
			}else{
				// var_dump($res);die;
				return ['code'=>'102','msg'=>'商户设置结算卡失败'];
			}
		}
		#4判断当前产品是否开通
		if(!isset($explode[3]) || !is_numeric(strpos($explode[3],$product_id)) ){
			$res=$elifepay->product_open($material_id,$product_id,$this->also->item_rate/100,$this->also->item_charges/100);
			if($res['epaypp_merchant_product_open_response'] && $res['epaypp_merchant_product_open_response']['result_code']=='00'){
				if(!isset($explode[3])){
					$MemberNet_value=$MemberNet_value.','.$product_id;
				}else{
					$MemberNet_value=$MemberNet_value.$product_id;
				}
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$MemberNet_value]);
				if(!$update){
					return ['code'=>'101','msg'=>'产品开通失败'];
				}
			}else{
				$msg=isset($res['epaypp_merchant_product_open_response']['sub_msg'])?$res['epaypp_merchant_product_open_response']['sub_msg']:$res['epaypp_merchant_product_open_response']['result_code_msg'];
				return ['code'=>'102','msg'=>$msg];
			}
		}
		
		#5判断是否需要变更费率 (查询该用户上次刷卡成功的费率，如果和系统不一致，变更)
		$last_order=CashOrder::where(['order_member'=>$this->member_infos->member_id,'order_passway'=>$this->passway_info->passageway_id])->order('order_id desc')->find();
		if($last_order['user_rate']!=$this->also->item_rate || $last_order['user_fix']!=$this->also->item_charges/100){
			$update_rate=$elifepay->product_rate_update($material_id,$product_id,$this->also->item_charges/100,$this->also->item_rate/100);
			if($res['epaypp_merchant_product_rate_set_response'] && $res['epaypp_merchant_product_rate_set_response']['result_code']=='00'){
				
			}else{
				return ['code'=>'102','msg'=>'修改费率失败'];
			}
		}	
		
		#6判断当前银行卡当前产品是否开通快捷
		$memberCreditPas=MemberCreditPas::where(['member_credit_pas_creditid'=> $this->card_info->card_id,'member_credit_pas_pasid'=>$this->passway_info->passageway_id])->find();
		if(!$memberCreditPas || $memberCreditPas['member_credit_pas_status']!=1){
			$res=$elifepay->product_quick_open($material_id,$product_id,$this->card_info,$this->also->item_rate/100,$this->also->item_charges);
			$msg=isset($res['epaypp_merchant_card_express_pay_open_response']['sub_msg'])?$res['epaypp_merchant_card_express_pay_open_response']['sub_msg']:$res['epaypp_merchant_card_express_pay_open_response']['result_code_msg'];
			if($res['epaypp_merchant_card_express_pay_open_response']['success']=='false'){
				return ['code'=>'101','msg'=>$msg];
			}
			if($res['epaypp_merchant_card_express_pay_open_response'] && $res['epaypp_merchant_card_express_pay_open_response']['result_code']=='00'){
				$memberCreditPas=new MemberCreditPas(['member_credit_pas_creditid'=> $this->card_info->card_id,'member_credit_pas_pasid'=>$this->passway_info->passageway_id,'member_credit_pas_status'=>1]);
				$res=$memberCreditPas->save();
				if(!$res){
					return ['code'=>'101','msg'=>'开通快捷支付失败'];
				}
			}else{
				$msg=isset($res['epaypp_merchant_card_express_pay_open_response']['sub_msg'])?$res['epaypp_merchant_card_express_pay_open_response']['sub_msg']:$res['epaypp_merchant_card_express_pay_open_response']['result_code_msg'];
				return ['code'=>'102','msg'=>$msg];
			}
		}
		#预下单 下单完成后返给APP一个静态页面地址
		$out_trade_no=generate_password();
		$des=System::getName('sitename').'-'.$this->member_infos->member_mobile;
		$res=$elifepay->order_create($product_id,$material_id,$price,$des,$out_trade_no);
		if($res['epaypp_trade_create_response'] && $res['epaypp_trade_create_response']['result_code']=='00'){

			$order_result=$this->writeorder($out_trade_no, $price, $price*($this->also->item_rate/100) ,$description);
			$card_bankno=$this->card_info->card_bankno;
			$card_phone=$this->card_info->card_phone;
			$card_idcard=$this->card_info->card_idcard;
			$url=System::getName('system_url').'/api/Userurl/order_pay/passageway_id/'.$this->passway_info->passageway_id.'/card_idcard/'.$card_idcard.'/card_name/'.$this->card_info->card_name.'/card_bankno/'.$card_bankno.'/card_phone/'.$card_phone.'/price/'.$price.'/out_trade_no/'.$out_trade_no;
			return ['code'=>200,'msg'=>'请求成功','data'=>['type'=>1,'url'=>$url]];
			// return redirect('Userurl/order_pay', ['passageway_id' =>$this->passway_info->passageway_id,'card_info'=>$this->card_info,'price'=>$price,'out_trade_no'=>$out_trade_no]);
		}else{
			// var_dump($res);die;
			$msg=isset($res['epaypp_trade_create_response']['sub_msg'])?$res['epaypp_trade_create_response']['sub_msg']:$res['epaypp_trade_create_response']['result_code_msg'];
			return ['code'=>'102','msg'=>$msg];
		}
	}
	/**
	 * 易生支付-收银台模式
	 * @return [type] [description]
	 */
	public function elifepay($tradeNo,$price,$description='银联快捷支付2'){

		$elifepay=new \app\api\payment\Elifepay($this->passway_info->passageway_mech);
		#1判断是否上传资料,看有没有存取子商户号
		$MemberNet=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->find();
		$MemberNet_value=$MemberNet[$this->passway_info->passageway_no];
		$explode=explode (',',$MemberNet_value);
		// print_r($explode);die;
		if(!$explode || $MemberNet[$this->passway_info->passageway_no]=="" || !$explode[0]){ //商户没有上传资料没生成商户号
			$MemberNet_value=$material_id=generate_password(16);
			$img=$this->member_infos->memberCert->IdPositiveImgUrl;//身份证正面
			$res=$elifepay->merch_upload_material($material_id,$img);
			if($res['epaypp_merchant_material_upload_response'] && $res['epaypp_merchant_material_upload_response']['result_code']=='00'){
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$material_id]);
				if(!$update){
					return ['code'=>'101','msg'=>'上传资料失败'];
				}
			}else{
				$msg=isset($res['epaypp_merchant_material_upload_response']['sub_msg'])?$res['epaypp_merchant_material_upload_response']['sub_msg']:$res['epaypp_merchant_material_upload_response']['result_code_msg'];
				return ['code'=>'102','msg'=>$msg];
			}
		}else{
			$material_id=$explode[0];
		}
		#2判断是否入网
		if(!isset($explode[1]) || $explode[1]!=1){
			$res=$elifepay->merch_income($material_id,$this->member_infos);
			if($res['epaypp_merchant_register_response'] && $res['epaypp_merchant_register_response']['result_code']=='00'){
				$MemberNet_value=$MemberNet_value.',1';
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$MemberNet_value]);
				if(!$update){
					return ['code'=>'101','msg'=>'商户入网失败'];
				}
			}else{
				$msg=isset($res['epaypp_merchant_register_response']['sub_msg'])?$res['epaypp_merchant_register_response']['sub_msg']:$res['epaypp_merchant_register_response']['result_code_msg'];
				return ['code'=>'102','msg'=>$msg];
			}
		}
		#3判断是否设置结算商户
		if(!isset($explode[2]) || $explode[2]!=1){
			$res=$elifepay->merch_Settlement_setting($material_id,$this->member_infos);
			if($res['epaypp_merchant_settle_account_set_response'] && $res['epaypp_merchant_settle_account_set_response']['result_code']=='00'){
				$MemberNet_value=$MemberNet_value.',1';
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$MemberNet_value]);
				if(!$update){
					return ['code'=>'101','msg'=>'商户设置结算卡失败'];
				}
			}else{
				$msg=isset($res['epaypp_merchant_settle_account_set_response']['sub_msg'])?$res['epaypp_merchant_settle_account_set_response']['sub_msg']:$res['epaypp_merchant_settle_account_set_response']['result_code_msg'];
				return ['code'=>'102','msg'=>$msg];
			}
		}
		#4判断当前产品是否开通
		$product_id='3007';
		if(!isset($explode[3]) || !is_numeric(strpos($explode[3],$product_id))  ){
			$res=$elifepay->product_open($material_id,$product_id,$this->also->item_rate/100,$this->also->item_charges/100);
			if($res['epaypp_merchant_product_open_response'] && $res['epaypp_merchant_product_open_response']['result_code']=='00'){
				if(!isset($explode[3])){
					$MemberNet_value=$MemberNet_value.','.$product_id;
				}else{
					$MemberNet_value=$MemberNet_value.$product_id;
				}
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$MemberNet_value]);
				if(!$update){
					return ['code'=>'101','msg'=>'产品开通失败'];
				}
			}else{
				// var_dump($res);die;
				return ['code'=>'102','msg'=>'产品开通失败'];
			}
		}
		#5判断是否需要变更费率 (查询该用户上次刷卡成功的费率，如果和系统不一致，变更)
		$last_order=CashOrder::where(['order_member'=>$this->member_infos->member_id,'order_passway'=>$this->passway_info->passageway_id])->order('order_id desc')->find();
		if($last_order['user_rate']!=$this->also->item_rate || $last_order['user_fix']!=$this->also->item_charges/100){
			$update_rate=$elifepay->product_rate_update($material_id,$product_id,$this->also->item_charges/100,$this->also->item_rate/100);
			if($update_rate['epaypp_merchant_product_rate_set_response'] && $update_rate['epaypp_merchant_product_rate_set_response']['result_code']=='00'){
				
			}else{
				return ['code'=>'102','msg'=>'修改费率失败'];
			}
		}	
		
		#预下单 下单完成后返给APP一个链接
		$out_trade_no=generate_password();
		$des=System::getName('sitename').'-'.$this->member_infos->member_mobile;
		$res=$elifepay->order_create($product_id,$material_id,$price,$des,$out_trade_no);
		if($res['epaypp_trade_create_response'] && $res['epaypp_trade_create_response']['result_code']=='00'){
			$order_result=$this->writeorder($out_trade_no, $price, $price*($this->also->item_rate/100) ,$description);
			$data=array(
				'card_name'=>$this->card_info->card_name,
				'card_idcard'=>$this->card_info->card_idcard,
				'bankAccountNo'=>$this->card_info->card_bankno,
				'mobile'=>$this->card_info->card_phone,
	            'out_trade_no' =>$out_trade_no
        	);
			$pay_data=array(
				'realName'=>$this->card_info->card_name,
				'certNo'=>$this->card_info->card_idcard,
				'bankAccountNo'=>$this->card_info->card_bankno,
				'mobile'=>$this->card_info->card_phone,
			);
			$pay=$elifepay->order_pay($data,$pay_data);
			if($pay['epaypp_wc_trade_pay_response'] && $pay['epaypp_wc_trade_pay_response']['result_code']=='00'){
				if($pay['epaypp_wc_trade_pay_response']['return_type']=='URL'){
					return ['code'=>'200','msg'=>'请求成功','data'=>['type'=>1,'url'=>$pay['epaypp_wc_trade_pay_response']['action_url']]];
				}else{
					$url=System::getName('system_url').'/api/Userurl/nohtml/data/'.base64_encode($pay['epaypp_wc_trade_pay_response']['html']);
					return ['code'=>'200','msg'=>$pay['epaypp_wc_trade_pay_response']['html'],'data'=>['type'=>2,'url'=>$pay['epaypp_wc_trade_pay_response']['html']]];
				}
				
			}
		}else{
			// var_dump($res);die;
			$msg=isset($res['epaypp_trade_create_response']['sub_msg'])?$res['epaypp_trade_create_response']['sub_msg']:$res['epaypp_trade_create_response']['result_code_msg'];
			return ['code'=>'102','msg'=>$msg];
		}
	}
	/**
	 * 易生支付-3008航旅模式
	 * @return [type] [description]
	 */
	public function elife_hanglv($tradeNo,$price,$description='银联快捷支付2'){
		$elifepay=new \app\api\payment\Elifepay($this->passway_info->passageway_mech);
		#1判断是否上传资料,看有没有存取子商户号
		$MemberNet=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->find();
		$MemberNet_value=$MemberNet[$this->passway_info->passageway_no];
		$explode=explode (',',$MemberNet_value);
		// print_r($explode);die;
		if(!$explode || $MemberNet[$this->passway_info->passageway_no]=="" || !$explode[0]){ //商户没有上传资料没生成商户号
			$MemberNet_value=$material_id=generate_password(16);
			$img=$this->member_infos->memberCert->IdPositiveImgUrl;//身份证正面
			$res=$elifepay->merch_upload_material($material_id,$img);
			if($res['epaypp_merchant_material_upload_response'] && $res['epaypp_merchant_material_upload_response']['result_code']=='00'){
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$material_id]);
				if(!$update){
					return ['code'=>'101','msg'=>'上传资料失败'];
				}
			}else{
				$msg=isset($res['epaypp_merchant_material_upload_response']['sub_msg'])?$res['epaypp_merchant_material_upload_response']['sub_msg']:$res['epaypp_merchant_material_upload_response']['result_code_msg'];
				return ['code'=>'102','msg'=>$msg];
			}
		}else{
			$material_id=$explode[0];
		}
		#2判断是否入网
		if(!isset($explode[1]) || $explode[1]!=1){
			$res=$elifepay->merch_income($material_id,$this->member_infos);
			if($res['epaypp_merchant_register_response'] && $res['epaypp_merchant_register_response']['result_code']=='00'){
				$MemberNet_value=$MemberNet_value.',1';
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$MemberNet_value]);
				if(!$update){
					return ['code'=>'101','msg'=>'商户入网失败'];
				}
			}else{
				$msg=isset($res['epaypp_merchant_register_response']['sub_msg'])?$res['epaypp_merchant_register_response']['sub_msg']:$res['epaypp_merchant_register_response']['result_code_msg'];
				return ['code'=>'102','msg'=>$msg];
			}
		}
		#3判断是否设置结算商户
		if(!isset($explode[2]) || $explode[2]!=1){
			$res=$elifepay->merch_Settlement_setting($material_id,$this->member_infos);
			if($res['epaypp_merchant_settle_account_set_response'] && $res['epaypp_merchant_settle_account_set_response']['result_code']=='00'){
				$MemberNet_value=$MemberNet_value.',1';
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$MemberNet_value]);
				if(!$update){
					return ['code'=>'101','msg'=>'商户设置结算卡失败'];
				}
			}else{
				$msg=isset($res['epaypp_merchant_settle_account_set_response']['sub_msg'])?$res['epaypp_merchant_settle_account_set_response']['sub_msg']:$res['epaypp_merchant_settle_account_set_response']['result_code_msg'];
				return ['code'=>'102','msg'=>$msg];
			}
		}
		#4判断当前产品是否开通
		$product_id='3008';
		if(!isset($explode[3]) || !is_numeric(strpos($explode[3],$product_id))  ){
			$res=$elifepay->product_open($material_id,$product_id,$this->also->item_rate/100,$this->also->item_charges/100);
			if($res['epaypp_merchant_product_open_response'] && $res['epaypp_merchant_product_open_response']['result_code']=='00'){
				if(!isset($explode[3])){
					$MemberNet_value=$MemberNet_value.','.$product_id;
				}else{
					$MemberNet_value=$MemberNet_value.$product_id;
				}
				$update=MemberNet::where(['net_member_id'=>$this->member_infos->member_id])->update([$this->passway_info->passageway_no=>$MemberNet_value]);
				if(!$update){
					return ['code'=>'101','msg'=>'产品开通失败'];
				}
			}else{
				// var_dump($res);die;
				return ['code'=>'102','msg'=>'产品开通失败'];
			}
		}
		#5判断是否需要变更费率 (查询该用户上次刷卡成功的费率，如果和系统不一致，变更)
		$last_order=CashOrder::where(['order_member'=>$this->member_infos->member_id,'order_passway'=>$this->passway_info->passageway_id])->order('order_id desc')->find();
		if($last_order['user_rate']!=$this->also->item_rate || $last_order['user_fix']!=$this->also->item_charges/100){
			$update_rate=$elifepay->product_rate_update($material_id,$product_id,$this->also->item_charges/100,$this->also->item_rate/100);
			if($update_rate['epaypp_merchant_product_rate_set_response'] && $update_rate['epaypp_merchant_product_rate_set_response']['result_code']=='00'){
				
			}else{
				return ['code'=>'102','msg'=>'修改费率失败'];
			}
		}	
		
		#预下单 下单完成后返给APP一个链接
		$out_trade_no=generate_password();
		$des=System::getName('sitename').'-'.$this->member_infos->member_mobile;
		$res=$elifepay->order_create($product_id,$material_id,$price,$des,$out_trade_no);
	    
	    if($res['epaypp_trade_create_response'] && $res['epaypp_trade_create_response']['result_code']=='00'){

			$order_result=$this->writeorder($out_trade_no, $price, $price*($this->also->item_rate/100) ,$description);
			$card_bankno=$this->card_info->card_bankno;
			$card_phone=$this->card_info->card_phone;
			$card_idcard=$this->card_info->card_idcard;
			$url=System::getName('system_url').'/api/Userurl/order_pay/passageway_id/'.$this->passway_info->passageway_id.'/card_idcard/'.$card_idcard.'/card_name/'.$this->card_info->card_name.'/card_bankno/'.$card_bankno.'/card_phone/'.$card_phone.'/price/'.$price.'/out_trade_no/'.$out_trade_no.'/cvn2/'.$this->card_info['card_Ident'].'/expired/'.$this->card_info['card_expireDate'];
			return ['code'=>'200','msg'=>'请求成功','data'=>['type'=>1,'url'=>$url]];
			// return redirect('Userurl/order_pay', ['passageway_id' =>$this->passway_info->passageway_id,'card_info'=>$this->card_info,'price'=>$price,'out_trade_no'=>$out_trade_no]);
		}else{
			// var_dump($res);die;
			$msg=isset($res['epaypp_trade_create_response']['sub_msg'])?$res['epaypp_trade_create_response']['sub_msg']:$res['epaypp_trade_create_response']['result_code_msg'];
			return ['code'=>'102','msg'=>$msg];
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
	 	 $info = PassagewayItem::where(['item_passageway'=>$this->passway_info->passageway_id,'item_group'=>$this->member_infos->member_group_id])->find();
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
	      	 'order_passway_profit' =>$price*$this->passway_info->passageway_rate/100,
	      	 'order_platform' =>$charge-($price*$this->passway_info->passageway_rate/100),
	      	 // 'order_root'		=>find_root($this->member_infos->member_id)
	      	 'passageway_rate' => $this->passway_info->passageway_rate,
	      	 'passageway_fix' => $this->passway_info->passageway_income,
	      	 'user_rate' => $info['item_rate'],
	      	 'user_fix' => $info['item_charges']/100,
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