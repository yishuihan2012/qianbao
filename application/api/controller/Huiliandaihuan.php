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
 class Huiliandaihuan{
 	protected $url;
 	protected $version;
 	public function __construct(){
 		$this->version='1.0';
 		$this->url='http://39.108.137.8:8099/v1.0/facade';
 	}
 	/**
 	 * 进件
 	 * @return [type] [description]
 	 */
 	public function huilian_income($Passageway,$card_id){
 		 //获取行用卡信息
        $card_info=MemberCreditcard::where(['card_id'=>$card_id])->find();
        if(!$card_info){
            return ['code'=>'101','msg'=>'获取信用卡信息失败'];
        }
        //获取用户信息
        $member_info=Member::where(['member_id'=>$card_info['card_member_id']])->find();
        if(!$member_info){
            return ['code'=>'102','msg'=>'获取用户信息失败'];
        }
        //获取卡对应银行信息
        $bank_name=mb_substr($card_info['card_bankname'],-4,2);
        // echo $bank_name;die;
        $BankInfo=BankInfo::where('info_sortname','like','%'.$bank_name.'%')->find();
        // print_r($BankInfo);die;
        $idcard=$member_info->membercert->cert_member_idcard;
        //获取通道费率
        $rate=PassagewayItem::where(['item_passageway'=>$Passageway,'item_group'=>$member_info['member_group_id']])->find();
        $also=($rate->item_also)*100;
        $daikou=($rate->item_qffix);
        //获取通道信息
        $Passageways=Passageway::where(['passageway_id'=>$Passageway])->find();
        $agentId=$Passageways->passageway_mech;

 		$data=array(
 			'version'=>$this->version,
            'charset'=>'UTF-8',//   编码方式UTF-8
            'agentId'=>$agentId,//受理方预分配的渠道代理商标识
            'nonceStr'=>generate_password(16),//随机字符串，字符范围a-zA-Z0-9
            'signType'=>'RSA',//签名方式，固定RSA
            'isCompay'=>'0',//对公对私标识0为对私，1为对公
            'idcardType'=>'01',//证件类型 暂只支持 01 身份证
            'idcard'=>$idcard,//证件号码
            'name'=>$card_info['card_name'],//姓名
            'phone'=>$member_info['member_mobile'],//手机号
            'bankId'=>$BankInfo['info_union'],//联行号
            'bankCard'=>$card_info['card_bankno'],//银行卡号
            'bankName'=>$BankInfo['info_name'],//开户行名称
            'bankNo'=>$BankInfo['info_pab'],//开户行代码(PAB)
            'rate'=>$also,//费率万分制 ，不小于代理商费率
            'extraFee'=>$daikou,//手续费(分)
            'expDate'=>substr($card_info['card_expireDate'],0,2).'-'.substr($card_info['card_expireDate'],2,2),//N(String)   信用卡时必填，格式:mm-YY
            'CVN2'=>$card_info['card_Ident'] ,//N(String)   信用卡时必填
            // 'address'=>'',//N(String)    地址
 		);
 		// echo json_encode($data);
 		// print_r($data);die;
 		$url=$this->url.'/report';
 		// echo $url;die;
 		$res=$this->request($url,$data);
 		if($res['code']=='10000' && $res['merId']){ //成功存储商户号
 			$update['member_credit_pas_info']=$res['merId'];
            $has=MemberCreditPas::where(['member_credit_pas_creditid'=>$card_info['card_id'],'member_credit_pas_pasid'=>$Passageway])->update($update);
             if($has){
                return true;
            }else{
                return false;
            }

 		}else{
 			return false;
 		}
 	}
 	/**
 	 *  信用卡签约
 	 * @return [type] [description]
 	 */
 	public function card_sign($card_id=70,$merId='9000105494',$Passageway=29){
 		 //获取行用卡信息
        $card_info=MemberCreditcard::where(['card_id'=>$card_id])->find();
        if(!$card_info){
            return ['code'=>'101','msg'=>'获取信用卡信息失败'];
        }
        //获取通道信息
        $Passageways=Passageway::where(['passageway_id'=>$Passageway])->find();
        $agentId=$Passageways->passageway_mech;
 		$data=array(
 			'version'=>'1.0',//	版本号 tr (8)	是	目前版本号：1.0
			'serviceUri'=>"YX0001",	//交易代码	str (8)	是	YX0001
			'charset'=>'UTF-8',// 编码格式	str (8)	是	
			'signType'=>"RSA",//签名方式是	
			'nonceStr'=>generate_password(16),// 随机字符串		str (32)	是	随机字符串
			'agentId'=>$agentId, //代理商号		是	受理方预分配的渠道代理商标识
			'merId'=>$merId,// 商户号是	进件返回的merId
			'orderNo'=>generate_password(16),// 订单号	是	商户交易订单号
			'phone'=>$card_info->card_phone,//手机号码	是	银行预留手机号
			'bankCard'=>$card_info->card_bankno,//银行卡号是	用于支付的银行卡号(只支持借记卡)
			'expDate'=>substr($card_info['card_expireDate'],0,2).'-'.substr($card_info['card_expireDate'],2,2),//N(String)   信用卡时必填，格式:mm-YY
            'CVN2'=>$card_info['card_Ident'] ,//N(String)   信用卡时必填
 		);
 		echo json_encode($data);
 		$url=$this->url.'/repay';
 		$res=$this->request($url,$data);
 		var_dump($res);die;
 	}
 	/**
 	 * 確認簽約
 	 * @param  string $agentId  [description]
 	 * @param  string $merId    [description]
 	 * @param  string $orderNo  [description]
 	 * @param  string $authCode [description]
 	 * @return [type]           [description]
 	 */
 	public function card_sign_confirm($agentId='1001057',$merId='9000105494',$orderNo='mClfuC2yGOkk0by8',$authCode='850138'){
 		$data=array(
 			'version'=>'1.0',//版本号	str (8)	是	目前版本号：1.0
			'serviceUri'=>'YX0002',//交易代码		str (8)	是	
			'charset'=>'UTF-8',//编码格式 str (8)	是	
			'signType'=>'RSA',//签名方式str (8) 	是	
			'nonceStr'=>generate_password(16),//随机字符串	是	随机字符串
			'agentId'=>$agentId,//代理商号		str (8)	是	受理方预分配的渠道代理商标识
			'merId'=>$merId,//商户号		str (10)	是	进件返回的merId
			'orderNo'=>$orderNo,//订单号		str (32)	是	交易订单号(与签约订单号一致)
			'authCode'=>$authCode,//手机验证码		str (6)	是	签约验证码
 		);
 		echo json_encode($data);
 		$url=$this->url.'/repay';
 		$res=$this->request($url,$data);
 		var_dump($res);die;
 	}
 	/**
 	 * 上传资料文件
 	 * @return [type] [description]
 	 */
 	public function upload_material($agentId='1001057',$merId='9000105494',$uid=42){
 		$cert=MemberCert::where(['cert_member_id'=>$uid])->find();
 		if(!$cert || !$cert->IdPositiveImgUrl || !$cert->IdNegativeImgUrl || !$cert->IdPortraitImgUrl){
 			return ['code'=>'101','msg'=>'实名认证信息不全，请补全实名信息。'];
 		}
 		$data=array(
 			'version'=>'1.0',//版本号		str (8)	是	目前版本号：1.0
			'serviceUri'=>'YX0003',//交易代码		str (8)	是	YX0003
			'charset'=>'UTF-8',//编码格式	charset	str (8)	是	UTF-8
			'signType'=>'RSA',//签名方式	signType	str (8) 	是	RSA
			'nonceStr'=>generate_password(16),//随机字符串		str (32)	是	随机字符串
			'agentId'=>$agentId,//代理商号		str (8)	是	受理方预分配的渠道代理商标识
			'merId'=>$merId,//商户号	merId	str (10)	是	进件返回的merId
			'image1'=>base64_encode(file_get_contents($cert->IdPositiveImgUrl)),//图片字符串		str (256)	是	身份证正面
			'image2'=>base64_encode(file_get_contents($cert->IdNegativeImgUrl)),//图片字符串		str (256)	是	身份证反面
			'image3'=>base64_encode(file_get_contents($cert->IdPortraitImgUrl)),//图片字符串		str (256)	是	手持身份证
 		);
 		echo json_encode($data);
 		$url=$this->url.'/repay';
 		$res=$this->request($url,$data);
 		var_dump($res);die;
 	}
 	/**
 	 * 订单支付
 	 * @return [type] [description]
 	 */
 	public function order_pay($agentId='1001057',$merId='9000105494'){
 		$data=array(
 			'version'=>'1.0',//版本号		str (8)	是	目前版本号：
			'serviceUri'=>'YX0004',//交易代码		str (8)	是	
			'charset'=>'UTF-8',//编码格式		str (8)	是	
			'signType'=>'RSA',//签名方式		str (8) 	是	
			'nonceStr'=>generate_password(16),//随机字符串		str (32)	是	随机字符串
			'agentId'=>$agentId,//代理商号		str (8)	是	受理方预分配的渠道代理商标识
			'merId'=>$merId,//商户号		str (10)	是	进件返回的merId
			'orderNo'=>generate_password(16),//订单号		str (32)	是	交易订单号
			'bankCard'=>'6258101661675746',//银行卡号		str (16)	是	用于交易的银行卡号
			'notifyUrl'=>System::getName('system_url').'/Api/Huiliandaihuan/payCallback',//通知地址		str (256)	是	异步通知地址(暂无)
			'amount'=>'19800',//交易金额		str (8)	是	以分为单位
 		);
 		echo json_encode($data);
 		$url=$this->url.'/repay';
 		$res=$this->request($url,$data);
 		var_dump($res);die;
 	}
 	/**
 	 * 支付回调
 	 * @return [type] [description]
 	 */
 	public function payCallback(){
 		$data = file_get_contents("php://input");
 		file_put_contents('hulian_luodi2.txt', $data);
 		file_put_contents('hulian_luodi.txt', json_encode($data));
 		$data=array(
 		);
 	}
 	/**
 	 * 订单查询
 	 * @return [type] [description]
 	 */
 	public function order_query($agentId='1001057',$orderNo='ltO5vxQGIFx8ZjCv'){
 		$data=array(
 			'version'=>'1.0',//版本号		str (8)	是	目前版本号：1.0
			'serviceUri'=>'YX0005',//交易代码		str (8)	是	YX0005
			'charset'=>'UTF-8',//编码格式		str (8)	是	UTF-8
			'signType'=>'RSA',//签名方式		str (8) 	是	RSA
			'nonceStr'=>generate_password(16),//随机字符串		str (32)	是	随机字符串
			'agentId'=>$agentId,//代理商号		str (8)	是	受理方预分配的渠道代理商标识
			'orderNo'=>$orderNo,//订单号		str (32)	是	原交易订单号
 		);
 		// echo json_encode($data);
 		$url=$this->url.'/repay';
 		$res=$this->request($url,$data);
 		var_dump($res);die;
 	}
 	   /**
     * 获取请求字符串
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function get_sign($arr){
        // $private_key="./static/rsakey/huilian/hldh.pem";
        // $pub_key="./static/rsakey/1001034_pub.pem";
        $arr=$this->SortByASCII($arr);
        $string=http_build_query($arr);
        // echo $string;die;
        $string=urldecode($string);
        // echo $string;die;
        $res=$this->pri_encode($string);
        // echo $res;die;
        // $rsa=new \app\api\controller\Rsa($pub_key,$private_key);
        // $res=$rsa->encrypt($string);
        return $res;  
    }
    /**
     * 加密
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    function pri_encode($data){
        $encrypted='';
        $private_key=file_get_contents("./static/rsakey/huilian/1001057_prv.pem"); //秘钥
        $pi_key =  openssl_pkey_get_private($private_key);  //这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id  
        $str='';
        foreach (str_split($data, 117) as $chunk) {
            openssl_private_encrypt($chunk,$encryptedTemp,$pi_key);  //私钥加密  
            $str .= $encryptedTemp;
        }
        $encrypted = base64_encode($str);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
        return $encrypted;
    }
    /**
     * 数组按照ASCII码排序
     * @return [type] [description]
     */
    public function SortByASCII($arr){
        $keys=array_keys($arr);
        $newrr=[];
        foreach ($keys as $k => $v) {
            if(!$v){
                exit(json_encode(['code'=>101,'msg'=>'参数'.$k.'获取失败','data'=>'']));
            }
            $newrr[$k]['asc']=ord($v);
            $newrr[$k]['key']=$v;
            $keys[$k]=ord($v);
        }
        array_multisort($keys, SORT_ASC, $newrr);
        $return=[];
        foreach ($newrr as $k => $v) {
           $return[$v['key']]=$arr[$v['key']];
        }
        return $return;
    }
    /**
     * 发送请求
     * @param  [type] $url [description]
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function request($url,$arr){
        $sign=$this->get_sign($arr);
        $arr['sign']=$sign;//签名数据
        // echo json_encode($arr);die;
        $arr=http_build_query($arr);
        // $arr=urldecode($arr);
        // echo $arr;die;
        $return=curl_post($url,'post',$arr,0);
        echo $return;die;
        $result=json_decode($return,true);
        return $result;
    }
 }