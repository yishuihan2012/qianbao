<?php
 namespace app\api\controller;
 use think\Db;
 use think\Session;
 use think\Config;
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
 *  @version Huilianluodi controller / Api 代还入网
 *  @author 许成成(1015571416@qq.com)
 *   @datetime    2018-02-23 15:13:05
 *   @return 
 */
 class Yipay{
    protected $url;
    private $version;
    public function __construct(){
        $this->version='1.0';
        $this->orgno='18000042';
        $this->merno='990584000011008';
        $this->secretkey='866b0eb1616d3c3ffa8e86ba8a27a55d';
        $this->url='http://cgi.yiyoupay.net/cgi-bin/nps/action';
        $this->branchId='70900000';
    }
    /**
     * 商户入网
     * @return [type] [description]
     */
    public function mech_income($members){
        $data=array(
            'userId'=>generate_password(16),//商户编号
            'userName'=>$members->memberCert->cert_member_name,//商户名称
            'userNick'=>$members->memberCert->cert_member_name,//商户昵称
            'userPhone'=>$members->member_mobile,//法人电话
            'userAccount'=>$members->memberCert->cert_member_name,//法人姓名
            'userCert'=>$members->memberCert->cert_member_idcard,//法人身份证号
            'userEmail'=>"855422537@qq.com",//商户邮箱
            'userAddress'=>"山东省济南市",//商户地址
            'userMemo'=>System::getName("sitename").$members->member_mobile,//商户备注
            'settleBankNo'=>$members->memberCashcard->card_bankno,//结算卡号
            'settleBankPhone'=>$members->memberCashcard->card_phone,//结算卡预留手机号
            'settleBankCnaps'=>"102100099996",//结算卡联行号
            'branchId'=>$this->branchId,
        );
        // echo json_encode($data);die;
        $card_union=$this->get_card_union($members->memberCashcard->card_bankno);
        // print_r($card_union);die;
        if($card_union['code']==200){
            $data['settleBankCnaps']=$card_union['bankCode'];
        }else{
            return['code'=>-1,'msg'=>"获取联行号失败"];
        }
        // echo json_encode($data);die;
        $res=$this->request('SdkUserStoreBind',$data);
        if($res['code']=='0000'){
            $res['code']=200;
        }
        // var_dump($res);die;
        return $res;
    }
    /**
     * 商户信息变更 
     * @return [type] [description]
     */
    public function mech_update(){
        $data=array(
            'userCode'=>'ST0001000470755',// 商户编号               
            'settleBankNo'=>"",// 绑定结算卡号                 
            'settleBankPhone'=>"",//绑定结算卡手机           
            'settleBankCnaps'=>"",// 绑定结算卡联行号
        );
        $res=$this->request('SdkUserStoreModify',$data);
        return $res;
    }
    public function mech_rate_set($mech_id,$mech_secret,$passageway,$members){
        $rate=PassagewayItem::where(['item_passageway'=>$passageway->passageway_id,'item_group'=>$members->member_group_id])->find();
        // print_r($rate);die;
        $data=array(
            'userCode'=>$mech_id,// 商户编号 系统返回商户编号                                                                    
            'payType' =>"1",// 交易类型                                                                            
            'orderRateT0'=>(string)$rate->item_also,//交易费率  0.36（费率0.36/100）                                                                    
            'settleChargeT0'=>(string)($rate->item_qffix),//提现附加费用 单位：分（200）                                                                   
        );
        $res=$this->request('SdkUserStoreRate',$data,$mech_id,$mech_secret);
        if($res['code']=='0000'){
            $res['code']=200;
        }
        return $res;
    }
    /**
     *  商户查询
     * @return [type] [description]
     */
    public function mech_query(){

    }
    /**
     *  绑卡查询
     * @param  [type] $bankNo      [description]
     * @param  [type] $mech_id     [description]
     * @param  [type] $mech_secret [description]
     * @return [type]              [description]
     */
    public function card_bind_query($bankNo,$mech_id,$mech_secret){
        $data['bankNo']=$bankNo;
        $res=$this->request('SdkBindCardH5Query',$data,$mech_id,$mech_secret);
        // print_r($res);die;
        if($res['code']=='0000'){
            $res['code']=200;
        }
        return  $res;
    }
    /**
     *  签约绑卡
     * @return [type] [description]
     */
    public function card_bind($mech_id,$mech_secret,$MemberCreditcard,$passageway){
        $data=array(
            'linkId'=>$passageway->passageway_id.','.$MemberCreditcard->card_id.','.generate_password(10),// 订单流水号    三方平台唯一 
            'payType'=>1,                                                                
            'bankNo'=>$MemberCreditcard->card_bankno,//银行卡号                                                                           
            'bankPhone'=>$MemberCreditcard->card_phone,//绑定手机号码String(11)                                                                                        
            'frontUrl'=>System::getName('system_url')."/api/Yipay/card_bind_fronturl",//页面通知地址                                                                            
            'notifyUrl'=>System::getName('system_url')."/api/Yipay/card_bind_notifyUrl",//异步通知地址  String                    
        );
        // Cache::set($data['linkId'],$data,600);
        $res=$this->request('SdkBindCardH5',$data,$mech_id,$mech_secret);
        if($res['code']=='0000'){
            $res['code']=200;
        }
        return $res;
    }
    /**
     * 绑卡页面回跳地址
     * @return [type] [description]
     */
    public function card_bind_fronturl(){
        $params=input();
        $str = var_export($params,TRUE);
        file_put_contents('card_bind_fronturl.txt', $str);
        if(!is_array($params)){
            $params=json_decode($params,true);
        }
        if($params['respCode']=='00'){
            $return['msg']="绑卡成功，请关闭当前页面重新提交";

        }else{
            $return['msg']=$params['respMsg'];
        }
        return redirect('Userurl/show_error', ['data' =>$return['msg']]);
    }
    public function card_bind_notifyUrl(){
        $params=input();
        file_put_contents('card_bind_notifyUrl', json_encode($params));
        if($params['orderStatus']=='0000'){//快捷绑卡成功
            // $cache=Cache::pull($params['linkId']);
            $bind_info=explode(',', $params['linkId']);
            // print_r($bind_info);die;
            $res=MemberCreditPas::where(['member_credit_pas_creditid'=>$bind_info[1],'member_credit_pas_pasid'=>$bind_info[0]])->update(['member_credit_pas_status'=>1]);
            if($res){
                echo 'success';die;
            }
        }
    }
    /**
     * 支付
     * @return [type] [description]
     */
    public function pay($order,$passageway_mech){
        #1获取卡信息
        $card_info=MemberCreditcard::where(['card_bankno'=>$order['order_card']])->find();

        #2获取通道信息
        $merch=Passageway::where(['passageway_id'=>$order['order_passageway']])->find();
        $member_net=MemberNets::where(['net_member_id'=>$order['order_member']])->find();
        $passageway_mech=explode(',', $member_net[$merch->passageway_no]);
        //查询子商户号
        $member_pas=MemberCreditPas::where(['member_credit_pas_pasid'=>$order['order_passageway'],'member_credit_pas_creditid'=>$card_info['card_id']])->find();
        // $order=GenerationOrder::where(['order_type'=>1])->where('order_no','lt',$value['order_no'])->order('order_id desc')->find();
        // if($order['user_rate'] !=$value['user_rate']){//重新报备
        //     $arr['rate']=$value['user_rate']*100;
        //     // $res=$this->reincome($passageway_mech,$member_pas['member_credit_pas_info'],$arr);
        // }
        $member_base=Member::where(['member_id'=>$order['order_member']])->find(); 

        if(!$order['order_platform_no'] || $order['order_status']!=1){
            $update_order['order_platform_no']=$order['order_platform_no']=get_plantform_pinyin().$member_base->member_mobile.make_rand_code();
            $update_res=GenerationOrder::where(['order_id'=>$order['order_id']])->update($update_order);
        }

        $data=array(
            'linkId'  =>$order['order_platform_no'],//订单流水号  M  String 三方平台唯一                                                                  
            'orderType'=>"10",//订单类型M  String 10:实时到账                                                                 
            'amount'=>(string)($order['order_money']*100),// 消费金额M String 单位:分                                                                    
            'bankNo'=>$order['order_card'],//银行卡号M     String                                                                                              
            'bankAccount'=>$card_info['card_bankname'],// 银行账户 M  String                                                                                              
            'bankPhone'=>$card_info['card_phone'],//绑定手机号码  M String(11)                                                                                              
            'bankCert' =>$member_base->memberCert->cert_member_idcard,//绑定身份证号                                                                                  
            'bankCvv'=>$card_info['card_Ident'],//信用卡后三位 tring(3) 信用卡消费为必选项                                                                   
            'bankYxq'=>$card_info['card_expireDate'],//  信用卡有效期  信用卡消费为必选项-MMYY(月年格式)                                                                    
            'notifyUrl'=>System::getName('system_url')."/api/Yipay/card_pay_notifyUrl",//异步通知地址,不传系统将不做异步通知                                                                 
            // 'goodsName' =>"超意兴快餐",//商品名称 String                                                                                              
        );
        $res=$this->request('SdkNocardOrderPayNoSms',$data,$passageway_mech[0],$passageway_mech[1]);
        // print_r($res);die;
        $income['code']=-1;
        $income['msg']=$income['msg']='FAIL';
        
        $update['back_statusDesc']=isset($res['orderMemo'])?$res['orderMemo']:$res['msg'];
        $is_commission=0;
        if($res['code']=='0000'){//成功
            if($res['orderStatus']=='0000'){//成功
                $update['back_tradeNo']=$res['orderNo'];
                $income['code']=200;
                $income['back_status']=$income['msg']='success';
                $update['order_status']='2';
                $is_commission=1;
            }else if($res['orderStatus']=='0100'){//处理中
                $update['order_status']='4';
            }else{//失败
                $update['order_status']='-1';
            }
        }else{
             $update['order_status']='-1';
        }
        //添加执行记录
        $res=GenerationOrder::where(['order_id'=>$order['order_id']])->update($update);
         #更改完状态后续操作
        $notice=new \app\api\controller\Membernet();
        $action=$notice->plan_notice($order,$income,$member_base,$is_commission,$merch);

    }
    /**
     * 支付回调
     * @return [type] [description]
     */
    public function card_pay_notifyUrl(){
        $params=input();
        file_put_contents('card_pay_notifyUrl.txt', json_encode($params));
        $pay=GenerationOrder::where(['order_platform_no'=>$params['linkId']])->find();
        if($params['orderStatus']=='0000'){//成功
            $income['code']=200;
            $income['back_status']=$arr['back_status']='success';
            $arr['order_status']='2';
            $is_commission=1;
        }else if($params['orderStatus']=='0100'){//处理中
            $arr['order_status']='4';
        }else{//失败
            $arr['order_status']='-1';
            $arr['back_status']='FAIL';
        }
        $arr['back_statusDesc']=$params['orderMemo'];
        $arr['back_status']=$params['orderMemo'];
        $arr['back_tradeNo']=$params['orderNo'];
        //添加执行记录
        $res=GenerationOrder::where(['order_id'=>$pay['order_id']])->update($arr);
        if($params['orderStatus']=='0000'){//成功
            // 极光推送
            if($pay['is_commission']=='0'){
                $has_fenrun=db('commission')->where('commission_from',$pay['order_id'])->find();
                if(!$has_fenrun){
                        $update_res=GenerationOrder::where(['order_id'=>$pay['order_id']])->update(['is_commission'=>1]);
                        $fenrun= new \app\api\controller\Commission();
                        $fenrun_result=$fenrun->MemberFenRun($pay['order_member'],$pay['order_money'],$pay['order_passageway'],3,'代还分润',$pay['order_id']);
                }
            }
            $card_num=substr($pay['order_card'],-4);
            jpush($pay['order_member'],'还款计划扣款成功通知',"您制定的尾号{$card_num}的还款计划成功扣款".$pay['order_money']."元，在APP内还款计划里即可查看详情。");
            echo "success";die;
        }
    }
    /**
     * 订单查询
     * @return [type] [description]
     */
    public function order_status($order_id,$is_print=0){
        $res['respCode']=-1;
        $order_detail=GenerationOrder::where(['order_id'=>$order_id])->find();

        $data['linkId']=$order_detail['order_platform_no'];
        $res=$this->request('SdkOrderQuery',$data);
        if($res['code']=="0000"){
            if( $res['orderStatus']=='0000'){
                 $res['respCode']='10000';
            }else if($res['orderStatus']=='0100'){

            }else{
                $res['respCode']='10001';
            }
        }
        $res['respMessage']=$res['msg']?$res['msg']:$res['orderMemo'];
        if($is_print){
            echo json_encode($res);die;
        }
        return $res;
    }
    /**
     * 查询商户余额
     * @return [type] [description]
     */
    public function merch_remain($order_id){
        $order_detail=GenerationOrder::where(['order_id'=>$order_id])->find();
        $data['linkId']=$order_detail['order_platform_no'];
        $data['payType']=1;
        $res=$this->request('SdkSettleBalance',$data);
        var_dump($res);die;
    }
    /**
     * 代付
     * @return [type] [description]
     */
    public function qfpay($order,$passageway_mech){
        $card_info=MemberCreditcard::where(['card_bankno'=>$order['order_card']])->find();

        $merch=Passageway::where(['passageway_id'=>$order['order_passageway']])->find();
         //查询子商户号
        $Membernet=MemberNets::where(['net_member_id'=>$order['order_member']])->find();
        $merId=$Membernet[$merch->passageway_no];
        $passageway_mech=explode(',', $merId);
        //查询子商户号
        $member_pas=MemberCreditPas::where(['member_credit_pas_pasid'=>$order['order_passageway'],'member_credit_pas_creditid'=>$card_info['card_id']])->find();
        
        $member_base=Member::where(['member_id'=>$order['order_member']])->find();
        // $rate=PassagewayItem::where(['item_passageway'=>$order['order_passageway'],'item_group'=>$member_info['member_group_id']])->find();
        if(!$order['order_platform_no'] || $order['order_status']!=1){
            $update_order['order_platform_no']=$order['order_platform_no']=get_plantform_pinyin().$member_base->member_mobile.make_rand_code();
            $update_res=GenerationOrder::where(['order_id'=>$order['order_id']])->update($update_order);
        }

        $data=array(
            'linkId'=>$order['order_platform_no'],// 三方订单流水号                                                                                                      
            'amount'=>(string)($order['order_real_get']*100),// 结算到账金额                                           
            'bankNo'=>$order['order_card'],//结算银行卡号                                                               
            'bankAccount'=>$member_base->memberCert->cert_member_name,//结算银行账户                                                                                                           
            'bankPhone'=>$card_info['card_phone'],// 绑定手机号码                                           
            'bankCert' =>$member_base->memberCert->cert_member_idcard,//身份证号                            
            'bankName'=>$card_info['card_bankname'],//银行名称String                                                                                              
            'bankCode'=>"03080000",//银行支行联行号支行联行号-大额(超5W)代付需要精确到支行信息                                                                    
            'notifyUrl'=>System::getName('system_url')."/api/Yipay/card_qfpay_notifyUrl",//支付结果回调地址 不传，系统不做后台异步通知推送                                                                 
        );
        $card_union=$this->get_card_union($member_base->memberCashcard->card_bankno);
        // print_r($card_union);die;
        if($card_union['code']==200){
            $data['bankCode']=$card_union['bankCode'];
        }else{
            return['code'=>-1,'msg'=>"获取联行号失败"];
        }
        $res=$this->request('SdkSettleMcg',$data,$passageway_mech[0],$passageway_mech[1]);
        $income['code']=-1;
        $income['msg']=$income['msg']='FAIL';
        
        $update['back_statusDesc']=$res['msg'];
        $is_commission=0;
        if($res['code']=='0000'){//成功
            $update['back_tradeNo']=$res['orderNo'];
            $income['code']=200;
            $income['back_status']=$income['msg']='success';
            $update['order_status']='2';
            $is_commission=1;
        }else if($res['code']=='0100'){//处理中
            $update['order_status']='4';
        }else{//失败
            $update['order_status']='-1';
        }
        //添加执行记录
        $res=GenerationOrder::where(['order_id'=>$order['order_id']])->update($update);
         #更改完状态后续操作
        // $notice=new \app\api\controller\Membernet();
        // $action=$notice->plan_notice($order,$income,$member_base,$is_commission,$merch);
    }
    /**
     *  代付回调
     * @return [type] [description]
     */
    public function card_qfpay_notifyUrl(){
        $params=input();
        file_put_contents('card_qfpay_notifyUrl.txt', json_encode($params));
        if(!is_array($params)){
            $params=json_decode($params,true);
        }
        $pay=GenerationOrder::where(['order_platform_no'=>$params['linkId']])->find();
        if($params['settleStatus']=='0000'){//成功
            $income['code']=200;
            $income['back_status']=$arr['back_status']='success';
            $arr['order_status']='2';
            $is_commission=1;
        }else if($params['settleStatus']=='0100'){//处理中
            $arr['order_status']='4';
        }else{//失败
            $arr['order_status']='-1';
            $arr['back_status']='FAIL';
        }
        $arr['back_statusDesc']=$params['settleMemo'];
        $arr['back_status']=$params['settleMemo'];
        $arr['back_tradeNo']=$params['settleNo'];
        //添加执行记录
        $res=GenerationOrder::where(['order_id'=>$pay['order_id']])->update($arr);
        if($params['settleStatus']=='0000'){//成功
            echo 'success';die;
        }

    }
    /**
     * 获取联行号
     * @return [type] [description]
     */
    public function get_card_union($bank_no='6225768621318847'){
        $data['bankNo']=$bank_no;
        $res=$this->request('SdkSettleBankCnaps',$data);
        // var_dump($res);die;
        return $res;
    }
    /**
     * 获取请求字符串
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function get_sign($arr){
        $private_key="./static/rsakey/1001034_prv.pem";
        $pub_key="./static/rsakey/1001034_pub.pem";
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
        $private_key=file_get_contents('./static/rsakey/yipay/JN000001_private_key.pem');  //秘钥
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
     * aes加密
     * @param [type] $plainText [description]
     * @param [type] $aesKey    [description]
     */
    public function AESencode($plainText,$aesKey){
        $encryptStr = openssl_encrypt($plainText, 'AES-128-ECB', $aesKey, OPENSSL_RAW_DATA);
        $encryptStr = strtoupper(bin2hex($encryptStr));
        return $encryptStr;
    }
    /**
     * data数据解密
     * @param  [type] $encryptData [description]
     * @return [type]              [description]
     */
    public function dataDecrypt($encryptData) {
        $encryptKey = $encryptData['encryptkey'];
        $data = $encryptData['data'];

        //base64解码
        $encryptKey = base64_decode($encryptKey);
        //rsa解密aes密钥
        $priKey=file_get_contents('./static/rsakey/yipay/JN000001_private_key.pem');  //秘钥
        $privateKey = openssl_pkey_get_private($priKey);
        openssl_private_decrypt($encryptKey,$aesKey, $privateKey);
        //BCD解码
        $data = pack("H*", $data);
        //aes解码
        $encryptStr = openssl_decrypt($data, 'AES-128-ECB', $aesKey, OPENSSL_RAW_DATA);

        return json_decode($encryptStr, TRUE);
    }
    /**
     * 发送请求
     * @param  [type] $url [description]
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function request($action,$data,$merno='',$secretkey=''){
        $rand_string=generate_password(16);
        // var_dump(base64_decode(AESencode(json_encode($data),$rand_string)));die;
        $merno=$merno?$merno:$this->merno;
        $params=array(
            'version'=>"2.0",
            'orgNo'=>$this->orgno,
            'merNo'=>$merno,
            'action'=>$action,
            'data'=>$this->AESencode(json_encode($data),$rand_string),
            'encryptkey'=>$this->pri_encode($rand_string),
        );
        // echo json_encode($params);
        // version+orgNo+merNo+action+data+商户蜜钥KEY
        $secretkey=$secretkey?$secretkey:$this->secretkey;
        $params['sign']=md5($params['version'].$this->orgno.$merno.$action.$params['data'].$secretkey);
        // echo $this->url."?".http_build_query($params);die;
        $res=curl_post($this->url,'post',$params,0);
        // echo $res;die;
        $result=json_decode($res,true);
        if($result && $result['data']){
            //解密
            $return=$this->dataDecrypt($result);
            if($return['code']=='0000'){
                $return['code']=200;
            }
        }else{
            $back=json_decode($result['data'],true);
            $return['code']=-1;
            $return['msg']=$back['respMsg'];
        }
        return $return;
    }
 }