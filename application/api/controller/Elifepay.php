<?php
 namespace app\api\controller;
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
 		$this->partner_id='1818001000003822';
 		$this->priKey='MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCSGfFw8I+kSnz2hJEiJcVQOpTsOLR2tT+0fvl1YM1duDRsDxppBA88QpM4RH7YYameuBbqxp6Ht2zzJXoZ6EEsVqhskjV4X8/VQYi1dCcqEXI7Tq78L2fy1GcD0fDX1CAKP2bL8KJuu3R5Vd9K6WQLzleQEstMXNxqnwxom1zbY5rKE+ZfbMyPOylsehsk8Lob10w8HvLUSMGIeip23+pQ9GRCeo55Oq6DDmX9NQS0fteb60qW/3zlYVK7fbJTZLbHXBAS2TdoPrKKE3c1tdvxosmu6vfmzTI8AIExvf5s7OSvv9qM49j9XE7nPszIZajRY+jcoCm/8N3XGrMYX67/AgMBAAECggEASu7ta3ymX6AouZNCkN4Idl6ldQacYGoTs3KQZYhxrEjG8klIxWXknoaS1YAkAr0MbzCB6IZYVslYItks5868Zo5Hse/HZubVRM5o3JAnaicqjIqNqyBxUxVnhIkP2tKcYEUmZyETXnHcikLl1JkhzABX3rgU9ySFlFXg2mIc3RRQvWw4SefoF1DH+cGjKh2iHT8eAB0aEot+7vDVt4gaaqyhengP0P3rTQwxS+VKLtldRGCcgvhu586eeZSSMllYrfEoYHI5FINVMFLdoXjNdZhj2QVlXgL7h8Wofs60Z4Q11UPg/+83pt/QGTte9DKOTmR0pCqOVTAA2XMvhG2+YQKBgQDVcPGCHCg+CSF3Wb2aLosRrqcZCNNfea5Mh9j7n5eei/T2fXjTmd/FKxog79K1m7BysIXlOGZcJRJOOSF+yCFbRl326Ar67RqwsmLXt7hWkR2IuvVGQjNTpU8UthKWWqaLcOZVTwxsaN8zLymMzM4RuaxxxLgYBbWY5iCpm5D0kQKBgQCvO6cYx1RaQLnttAKeQYI1fDh/6mSjho0xmrCbsfEpGkw4hFtwFu6MIV+Zz6qR537T8vHOqF49vm5dA7BWjkpJsfsGzsxtXut7lqnKrjRddUCwsXoYzMneoOuIVtgcho2A0K1n760Bb5+MageV0cx4+p4K2zJzsA3JIWozGtbyjwKBgQDAqauGi44TuUA5MItCImMr+eAha+MImpinwjQtpXhCCAl9efLX5lyj6G00b+ZeQgO68vZZ21giMuBcNZuzikj50AG/fuNybxYZi1xHZjICCgmDw2blHZqhFWXVxyfuCjOtSKLRPIJ1VRCsbhTuYGxeeaBcLXsTTAwI0SmIj8D/0QKBgQCPI61FIl4XM1QthaO13lEcm5ITe0YmBd0ELhYhuGMEbkTgzc1bbIAD26caH3Z3pKAHRiab5xDEYvAH7uF2ctjgBhDF6Ns4ZBb7Z4De3RpNVWA4dWEFLROhVdXQExCJjKe+F7fudOvfhmzP6DS1/yCFmkLLH27A7Yj1SORVRpFapQKBgBuSrZhAtqenQd4qG+oTxWdwUSGIVo/WUAJE+T2dLID5pcmw1ER8yUDPNAb4v/bZA6tow+vyrBdslFyXT2+yaCS+KlYoEEdtisGiaUfHKpvQDGlmTRuInV7megvoqeOMD4k8suT0434aU2D3lEPqdQ6jxqiyUZGGFwyoVHWxgPOA';
 		$this->pubKey='MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCOxOjh/1dxibWJumJThn8OkrKgTWMsCpy/5tLQ52oDyahvbLu2e7eNOj4+06clOKJReE7touHsTpNxh7ZCNCUEhRxQbsBF0KELjhaRHs2QGVtI4KDofsFhHG/6zHnNo1RP6jsfBFnZENo3PCbT6O0wdOyS1Yg6vYJJM7LIaiT5gQIDAQAB';
 	}
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
	public function merch_upload_material(){
		$data=array(
			"material_id"=>"1504882816522", //材料编号，需要保证唯一，建议使用out_user_id
		    "type"=>"IDCARD",//材料类型，详见2.2.1材料类型表
		    "index"=>"0",//材料索引，详见2.2.1材料类型表
		    "content"=>"材料内容转换成base64字符串后内容过多，这里省略"
		);
		$res=$this->request('epaypp.merchant.material.upload',$data);
		echo $res;die;
	}
	/**
	 * 商户进件
	 * @return [type] [description]
	 * 注：省份编号、城市编号、县/区编号需要按照要求上传，否则后期开通产品时会失败。
	 */
	public function merch_income(){
		$data=array(
			'out_user_id'=>'',//String	是	商户在合作伙伴系统的唯一编号，必填
			'material_no'=>"",//String	是	材料单号，和材料上传接口保持一致
			'merchant_type'=>'PRIVATE_ACCOUNT',//String	是	商户类型，必填 个人：PRIVATE_ACCOUNT  企业：CORPORATE_ACCOUNT  暂时只支持个人
			'merchant_name'=>'',//String	是	商户名称，必填。个人名字由个人自己定义，企业必须为企业名称
			'cert_type'=>'IDCARD',//String	是	证件类型，必填。个人身份证、公司营业执照。个人：IDCARD 企业，营业执照：LICENSE；多合一营业执照：LICENSE_ALL_IN_ONE
			'cert_no'=>'',//String	是	证件号码，必填。个人身份证号、企业营业执照编号
			// 'cert_expiration_time'=>"",//String	否	证件有效时间
			// 'corp_name'=>""	,//String	否	法人姓名，企业必填
			// 'corp_cert_type'=>"",String	否	法人证件类型，企业必填
			// 'corp_cert_no'=>"",//	String	否	法人身份证号，企业必填
			// 'corp_cert_expiration_time'=>"",//String	否	法人证件有效时间
			'contact_name'=>"",//String	是	联系人姓名，必填
			'contact_mobile'=>"",//String	是	联系人手机，必填
			// 'contact_phone'=>"",//String	否	联系人座机
			'contact_email'=>"",//String	是	联系人邮箱，必填
			'province'=>"",//String	是	省份编号，必填，详见地址编码表
			'city'=>"",//String	是	城市编号，必填，详见地址编码表
			'district'=>"",//String	是	县/区编号，必填，详见地址编码表
			'address'=>"",//String	是	地址，必填
			// 'zip'=>"",//String	否	邮政编码
			// 'memo'=>"",//String	否	备注
		);
		$res=curl_post('url','post',$data);
		echo $res;die;
	}
	/**
	 *商户结算账户设置
	 * @return [type] [description]
	 */
	public function merch_Settlement_setting(){
		$data=array(
			'out_user_id'=>"",//String	是	商户在合作伙伴系统的唯一编号，必填
			'bank_account_type'=>'PRIVATE_ACCOUNT',//	String	是	银行账户类型，对公，对私  对公：CORPORATE_ACCOUNT  对私：PRIVATE_ACCOUNT
			'bank_account_no'=>"",//String	是	银行账户号
			'cert_type'=>"IDCARD",//String	是	证件类型 身份证：IDCARD
			'cert_no'=>"",//String	是	证件号码
			'name'=>"",//String	是	开户姓名
			'mobile'=>"",//String	是	银行预留手机号
		);
	} 
	/**
	 * 产品开通 
	 * @return [type] [description]
	 */
	public function product_open(){
		$data=array(
			'out_user_id'=>'',//String	是	商户在合作伙伴系统的唯一编号，必填
			'product'=>"",//String	是	产品编号，详见产品表
			'bottom'=>"",//String	是	保底收费金额，单位：元，目前无效，请设置为0
			'top'=>"",//String	是	封顶收费金额，单位：元，目前无效，请设置为0
			'fixed'=>"",//String	是	代付手续费，单位：元
			'rate'=>"",//String	是	费率：0.005，表示0.5%
			// 'uniq_no'=>"",//String	否	此参数目前只对扫码产品生效 结算卡唯一编号，增加结算卡后返回
		);
	}
	/**
	 * 产品费率修改
	 * @return [type] [description]
	 */
	public function product_rate_update(){
		$data=array(
			'out_user_id'=>"",//String	是	商户在合作伙伴系统的唯一编号，必填
			'product'=>"",//String	是	产品编号，详见产品表
			'bottom'=>"",//String	是	保底收费金额，单位：元，目前无效，请设置为0
			'top'=>"",//String	是	封顶收费金额，单位：元，目前无效，请设置为0
			'fixed'=>"",//String	是	代付手续费，单位：元
			'rate'=>"",//String	是	费率：0.005，表示0.5%
		);
	}
	/**
	 * 产品快捷开通
	 * @return [type] [description]
	 */
	public function product_quick_open(){
		$data=array(
			'out_user_id'=>"",//String	是	商户在合作伙伴系统的唯一编号，必填
			'product'=>'',//String	是	产品编号，详见：3.6.1
			'bank_account_type'=>"PRIVATE_ACCOUNT",//String	是	银行账户类型，对公，对私对公：CORPORATE_ACCOUNT 对私：PRIVATE_ACCOUNT
			'bank_account_no'=>"",//String	是	银行账户号
			'cert_type'=>"IDCARD",//String	是	证件类型 身份证：IDCARD
			'cert_no'=>"",//String	是	证件号码
			'name'=>"",//String	是	开户姓名
			'mobile'=>"",//String	是	银行预留手机号
			'cvn2'=>"",//String	否	cvn2，信用卡必传
			'expired'=>"",//String	否	过期时间，信用卡必传
		);
	}
	/**
	 * 创建交易
	 * @return [type] [description]
	 */
	public function order_create(){
		$dat=array(
			'product'=>"",//String	是	产品编号，详见：2.5.1
			'out_user_id'=>"",//String	是	商户在合作伙伴系统的唯一编号，必填
			'terminal_id'=>"",//String	是	终端编号，固定值：000000
			'timeout'=>"",//String	否	订单支付超时时间，单位：秒（默认为600s）
			'currency'=>"",//String	是	货币类型，固定值：156
			'total_fee'=>"",//String	否	支付总额，单位元（和price有一个必填，都填时取total_fee的值）
			'summary'=>"",//String	是	交易摘要
			'category'=>"",//String	否	商品类目
			'good_id'=>"",//String	否	商品编号
			'price'=>"",//String	否	商品单价，单位元
			'quantity'=>"1",//String	否	商品数量（当total_fee不传时，总价为quantity*price，quantity默认为1）
			'memo'=>'',//String	否	备注
			'out_trade_no'=>"",//String	是	商户交易号
			'gmt_out_create'=>"",//String	是	商户交易创建时间格式：yyyy-MM-dd HH:mm:ss
			// 'gps'=>"",//String	否	经纬度
		);
	}
	/**
	 * 交易支付请求
	 * @return [type] [description]
	 */
	public function order_pay(){
		$pay_data=array(
			'realName'=>"持卡人姓名",
			'certNo'=>"持卡人证件号",
			'bankAccountNo'=>"银行卡卡号",
			'mobile'=>"银行卡预留手机号",
		);
		foreach ($pay_data as $k => $v) {
			$other_params[]=$k.'^'.$v;
		}
		$other_params=implode('|', $other_params);
		echo $other_params;die;
		$data=array(
			'out_trade_no'=>"商户交易号",
			'other_params'=>$other_params,
		);
	}
	/**
	 * 快捷支付验证码提交
	 * @return [type] [description]
	 */
	public function order_sms_submit(){
		$data=array(
			"out_trade_no"=>"1511838018298", //订单号
		    "verify_code"=>"154818",	//验证码
		    "mobile"=>"18666666666"	//手机号
		);
	}
	/**
	 * 交易查询
	 * @return [type] [description]
	 */
	public function order_query(){
		$data['out_trade_no']='';//订单号
		
	}	
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
		$params['notify_url'] = '异步通知地址';
        $params['method'] = $method;
        $params['biz_content'] = $bizContent;
        $params['timestamp'] = date('Y-m-d H:i:s');
        $params['sign'] = $this->signature($params);
        $response =curl_post($this->url,'post',http_build_query($params));
        echo $response;die;
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
    public static function signature($params){
        uksort($params, function ($a, $b) {
            return strcasecmp($a, $b);
        });
        $paramStr = "";
        foreach ($params as $key => $value) {
            $paramStr .= $key . $value;
        }
        return $this->merchantPrivateSign($paramStr);
    }
     public static function merchantPrivateSign($data){
        // $priKey = file_get_contents(dirname(__FILE__).'/../../../../key/epay_merchant_private_key.pem');
        $priKey=$this->priKey;
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
    public static function merchantPublicVerify($data, $sign){
        // $pubKey = file_get_contents(dirname(__FILE__).'/../../../../key/epay_merchant_public_key.pem');
        $pubKey=$this->pubKey;
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
    public static function epayPublicVerify($data, $sign){
        // $pubKey = file_get_contents(dirname(__FILE__).'/../../../../key/epay_public_key.pem');
        $pubKey=$this->pubKey;
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
    public static function merchantPrivateEncrypt($data){
        // $pubKey = file_get_contents(dirname(__FILE__).'/../../../../key/epay_merchant_private_key.pem');
        $pubKey=$this->pubKey;
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
    public static function merchantPrivateDecrypt($data){
        // $pubKey = file_get_contents(dirname(__FILE__).'/../../../../key/epay_merchant_private_key.pem');
        $pubKey=$this->pubKey;
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
    public static function epayPublicDecrypt($data){
        $pubKey = file_get_contents(dirname(__FILE__).'/../../../../key/epay_public_key.pem');
        $res = openssl_get_publickey($pubKey);
        openssl_public_decrypt(hex2bin($data), $decryptData, $res, OPENSSL_PKCS1_PADDING);
        openssl_free_key($res);
        return $decryptData;
    }
 }

