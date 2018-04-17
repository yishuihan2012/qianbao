<?php
 namespace app\api\controller;
 use think\Db;
 use think\Session;
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
 class Huilianluodi{
    protected $url;
    private $version;
    public function __construct(){
        $this->version='1.0';
        $this->url='http://39.108.137.8:8099/v1.0/facade/';
    }
    /**
     * 进件请求
     * @return [type] [description]
     */
    public function income($Passageway,$card_id){
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
        $arr=array(
            'version'=>$this->version,
            'serviceUri'=>'SJ0001',
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
            'expDate'=>substr($card_info['card_expireDate'],0,2).'-'.substr($card_info['card_expireDate'],2,2),//N(String)   信用卡时必填，格式:mm-YY
            'CVN2'=>$card_info['card_Ident'] ,//N(String)   信用卡时必填
            // 'address'=>'',//N(String)    地址
        );
        // echo json_encode($arr);die;
        $url=$this->url.'/repay';
        $res=$this->request($url,$arr);
        // echo json_encode($res);die;
        // return $res;
        if( isset($res['code']) && $res['code']=='10000' &&  isset($res['respCode']) && $res['respCode']=='10000' && $res['merId']){ //成功存储商户号
            $update[$Passageways->passageway_no]=$res['merId'];
            $has=MemberNets::where(['net_member_id'=>$card_info['card_member_id']])->update($update);
             if($has){
                $return['code']='200';
                $return['msg']='入网成功';
                $return['merId']=$res['merId'];
            }else{
                $return['code']='-1';
                $return['msg']=isset($res['respMessage'])? $res['respMessage']:$res['message'];
            }

        }else{
            $return['code']='-1';
            $return['msg']="商户入网失败";
            
        }
        return $return;
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
    public function reincome($agentid,$merId,$data){
        $type="N"; //R、N、B  N修改银行卡相关信息 B 修改手机号等  R 修改费率信息
        $arr=array(
            'version'=>$this->version,
            'charset'=>'UTF-8',//   编码方式UTF-8
            'agentId'=>$agentid,//受理方预分配的渠道代理商标识
            'merId'=> $merId,//要修改的商户号
            'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
            'signType'=>'RSA',//签名方式，固定RSA
        );
        //修改费率
        if(isset($data['rate'])){
            
            $arr['rate']=$data['rate']*100;
            $arr['type']='R';
        }
        if(isset($data['extraFee'])){
            
            $arr['extraFee']=$data['extraFee']; //文档上写的单位是分，但测试实际结果为元
            $arr['type']='R';
        }
        //如果更换卡号就像当于重新绑新卡了，不用重新进件
        if(isset($arr['bankCard'])){
            // $arr['type']='N';
            // $arr['bankCard']=$data['bankCard'];
            // $arr['bankNo']=
            // $arr['bankId']=
        }
        // 修改预留手机
        if(isset($data['phone'])){
            $arr['type']='B';
            $arr['phone']=$data['phone'];
        }
        $url=$this->url.'/updateMid';
        $res=$this->request($url,$arr);
        return $res;
        // print_r($res);die;
    }
    
    public function card_bind($agentId,$merId,$phone,$bankCard,$passageway,$card_id){
       $data=array(
            'version'=>$this->version,
            'serviceUri'=>'YQ0001',//交易代码      str (8) 是   YQ0001
            'charset'=>'UTF-8',//编码格式     str (8) 是   UTF-8
            'signType'=>'RSA',//签名方式        str (8)     是   RSA
            'nonceStr'=>make_rand_code(),//随机字符串   nonceStr    str (32)    是   随机字符串
            'agentId'=>$agentId,//代理商号     str (8) 是   受理方预分配的渠道代理商标识
            'merId'=>$merId,//商户号    str (10)    是   进件返回的merId
            'orderNo'=>$passageway.'-'.$card_id.'-'.$phone,//订单号 orderNo str (32)    是   商户交易订单号
            'phone'=>$phone,//手机号码       str (11)    是   银行预留手机号
            'bankCard'=>$bankCard,//银行卡号        str (32)    是   用于支付的银行卡号(信用卡)
            'notifyUrl'=>System::getName('system_url').'/Api/Huilianluodi/card_bind_notify',//通知地址       str (256)   是   异步通知地址
        );
        // echo json_encode($data);
        $url=$this->url.'/repay';
        $res=$this->request($url,$data);
        // echo json_encode($res);die;
        if(isset($res['code']) && $res['code']=='10000' &&  isset($res['respCode']) && $res['respCode']=='10000' && $res['url']){ 
            $return['code']='200';
            $return['msg']='第一次使用请先签约快捷支付';
            $return['url']=$res['url'];
        }else{
            $return['code']='-1';
            $return['msg']=isset($res['respMessage'])? $res['respMessage']:$res['message'];
        }
        return $return;

    }
    public function card_bind_notify(){
        $params=input('');
        //file_put_contents('huilian_new.txt',json_encode($params));
        if(isset($params['code']) && $params['code']=='10000'){ 
            $arr=explode('-', $params['orderNo']);
            $res=MemberCreditPas::where(['member_credit_pas_creditid'=>$arr[1],'member_credit_pas_pasid'=>$arr[0]])->update(['member_credit_pas_status'=>1]);
            if($res){
                $order_no=Session::get($arr[2].'order_no');
                return redirect(System::getName('system_url').'/api/Userurl/repayment_plan_create_detail/order_no/'.$order_no);
            }
        }else{
          
        }
    }
    /**
     * 支付请求
     * @return [type] [description]
     */
    public function pay($value,$passageway_mech){
        #1获取卡信息
        $card_info=MemberCreditcard::where(['card_bankno'=>$value['order_card']])->find();

        #2获取通道信息
        $merch=Passageway::where(['passageway_id'=>$value['order_passageway']])->find();
        //查询子商户号
        $Membernet=MemberNets::where(['net_member_id'=>$value['order_member']])->find();
        $merId=$Membernet[$merch->passageway_no];
        $member_pas=MemberCreditPas::where(['member_credit_pas_pasid'=>$value['order_passageway'],'member_credit_pas_creditid'=>$card_info['card_id']])->find();
        //查询上次刷卡费率是否和这次一样，不一样需要变更费率。
        $order=GenerationOrder::where(['order_type'=>1])->where('order_no','lt',$value['order_no'])->order('order_id desc')->find();
        if($order['user_rate'] !=$value['user_rate']){//重新报备
            $arr['rate']=$value['user_rate']*100;
            // $res=$this->reincome($passageway_mech,$member_pas['member_credit_pas_info'],$arr);
        }
        $member_base=Member::where(['member_id'=>$value['order_member']])->find(); 
        
        //订单号
        if(!$value['order_platform_no'] || $value['order_status']!=1){
            $update_order['order_platform_no']=$value['order_platform_no']=uniqid();
            $update_res=GenerationOrder::where(['order_id'=>$value['order_id']])->update($update_order);
        }

        //商品类别
        $goods=db('goods')->select();
        $rand_good=array_rand($goods,1);
        $rand_good=$goods[$rand_good];
        $update_res=GenerationOrder::where(['order_id'=>$value['order_id']])->update(['order_product_type'=>$rand_good['type_id'],'order_product_name'=>$rand_good['name']]);
        $data=array(
            'version'=>$this->version,// M(String)   1.0
            'serviceUri'=>'YQ0002',
            'charset'=>'UTF-8',// M(String)   编码方式UTF-8
            'agentId'=>$passageway_mech ,//M(String)   受理方预分配的渠道代理商标识
            'merId'=>$merId,// M(String)   子商户号
            'nonceStr'=>make_rand_code(),// M(String)   随机字符串，字符范围a-zA-Z0-9
            'signType'=>'RSA',//  M(String)   签名方式，固定RSA
            'orderNo'=>$value['order_platform_no'],// M(String)   订单号
            'notifyUrl'=>System::getName('system_url').'/Api/Huilianluodi/payCallback',// M(String)   异步通知地址
            'amount'=>$value['order_money']*100 ,//M(String)   金额(分)
            'bankCard'=>$value['order_card'],//银行卡号        str (32)    是   用于支付的银行卡号(信用卡)
            'product'=>$rand_good['type_id'],// 商品类别str (8) 是   
            'goods'=>$rand_good['name'],//商品描述   str (32)    是   
            // 'chnSeriaNo'=>''.//交易使用商户号   str (8) 否   
            // 'cityCode'=>'',//城市编码        str (8) 否   
            // 'caregoryUnion'=>'',//银联行业类型     str (8) 否   
        );
        echo json_encode($data);die;
        $url=$this->url.'/repay';
        $res=$this->request($url,$data);
        $income['code']=-1;
        $income['back_status']=$income['status']='FAIL';
        $is_commission=0;
        if(isset($res['code']) && $res['code']=='10000' && $res['respCode']=='10000'){
            $update['back_tradeNo']=$res['orderNum'];
            $update['back_status']=$res['respCode'];
            $update['back_statusDesc']=$res['respMessage'];
            if($res['respCode']=="10000"){
                $income['code']=200;
                $income['back_status']=$income['status']='success';
                $update['order_status']='2';
                $is_commission=1;
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
        $res=GenerationOrder::where(['order_id'=>$value['order_id']])->update($update);
         #更改完状态后续操作
        $notice=new \app\api\controller\Membernet();
        $action=$notice->plan_notice($value,$income,$member_base,$is_commission,$merch);

    }
    /**
     * 支付回调
     * @return [type] [description]
     */
    public function payCallback(){
        $data = file_get_contents("php://input");
        file_put_contents('huilianpay_new_callback.txt', $data);
        $pay=GenerationOrder::where(['order_platform_no'=>$data['orderNo']])->find();
        if($data['code']==10000){ //是否处理成功
                if($data['respCode']==10000){
                    $arr['order_status']='2';
                }else if($data['respCode']=="10002"){
                    //处理中
                    $update['order_status']='4';
                }else{
                    $update['order_status']='-1';
                    //失败
                }
                $arr['back_statusDesc']=$data['respMessage'];
                $arr['back_status']=$data['respCode'];
        }else{
            $arr['order_status']='-1';
            $arr['back_statusDesc']=$data['message'];
        }
        $arr['back_status']='FAIL';
        // $arr['back_statusDesc']=$data['statusDesc'];
        $arr['back_tradeNo']=$data['orderNum'];
        //添加执行记录
        $res=GenerationOrder::where(['order_id'=>$pay['order_id']])->update($arr);
        if($data['code']==10000 && $data['respCode']==10000){
            // 极光推送
            $card_num=substr($pay['order_card'],-4);
            jpush($pay['order_member'],'还款计划扣款成功通知',"您制定的尾号{$card_num}的还款计划成功扣款".$pay['order_money']."元，在APP内还款计划里即可查看详情。");
            echo "success";die;
        }
    }
    /**
     * 代付
     * @return [type] [description]
     */
    public function qfpay($order,$passageway_mech){
        $card_info=MemberCreditcard::where(['card_bankno'=>$order['order_card']])->find();

        $merch=Passageway::where(['passageway_id'=>$order['order_passageway']])->find();
         //查询子商户号
        $Membernet=MemberNets::where(['net_member_id'=>$value['order_member']])->find();
        $merId=$Membernet[$merch->passageway_no];

        $member_base=Member::where(['member_id'=>$order['order_member']])->find();
        // $rate=PassagewayItem::where(['item_passageway'=>$order['order_passageway'],'item_group'=>$member_info['member_group_id']])->find();
        if(!$order['order_platform_no'] || $order['order_status']!=1){
            $update_order['order_platform_no']=$order['order_platform_no']=uniqid();
            $update_res=GenerationOrder::where(['order_id'=>$order['order_id']])->update($update_order);
        }
        //查询上次刷卡费率是否和这次一样，不一样需要变更费率。
        $order_last=GenerationOrder::where(['order_type'=>1])->where('order_no','lt',$order['order_no'])->order('order_id desc')->find();
        if($order_last['user_fix'] !=$order['user_fix']){//重新报备
            $arr['extraFee']=$order['user_fix']*100;
            // $res=$this->reincome($passageway_mech,$member_pas['member_credit_pas_info'],$arr);
        }
        //获取用户入网信息
        // $member_net=MemberNets::where(['net_member_id'=>$order['order_member']])->find();
        $arr=array(
            'version'=>$this->version,
            'serviceUri'=>'YQ0003',
            'charset'=>'UTF-8',//编码方式UTF-8
            'agentId'=>$passageway_mech,//受理方预分配的渠道代理商标识
            'merId'=>$merId,//子商户号
            'nonceStr'=>$order['order_platform_no'],//随机字符串，字符范围a-zA-Z0-9
            'signType'=>"RSA",//签名方式，固定RSA
            'orderNo'=>$order['order_platform_no'],//订单号
            'notifyUrl'=>System::getName('system_url').'/Api/Huilianjinchuang/cashCallback',//异步通知地址
            'bankCard'=>$card_info['card_bankno'],//银行卡号        str (32)    是   用于代付的银行卡号(信用卡)
            'bankName'=>$card_info['card_bankname'],//银行名称        str (32)    是   开户银行的行名
            'Phone'=>$card_info['card_phone'],//手机号    str (11)    是   银行预留手机号
            'amount'=>$order['order_real_get']*100,//金额(分)
        );
        // echo json_encode($arr);
        $url=$this->url.'/repay';
        $res=$this->request($url,$arr);
        // print_r($res);
        $income['code']=-1;
        $income['status']="FAIL";
        if($res['code']=='10000' && $res['respCode']=='10000'){
             $update['back_tradeNo']=isset($res['orderNum'])?$res['orderNum']:'';
             $update['back_status']=$res['respCode'];
             $update['back_statusDesc']=$res['respMessage'];
            if($res['respCode']=="10000"){
                $income['code']='200';
                $income['status']="success";
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
          $generation['generation_state']=-1;
          // $update['order_buckle']=$rate['item_charges']/100;         
        }
        //添加执行记录
        $res=GenerationOrder::where(['order_id'=>$order['order_id']])->update($update);
        // 更新卡计划
        if(isset($generation)){
            Generation::where(['generation_id'=>$order['order_no']])->update($generation);
        }
         #更改完状态后续操作
        $notice=new \app\api\controller\Membernet();
        $action=$notice->plan_notice($order,$income,$member_base,0,$merch);
    }
    /**
     * 还款回调
     * @return [type] [description]
     */
    public function cashCallback(){
        $data = file_get_contents("php://input");
        file_put_contents('huiliancash_new_ callback.txt', $data);
        $pay=GenerationOrder::where(['order_platform_no'=>$data['orderNo']])->find();
        if($data['code']==10000){ //是否处理成功
                if($data['respCode']==10000){
                    $arr['order_status']='2';
                }else if($data['respCode']=="10002"){
                    //处理中
                    $update['order_status']='4';
                }else{
                    $update['order_status']='-1';
                    //失败
                }
                $arr['back_statusDesc']=$data['respMessage'];
                $arr['back_status']=$data['respCode'];
        }else{
            $arr['order_status']='-1';
            $arr['back_statusDesc']=$data['message'];
        }
        $arr['back_status']='FAIL';
        // $arr['back_statusDesc']=$data['statusDesc'];
        $arr['back_tradeNo']=$data['orderNum'];
        //添加执行记录
        $res=GenerationOrder::where(['order_id'=>$pay['order_id']])->update($arr);
        if($data['code']==10000 && $data['respCode']==10000){
            // 极光推送
            $card_num=substr($pay['order_card'],-4);
            jpush($pay['order_member'],'还款计划扣款成功通知',"您制定的尾号{$card_num}的还款计划成功扣款".$pay['order_money']."元，在APP内还款计划里即可查看详情。");
            echo "success";die;
        }
    }
    /**
     * 订单状态查询
     * @return [type] [description]
     */
    public function order_status($id,$is_print=''){
        $order_detail=GenerationOrder::where(['order_id'=>$id])->find();
        $passageway=Passageway::where(['passageway_id'=>$order_detail['order_passageway']])->find();
        $arr=array(
            'version'=>$this->version,
            'charset'=>'UTF-8',//编码方式UTF-8
            'agentId'=>$passageway['passageway_mech'],//受理方预分配的渠道代理商标识
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
    public function query_remain($Passageway,$is_print=''){
        $passageway=Passageway::where(['passageway_id'=>$Passageway])->find();
        // var_dump($passageway);die;
        $arr=array(
            'version'=> $this->version,
            'charset'=>'UTF-8',//编码方式UTF-8
            'agentId'=>$passageway['passageway_mech'],//受理方预分配的渠道代理商标识
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
        $private_key=file_get_contents('./static/rsakey/1001034_prv.pem');  //秘钥
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
        $arr=http_build_query($arr);
        $return=curl_post($url,'post',$arr,0);
        // echo $return;die;
        $result=json_decode($return,true);
        return $result;
    }
 }