<?php
 namespace app\api\payment;
 use think\Db;
 use think\Controller;
 use app\index\model\Member;
 use app\index\model\System;
 use app\index\model\Wallet;
 use app\index\model\WalletLog;
 use app\index\model\MemberGroup;
 use app\index\model\PassagewayItem;
 use app\index\model\MemberRelation;
 use app\index\model\MemberCert;
 use app\index\model\MemberCashcard;
 use app\index\model\Passageway;
 use app\index\model\Generation;
 use app\index\model\GenerationOrder;
 use app\index\model\Reimbur;
 use app\index\model\MemberNet as MemberNets;
 use app\index\model\MemberCreditcard;
 use app\index\model\BankInfo;
 use app\index\model\MemberCreditPas;
 /**
 *  @version Elifepay controller / Api 代还入网
 *  @author 许成成(1015571416@qq.com)
 *   @datetime    2018-04-08 15:13:05
 *   @return 
 */
 class Elifepay{
 	protected $url;
 	protected $priKey;
 	protected $pubKey;
 	public function __construct(){
 		$this->url="https://gw.epayxx.net/mapi/gateway.htm";
 		$this->partner_id='1818001000025664';
 	}
 	public static $materials = [

        ['name' => '营业执照', 'code' => 'LICENSE', 'hasBack' => false],
        ['name' => '组织机构代码证', 'code' => 'ORGANIZATION_CODE', 'hasBack' => false],
        ['name' => '多合一营业执照', 'code' => 'LICENSE_ALL_IN_ONE', 'hasBack' => false],
        ['name' => '法人身份证', 'code' => 'CORP_IDCARD', 'hasBack' => true],
        ['name' => '代理人身份证', 'code' => 'AGENT_IDCARD', 'hasBack' => true],
        ['name' => '委托书', 'code' => 'ATTORNEY', 'hasBack' => false],
        ['name' => '个人身份证', 'code' => 'IDCARD', 'hasBack' => true],
        ['name' => '个人手持身份证', 'code' => 'HEAD_IDCARD', 'hasBack' => false],
        ['name' => '结算银行账户', 'code' => 'SETTLE_BANK_ACCOUNT', 'hasBack' => true],
    ];
 	#1.	商户材料上传
	#2.	商户注册
	#3.	商户结算账户设置
	#4.	商户产品开通
	#5.	商户产品费率修改
	#6.	交易创建
	#7.	交易支付请求
	#8.	交易查询（单笔）
	#9.	服务器异步通知接口
	#10.客户端同步跳转接口
	/**
	 * 上传材料
	 * @return [type] [description]
	 * 个人商户注册，需要上传个人身份证正、反面照片，个人手持身份证照片，结算银行账户正、反面照材料信息。
	 */
	public function merch_upload_material($material_id,$img){
		$img_content=file_get_contents($img);
		if(!$img_content){
			$return['epaypp_merchant_material_upload_response']['result_code']='-1';
			$return['epaypp_merchant_material_upload_response']['sub_msg']='实名照片信息未找到，请联系管理员';
			return $return;
		}
		$data=array(
			"material_id"=>$material_id, //材料编号，需要保证唯一，建议使用out_user_id
		    "type"=>"IDCARD",//材料类型，详见2.2.1材料类型表
		    "index"=>"0",//材料索引，详见2.2.1材料类型表
		    "content"=>base64_encode($img_content)
		);
		// echo json_encode($data);
		// var_dump($data);die;
		$res=$this->request('epaypp.merchant.material.upload',$data);
		// var_dump($res);die;
		return json_decode($res,true);
		// echo $res;die;
	}
	/**
	 * 商户进件
	 * @return [type] [description]
	 * 注：省份编号、城市编号、县/区编号需要按照要求上传，否则后期开通产品时会失败。
	 */
	public function merch_income($out_user_id,$member_infos){
		$data=array(
			'out_user_id'=>$out_user_id,//String	是	商户在合作伙伴系统的唯一编号，必填
			'material_no'=>$out_user_id,//String	是	材料单号，和材料上传接口保持一致
			'merchant_type'=>'PRIVATE_ACCOUNT',//String	是	商户类型，必填 个人：PRIVATE_ACCOUNT  企业：CORPORATE_ACCOUNT  暂时只支持个人
			'merchant_name'=>$member_infos->MemberCert->cert_member_name,//String	是	商户名称，必填。个人名字由个人自己定义，企业必须为企业名称
			'cert_type'=>'IDCARD',//String	是	证件类型，必填。个人身份证、公司营业执照。个人：IDCARD 企业，营业执照：LICENSE；多合一营业执照：LICENSE_ALL_IN_ONE
			'cert_no'=>$member_infos->MemberCert->cert_member_idcard,//String	是	证件号码，必填。个人身份证号、企业营业执照编号
			// 'cert_expiration_time'=>"",//String	否	证件有效时间
			// 'corp_name'=>""	,//String	否	法人姓名，企业必填
			// 'corp_cert_type'=>"",String	否	法人证件类型，企业必填
			// 'corp_cert_no'=>"",//	String	否	法人身份证号，企业必填
			// 'corp_cert_expiration_time'=>"",//String	否	法人证件有效时间
			'contact_name'=>$member_infos->MemberCert->cert_member_name,//String	是	联系人姓名，必填
			'contact_mobile'=>$member_infos->member_mobile,//String	是	联系人手机，必填
			// 'contact_phone'=>"",//String	否	联系人座机
			'contact_email'=>"101551422@qq.com",//String	是	联系人邮箱，必填
			'province'=>"370000",//String	是	省份编号，必填，详见地址编码表
			'city'=>"370900",//String	是	城市编号，必填，详见地址编码表
			'district'=>"370983",//String	是	县/区编号，必填，详见地址编码表
			'address'=>"济南市天桥区济洛路北闸子小区",//String	是	地址，必填
			// 'zip'=>"",//String	否	邮政编码
			// 'memo'=>"",//String	否	备注
		);
		$res=$this->request('epaypp.merchant.register',$data);
		return json_decode($res,true);
	}
	/**
	 *商户结算账户设置
	 * @return [type] [description]
	 */
	public function merch_Settlement_setting($out_user_id,$info){
		$data=array(
			'out_user_id'=>$out_user_id,//String	是	商户在合作伙伴系统的唯一编号，必填
			'bank_account_type'=>'PRIVATE_ACCOUNT',//	String	是	银行账户类型，对公，对私  对公：CORPORATE_ACCOUNT  对私：PRIVATE_ACCOUNT
			'bank_account_no'=>$info->MemberCashcard->card_bankno,//String	是	银行账户号
			'cert_type'=>"IDCARD",//String	是	证件类型 身份证：IDCARD
			'cert_no'=>$info->MemberCashcard->card_idcard,//String	是	证件号码
			'name'=>$info->MemberCashcard->card_name,//String	是	开户姓名
			'mobile'=>$info->MemberCashcard->card_phone,//String	是	银行预留手机号
		);
		// echo json_encode($data);die;
		$res=$this->request('epaypp.merchant.settle.account.set',$data);
		return json_decode($res,true);
	} 
	/**
     * 商户结算账户增加（扫码专用）
     *
     * @param $outUserID
     * @param $name
     * @param $mobile
     * @param $certType
     * @param $certNo
     * @param $bankAccountType
     * @param $bankAccountNo
     * @param $bankAccountFront
     * @param $bankAccountBack
     * @return array
     */
    public static function merchantSettleAccountAdd(){
        $data = [
            'out_user_id' => 'DU5TIG18',
            'name' => $name,
            'mobile' => $mobile,
            'cert_type' => $certType,
            'cert_no' => $certNo,
            'bank_account_type' => $bankAccountType,
            'bank_account_no' => $bankAccountNo,
            'bank_account_front' => $bankAccountFront,
            'bank_account_back' => $bankAccountBack,
        ];
        $return = $this->request('epaypp.merchant.settle.account.add', $data);
        return $return;
    }
	/**
	 * 产品开通 
	 * @return [type] [description]
	 * 无卡带积分航旅-C2	3001	带积分
	 * 无卡带积分百货-B2	3005	带积分
	 * 无卡带积分航旅-C4	3006	带积分
	 * 无卡无积分产品	3002	无积分
	 * 支付宝扫码产品	3003	支付宝用户扫
	 * 微信扫码产品	3004	微信用户扫
	 */
	public function product_open($out_user_id,$product,$rate,$fix){
		$data=array(
			'out_user_id'=>$out_user_id,//String	是	商户在合作伙伴系统的唯一编号，必填
			'product'=>$product,//String	是	产品编号，详见产品表
			'bottom'=>"0",//String	是	保底收费金额，单位：元，目前无效，请设置为0
			'top'=>"0",//String	是	封顶收费金额，单位：元，目前无效，请设置为0
			'fixed'=>"1.5",//String	是	代付手续费，单位：元
			'rate'=>"0.0047",//String	是	费率：0.005，表示0.5%
			// 'uniq_no'=>"",//String	否	此参数目前只对扫码产品生效 结算卡唯一编号，增加结算卡后返回
		);
		// echo json_encode($data);
		$res=$this->request('epaypp.merchant.product.open',$data);
		return json_decode($res,true);
	}
	
	/**
	 * 产品费率修改
	 * @return [type] [description]
	 */
	public function product_rate_update(){
		$data=array(
			'out_user_id'=>"DU5TIG18",//String	是	商户在合作伙伴系统的唯一编号，必填
			'product'=>"3006",//String	是	产品编号，详见产品表
			'bottom'=>"0",//String	是	保底收费金额，单位：元，目前无效，请设置为0
			'top'=>"0",//String	是	封顶收费金额，单位：元，目前无效，请设置为0
			'fixed'=>"1",//String	是	代付手续费，单位：元
			'rate'=>"0.0042",//String	是	费率：0.005，表示0.5%
		);
		// echo json_encode($data);
		$res=$this->request('epaypp.merchant.product.rate.set',$data);
		return json_decode($res,true);
	}
	/**
	 * 银行卡快捷开通
	 * @return [type] [description]
	 */
	public function product_quick_open($out_user_id,$product_id,$card_info,$rate,$fix){
		$data=array(
			'out_user_id'=>$out_user_id,//String	是	商户在合作伙伴系统的唯一编号，必填
			'product'=>$product_id,//String	是	产品编号，详见：3.6.1
			'bank_account_type'=>"PRIVATE_ACCOUNT",//String	是	银行账户类型，对公，对私对公：CORPORATE_ACCOUNT 对私：PRIVATE_ACCOUNT
			'bank_account_no'=>$card_info->card_bankno,//String	是	银行账户号
			'cert_type'=>"IDCARD",//String	是	证件类型 身份证：IDCARD
			'cert_no'=>$card_info->card_idcard,//String	是	证件号码
			'name'=>$card_info->card_name,//String	是	开户姓名
			'mobile'=>$card_info->card_phone,//String	是	银行预留手机号
			'cvn2'=>$card_info->card_Ident,//String	否	cvn2，信用卡必传
			'expired'=>$card_info->card_expireDate,//String	否	过期时间，信用卡必传
		);
		// echo json_encode($data);die;
		$res=$this->request('epaypp.merchant.card.express.pay.open',$data);
		return json_decode($res,true);
		// echo $res;die;
	}
	/**
	 * 创建交易
	 * @return [type] [description]
	 */
	public function order_create($product,$out_user_id,$total_fee,$passageway_name,$out_trade_no){
		$data=array(
			'product'=>$product,//String	是	产品编号，详见：2.5.1
			'out_user_id'=>$out_user_id,//String	是	商户在合作伙伴系统的唯一编号，必填
			'terminal_id'=>"000000",//String	是	终端编号，固定值：000000
			'timeout'=>"600",//String	否	订单支付超时时间，单位：秒（默认为600s）
			'currency'=>"156",//String	是	货币类型，固定值：156
			'total_fee'=>$total_fee,//String	否	支付总额，单位元（和price有一个必填，都填时取total_fee的值）
			'summary'=>$passageway_name,//String	是	交易摘要
			// 'category'=>"",//String	否	商品类目
			// 'good_id'=>"",//String	否	商品编号
			// 'price'=>"",//String	否	商品单价，单位元
			// 'quantity'=>"1",//String	否	商品数量（当total_fee不传时，总价为quantity*price，quantity默认为1）
			// 'memo'=>'',//String	否	备注
			'out_trade_no'=>$out_trade_no,//String	是	商户交易号
			'gmt_out_create'=>date('Y-m-d H:i:s'),//String	是	商户交易创建时间格式：yyyy-MM-dd HH:mm:ss
			// 'gps'=>"",//String	否	经纬度
		);
		// echo json_encode($data);die;
		$res=$this->request('epaypp.trade.create',$data);
		return json_decode($res,true);
	}
	/**
	 * 交易支付请求
	 * @return [type] [description]
	 */
	public function order_pay($card_info){
		$pay_data=array(
			'realName'=>$card_info['card_name'],
			'certNo'=>$card_info['card_idcard'],
			'bankAccountNo'=>$card_info['bankAccountNo'],
			'mobile'=>$card_info['mobile'],
		);
		foreach ($pay_data as $k => $v) {
			$other_params[]=$k.'^'.$v;
		}
		$other_params=implode('|', $other_params);
		$data = [
            'out_trade_no' =>$card_info['out_trade_no'],
            'other_params' => $other_params
        ];
        // echo json_encode($data);
        $return = $this->request('epaypp.wc.trade.pay', $data);
        return json_decode($return,true);
	}
	/**
	 * 快捷支付验证码提交
	 * @return [type] [description]
	 */
	public function order_sms_submit($out_trade_no,$sms,$mobile){
		$data=array(
			"out_trade_no"=>$out_trade_no, //订单号
		    "verify_code"=>$sms,	//验证码
		    "mobile"=>$mobile	//手机号
		);
		// echo json_encode($data);
		$return = $this->request('epaypp.wc.trade.express.verifycode.submit', $data);
        return json_decode($return,true);
	}
	/**
	 * 交易查询
	 * @return [type] [description]
	 */
	public function order_query(){
		$data['out_trade_no']='8QSA4U78';//订单号
		$return = $this->request('epaypp.trade.query', $data);
		echo $return;die;
	}	
	/**
	 * 异步通知
	 * @return [type] [description]
	 */
	public function order_notify(){

	}
	public function order_turn_url(){

	}
	public function request($method,$data){
		$data['partner_id'] = $this->partner_id;
        foreach ($data as $key => $value){
            if(!is_string($value)){
                $data[$key] = ''.$value;
            }
        }
        $bizContent = json_encode($data, JSON_UNESCAPED_UNICODE);
		$params = [];
		$params['partner_id'] =$this->partner_id;
		$params['format'] = 'json';
		$params['charset'] = 'utf-8';
		$params['sign_method'] = 'rsa';
		$params['v'] = '1.1';
		$params['notify_url'] = System::getName('system_url').'/index/Cashoutcallback/elife_notify';
        $params['method'] = $method;
        $params['biz_content'] = $bizContent;
        $params['timestamp'] = date('Y-m-d H:i:s');
        $params['sign'] = $this->signature($params);
        $response =curl_post($this->url,'post',http_build_query($params),'application/x-www-form-urlencoded');
        return  $response;
        // print_r($response);die;
        // if($method == 'epaypp.merchant.material.upload'){
        //     $logData['biz_content'] = json_decode($logData['biz_content'], true);
        //     unset($logData['biz_content']['content']);
        //     $logData['biz_content'] = json_encode($logData['biz_content'], JSON_UNESCAPED_UNICODE);
        // }
	}
	/**
     * 签名
     *
     * @param $params
     * @return string
     */
    public  function signature($params){
        uksort($params, function ($a, $b) {
            return strcasecmp($a, $b);
        });
        $paramStr = "";
        foreach ($params as $key => $value) {
            $paramStr .= $key . $value;
        }
        return $this->merchantPrivateSign($paramStr);
    }
     public  function merchantPrivateSign($data){
        $priKey = file_get_contents('./static/rsakey/elife/prv.pem');
        // $priKey=$this->priKey;
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $encryp_data, $res, OPENSSL_ALGO_SHA1);
        openssl_free_key($res);
        return strtoupper(bin2hex($encryp_data));
    }
    /**
     * 商户公钥校验
     *
     * @param $data
     * @param $sign
     * @return bool
     */
    public  function merchantPublicVerify($data, $sign){
        $pubKey = file_get_contents('./static/rsakey/elife/pub.pem');
        // $pubKey=$this->pubKey;
        $res = openssl_get_publickey($pubKey);
        $result = (bool)openssl_verify($data, hex2bin($sign), $res, OPENSSL_ALGO_SHA1);
        openssl_free_key($res);
        return $result;
    }
    /**
     * 商户公钥校验
     *
     * @param $data
     * @param $sign
     * @return bool
     */
    public  function epayPublicVerify($data, $sign){
        $pubKey = file_get_contents('./static/rsakey/elife/pub.pem');
        // $pubKey=$this->pubKey;
        $res = openssl_get_publickey($pubKey);
        $result = (bool)openssl_verify($data, hex2bin($sign), $res);
        openssl_free_key($res);
        return $result;
    }
    /**
     * 商户私钥加密
     *
     * @param $data
     * @return bool
     */
    public  function merchantPrivateEncrypt($data){
        $pubKey = file_get_contents('./static/rsakey/elife/pub.pem');
        // $pubKey=$this->pubKey;
        $res = openssl_get_privatekey($pubKey);
        openssl_private_encrypt($data, $encryptData, $res);
        openssl_free_key($res);
        return strtoupper(bin2hex($encryptData));
    }
    /**
     * 商户私钥解密
     *
     * @param $data
     * @return bool
     */
    public  function merchantPrivateDecrypt($data){
        $pubKey = file_get_contents('./static/rsakey/elife/prv.pem');
        // $pubKey=$this->pubKey;
        $res = openssl_get_privatekey($pubKey);
        openssl_private_decrypt(hex2bin($data), $decryptData, $res);
        openssl_free_key($res);
        return $decryptData;
    }
    /**
     * 平台公钥解密
     *
     * @param $data
     * @return bool
     */
    public  function epayPublicDecrypt($data){
        $pubKey = file_get_contents('./static/rsakey/elife/pub.pem');
        $res = openssl_get_publickey($pubKey);
        openssl_public_decrypt(hex2bin($data), $decryptData, $res, OPENSSL_PKCS1_PADDING);
        openssl_free_key($res);
        return $decryptData;
    }
     /**
     * 签名
     *
     * @param $params
     * @param $sign
     * @return string
     */
    public  function verifySignature($params, $sign){
        uksort($params, function ($a, $b) {
            return strcasecmp($a, $b);
        });
        $paramStr = "";
        foreach ($params as $key => $value) {
            $paramStr .= $key . $value;
        }
        return $this->epayPublicVerify($paramStr, $sign);
    }
    /**
     * OPENSSL加密
     *
     * @param $plaintext
     * @param $iv
     * @param null $key
     * @return string
     */
    public static function opensslEncrypt($plaintext, $iv, $key = null)
    {
        return bin2hex(openssl_encrypt($plaintext, "aes-128-cbc", $key, OPENSSL_RAW_DATA, $iv));
    }

    /**
     * OPENSSL解密
     *
     * @param $encrypted
     * @param $iv
     * @param null $key
     * @return string
     */
    public static function opensslDecrypt($encrypted, $iv, $key = null)
    {
        return openssl_decrypt($encrypted, "aes-128-cbc", $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
    }
 }

