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
 /**
 *  @version Huilianjinchuang controller / Api 代还入网
 *  @author 许成成(1015571416@qq.com)
 *   @datetime    2018-02-23 15:13:05
 *   @return 
 */
 class Huilianjinchuang{
 	protected $url;
    private $version;
 	public function __construct(){
        $this->version='1.0';
 		$this->url='http://120.77.180.22:8089/v1.0/facade';
 	}
 	/**
 	 * 进件请求
 	 * @return [type] [description]
 	 */
 	public function income($Passageway,$card_id){
        //获取行用卡新
 		$card_info=MemberCreditcard::where(['card_id'=>$card_id])->find();
 		if(!$card_info){
 			exit();
 		}
        //获取用户信息
 		$member_info=Member::where(['member_id'=>$card_info['card_member_id']])->find();
 		if(!$member_info){
 			exit();
 		}
        //获取卡对应银行信息
 		$bank_name=mb_substr($card_info['card_bankname'],-4,2);
 		// echo $bank_name;die;
 		$BankInfo=BankInfo::where('info_sortname','like','%'.$bank_name.'%')->find();
 		// print_r($BankInfo);die;
 		
 		$idcard=$member_info->membercert->cert_member_idcard;
 		$name=$card_info['card_name'];
 		$bankId=$BankInfo['info_pab'];
 		$bankCard=$card_info['card_bankno'];
 		$bankName=$BankInfo['info_name'];
        //获取通道费率
 		$rate=PassagewayItem::where(['item_passageway'=>$Passageway,'item_group'=>$member_info['member_group_id']])->find();
        $also=($rate->item_also)*10;
        $daikou=($rate->item_charges);
        //获取通道信息
        $agentId='1001001';
 		$arr=array(
 			'version'=>$this->version,
			'charset'=>'UTF-8',//	编码方式UTF-8
			'agentId'=>$agentId,//受理方预分配的渠道代理商标识
			'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
			'signType'=>'RSA',//签名方式，固定RSA
			'isCompay'=>'0',//对公对私标识0为对私，1为对公
			'idcardType'=>'01',//证件类型 暂只支持 01 身份证
			'idcard'=>$idcard,//证件号码
			'name'=>$name,//姓名
			'phone'=>$member_info['member_mobile'],//手机号
			'bankId'=>$bankId,//联行号
			'bankCard'=>$bankCard,//银行卡号
			'bankName'=>$bankName,//开户行名称
			'bankNo'=>$BankInfo['info_pab'],//开户行代码(PAB)
			'rate'=>$also,//费率‱ ，不小于代理商费率
			'extraFee'=>$daikou,//手续费(分)
			// 'address'=>'',//N(String)	地址
			'remark'=>'汇联金创代还进件',//备注
 		);
 		$sign=$this->get_string($arr);
 		$arr['sign']=$sign;//签名数据
 		$url=$this->url.'/report';
 		$res=curl_post($url,'post',json_encode($arr));
        print_r($res);die;
 	}
 	/**
 	 * 下单支付
 	 * @return [type] [description]
 	 */
 	public function pay($order){
        $agentid=1001001;
        $merId=122;

        $card_info=MemberCreditcard::where(['card_idcard'=>$order['order_card']])->find();

        $member_info=Member::where(['member_id'=>$order['order_member']])->find();

        $bank_name=mb_substr($card_info['card_bankname'],-4,2);
        // echo $bank_name;die;
        $BankInfo=BankInfo::where('info_sortname','like','%'.$bank_name.'%')->find();
        $expDate=$card_info['card_expireDate'];
        // $expDate=
        $arr=array(
            'version'=>$this->version,
            'charset'=>'UTF-8',//编码方式UTF-8
            'agentId'=>$agentid,//受理方预分配的渠道代理商标识
            'merId'=>$merId,//子商户号
            'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
            'signType'=>'RSA',//签名方式，固定RSA
            'isCompay'=>'0',//对公对私标识0为对私，1为对公
            'idcardType'=>'01',//证件类型 暂只支持 01 身份证
            'orderNo'=>make_rand_code(),//订单号
            'idcard' =>$member_info->membercert->cert_member_idcard,//证件号码
            'name'=>$member_info->membercert->cert_member_idcard, //姓名
            'phone'=>$member_info['member_mobile'],//手机号
            'bankId'=>$BankInfo['info_pab'],//联行号
            'bankCard'=>$order['order_card'],//银行卡号
            'notifyUrl'=>System::getName('system_url').'/Api/Huilianjinchuang/payCallback',//异步通知地址
            // 'returnUrl'=>'', //N(String)   返回地址
            'CVN2'=>$card_info['card_Ident'],//CVN2
            'expDate'=>$expDate,//信用卡有效期，格式 MM-yy
            'amount'=>$order['order_money']*100,//金额(分)
        );
        $url=$this->url.'/report';
        $res=$this->request($url,$arr);
        print_r($res);die;
 	}
    /**
     * 支付回调
     * @return [type] [description]
     */
    public function payCallback(){
        $data = file_get_contents("php://input");
        file_put_contents('huilianpay_callback.txt', $data);
    }
 	/**
 	 * 代付
 	 * @return [type] [description]
 	 */
 	public function qfpay($order){
        $agentid=1001001;
        $merId=122;

        $card_info=MemberCreditcard::where(['card_idcard'=>$order['order_card']])->find();

        $member_info=Member::where(['member_id'=>$order['order_member']])->find();

        $bank_name=mb_substr($card_info['card_bankname'],-4,2);
        // echo $bank_name;die;
        $BankInfo=BankInfo::where('info_sortname','like','%'.$bank_name.'%')->find();
        $expDate=$card_info['card_expireDate'];

        $arr=array(
            'version'=>$this->version,
            'charset'=>'UTF-8',//编码方式UTF-8
            'agentId'=>$agentid,//受理方预分配的渠道代理商标识
            'merId'=>$merId,//子商户号
            'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
            'signType'=>"RSA",//签名方式，固定RSA
            'orderNo'=>make_rand_code(),//订单号
            'notifyUrl'=>System::getName('system_url').'/Api/Huilianjinchuang/cashCallback',//异步通知地址
            // 'returnUrl'=>'', //N(String)   返回地址
            'amount'=>$order['order_money']*100,//金额(分)
        );
        $url=$this->url.'/mercPay';
        $res=$this->request($url,$arr);
        print_r($res);die;
 	}
    public function cashCallback(){
        $data = file_get_contents("php://input");
        file_put_contents('huilianpay_cashcallback.txt', $data);
    }
 	/**
 	 * 订单状态查询
 	 * @return [type] [description]
 	 */
 	public function order_status($order_id,$is_print=''){
        $arr=array(
            'version'=>$this->version,
            'charset'=>'UTF-8',//编码方式UTF-8
            'agentId'=>$agentid,//受理方预分配的渠道代理商标识
            'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
            'signType'=>"RSA",//签名方式，固定RSA
            'orderNo'=>$order_detail[''],//订单号

        );
        $url=$this->url.'/query';
        $res=$this->request($url,$arr);
        if($is_print){
            echo json_encode($res);
        }else{
            return $res;
        }
 	}
 	/**
 	 * 余额查询
 	 * @return [type] [description]
 	 */
 	public function query_remain($uid,$is_print=''){
        $agentid=1001001;
        $merId=122;
        $arr=array(
            'version'=> $this->version,
            'charset'=>'UTF-8',//编码方式UTF-8
            'agentId'=>$agentid,//受理方预分配的渠道代理商标识
            'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
            'signType'=>'RSA',// 签名方式，固定RSA
        );
        $url=$this->url.'/queryBalance';
        $res=$this->request($url,$arr);
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
 		$private_key="./static/rsakey/1001001_prv.pem";
		$pub_key="./static/rsakey/1001001_pub.pem";
 		$arr=$this->SortByASCII($arr);
 		$string=http_build_query($arr);
        $res=$this->pri_encode($string);
 		// $rsa=new \app\api\controller\Rsa($pub_key,$private_key);
 		// $res=$rsa->encrypt($string);
 		return $res;
        
 	}
    function pri_encode($data){
        $encrypted='';
        $private_key=file_get_contents('./static/rsakey/1001001_prv.pem');  //秘钥
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
        $res=curl_post($url,'post',json_encode($arr));
        $result=json_decode($res,true);
        return $result;
    }
 }