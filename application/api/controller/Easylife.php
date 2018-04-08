<?php
 namespace app\api\controller;
 use think\Db;
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
 *  @version Huilianjinchuang controller / Api 代还入网
 *  @author 许成成(1015571416@qq.com)
 *   @datetime    2018-02-23 15:13:05
 *   @return 
 */
 class Easylife{
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
 }

