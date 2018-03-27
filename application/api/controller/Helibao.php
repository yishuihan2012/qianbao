<?php
/**
 *  @version   / 合利宝刷卡接口
 *  @author yishuihan$(1015571416@qq.com)
 *   @datetime    2018-03-08 15:31:05
 *   @return 
 */
 namespace app\api\controller;
 use think\Controller;
 use app\index\model\Member;
 use app\index\model\MemberCert;
 use app\index\model\MemberCashcard;
 use app\index\model\Passageway;
 use app\index\model\System;
 use app\index\model\MemberNet;
 use app\index\model\PassagewayItem;
 use app\index\model\CashOrder;
 use app\api\controller\Commission;
 use app\api\controller\HttpClient;
 use app\index\controller\Tool;
class Helibao extends Controller{
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
			// 'webSite'=>'http://7ysh.com',//网站网址		否	String(150)	http://www.merchant.com	商户网站地址
			// 'accessUrl'=>'',//接入地址		否	String(150)	http://www.merchant.com	接入地址，空
			'merchantType'=>'PERSON', //子商户类型		是	String(20)	企业商户	见附录5.7
			'legalPerson'=>'许成成',//法人名字		是	String(20)	张三	法人名字
			'legalPersonID'=>'370983199109202832',//法人身份证号		是	String(30)	440101199201010171	身份证
			'orgNum'=>'370983199109202832',//组织机构代码		是	String(30)	445221144522119998	机构号
			'businessLicense'=>'370983199109202832',//营业执照号		是	String(30)		执照号
			// 'province'=>'150000',//子商户所在省份		否	String(15)	广东省	省份
			// 'city'=>'150900',//子商户所在城市		否	String(15)	广州市	城市
			'regionCode'=>'150906',//区县编码		是	String(8)	010100	区县编码（见附件行业编码及地区编码表.zip）
			'address'=>'山东省肥城市',//通讯地址	address	是	String(150)		通讯地址
			'linkman'=>"许成成",//联系人		是	String(15)	李四	联系人
			'linkPhone'=>"17569615504",//联系电话	linkPhone	是	String(20)		联系电话
			'email'=>"1015571416@qq.com",//联系邮箱		是	String(50)	2862277315@qq.com	用户邮箱
			// 'bindMobile'=>'17569615504',//绑定手机		否	String(25)	14718090064	绑定手机号
			// 'servicePhone'=>'',//客服联系电话		否	String(20)	14718090064	用户支付后有疑问的, 可通过此号码进行咨询，如不填会上送默认电话，建议必填商户自有客服电话
			'bankCode'=>"102100000048",//结算卡联行号		是	String(13)	102100000048	联行号
			'accountName'=>"许成成",//开户名		是	String(50)	张三	开卡姓名
			'accountNo'=>"6215590200003242971",//开户账号		是	String(30)	907190100001000014	卡账号
			'settleBankType'=>"TOPRIVATE",//结算卡类型		是	String(20)	对公	见附录5.9
			'settlementPeriod'=>"D0",//结算类型		是	String(20)	T1	见附录5.10
			'settlementMode'=>"AUTO",//结算方式		是	String(20)	AUTO	见附录5.15
			// 'settlementRemark'=>'自动结算',//结算备注		否	String(20)	合利宝结算款	自动结算 / 自主结算 备注信息
			'merchantCategory'=>'GROUP_PURCHASE',//经营类别		是	String(35)		见附录5.5
			// 'industryTypeCode'=>'',//行业类型编码		否	String(10)	141	见附件(行业编码及地区编码表.zip)个人商户类型不需填写，其他类型必传
			'authorizationFlag'=>'true',//授权使用平台商秘钥		是	Boolean true	true代表授权，false代表不授权
			// 'unionPayQrCode'=>'',//银联二维码		否	String(100)	子商户若需绑定银联二维码，可填写
		);
		$encKey='lENn7v8OZ1z7WdlMKMgu5KNj';
		$signKey='blYXETCKznBGIcdEWlwgg1WeA8TVGuA6';
		$post['interfaceName']='register';
		// $post['body']=$this->get_body($arr);
		// $post['sign']=$this->get_sign($post['body']);
		$post['merchantNo']='C1800193823';
		$post['body']=$this->encrypt(json_encode($arr,JSON_UNESCAPED_UNICODE), $encKey);
		$post['sign']=md5($post['body']."&".$post['merchantNo']."&".$signKey);
		$res=$this->send_request($this->test_url,$post);
		if($res['code']=='0000'){
			$data='KHh+C5F4fAoDOFJ9TUiAzh2kw6wPLT5wOdExS8C/WmHznini7URbXU/k6mMBPMagp4LUin03Kjw93JMWGqLvbqPuDL27Wr3zdXyN4qGSmaU=';
			$res=$this->decrypt($data,$encKey);
			echo json_encode($res);die;
			$result=json_decode($res,true);
			var_dump($result);die;
		}else{
			echo json_encode($res);die;
		}
	}
	/**
	 * 入网进件查看查询
	 * @return [type] [description]
	 */
	public function registerQuery(){
		$arr=array(
			'orderNo'=>'2GLD1SSS',//是	String(50)	p_20170731163713	进件下单时的订单号
			'firstClassMerchantNo'=>'C1800193823',//平台商商编		是	String(16)	C1800000002	平台商编号
		);
		$encKey='lENn7v8OZ1z7WdlMKMgu5KNj';
		$signKey='blYXETCKznBGIcdEWlwgg1WeA8TVGuA6';
		$post['interfaceName']='registerQuery';
		$post['merchantNo']='C1800193823';
		$post['body']=$this->encrypt(json_encode($arr,JSON_UNESCAPED_UNICODE), $encKey);
		$post['sign']=md5($post['body']."&".$post['merchantNo']."&".$signKey);
		$res=$this->send_request($this->test_url,$post);
		if($res['code']=='0000'){
			$data='KHh+C5F4fAoDOFJ9TUiAzh2kw6wPLT5wOdExS8C/WmHznini7URbXU/k6mMBPMagp4LUin03Kjw93JMWGqLvbqPuDL27Wr3zdXyN4qGSmaU=';
			$res=$this->decrypt($data,$encKey);
			// echo json_encode($res);die;
			$result=json_decode($res,true);
			var_dump($result);die;
		}else{
			echo json_encode($res);die;
		}
	}
	/**
	 * 开通产品扫码
	 * @return [type] [description]
	 */
	public function open_scan(){
		$arr=array(
			'productType'=>'APPPAY',//产品类型		是	String(20)	APPPAY	扫码产品
			'firstClassMerchantNo'=>'C1800193823',//平台商商编		是	String(16)	C1800000002	平台商编号
			'merchantNo'=>'E1800195861',//子商户编号		是	String(16)	C1800001025	进件审核通过后才有的商户号
			'payType'=>'SCAN',//支付类型		是	String(20)		见附录5.11
			'appPayType'=>'WXPAY',//客户端类型		是	String(20)		见附录5.2
			'value'=>'0.25',//费率		否	Number(10.2)	0.01	单位(%或元),小数后两位
			'minFee'=>'0.01',//最低费率金额		否	Number(10.2)	0.01	单位(元),小数后两位
		);
		$encKey='lENn7v8OZ1z7WdlMKMgu5KNj';
		$signKey='blYXETCKznBGIcdEWlwgg1WeA8TVGuA6';

		$post['interfaceName']='openProduct';
		// $post['body']=$this->get_body($arr);
		// $post['sign']=$this->get_sign($post['body']);
		$post['merchantNo']='C1800193823';
		$post['body']=$this->encrypt(json_encode($arr,JSON_UNESCAPED_UNICODE), $encKey);
		$post['sign']=md5($post['body']."&".$post['merchantNo']."&".$signKey);
		$res=$this->send_request($this->test_url,$post);
		var_dump($res);
		if($res['code']=='0000'){
			$data='KHh+C5F4fAoDOFJ9TUiAzh2kw6wPLT5wOdExS8C/WmHznini7URbXU/k6mMBPMagp4LUin03Kjw93JMWGqLvbqPuDL27Wr3zdXyN4qGSmaU=';
			$res=$this->decrypt($data,$encKey);
			$result=json_decode($res,true);
			var_dump($result);die;
		}
	}
	/**
	 * 产品扫码查询
	 * @return [type] [description]
	 */
	public function productQuery(){
		$arr=array(
			'productType'=>'APPPAY',//产品类型		是	String(20)	APPPAY	扫码产品
			'firstClassMerchantNo'=>'C1800193823',//平台商商编		是	String(16)	C1800000002	平台商编号
			'merchantNo'=>'E1800195861',//子商户编号		是	String(16)	C1800001025	进件审核通过后才有的商户号
			'payType'=>'SCAN',//支付类型		是	String(20)		见附录5.11
			'appPayType'=>'WXPAY',//客户端类型		是	String(20)		见附录5.2
		);
		$encKey='lENn7v8OZ1z7WdlMKMgu5KNj';
		$signKey='blYXETCKznBGIcdEWlwgg1WeA8TVGuA6';

		$post['interfaceName']='openProduct';
		// $post['body']=$this->get_body($arr);
		// $post['sign']=$this->get_sign($post['body']);
		$post['merchantNo']='C1800193823';
		$post['body']=$this->encrypt(json_encode($arr,JSON_UNESCAPED_UNICODE), $encKey);
		$post['sign']=md5($post['body']."&".$post['merchantNo']."&".$signKey);
		$res=$this->send_request($this->test_url,$post);
		// var_dump($res);
		if($res['code']=='0000'){
			$data='KHh+C5F4fAoDOFJ9TUiAzh2kw6wPLT5wOdExS8C/WmHznini7URbXU/k6mMBPMagp4LUin03Kjw93JMWGqLvbqPuDL27Wr3zdXyN4qGSmaU=';
			$res=$this->decrypt($data,$encKey);
			echo $res;die;
			$result=json_decode($res,true);
			var_dump($result);die;
		}else{
			echo $res;die;
		}
	}
	/**
	 * 资质上传
	 * @return [type] [description]
	 */
	public function uploadCredential(){

		if(1){
			$file_url='http://wallet.dev.com/uploads/aptitude/20180310/a68caa8d66816c946af1a99d55e29e75.jpg';
			$file_info = file_get_contents($file_url);
			// $file = request()->file('file');
			// var_dump($file);die;
			// $tool=new Tool();
   //    	    $images=$tool->uploads($file, 'aptitude');
   //    	    $images=json_decode($images,true);
   //    	    if($images['code']==200){
   //    	    	$img_url=$images['data']['link'];
   //    	    	// echo $img_url;die;
   //    	    }else{
   //    	    	exit(json_encode(['code'=>101,'msg'=>'上传失败']));
   //    	    }
			$arr=array(
				'merchantNo'=>'E1800195861',//子商户编号		是	String(16)	C1800001025	进件审核通过后才有的商户号
				'orderNo'=>make_rand_code(),//请求单号		是	String(30)	P_20171022123bsff23	
				'credentialType'=>'FRONT_OF_ID_CARD',//资质类型	credentialType	是	String(30)	BUSINESS_LICENSE	见附录5.2
				'fileSign'=>hash('md5',$file_info),//资质文件 HASH 值	fileSign	是	String(32)	c81e728d9d4c2f636f067f89cc14862c	文件 MD5 校验码
			);
		    $file_path=__DIR__.'/1.jpg';
		    // echo $file_path;die;
		    file_put_contents($file_path, $file_info);
			$encKey='lENn7v8OZ1z7WdlMKMgu5KNj';
			$signKey='blYXETCKznBGIcdEWlwgg1WeA8TVGuA6';
			$post['interfaceName']='uploadCredential';
			// $post['body']=$this->get_body($arr);
			// $post['sign']=$this->get_sign($post['body']);
			$post['merchantNo']='C1800193823';
			$post['body']=$this->encrypt(json_encode($arr,JSON_UNESCAPED_UNICODE),$encKey);
			$post['sign']=md5($post['body']."&".$post['merchantNo']."&".$signKey);
			// $post['file']=$file;
			// $file = new \CURLFile($upload['file'], $upload['type'], $upload['name']);
			$file = new \CURLFile($file_path);
			// var_dump($file);die;
            // $post[$upload['get_name']] = $file;
            $post['file'] = $file;
			// print_r($file);die;
			$res=curl_post('http://test.trx.helipay.com/trx/merchantEntry/upload.action','post',$post,'multipart/form-data');
			unlink ($file_path);
			// echo $res;die;
			$res=json_decode($res,true);
			if($res['code']=='0000'){
				$res=$this->decrypt($res['data'],$encKey);
				// $result=json_decode($res,true);
				echo $res;die;
			}
			echo $red;die;
		}else{
			return view('/Userurl/uploadCredential');
		}
	}
	/**
	 * 子商户资质上传结果查询接口
	 * @return [type] [description]
	 */
	public function credentialQuery(){
		$arr=array(
			'merchantNo'=>'E1800195861',//子商户编号		是	String(16)	C1800001025	进件审核通过后才有的商户号
			'orderNo'=>make_rand_code(),//请求单号		是	String(30)	P_20171022123bsff23	
			'credentialType'=>'FRONT_OF_ID_CARD',//资质类型		是	String(30)	BUSINESS_LICENSE	见附录5.1
		);
		$encKey='lENn7v8OZ1z7WdlMKMgu5KNj';
		$signKey='blYXETCKznBGIcdEWlwgg1WeA8TVGuA6';
		$post['interfaceName']='credentialQuery';
		// $post['body']=$this->get_body($arr);
		// $post['sign']=$this->get_sign($post['body']);
		$post['merchantNo']='C1800193823';
		$post['body']=$this->encrypt(json_encode($arr,JSON_UNESCAPED_UNICODE), $encKey);
		$post['sign']=md5($post['body']."&".$post['merchantNo']."&".$signKey);
		$res=$this->send_request($this->test_url,$post);
		// var_dump($res);
		if($res['code']=='0000'){
			$res=$this->decrypt($res['data'],$encKey);
			echo $res;die;
			$result=json_decode($res,true);
			var_dump($result);die;
		}else{
			echo $res;die;
		}
	}
	/**
	 * 结算卡信息变更
	 * @return [type] [description]
	 */
	public function settlementCardAlteration(){
		$arr=array(
			'orderNo'=>make_rand_code(),//订单号		是	String(50)	p_20170726140544	变更请求订单号
			'merchantNo'=>'',//子商户编号		是	String(16)	C1800000002	进件审核通过后才有的子商户编号
			'accountName'=>'',//开户人名称		是	String(50)	张三	开户人名称
			'updateAccountName'=>'',//变更后开户人名称		否	String(50)	张三	变更后开户人名称，输入该字段需上传结算账户指定书
			'accountNo'=>'',//原结算卡号		是	String(30)	9071901000010000053423	原结算卡号
			'updateAccountNo'=>'',//变更后结算卡号		否	String(30)	9071901000010000053423	变更后结算卡号
			'settleBankType'=>'',//结算卡类型		是	String(20)	TOPRIVATE	见附录5.9
			'updateSettleBankType'=>'',//变更后结算卡类型		否	String(20)	TOPRIVATE	见附录5.9
			'bankCode'=>'',//结算卡联行号		是	String(20)	104100004048	结算卡联行号
			'updateBankCode'=>'',//变更后结算卡联行号		否	String(20)	104100004048	变更后结算卡联行号
			'merchantEntryAlterationType',//变更类型		是	String(20)	SETTLE_BANKCARD	见附录5.18
			'file'=>'',//结算账户指定书		否	Multipart File		变更开户人名称或结算卡号时必须上传
			'frontOfIdCard'=>'',//持卡人身份证正面照		否	Multipart File		变更开户人名称或结算卡号时并且结算卡类型对私必须上传
			'backOfIdCard'=>'',//持卡人身份证反面照		否	Multipart File		变更开户人名称或结算卡号时并且结算卡类型对私必须上传
			'handheldOfIdCard'=>'',//持卡人手持身份证照		否	Multipart File		变更开户人名称或结算卡号时并且结算卡类型对私必须上传
			'handheldOfBankCard'=>'',//持卡人手持银行卡照		否	Multipart File		变更开户人名称或结算卡号时并且结算卡类型对私必须上传
			'accountOpeningCertificate'=>'',//银行开户证明		否	Multipart File		变更开户人名称或结算卡号时并且结算卡类型对公必须上传
			'fileSign'=>'',//结算账户指定书文件 HASH 值		否	String(32)	c81e728d9d4c2f636f067f89cc14862c	结算账户指定书上传时必填，文件 MD5 校验码
			'frontOfIdCard'=>'',//持卡人身份证正面照文件 HASH 值	 FileSign	否	String(32)	c81e728d9d4c2f636f067f89cc14862c	持卡人身份证正面照上传时必填，文件 MD5 校验码
			'backOfIdCard'=>'',//持卡人身份证反面照文件 HASH 值	 FileSign	是	String(32)	c81e728d9d4c2f636f067f89cc14862c	持卡人身份证反面照上传时必填，文件 MD5 校验码
			'handheldOfIdCardFileSign'=>'',//持卡人手持身份证照文件 HASH 值		是	String(32)	c81e728d9d4c2f636f067f89cc14862c	持卡人手持身份证照上传时必填，文件 MD5 校验码
			'handheldOfBankCardFileSign'=>'',//持卡人手持银行卡照文件 HASH 值		是	String(32)	c81e728d9d4c2f636f067f89cc14862c	持卡人手持银行卡照上传时必填，文件 MD5 校验码
			'accountOpeningCertificate'=>'',//银行开户证明文件 HASH 值	 FileSign	是	String(32)	c81e728d9d4c2f636f067f89cc14862c	银行开户证明上传时必填，文件 MD5 校验码
		);
		$encKey='lENn7v8OZ1z7WdlMKMgu5KNj';
		$signKey='blYXETCKznBGIcdEWlwgg1WeA8TVGuA6';
		$post['interfaceName']='infoAlteration';
		$post['merchantNo']='C1800193823';
		$post['body']=$this->encrypt(json_encode($arr,JSON_UNESCAPED_UNICODE), $encKey);
		$post['sign']=md5($post['body']."&".$post['merchantNo']."&".$signKey);
		$res=$this->send_request($this->test_url,$post);
		// echo json_encode($res);die;
		if($res['code']=='0000'){
			$res=$this->decrypt($res['data'],$encKey);
			echo $res;die;
			$result=json_decode($res,true);
			var_dump($result);die;
		}else{
			echo json_encode($res);die;
			echo $res;die;
		}
	}
	/**
	 * 商户信息变更
	 * @return [type] [description]
	 */
	public function infoAlteration(){
		$arr=array(
			'orderNo'=>make_rand_code(),//订单号		是	String(50)	p_20170726140544	变更请求订单号
			'merchantNo'=>'E1800195861',//子商户编号		是	String(16)	C1800000002	子商户编号
			'merchantEntryAlterationType'=>'MERCHANT_CREDENTIAL',//变更类型		是	String(20)	MERCHANT_INFO	见附录5.18
			'updateShowName'=>"许成成",//变更展示名		是	String(100)	张三	
			'updateLinkPhone'=>'17569615504',//变更客服联系电话		是	String(20)	18812345678	
		);
		$encKey='lENn7v8OZ1z7WdlMKMgu5KNj';
		$signKey='blYXETCKznBGIcdEWlwgg1WeA8TVGuA6';

		$post['interfaceName']='infoAlteration';
		// $post['body']=$this->get_body($arr);
		// $post['sign']=$this->get_sign($post['body']);
		$post['merchantNo']='C1800193823';
		$post['body']=$this->encrypt(json_encode($arr,JSON_UNESCAPED_UNICODE), $encKey);
		$post['sign']=md5($post['body']."&".$post['merchantNo']."&".$signKey);
		$res=$this->send_request($this->test_url,$post);
		// echo json_encode($res);die;
		if($res['code']=='0000'){
			$data='KHh+C5F4fAoDOFJ9TUiAzh2kw6wPLT5wOdExS8C/WmHznini7URbXU/k6mMBPMagp4LUin03Kjw93JMWGqLvbqPuDL27Wr3zdXyN4qGSmaU=';
			$res=$this->decrypt($data,$encKey);
			echo $res;die;
			$result=json_decode($res,true);
			var_dump($result);die;
		}else{
			echo json_encode($res);die;
			echo $res;die;
		}
	}
	/**
	 * 资质变更
	 * @return [type] [description]
	 */
	public function uploadAlterationAptitude(){
		$file_url='http://wallet.dev.com/uploads/aptitude/20180310/a68caa8d66816c946af1a99d55e29e75.jpg';
		$file_info = file_get_contents($file_url);
		$arr=array(
			'orderNo'=>make_rand_code(),//订单号		是	String(50)	p_20170726140544	变更请求订单号
			'merchantNo'=>'E1800195861',//子商户编号		是	String(16)	C1800000002	子商户编号
			'credentialType'=>'FRONT_OF_ID_CARD',//资质类型		是	String(20)	BUSINESS_LICENSE	见附录5.21
			'merchantEntryAlterationType'=>'MERCHANT_CREDENTIAL',//见附录5.18
			'fileSign'=>hash('md5',$file_info),//资质文件 HASH 值		是	String(32)	c81e728d9d4c2f636f067f89cc14862c	文件 MD5 校验码
		);
		$file_path=__DIR__.'/'.time().'.jpg';
	    // echo $file_path;die;
	    file_put_contents($file_path, $file_info);
		$encKey='lENn7v8OZ1z7WdlMKMgu5KNj';
		$signKey='blYXETCKznBGIcdEWlwgg1WeA8TVGuA6';
		$post['interfaceName']='uploadAlterationAptitude';
		// $post['body']=$this->get_body($arr);
		// $post['sign']=$this->get_sign($post['body']);
		$post['merchantNo']='C1800193823';
		$post['body']=$this->encrypt(json_encode($arr,JSON_UNESCAPED_UNICODE),$encKey);
		$post['sign']=md5($post['body']."&".$post['merchantNo']."&".$signKey);
		// $post['merchantCredentialType']='FRONT_OF_ID_CARD';
		$file = new \CURLFile($file_path);
        $post['file'] = $file;
		// print_r($post);die;
		$res=curl_post('http://test.trx.helipay.com/trx/merchantEntry/upload.action','post',$post,'multipart/form-data');
		unlink ($file_path);
		// echo $res;die;
		$res=json_decode($res,true);
		if($res['code']=='0000'){
			$data='KHh+C5F4fAoDOFJ9TUiAzh2kw6wPLT5wOdExS8C/WmHznini7URbXU/k6mMBPMagp4LUin03Kjw93JMWGqLvbqPuDL27Wr3zdXyN4qGSmaU=';
			$res=$this->decrypt($data,$encKey);
			echo $res;die;
			$result=json_decode($res,true);
			var_dump($result);die;
		}else{
			echo json_encode($res);die;
			echo $res;die;
		}
	}
	/**
	 * 扫码支付
	 * @return [type] [description]
	 */
	public function scan_pay(){
		$params=array(
			'P1_bizType'=> "AppPay",
			'P2_orderId'=>make_rand_code(),//商户订单号		是	String(50)	p_20170302185347	商户系统内部订单号，要求50字符以内，同一商户号下订单号唯一
			'P3_customerNumber'=>"E1800195861",//商户编号		是	String(15)	C1800000002	合利宝分配的商户号
			'P4_payType'=>'SCAN',//支付类型		是	String(15)	SCAN	SWIPE:刷卡(被扫)SCAN:扫码(主扫)
			'P5_orderAmount'=>'10',//交易金额		是	Number(10,2)	0.01	订单金额，以元为单位，最小金额为0.01
			'P6_currency'=>"CNY",//币种类型		是	String(30)	CNY	CNY:人民币
			'P7_authcode'=>'1',//授权码		是	String(50)		payType为刷卡(被扫)时传入一组字符串(付款码),主扫支付类型传入1即可
			'P8_appType'=>'ALIPAY',//客户端类型		是	String(20)	WXPAY	ALIPAY：支付宝WXPAY:微信UNIONPAY:银联JDPAY:京东钱包QQPAY:QQ钱包
			'P9_notifyUrl'=>'http://wallet.test.xijiakeji.com/api/Test/paycallback',//通知回调地址		是	String(300)	http://wap.helipay.com/notify.php	异步接收合利宝支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
			'P10_successToUrl'=>'',//成功跳转URL		否	String(300)	http://wap.helipay.com/success.php	支付完成后，展示支付结果的页面地址(暂不用)
			'P11_orderIp'=>"127.0.0.1",//商户IP		是	String(20)	192.168.10.1	用户下单IP
			'P12_goodsName'=>'三星s9',//商品名称		是	String(128)	Iphone7	商品名称
			'P13_goodsDetail'=>"三星手机",//商户描述		否	String(255)	Iphone7	对交易或商品的描述
			'P14_desc'=>"我是备注",//备注		否	String(100)	备注	订单备注信息，原样返回
		);
		$signKey="E2ASpz3kPCZW3HJVeoQTqj0IOwMSR0F8";
		$str=$this->buildQueryString($params);
		$source=$str."&".$signKey;
		$sign= md5($source);
        $url = "http://test.trx.helipay.com/trx/app/interface.action";
        $params['sign']=$sign;
        $res=curl_post($url,'post',$params,0);
		echo $res;die;
	}
	public function test(){
		$params=array(
			'P1_bizType'=> "AppPay",
			'P2_orderId'=>make_rand_code(),//商户订单号		是	String(50)	p_20170302185347	商户系统内部订单号，要求50字符以内，同一商户号下订单号唯一
			'P3_customerNumber'=>"E1800195861",//商户编号		是	String(15)	C1800000002	合利宝分配的商户号
			'P4_payType'=>'SCAN',//支付类型		是	String(15)	SCAN	SWIPE:刷卡(被扫)SCAN:扫码(主扫)
			'P5_orderAmount'=>'10',//交易金额		是	Number(10,2)	0.01	订单金额，以元为单位，最小金额为0.01
			'P6_currency'=>"CNY",//币种类型		是	String(30)	CNY	CNY:人民币
			'P7_authcode'=>'1',//授权码		是	String(50)		payType为刷卡(被扫)时传入一组字符串(付款码),主扫支付类型传入1即可
			'P8_appType'=>'ALIPAY',//客户端类型		是	String(20)	WXPAY	ALIPAY：支付宝WXPAY:微信UNIONPAY:银联JDPAY:京东钱包QQPAY:QQ钱包
			'P9_notifyUrl'=>'http://wallet.test.xijiakeji.com/api/Test/paycallback',//通知回调地址		是	String(300)	http://wap.helipay.com/notify.php	异步接收合利宝支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
			'P10_successToUrl'=>'http://wap.helipay.com/success.php',//成功跳转URL		否	String(300)	http://wap.helipay.com/success.php	支付完成后，展示支付结果的页面地址(暂不用)
			'P11_orderIp'=>"127.0.0.1",//商户IP		是	String(20)	192.168.10.1	用户下单IP
			'P12_goodsName'=>'三星s9',//商品名称		是	String(128)	Iphone7	商品名称
			'P13_goodsDetail'=>"三星手机",//商户描述		否	String(255)	Iphone7	对交易或商品的描述
			'P14_desc'=>"我是备注",//备注		否	String(100)	备注	订单备注信息，原样返回
		);
		$signKey="E2ASpz3kPCZW3HJVeoQTqj0IOwMSR0F8";
		$str=$this->buildQueryString($params);
		$source=$str."&".$signKey;
		$sign= md5($source);
        $url = "http://test.trx.helipay.com/trx/app/interface.action";
        $params['sign']=$sign;
        $pageContents=curl_post($url,'post',$params,0);
        echo $pageContents; die;
		$obj = json_decode($pageContents);
		$rt1_bizType = $obj->{'rt1_bizType'};
		$rt2_retCode = $obj->{'rt2_retCode'};
		$rt3_retMsg = $obj->{'rt3_retMsg'};
		$rt4_customerNumber = $obj->{'rt4_customerNumber'};
		$rt5_orderId = $obj->{'rt5_orderId'};
		$rt6_serialNumber = $obj->{'rt6_serialNumber'};
		$rt7_orderStatus = $obj->{'rt7_orderStatus'};
		$rt8_orderAmount = $obj->{'rt8_orderAmount'};
		$rt9_currency = $obj->{'rt9_currency'};
		$rt10_desc = $obj->{'rt10_desc'};
		$json_sign = $obj->{'sign'};

		echo "rt1_bizType:".$rt1_bizType."<br/>";
		echo "rt2_retCode:".$rt2_retCode."<br/>";
		echo "rt3_retMsg:".$rt3_retMsg."<br/>";
		echo "rt4_customerNumber:".$rt4_customerNumber."<br/>";
		echo "rt5_orderId:".$rt5_orderId."<br/>";
		echo "rt6_serialNumber:".$rt6_serialNumber."<br/>";
		echo "rt7_orderStatus:".$rt7_orderStatus."<br/>";
		echo "rt8_orderAmount:".$rt8_orderAmount."<br/>";
		echo "rt9_currency:".$rt9_currency."<br/>";
		echo "rt10_desc:".$rt10_desc."<br/>";
		echo "json_sign:".$json_sign."<br/>";
	
	//当retCode为0000证明查询请求受理成功，订单是否支付成功根据r4_orderStatus判断，INIT:已接收;DOING: 处理中;SUCCESS:成功; FAIL:失败;CLOSE:关闭
	}
	function buildQueryString($data) {
        $querystring = '';
        if (is_array($data)) {
    		foreach ($data as $key => $val) {
    			$querystring.="&".$val;
    		}
    	} 
    	return $querystring;
    }
	/**
	 * 获取加密主体信息
	 * @return [type] [description]
	 */
	public function get_body($arr){
		$key = 'Ke0xG8INoyRkoRXxBe98uYUG';
		$json=json_encode($arr,JSON_UNESCAPED_UNICODE);
		$return=$this->encrypt($json,$key);
		return $return;
	}

	public function encrypt($input, $key)
 	{
 		$size = mcrypt_get_block_size(MCRYPT_3DES,'ecb');
 		$input = $this->pkcs5_pad($input, $size);
 		$key = str_pad($key,24,'0');
 		$td = mcrypt_module_open(MCRYPT_3DES, '', 'ecb', '');
 		$iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
 		@mcrypt_generic_init($td, $key, $iv);
 		$data = mcrypt_generic($td, $input);
 		mcrypt_generic_deinit($td);
 		mcrypt_module_close($td);
 		$data = base64_encode($data);
 		return $data;
 	}
 	//数据解密
 	function decrypt($encrypted, $key)
 	{
 		$encrypted = base64_decode($encrypted);
 		$key = str_pad($key,24,'0');
 		$td = mcrypt_module_open(MCRYPT_3DES,'','ecb','');
 		$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
 		$ks = mcrypt_enc_get_key_size($td);
 		@mcrypt_generic_init($td, $key, $iv);
 		$decrypted = mdecrypt_generic($td, $encrypted);
 		mcrypt_generic_deinit($td);
 		mcrypt_module_close($td);
 		$y=$this->pkcs5_unpad($decrypted);
 		return $y;
 	}
 	
 	function pkcs5_pad ($text, $blocksize) 
 	{
 		$pad = $blocksize - (strlen($text) % $blocksize);
 		return $text . str_repeat(chr($pad), $pad);
 	}
 	
	function pkcs5_unpad($text)
	{
 		$pad = ord($text{strlen($text)-1});
 		if ($pad > strlen($text)) 
 		{
 		return false;
 		}
 		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
 		{
 			return false;
 		}
 		return substr($text, 0, -1 * $pad);
 	}
	/**
	 * 获取签名
	 * @return [type] [description]
	 */
	public function get_sign($body){
		//拼接的原文串加上商户号再加上商户签名秘钥
		$merch_id='C1800193823';
		$sign_key='E2ASpz3kPCZW3HJVeoQTqj0IOwMSR0F8';
		return md5($body.'&'.$merch_id.'&'.$sign_key);
	}
	/**
	 * 发送请求
	 * @return [type] [description]
	 */
	public function send_request($url,$post){
		$res=curl_post($url,'post',$post,0);
		// echo $res;die;
        $result=json_decode($res,true);
        return $result;
	}
}