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
 	public function huilian_income($Passageway=29,$card_id=63){
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
 		if($res['code']=='10000' &&  isset($res['respCode']) && $res['respCode']=='10000' && $res['merId']){ //成功存储商户号
 			$update[$Passageways->passageway_no]=$res['merId'];
            $has=MemberNets::where(['net_member_id'=>$card_info['card_member_id']])->update($update);
             if($has){
                return $res['merId'];
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
 	public function card_sign(){
        $params=input('');
 		$data=array(
 			'version'=>'1.0',//	版本号 tr (8)	是	目前版本号：1.0
			'serviceUri'=>"YX0001",	//交易代码	str (8)	是	YX0001
			'charset'=>'UTF-8',// 编码格式	str (8)	是	
			'signType'=>"RSA",//签名方式是	
			'nonceStr'=>generate_password(16),// 随机字符串		str (32)	是	随机字符串
			'agentId'=>$params['agentid'], //代理商号		是	受理方预分配的渠道代理商标识
			'merId'=>$params['merId'],// 商户号是	进件返回的merId
			'orderNo'=>generate_password(16),// 订单号	是	商户交易订单号
			'phone'=>$params['phone'],//手机号码	是	银行预留手机号
			'bankCard'=>$params['creditCardNo'],//银行卡号是	用于支付的银行卡号(只支持借记卡)
			'expDate'=>substr($params['expireDate'],0,2).'-'.substr($params['expireDate'],2,2),//N(String)   信用卡时必填，格式:mm-YY
            'CVN2'=>$params['cvv'] ,//N(String)   信用卡时必填
 		);
 		// print_r($data);die;
 		$url=$this->url.'/repay';
 		$res=$this->request($url,$data);
        // print_r($res);die;
        if($res['code']=='10000' &&  isset($res['respCode']) && $res['respCode']=='10000'){ 
            $return['code']='200';
            $return['msg']='验证码发送成功';
            $return['orderNo']=$data['orderNo'];
        }else{
            $return['code']='-1';
            $return['msg']=isset($res['respMessage'])? $res['respMessage']:$res['message'];
            $return['orderNo']=$data['orderNo'];
        }
 		return $return;
 	}
 	/**
 	 * 確認簽約
 	 * @param  string $agentId  [description]
 	 * @param  string $merId    [description]
 	 * @param  string $orderNo  [description]
 	 * @param  string $authCode [description]
 	 * @return [type]           [description]
 	 */
 	public function card_sign_confirm(){
        $params=input('');
 		$data=array(
 			'version'=>'1.0',//版本号	str (8)	是	目前版本号：1.0
			'serviceUri'=>'YX0002',//交易代码		str (8)	是	
			'charset'=>'UTF-8',//编码格式 str (8)	是	
			'signType'=>'RSA',//签名方式str (8) 	是	
			'nonceStr'=>generate_password(16),//随机字符串	是	随机字符串
			'agentId'=>$params['agentid'],//代理商号		str (8)	是	受理方预分配的渠道代理商标识
			'merId'=>$params['merId'],//商户号		str (10)	是	进件返回的merId
			'orderNo'=>$params['orderNo'],//订单号		str (32)	是	交易订单号(与签约订单号一致)
			'authCode'=>$params['smsCode'],//手机验证码		str (6)	是	签约验证码
 		);
 		// print_r($data);die;
 		$url=$this->url.'/repay';
 		$res=$this->request($url,$data);
 		if($res['code']=='10000' &&  isset($res['respCode']) && $res['respCode']=='10000'){ 
            $res=MemberCreditPas::where(['member_credit_pas_creditid'=>$params['cardid'],'member_credit_pas_pasid'=>$params['passageway_id']])->update(['member_credit_pas_status'=>1]);
            if($res){
                $return['code']='200';
                $return['msg']='签约成功';
            }else{
                $return['code']='-1';
                $return['msg']='签约失败，请重试。';
            }
        }else{
            $return['code']='-1';
            $return['msg']=isset($res['respMessage'])? $res['respMessage']:$res['message'];
        }
        return $return;
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
 		$image1=base64_encode(gzcompress($cert->IdPositiveImgUrl));
 		$image2=base64_encode(gzcompress($cert->IdNegativeImgUrl)); 
 		$image3=base64_encode(gzcompress($cert->IdPortraitImgUrl));
 		// @unlink('./thumb.jpg');
 		$data=array(
 			'version'=>'1.0',//版本号		str (8)	是	目前版本号：1.0
			'serviceUri'=>'YX0003',//交易代码		str (8)	是	YX0003
			'charset'=>'UTF-8',//编码格式	charset	str (8)	是	UTF-8
			'signType'=>'RSA',//签名方式	signType	str (8) 	是	RSA
			'nonceStr'=>generate_password(16),//随机字符串		str (32)	是	随机字符串
			'agentId'=>$agentId,//代理商号		str (8)	是	受理方预分配的渠道代理商标识
			'merId'=>$merId,//商户号	merId	str (10)	是	进件返回的merId
			'image1'=>$image1,//图片字符串		str (256)	是	身份证正面
			'image2'=>$image2,//图片字符串		str (256)	是	身份证反面
			'image3'=>$image3,//图片字符串		str (256)	是	手持身份证
 		);
 		// echo json_encode($data);die;
 		$url=$this->url.'/repay';
 		$res=$this->request($url,$data);
        if($res['code']=='10000' &&  isset($res['respCode']) && $res['respCode']=='10000'){ 
             $return['code']='200';
             $return['msg']='上传成功';
        }else{
             $return['code']='-1';
             $return['msg']='上传资料失败';
        }
        return $return;
 	}
 	/**
 	 * 订单支付
 	 * @return [type] [description]
 	 */
 	public function pay($order,$agentId='1001057'){
        $Passageway=Passageway::where(['passageway_id'=>$order['order_passageway']])->find();
        $member_net=MemberNets::where(['net_member_id'=>$order['order_member']])->find();
        $member_base=Member::where(['member_id'=>$order['order_member']])->find(); 
        //订单号
        if(!$order['order_platform_no'] || $order['order_status']!=1){
            $update_order['order_platform_no']=$order['order_platform_no']=uniqid();
            $update_res=GenerationOrder::where(['order_id'=>$order['order_id']])->update($update_order);
        }
 		$data=array(
 			'version'=>'1.0',//版本号		str (8)	是	目前版本号：
			'serviceUri'=>'YX0004',//交易代码		str (8)	是	
			'charset'=>'UTF-8',//编码格式		str (8)	是	
			'signType'=>'RSA',//签名方式		str (8) 	是	
			'nonceStr'=>generate_password(16),//随机字符串		str (32)	是	随机字符串
			'agentId'=>$agentId,//代理商号		str (8)	是	受理方预分配的渠道代理商标识
			'merId'=>$member_net[$Passageway->passageway_no],//商户号		str (10)	是	进件返回的merId
			'orderNo'=>generate_password(16),//订单号		str (32)	是	交易订单号
			'bankCard'=>$order['order_card'],//银行卡号		str (16)	是	用于交易的银行卡号
			'notifyUrl'=>System::getName('system_url').'/Api/Huiliandaihuan/payCallback',//通知地址		str (256)	是	异步通知地址(暂无)
			'amount'=>$order['order_money']*100,//交易金额		str (8)	是	以分为单位
 		);
 		// echo json_encode($data);die;
 		$url=$this->url.'/repay';
 		$res=$this->request($url,$data);
 		$income['code']=-1;
        $income['back_status']='FAIL';
        if(isset($res['code']) && $res['code']=='10000'){
            $update['back_tradeNo']=$res['orderNum'];
            $update['back_status']=$res['respCode'];
            $update['back_statusDesc']=isset($res['respMessage'])?$res['respMessage']:$res['message'];
            if($res['respCode']=="10000"){
                $income['code']=200;
                $income['back_status']='success';
                $update['order_status']='2';
            }else if($res['respCode']=="10002"){
                //处理中
                $update['order_status']='4';
            }else{
                $update['order_status']='-1';
                //失败
            }
        }else{
          $update['back_statusDesc']=isset($res['respMessage'])?$res['respMessage']:$res['message'];
          $update['back_status']='FAIL';
          $update['order_status']='-1';  
        }
        //添加执行记录
        $res=GenerationOrder::where(['order_id'=>$order['order_id']])->update($update);
         #更改完状态后续操作
        $notice=new \app\api\controller\Membernet();
        $action=$notice->plan_notice($order,$income,$member_base,1,$Passageway);
 	}
 	/**
 	 * 支付回调
 	 * @return [type] [description]
 	 */
 	public function payCallback(){
 		$data = file_get_contents("php://input");
        parse_str($data,$res);
        $pay=GenerationOrder::where(['order_platform_no'=>$res['orderNo']])->find();
        $income['code']=-1;
        $income['back_status']='FAIL';
        if($res && is_array($res)){
        	if(isset($res['code']) && $res['code']==10000){ //是否处理成功
                if(isset($res['respCode']) && $res['respCode']==10000){
                    $income['code']=200;
                    $income['back_status']='success';
                    $arr['order_status']='2';
                }else if($res['respCode']=="10002"){
                    //处理中
                    $update['order_status']='4';
                }else{
                    $update['order_status']='-1';
                    //失败
                }
                $arr['back_statusDesc']=isset($res['respMessage'])?$res['respMessage']:$res['message'];
                $arr['back_status']=$res['respCode'];
	        }else{
	            $arr['order_status']='-1';
	            $arr['back_statusDesc']=isset($res['respMessage'])?$res['respMessage']:$res['message'];
	        }
            $update=GenerationOrder::where(['order_platform_no'=>$res['orderNo']])->update($arr);

            if($pay['order_status'] !=$arr['order_status']){
                 $notice=new \app\api\controller\Membernet();
                 $Passageway=Passageway::where(['passageway_id'=>$pay['order_passageway']])->find();
                 $member_base=Member::where(['member_id'=>$pay['order_member']])->find(); 
                 $action=$notice->plan_notice($pay,$income,$member_base,1,$Passageway);
            }
        }
        if(isset($res['respCode']) && $res['respCode']==10000){
             echo "success";die;
        }

 	}
 	/**
 	 * 订单查询
 	 * @return [type] [description]
 	 */
 	public function order_status($order_id='1001057',$is_print='1'){
        $order_detail=GenerationOrder::where(['order_id'=>$order_id])->find();
        $passageway=Passageway::where(['passageway_id'=>$order_detail['order_passageway']])->find();
 		$data=array(
 			'version'=>'1.0',//版本号		str (8)	是	目前版本号：1.0
			'serviceUri'=>'YX0005',//交易代码		str (8)	是	YX0005
			'charset'=>'UTF-8',//编码格式		str (8)	是	UTF-8
			'signType'=>'RSA',//签名方式		str (8) 	是	RSA
			'nonceStr'=>generate_password(16),//随机字符串		str (32)	是	随机字符串
			'agentId'=>$passageway->passageway_mech,//代理商号		str (8)	是	受理方预分配的渠道代理商标识
			'orderNo'=>$order_detail['order_platform_no'],//订单号		str (32)	是	原交易订单号
 		);
 		// echo json_encode($data);
 		$url=$this->url.'/repay';
 		$res=$this->request($url,$data);
 		if($is_print){
            echo json_encode($res);
        }else{
            return $res;
        }
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
        // echo $return;die;
        $result=json_decode($return,true);
        return $result;
    }
 }