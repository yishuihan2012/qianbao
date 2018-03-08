<?php
/**
 *  @version   / 合利宝刷卡接口
 *  @author yishuihan$(1015571416@qq.com)
 *   @datetime    2018-03-08 15:31:05
 *   @return 
 */
 namespace app\api\controller;

 use app\index\model\Member;
 use app\index\model\MemberCert;
 use app\index\model\MemberCashcard;
 use app\index\model\Passageway;
 use app\index\model\System;
 use app\index\model\MemberNet;
 use app\index\model\PassagewayItem;
 use app\index\model\CashOrder;
 use app\api\controller\Commission;
class Helibao{

	public function __construct(){
		$test_url='http://test.trx.helipay.com/trx/merchantEntry/interface.action';
		$product_url='http://pay.trx.helipay.com/trx/merchantEntry/interface.action';
		$scan_test='http://test.trx.helipay.com/trx/app/interface.action';
	}
	/**
	 * 商户进件
	 * @return [type] [description]
	 */
	public function income(){ //register
		$arr=array(
			'firstClassMerchantNo'=>'370983199109202832',	//平台商商编是	String(16)	C1800000002	平台商编号
			'orderNo'=>make_rand_code(),	//商户订单号	是	String(50)	p_20170731163713	商户系统内部订单号，要求50字符以内，同一商户号下订单号唯一
			'signName'=>'许成成',	//子商户签约名	 是	String(150)	测试子商户01	签约名
			'showName'=>'水寒科技',	//展示名（收银台展示名）	是	String(100)	测试子商户01	用于收银台的展示名
			'webSite'=>'http://7ysh.com',//网站网址		否	String(150)	http://www.merchant.com	商户网站地址
			'accessUrl'=>'',//接入地址		否	String(150)	http://www.merchant.com	接入地址，空
			'merchantType'=>'PERSON', //子商户类型		是	String(20)	企业商户	见附录5.7
			'legalPerson'=>'许成成',//法人名字		是	String(20)	张三	法人名字
			'legalPersonID'=>'370983199109202832',//法人身份证号		是	String(30)	440101199201010171	身份证
			'orgNum'=>'445221144522119998',//组织机构代码		是	String(30)	445221144522119998	机构号
			'businessLicense'=>'445221144522119998',//营业执照号		是	String(30)		执照号
			'province'=>'',//子商户所在省份		否	String(15)	广东省	省份
			'city'=>'',//子商户所在城市		否	String(15)	广州市	城市
			'regionCode'=>'',//区县编码		是	String(8)	010100	区县编码（见附件行业编码及地区编码表.zip）
			'address'=>'天河区珠江东路28号',//通讯地址	address	是	String(150)		通讯地址
			'linkman'=>"许成成",//联系人		是	String(15)	李四	联系人
			'linkPhone'=>"17569615504",//联系电话	linkPhone	是	String(20)		联系电话
			'email'=>"1015571416@qq.com",//联系邮箱		是	String(50)	2862277315@qq.com	用户邮箱
			'bindMobile'=>'',//绑定手机		否	String(25)	14718090064	绑定手机号
			'servicePhone'=>'',//客服联系电话		否	String(20)	14718090064	用户支付后有疑问的, 可通过此号码进行咨询，如不填会上送默认电话，建议必填商户自有客服电话
			'bankCode'=>"102100000048",//结算卡联行号		是	String(13)	102100000048	联行号
			'accountName'=>"许成成",//开户名		是	String(50)	张三	开卡姓名
			'accountNo'=>"6215590200003242971",//开户账号		是	String(30)	907190100001000014	卡账号
			'settleBankType'=>"TOPRIVATE",//结算卡类型		是	String(20)	对公	见附录5.9
			'settlementPeriod'=>"D0",//结算类型		是	String(20)	T1	见附录5.10
			'settlementMode'=>"AUTO",//结算方式		是	String(20)	AUTO	见附录5.15
			'settlementRemark'=>'自动结算',//结算备注		否	String(20)	合利宝结算款	自动结算 / 自主结算 备注信息
			'merchantCategory'=>'GROUP_PURCHASE',//经营类别		是	String(35)		见附录5.5
			'industryTypeCode'=>'',//行业类型编码		否	String(10)	141	见附件(行业编码及地区编码表.zip)个人商户类型不需填写，其他类型必传
			'authorizationFlag'=>'true',//授权使用平台商秘钥		是	Boolean true	true代表授权，false代表不授权
			'unionPayQrCode'=>'',//银联二维码		否	String(100)	子商户若需绑定银联二维码，可填写
		);
		$post['body']=$this->get_body($arr);
		$post['sign']=$this->get_sign($post['body']);
		$post['interfaceName']='register';
		$res=$this->send_request($test_url,$post);
	}
	/**
	 * 获取加密主体信息
	 * @return [type] [description]
	 */
	public function get_body($arr){
		$json=json_encode($arr);
		$return=$this->des3($json);
		return $return;
	}
	/**
	 * 加密
	 * @return [type] [description]
	 */
	public function des3($str){
		return $str;
	}
	/**
	 * 获取签名
	 * @return [type] [description]
	 */
	public function get_sign($body){
		//拼接的原文串加上商户号再加上商户签名秘钥
		$merch_id='C1800001025';
		$sign_key='rV8u3c2n2hlTCIDWyzei7iz66DiQlYTh';
		return md5($body.'&'.$merch_id.'&'.$sign_key);
	}
	/**
	 * 发送请求
	 * @return [type] [description]
	 */
	public function send_request($url,$post){
		$res=curl_post($url,'post',json_encode($post));
		echo $res;die;
        $result=json_decode($res,true);
        return $result;
	}
}