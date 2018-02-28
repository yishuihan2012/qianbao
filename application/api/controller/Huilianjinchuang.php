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
            exit('1231');
        }
        //获取用户信息
        $member_info=Member::where(['member_id'=>$card_info['card_member_id']])->find();
        if(!$member_info){
            exit('456');
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
        $daikou=($rate->item_charges);
        //获取通道信息
        $agentId='1001001';//****
        $arr=array(
            'version'=>$this->version,
            'charset'=>'UTF-8',//   编码方式UTF-8
            'agentId'=>$agentId,//受理方预分配的渠道代理商标识
            'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
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
            // 'address'=>'',//N(String)    地址
            'remark'=>'汇联金创代还进件',//备注
        );
        // var_dump($arr);die;
        $url=$this->url.'/report';
        $res=$this->request($url,$arr);
        return $res;
        // if($res['code']=="10000" && $res['respCode']=10000){
        //     $merId=$res['merId'];
        // }
        // echo json_encode($res);die;
    }
    /**
     * 重新进件
     * @return [type] [description]
     * 修改费率传入
     *      rate    N(String)   费率‱ ，不小于代理商费率
            extraFee    N(String)   手续费(分)
     * 修改 银行卡信息 传入
     *      bankId  N(String)   联行号
     *      bankCard    N(String)   银行卡号
     *      bankName    N(String)   开户行名称
     * 修改手机号
     *      phone   N(String)   手机号
     *      address N(String)   地址
     *      remark  N(String)   备注
     */
    public function reincome($merId,$type,$data){
        $agentId=1001001;
        $merId=9000000530;
        $type="N"; //R、N、B  N修改银行卡相关信息 B 修改手机号等  R 修改费率信息
        $arr=array(
            'version'=>$this->version,
            'charset'=>'UTF-8',//   编码方式UTF-8
            'agentId'=>$agentId,//受理方预分配的渠道代理商标识
            'merId'=> $merId,//要修改的商户号
            'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
            'signType'=>'RSA',//签名方式，固定RSA
            'type'=>$type,//R、N、B
        );
        $arr=array_merge($arr,$data);
        $url=$this->url.'/updateMid';
        $res=$this->request($url,$arr);
        print_r($res);die;
    }
    /**
     * 执行某个计划
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function action_single_plan($id){
        $order_info=GenerationOrder::where(['order_id'=>$id])->find();
        if($order_info['order_type']==1){
            $this->pay($order_info);
        }else{
            $this->qfpay($order_info);
        }
        
    }
    /**
     * 下单支付
     * @return [type] [description]
     */
    public function pay($order,$passageway_mech){
        $card_info=MemberCreditcard::where(['card_bankno'=>$order['order_card']])->find();
        $member_info=Member::where(['member_id'=>$order['order_member']])->find();

        $bank_name=mb_substr($card_info['card_bankname'],-4,2);
        // echo $bank_name;die;
        $BankInfo=BankInfo::where('info_sortname','like','%'.$bank_name.'%')->find();
        $expDate=$card_info['card_expireDate'];

        $arr=array(
            'version'=>$this->version,
            'charset'=>'UTF-8',//编码方式UTF-8
            'agentId'=>$passageway_mech,//受理方预分配的渠道代理商标识
            'merId'=>$card_info['huilian_income'],//子商户号
            'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
            'signType'=>'RSA',//签名方式，固定RSA
            'isCompay'=>'0',//对公对私标识0为对私，1为对公
            'idcardType'=>'01',//证件类型 暂只支持 01 身份证
            'orderNo'=>$order['order_platform_no'],//订单号
            'idcard' =>$member_info->membercert->cert_member_idcard,//证件号码
            'name'=>$member_info->membercert->cert_member_name, //姓名
            'phone'=>$member_info['member_mobile'],//手机号
            'bankId'=>$BankInfo['info_pab'],//联行号
            'bankCard'=>$order['order_card'],//银行卡号
            'notifyUrl'=>System::getName('system_url').'/Api/Huilianjinchuang/payCallback',//异步通知地址
            // 'returnUrl'=>'', //N(String)   返回地址
            'CVN2'=>$card_info['card_Ident'],//CVN2
            'expDate'=>$expDate,//信用卡有效期，格式 MM-yy
            'amount'=>$order['order_money']*100,//金额(分)
        );
        $url=$this->url.'/pay';
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
        if($data['code']==10000){ //是否处理成功
                if($data['code']==10000){
                    $arr['order_status']='2';
                }else{
                    $arr['order_status']='-1';
                }
                $arr['back_statusDesc']=$data['respMessage'];
        }else{
            $arr['order_status']='-1';
            $arr['back_statusDesc']=$data['message'];
        }
        // $arr['back_status']=$data['status'];
        // $arr['back_statusDesc']=$data['statusDesc'];
        $arr['back_tradeNo']=$data['orderNum'];
        //添加执行记录
        $res=GenerationOrder::where(['order_id'=>$pay['order_id']])->update($arr);
    }
    /**
     * 代付
     * @return [type] [description]
     */
    public function qfpay($order,$passageway_mech){

        $card_info=MemberCreditcard::where(['card_idcard'=>$order['order_card']])->find();

        $member_info=Member::where(['member_id'=>$order['order_member']])->find();

        $bank_name=mb_substr($card_info['card_bankname'],-4,2);
        // echo $bank_name;die;
        $BankInfo=BankInfo::where('info_sortname','like','%'.$bank_name.'%')->find();
        $expDate=$card_info['card_expireDate'];

        $arr=array(
            'version'=>$this->version,
            'charset'=>'UTF-8',//编码方式UTF-8
            'agentId'=>$passageway_mech,//受理方预分配的渠道代理商标识
            'merId'=>$card_info['huilian_income'],//子商户号
            'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
            'signType'=>"RSA",//签名方式，固定RSA
            'orderNo'=>$order['order_platform_no'],//订单号
            'notifyUrl'=>System::getName('system_url').'/Api/Huilianjinchuang/cashCallback',//异步通知地址
            // 'returnUrl'=>'', //N(String)   返回地址
            'amount'=>$order['order_money']*100,//金额(分)
        );
        // echo json_encode($arr);die;
        $url=$this->url.'/mercPay';
        $res=$this->request($url,$arr);
        print_r($res);die;
    }
    /**
     * 还款回调
     * @return [type] [description]
     */
    public function cashCallback(){
        $data = file_get_contents("php://input");
        file_put_contents('huilianpay_cashcallback.txt', $data);
    }
    /**
     * 订单状态查询
     * @return [type] [description]
     */
    public function order_status($id,$is_print=''){
        $agentid=1001001;
        $order_detail=GenerationOrder::where(['order_id'=>$id])->find();
        $arr=array(
            'version'=>$this->version,
            'charset'=>'UTF-8',//编码方式UTF-8
            'agentId'=>$agentid,//受理方预分配的渠道代理商标识
            'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
            'signType'=>"RSA",//签名方式，固定RSA
            'orderNo'=>$order_detail['order_platform_no'],//订单号

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
        $merId=9000000530;
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
        $string=urldecode($string);
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
        $return=curl_post($url,'post',$arr,0);
        // echo $return;die;
        $result=json_decode($return,true);
        return $result;
    }
 }