<?php
 namespace app\api\payment;
 use app\index\model\Order;
 use app\index\model\Member;
 use app\index\model\Passageway;
 use app\index\model\MemberCreditcard;
 class mishua
 {
    protected $version = 1;
    protected $mchNo = '';
    protected $url = "http://API_URL /quick.do?m=order"; 
    public function __construct(){
        
    }
    //支付
    public function pay(Order $order,$versionNo){
        $versionNo='1';//米刷版本号 , 值固定为1
        $arr = array(
              'versionNo'   => $versionNo, //版本固定为1
              'mchNo'       	=> $this->passway_info->passageway_mech, //商户号
              'price'       	=> $price, //单位为元，精确到0.01,必须大于1元
              'description' 	=> $description, //交易描述
              'orderDate'   => date('YmdHis', time()), //订单日期
              'tradeNo'     	=> $tradeNo, //商户平台内部流水号，请确保唯一 TOdo
              'notifyUrl'   	=> $this->passway_info->cashout->cashout_callback/*HOST . "/index.php?s=/Api/Quckpayment/qucikPayCallBack"*/, //异步通知URL
              'callbackUrl' 	=>'123'/*HOST . "/index.php?s=/Api/Quckpayment/turnurl"*/, //页面回跳地址
              'payCardNo' => $this->card_info->card_bankno, //信用卡卡号
              'accName'    => $this->card_info->card_name, //持卡人姓名 必填
              'accIdCard'   => $this->card_info->card_idcard, //卡人身份证  必填
              'bankName'   => $this->member_card->card_bankname, //  结算卡开户行  必填  结算卡开户行
              'cardNo'      	 => $this->member_card->card_bankno, //算卡卡号 必填  结算卡卡号
              'downPayFee'  	=> $this->also->item_rate*10, //结算费率  必填  接入机构给商户的费率，D0直清按照此费率结算，千分之X， 精确到0.01
              'downDrawFee' => '0', // 代付费 选填  每笔扣商户额外代付费。不填为不扣。
        );

        //请求体参数加密 AES对称加密 然后连接加密字符串转MD5转为大写
        $payload = $this->encrypt(json_encode($arr),$this->passway_info->passageway_pwd_key);
        //return $payload;
        $sign    	= strtoupper(md5($payload.$this->passway_info->passageway_key));
        $request = array('mchNo' =>$this->passway_info->passageway_mech,'payload' => $payload, 'sign' => $sign,);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8"));
        curl_setopt($ch, CURLOPT_URL, $this->passway_info->cashout->cashout_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper('post'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $result = json_decode($res, true);
        if ($result['code'] == 0) {
             $datas=$this->decrypt($result['payload'],$this->passway_info->passageway_pwd_key);
              $datas = trim($datas);
              $datas = substr($datas, 0, strpos($datas, '}') + 1);
              $resul = json_decode($datas, true);
              //写入快捷支付订单
              $order_result=$this->writeorder($tradeNo, $price, $price*($this->also->item_rate/100) ,$description='米刷测试',$resul['transNo']);
             if(!$order_result)
                  return ['code'=>327];
               return $resul['tranStr'];
        }else{
            return $result;
        }
    }
    /**
     * 订单查询
     * pay_status 1待支付 2成功 -1 失败 -2超时 
     * qf_status -1代付失败  1代付中 2代付成功
     * resp_message 交易成功
     */
    public function order_query($order){
        $url='http://pay.mishua.cn/zhonlinepay/service/down/trans/checkDzero';
        $p=Passageway::get($order->order_passway)->toarray();
        $data=[
          'versionNo'=>1,
          'mchNo'=>$p['passageway_mech'],
          'transNo'=>$v['order_thead_no']
        ];
        $res=repay_request($data,$p['passageway_mech'],$url,'0102030405060708',$p['passageway_pwd_key'],$p['passageway_key']);
        $result=[];
        if(isset($res['status']) && isset($res['qfStatus']) && isset($res['statusDesc'])){
          #支付状态
          if($res['status']=='00'){
            $result['pay_status']=2;
          }elseif($res['status']=='01' || $res['status']=='09'){
            $result['pay_status']=1;
          }else{
            $result['pay_status']=-1;
          }
          #代付状态
          if($res['qfStatus']=='SUCCESS'){
            $result['qf_status']=2;
          }elseif($res['qfStatus']=='IN_PROCESS'){
            $result['qf_status']=1;
          }else{
            $result['qf_status']=-1;
          }
          $result['resp_message']=$res['statusDesc'];
        }else{
          $result=[
            'pay_status'=>-1,
            'qf_status'=>-1,
            'resp_message'=>'xj:接口查询失败或返回参数不全',
          ];
        }
        return $result;
    }
    //回调
    public function callback(){
        
    }
 }
?>