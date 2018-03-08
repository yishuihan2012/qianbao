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
 use Think\Crypt\Driver\Des; //导入类库
class Helibao{
	public $test_url;
	public $product_url;
	public $scan_test;
	public function __construct(){
		$this->test_url='http://test.trx.helipay.com/trx/merchantEntry/interface.action';
		$this->product_url='http://pay.trx.helipay.com/trx/merchantEntry/interface.action';
		$this->scan_test='http://test.trx.helipay.com/trx/app/interface.action';
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
		// echo $post['body'];die;
		$post['sign']=$this->get_sign($post['body']);
		$post['interfaceName']='register';
		$res=$this->send_request($this->test_url,$post);
	}
	public function scan_pay(){
		$arr=array(
			'P2_orderId'=>make_rand_code(),//商户订单号		是	String(50)	p_20170302185347	商户系统内部订单号，要求50字符以内，同一商户号下订单号唯一
			'P3_customerNumber'=>"C1800193823",//商户编号		是	String(15)	C1800000002	合利宝分配的商户号
			'P4_payType'=>'SCAN',//支付类型		是	String(15)	SCAN	SWIPE:刷卡(被扫)SCAN:扫码(主扫)
			'P5_orderAmount'=>'0.01',//交易金额		是	Number(10,2)	0.01	订单金额，以元为单位，最小金额为0.01
			'P6_currency'=>"CNY",//币种类型		是	String(30)	CNY	CNY:人民币
			'P7_authcode'=>'1',//授权码		是	String(50)		payType为刷卡(被扫)时传入一组字符串(付款码),主扫支付类型传入1即可
			'P8_appType'=>'ALIPAY',//客户端类型		是	String(20)	WXPAY	ALIPAY：支付宝WXPAY:微信UNIONPAY:银联JDPAY:京东钱包QQPAY:QQ钱包
			'P9_notifyUrl'=>'',//通知回调地址		是	String(300)	http://wap.helipay.com/notify.php	异步接收合利宝支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
			'P10_successToUrl'=>'',//成功跳转URL		否	String(300)	http://wap.helipay.com/success.php	支付完成后，展示支付结果的页面地址(暂不用)
			'P11_orderIp'=>"127.0.0.1",//商户IP		是	String(20)	192.168.10.1	用户下单IP
			'P12_goodsName'=>'三星s9',//商品名称		是	String(128)	Iphone7	商品名称
			'P13_goodsDetail'=>"三星手机",//商户描述		否	String(255)	Iphone7	对交易或商品的描述
			'P14_desc'=>"我是备注",//备注		否	String(100)	备注	订单备注信息，原样返回
		);
		$
	}
	/**
	 * 获取加密主体信息
	 * @return [type] [description]
	 */
	public function get_body($arr){
		$str = json_encode($arr);
		$key = 'E2ASpz3kPCZW3HJVeoQTqj0IOwMSR0F8';
		$des = new Des();
		$re = $des->encrypt($str, $key); //加密

		echo bin2hex($re);die; //给二进制转为16进制，所谓的解决乱码
		// return 'C0HdQBfH3pJ2iBH6zEM9udEvoPW7k8AqLIvdOqpskQ+jomvhpC1pf7uy1kk/+B5sF9GbM8yDjuANrqu+er5GXmHskmvi4arr2WxWhWQAiQ/HTjufLU8Qb5Z9ZkHOnR2u';
		$json=json_encode($arr);
		$return=AESencode($json,'E2ASpz3kPCZW3HJVeoQTqj0IOwMSR0F8','Ke0xG8INoyRkoRXxBe98uYUG');
		return $return;
	}

	function encrypt3DES($str,$key,$iv){  
	    $td = mcrypt_module_open(MCRYPT_3DES, "", MCRYPT_MODE_CBC, "");  
	    if ($td === false) {  
	        return false;  
	    }  
	    echo $key;
	    // 检查加密key，iv的长度是否符合算法要求  
	    $key = $this->fixLen($key, mcrypt_enc_get_key_size($td));  
	    $iv =$this->fixLen($iv, mcrypt_enc_get_iv_size($td));  
	    echo '<br/>';
	    echo $key;die;
	    //加密数据长度处理  
	    $str = $this->strPad($str, mcrypt_enc_get_block_size($td));  
	      
	    if (mcrypt_generic_init($td, $key, $iv) !== 0) {  
	        return false;  
	    }  
	    $result = mcrypt_generic($td, $str);  
	    mcrypt_generic_deinit($td);  
	    mcrypt_module_close($td);  
	    return $result;  
	}  
    function fixLen($str, $td_len)  
    {  
        $str_len = strlen($str);  
        if ($str_len > $td_len) {  
            return substr($str, 0, $td_len);  
        } else if($str_len < $td_len) {  
            return str_pad($str, $td_len, '0');  
        }  
        return $str;  
    }  
    function strPad($str, $td_group_len)  
    {  
        $padding_len = $td_group_len - (strlen($str) % $td_group_len);  
        return str_pad($str, strlen($str) + $padding_len, "\0");  
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
		var_dump($post);die;
		$res=curl_post($url,'post',$post);
		echo $res;die;
        $result=json_decode($res,true);
        return $result;
	}
}