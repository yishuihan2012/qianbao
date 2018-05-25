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
class Yilian{
	protected $version;
	protected $merch;
	protected $secret;
	protected $url;
	public function __construct(){
		$this->version='1.0.0';
		$this->mech='M20180523092105';
		$this->secret='56BEC1E0F22405044240D6D523661349';
		$this->url='http://47.98.52.127/scpay/shortcut/';
	}
	public function pay($member_infos,$member_cert,$member_card,$card_info,$also,$ord_amount,$ord_no){
		$ord_amount=$ord_amount*100;
		$pay_family_name=$member_cert['cert_member_name'];
		$payee_id_card=$pay_id_card=$member_cert['cert_member_idcard'];
		$pay_bank_no=$card_info['card_bankno'];
		$pay_mobile=$card_info['card_phone'];
		$payee_bank_nm=$member_card['card_bankname'];
		// 获取联行号 
		$bank_name=mb_substr($payee_bank_nm,-4,2);
        // echo $bank_name;die;
        $BankInfo=BankInfo::where('info_sortname','like','%'.$bank_name.'%')->find();

		$payee_bank_id=isset($BankInfo['info_union'])?$BankInfo['info_union']:'403100000004';
		$payee_bank_no=$member_card['card_bankno'];
		$payee_mobile=$member_card['card_phone'];
		$payee_family_name=$member_card['card_name'];
		$rate_t0=$also['item_rate'];
		$counter_fee_t0=$also['item_charges'];
		//operation_fee=trans_amount*(rate_t0/100)
		$operation_fee=ceil($ord_amount*($rate_t0/100));
		// echo $operation_fee;
		// echo "</br>";
		$pay_amount=$ord_amount-$operation_fee-$counter_fee_t0;
		// echo $pay_amount;die;
		$data=array(
			'merc_no'=>$this->mech,//商户号	string 32	Y		
			'version'=>$this->version,//版本号	String 20	Y		默认1.0.0
			'ord_no'=>$ord_no,//商户订单号	String 32	Y		
			'is_encypt'=>"N",//是否加密	String	Y		重要字段是否RSA加密。Y：RSA加密需要加密的字段;N：不加密
			'req_time'=>date('YmdHis',time()),//订单时间	string 14	Y		商户订单时间格式：YYYYMMDD24HHMMSS
			'req_date'=>date('Ymd',time()),//订单日期	string 8	Y		YYYYMMDD
			'ord_amount'=>(string)$ord_amount,//订单金额	string <=8	Y		单位(分)
			'pay_family_name'=>$pay_family_name,//支付者姓名	string <=32	Y	Y	如果需同名校验，录入姓名必须和入网时真实姓名一致
			'pay_id_card'=>$pay_id_card,//支付者身份证号	string <=32	Y	Y	15位或18位身份证号；如果需同名校验，录入身份证号必须和入网时身份证号一致
			'pay_bank_no'=>$pay_bank_no,//	支付者卡号	string<=32	Y	Y	支付银行卡号
			'pay_mobile'=>$pay_mobile,//	支付者手机号	string <=32	Y	Y	支付银行卡预留手机号
			'payee_bank_nm'=>$payee_bank_nm,//	结算银行名称	String	Y		见附录
			'payee_bank_id'=>$payee_bank_id,//	结算银行联行号	string <=32	Y		见附录
			'payee_bank_no'=>$payee_bank_no,//	结算账号	string <=32	Y	Y	
			'payee_bank_province'=>"山东省",//结算开户行省份	String	Y		
			'payee_bank_city'=>'泰安市',//结算开户行城市	String	Y		
			'payee_id_card'=>$payee_id_card,//	结算 身份证号	String	Y	Y	
			'payee_mobile'=>$payee_mobile,//	结算手机号	String	Y	Y	
			'payee_family_name'=>$payee_family_name,//结算者姓名	String	Y	Y	
			'pay_amount'=>(string)$pay_amount,//到账金额	string <=8	Y		单位分，向下取整 pay_amount=trans_amount-operation_fee-counter_fee_t0
			'operation_fee'=>(string)$operation_fee,//手续费	string <=8	Y		单位分，向上取整，手续费=费率*交易金额 operation_fee=trans_amount*(rate_t0/100)
			'counter_fee_t0'=>(string)$counter_fee_t0,//单笔消费交易手续费	string <=8	Y		单位分，每笔固定交易手续费，比如200（2元）
			'rate_t0'=>$rate_t0,//	费率	string <=8	Y		如0.6%笔则填0.60，小数点后最多不超过2位终端用户费率不能低于0.30
			'memo'=>'备注',//备注	string <=32	N		
			'front_notify_url'=>System::getName('system_url').'/api/Userurl/calllback_success',//前台通知地址URL	string <=128	N		支付完成（结果未知）之后由第三方支付页面重定向到支付完成页面地址,注意该页面请求不能有session
			'back_notify_url'=>System::getName('system_url').'/index/Cashoutcallback/yilian_notify',//后台通知地址URL	string	N		支付结果通知地址，具体看回调通知接口
		);
		// echo json_encode($data);die;
		$result=$this->request('uspay',$data);
		return json_decode($result,true);
		var_dump($result);die;
	}
	public function sign($data){
		$data=SortByASCII($data);
		$string=http_build_query($data).$this->secret;
		$string=urldecode($string);
		// $string=$this->getBytes($string);
		// print_r($string);die;
		$sign=md5($string);
		// echo $sign;die;
		return $sign;
	}
	public function request($method,$data){
		$data['sign']=$this->sign($data);
		// echo json_encode($data);die;
		$result=curl_post($this->url.$method,'post',json_encode($data));
		// echo $result;die;
		return $result;
	}
}