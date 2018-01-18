<?php
/**
 *  @version MemberNet controller / Api 会员进件入网接口
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-25 15:31:05
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
 use app\api\controller\Commission;

 class Membernets{ 
      public $error;
      private $member; //会员信息
      private $membercert; //会员认证信息
      private $membercard; //会员结算卡信息
      private $passway; //通道信息
      function __construct($memberId,$passwayId){
           try{
                 #根据memberId获取会员信息和会员的实名认证信息还有会员银行卡信息
                 $this->member=Member::get($memberId);
                 if(! $this->member)
                      $this->error=314;
                 if($this->member->member_cert!='1')
                      $this->error=356;
                 $this->membercert=MemberCert::get(['cert_member_id'=>$memberId]);
                 if(!$this->membercert)
                      $this->error=367;
                 #获取用户结算卡信息
                 $this->membercard=MemberCashcard::get(['card_member_id'=>$memberId]);
                 if(!$this->membercard)
                      $this->error=459;
                 #获取通道信息
                 $this->passway=Passageway::get($passwayId);
                 if(!$this->passway)
                      $this->error=454; 
           }catch (\Exception $e) {
                 $this->error=460; //TODO 更改错误码 入网失败错误码
           }
      }

      /**
      *  @version quickNet / Api 快捷支付商户入网接口
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-25 14:36:05
      *  @param   $member=要入网的会员   ☆☆☆::使用中
      **/
      public function quickNet()
      {
          $memberAlso=PassagewayItem::where(['item_group'=>$this->member->member_group_id,'item_passageway'=>$this->passway->passageway_id])->value('item_rate');
           $arr=array( 
                 'accountName' => $this->membercard->card_name,//账户户名，采用URLEncode编码
                 'accountno'      => $this->membercard->card_bankno,//结算账号，不能重复
                 'accountType'  =>1,//1或2。1(对私), 2(对公)
                 'address'          => "无影山中路四建美林大厦20层2007-1",//采用URLEncode编码
                 'agentno'          => $this->passway->passageway_mech,//商户编号
                 'area'               => "370105:天桥区",//同上
                 'bankName'      => $this->membercard->card_bank_address,//开户行支行名称。采用URLEncode编码
                 'bankno'           => $this->membercard->card_bank_lang,//开户行支行联行号，例如310305500198。所支持银行参见码表
                 'bizLicense'      => System::getName('business_license'),//商户营业执照
                 'city'                 => "370100:济南" ,// 同上
                 'd0Rate'           => $memberAlso/100,//小数点后四位，例如0.0035 D0费率
                 'email'              => System::getName('platform_email'), //email
                 'fullName'         => $this->membercard->card_name.rand(1000,9999), //商户全称 采用URLEncode编码
                 'identityCard'   => $this->membercard->card_idcard,//银行预留身份证号
                 'merchName'    => $this->membercard->card_name.rand(100,999), //商户简称 采用URLEncode编码
                 'mobile'            => $this->membercard->card_phone,//不能重复
                 'province'         => "370000:山东",//固定格式，必须是“编码:名称”一起上送，标准地区码（440000:广东）参见码表
                 'quickFixed'     => System::getName('charge_max'),//封顶值 10000为不封顶
                 'settleType'       => 0,//结算类型 0或1。0(D0), 1(T1)      
                 't1Rate'           => System::getName('charge_t1'), //小数点后四位，例如0.0035
                 'version'            => "v1.2",//接口固定版本号
           );
           //dump($arr);
           $param=get_signature($arr,$this->passway->passageway_key);
           //dump($param);
           $result=curl_post("http://api.ekbuyclub.com:6001/quick.do?m=registermerch",'post',$param,'Content-Type: application/x-www-form-urlencoded; charset=gbk');
           $data=json_decode(mb_convert_encoding($result, 'utf-8', 'GBK,UTF-8,ASCII'),true);
           //dump($data);
           if($data['respCode']=="00" || $data['merchno']!="")
                 $res=MemberNet::where(['net_member_id'=>$this->member->member_id])->setField($this->passway->passageway_no, $data['merchno']);
           // return ($data['respCode']=="00" || $res) ? true :  false;
           return $data;
      }
      
      /**
      *  @version rongbangnet / Api 荣邦1.4.1.快速进件
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-25 14:36:05
      *  @param   $member=要入网的会员   ☆☆☆::使用中
      *  成功 返回 数组 0、appid 1、companycode 2、secretkey 3、session
      *  失败 返回 接口返回的失败说明
      **/
      public function rongbangnet(){
         #定义请求报文组装
         $arr=array(
          //全平台唯一 加通道id以区分
           'companyname'    =>$this->membercard->card_name . $this->passway->passageway_id .rand(100,999),
           // 'companyname'    =>"test".time(),
           // 'companycode'     =>$this->member->member_mobile,
          //全平台唯一 加通道id以区分
           'companycode'     =>$this->member->member_mobile . $this->passway->passageway_id.rand(100,999),
           'accountname'      =>$this->membercard->card_name,
           'bankaccount'       =>$this->membercard->card_bankno,
           'bank'                   =>$this->membercard->card_bank_address,
           "bankcode"          =>$this->membercard->card_bank_lang,
           "accounttype"      =>"1",
           "bankcardtype"    =>"1",
           'mobilephone'      =>$this->membercard->card_phone,
           'idcardno'            =>$this->membercard->card_idcard,
           'address'             =>"山东省济南市天桥区泺口皮革城",
         );
        // var_dump($arr);die;
        $data=rongbang_curl($this->passway,$arr,'masget.webapi.com.subcompany.add');
        if($data['ret']==0){
          #储存商户信息到memberNet关联字段中，因为信息有多条，以,分割后存储。
          #信息顺序 0、appid 1、companycode 2、secretkey 3、session 4、companyname
          $passageway_no=$data['data']['appid'].','.$data['data']['companycode'].','.$data['data']['secretkey'].','.$data['data']['session'].','.$data['data']['companyname'];
          $res=MemberNet::where(['net_member_id'=>$this->member->member_id])->setField($this->passway->passageway_no, $passageway_no);
            return true;
        }elseif($data['ret']==34){
          //34 为该商户商户名称已存在 调用该商户的信息
          $data=$this->rongbang_getinfo();
          if(is_array($data)){
            //存储拉取的商户信息
            $passageway_no=$data['appid'].','.$data['companycode'].','.$data['secretkey'].','.$data['session'].','.$data['companyname'];
            $res=MemberNet::where(['net_member_id'=>$this->member->member_id])->setField($this->passway->passageway_no, $passageway_no);
        // var_dump($data);die;
            return true;
          }else{
            return $data;
          }
        }else{
          return $data['message'];
        }
        // var_dump($data);die;
      }
      #荣邦 1.4.2.子商户秘钥下载 用于判断该用户是否已经在荣邦存在商户信息
      #已存在 返回data字段 不存在返回false
      public function rongbang_getinfo(){
        $arr=[
          'companycode'=>$this->member->member_mobile ,
        ];
          $data=rongbang_curl($this->passway,$arr,'masget.webapi.com.subcompany.get');
           // var_dump($data);die;
          if($data['ret']==0){
            return $data['data'];
          }else{
            return $data['message'];
          }
      }
      #荣邦1.4.3.商户通道入驻接口
      public function rongbang_in(){
        $userinfo=db('member_net')->where('net_member_id',$this->member->member_id)->value($this->passway->passageway_no);
        // var_dump($userinfo);die;
        $userinfo=explode(',', $userinfo);
        $arr=array(
          'companyid'   =>$userinfo[0],
          // 'accounttype'   =>1,
          // 'bankaccount'   =>1,
        );
        // echo json_encode($arr);die;
        $data=rongbang_curl(rongbang_foruser($this->member,$this->passway),$arr,'masget.pay.compay.router.samename.open');
           // var_dump($data);die;
           // 5041 为已经进件
        if($data['ret']==0 ||$data['ret']==5041){
          return $data;
        }else{
          return $data['message'];
        }
      }
      #荣邦1.6.1.申请开通快捷协议
      public function rongbang_openpay($cardid){
       $credit=db('member_creditcard')->where('card_id',$cardid)->find();
        $arr=[
          'mobilephone'=>$this->member->member_mobile,
          'accountname'=>$this->member->member_nick,
          'certificateno'=>$this->membercert->cert_member_idcard,
          'accounttype'=>1,
          'certificatetype'=>1,
          'collecttype'=>1,
          // 'expirationdate'=>1,
          'bankaccount'=>$credit['card_bankno'],
          'cvv2'=>$credit['card_Ident'],
          'expirationdate'=>$credit['card_expireDate'],
        ];
         // var_dump($arr);die;
        $data=rongbang_curl(rongbang_foruser($this->member,$this->passway),$arr,'masget.pay.collect.router.treaty.apply');
        // var_dump($data);die;
         if($data['ret']==0){
            //$arr=$data['data']['html'];
         
            //$arr=urlsafe_b64decode($data['data']['html']);
           //$arr=base64_decode($data['data']['html']);
           // var_dump($data['data']['html']);die;
          return $data['data'];
         }else{
          return $data['message'];
         }
      }
      #荣邦1.6.2.确认开通快捷协议
      public function rongbang_confirm_openpay($treatycode,$smsseq,$authcode){
        $arr=[
          'treatycode'=>$treatycode,
          'smsseq'=>$smsseq,
          'authcode'=>$authcode,
        ];
        $data=rongbang_curl($this->passway,$arr,'masget.pay.collect.router.treaty.apply');
        if($data['ret']==0){
          //将快捷支付状态改为已开通
          db('member_credit_pas')->where(['member_credit_pas_info'=>$treatycode,'member_credit_pas_pasid'=>$this->passway->passageway_id])->update(['member_credit_pas_status'=>1]);
          return true;
        }else{
          return $data['message'];
        }
      }
      #荣邦 1.6.3.查询快捷协议
      public function rongbang_check($treatycode){
      //提取转换存储的商户信息
          #信息顺序 0、appid 1、companycode 2、secretkey 3、session 4、companyname
        $userdata=db('member_net')->where(['net_member_id'=>$this->member->member_id])->value($this->passway->passageway_no);
        if($userdata){
          $userdata=explode(',', $userdata);
        }
        $arr=[
          'pcompanyid'=>$this->passway->passageway_mech,
          'companyid'=>$userdata[0],
          'treatycode'=>$treatycode,
        ];
          $data=rongbang_curl(rongbang_foruser($this->member,$this->passway),$arr,'masget.pay.collect.router.treaty.query');
         if($data['ret']==0){
          //返回商户信息
          return $data['data'];
         }else{
          return $data['message'];
         }
      }
      #荣邦 1.5.1.订单支付(后台)
      public function rongbang_pay($card_id,$tradeNo,$price,$description){
        //取出该用户的荣邦商户信息
        $userinfo=db('member_net')->where(['net_member_id'=>$this->member->member_id])->value($this->passway->passageway_no);
        //取出支付凭据
        $treatycode=db('member_credit_pas')->where(['member_credit_pas_creditid'=>$card_id,'member_credit_pas_pasid'=>$this->passway->passageway_id])->value('member_credit_pas_info');
        $userinfo=explode(',', $userinfo);
        $companyid=$userinfo[0];
       $credit=db('member_creditcard')->where('card_id',$card_id)->find();
        // 荣邦发来的demo json
        $arr='{
  "amount": "1020",
  "subpaymenttypeid": "25",
  "backurl": "http://gongke.iask.in:21339/api/Userurl/passway_rongbang_paycallback/passageway_id/10/member_id/44",
  "body": "订单收款-3301073122471101",
  "businesstype": "1001",
  "payextraparams": "{\"treatycode\":\"701218011013424102\"}",
  "paymenttypeid": "25",
  "ordernumber": "test201801101523"
}';
        //402573747  封顶通道 
        if($this->passway->passageway_id==11){
          $paymenttypeid='4';
          $payextraparams='"{\"bankaccount\":\"'.$credit['card_bankno'].'\"}"';
          //402512992 快捷无积分
        }else{
          $paymenttypeid='25","subpaymenttypeid":"25';
          $payextraparams='"{\"treatycode\":\"'.$treatycode.'\"}"';
        }

        //由demo而来的终极拼接版
        $arr='{
          "amount": "'.$price*100 .'",
          "backurl": "'.request()->domain(). '/api/Userurl/passway_rongbang_paycallback/passageway_id/' . $this->passway->passageway_id . '/member_id/' . $this->member->member_id .'/order_no/'.$tradeNo.'",
          "body": "'.$description.'",
          "businesstype": "1001",
          "payextraparams": '.$payextraparams.',
          "paymenttypeid": "'.$paymenttypeid.'",
          "ordernumber": "'.$tradeNo.'"
        }';

        // echo ($arr);die;
        // echo (json_encode($arr));die;
          $data=rongbang_curl(rongbang_foruser($this->member,$this->passway),$arr,'masget.pay.compay.router.back.pay');
        // dump($data);die;
          if(isset($data['ret']) && $data['ret']==0){
            return $data['data'];
            #封顶 通道 成功返回html 字符串
          }elseif(is_string($data)){
            # 因为 无积分是要转换的，所以在此做成与无积分返回格式一致
            return [
              'html'=>base64_encode($data),
              'ishtml'=>1,
              ##没有第三方订单号，生成一个用不到的站位
              'ordercode'=>rand(100000,999999),
            ];
          }else{
            return $data['message'];
          }
      }
      #荣邦 1.5.2.查询交易订单
      public function rongbang_check_pay($ordernumber){
        $userinfo=db('member_net')->where('net_member_id',$this->member->member_id)->value($this->passway->passageway_no);
        $userinfo=explode(',', $userinfo);
        $arr=[
          'ordernumber'=>$ordernumber,
          'companyid'=>$userinfo[0],
        ];
        $data=rongbang_curl($this->passway,$arr,'masget.pay.compay.router.paymentjournal.get');
        if($data['ret']==0){
          // dump($data);die;
          return $data['data'];
        }else{
          return $data['message'];
        }
      }

      #荣邦 1.5.3.确认支付
      public function rongbang_confirm_pay($ordercode,$card_id,$authcode){
        $arr=[
          'ordercode'=>$ordercode,
          'authcode'=>$authcode,
        ];
        $data=rongbang_curl($this->passway,$arr,'masget.pay.compay.router.confirmpay');
          w_log($data);
        if($data['ret']==0){
          $data=$data['data'];
          if($data['respcode']==2){
            //支付成功 更新套现订单表状态
            $order=db('cash_order')->where('order_no',$data['ordernumber'])->find();
            //仅在待支付情况下操作
            if($order['order_state']==1){
              db('cash_order')->where('order_no',$data['ordernumber'])->update(['order_state'=>2]);
              //进行分润
              $fenrun= new \app\api\controller\Commission();
              $fenrun_result=$fenrun->MemberFenRun($this->member->member_id,$order['order_money'],$this->passway->passageway_id,1,'交易手续费分润',$order['order_id']);
              #交易失败 待支付 已关闭 交易撤销
              return $data;
            }else{
              return $data['respmsg'];
            }
          }else{
            return $data['respmsg'];
          }
        }else{
          return $data['message'];
        }
      }
      #荣邦银行签约接口
      #封顶通道专用
      public function rongbang_sign_card($card_id){
        $credit=db('member_creditcard')->where('card_id',$card_id)->find();
        $userinfo=db('member_net')->where('net_member_id',$this->member->member_id)->value($this->passway->passageway_no);
        $userinfo=explode(',', $userinfo);
        $arr=array(
          #公司ID
          'companyid'   =>$userinfo[0],
          'bankaccount'   =>$credit['card_bankno'],
          'cvv2'   =>$credit['card_Ident'],
          'expirationdate'   =>$credit['card_expireDate'],
          'mobilephone'   =>$credit['card_phone'],
        );
        // echo json_encode($arr);die;
        $data=rongbang_curl(rongbang_foruser($this->member,$this->passway),$arr,'masget.pay.compay.router.sign.card');
        #成功返回html，是一个string
        return $data;
        var_dump($data);die;
      }
      #荣邦银行签约状态查询
      public function rongbang_signquery_card($card_id){
        $credit=db('member_creditcard')->where('card_id',$card_id)->find();
        $userinfo=db('member_net')->where('net_member_id',$this->member->member_id)->value($this->passway->passageway_no);
        $userinfo=explode(',', $userinfo);
        $arr=array(
          #公司ID
          'companyid'   =>$userinfo[0],
          'bankaccount'   =>$credit['card_bankno'],
        );
        // $data=rongbang_curl($this->passway,$arr,'masget.pay.compay.router.signquery.card');
        $data=rongbang_curl(rongbang_foruser($this->member,$this->passway),$arr,'masget.pay.compay.router.signquery.card');
        // var_dump($data);die;
        if($data['ret']==0){
          $data=$data['data'];
          return $data;
        }else{
          return $data['message'];
        }
      }

        /**
      *  @version jinyifu / Api 金易付商户入网接口
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-25 14:36:05
      *  @param   $member=要入网的会员   ☆☆☆::使用中
      **/
      public function jinyifu()
      {
          $memberAlso=PassagewayItem::where(['item_group'=>$this->member->member_group_id,'item_passageway'=>$this->passway->passageway_id])->value('item_rate');
           $arr=array( 
                 'branchId' => $this->passway->passageway_mech,//机构号
                 'lpName'      => $this->membercard->card_name,//法人姓名
                 'lpCertNo'  => $this->membercard->card_idcard,//法人身份证
                 'merchName'          => $this->member->member_mobile,//商户名称
                 'accNo'               => $this->membercard->card_bankno,//必须为法人本人卡号
                 'telNo'      => $this->member->member_mobile,//商户手机号
                 'city'           =>  "370100",//结算卡所在市编码
                 'bizTypes'                 => "4301" ,// 开通业务类型
                 '5001_fee'           => $memberAlso/100,//5001交易手续费例:0.0038  10000元交易手续费38（业务类型包含时必填）
                 '5001_tzAddFee'              => 2, //5001T0额外手续费例:2  提现额外收取2元提现费（业务类型包含时必填）
                 '4301_fee'         => $memberAlso/100, //4401交易手续费例:0.0038  10000元交易手续费38（业务类型包含时必填）
                 '4301_tzAddFee'   => 2,//4401T0额外手续费例:2  提现额外收取2元提现费（业务类型包含时必填）
           );
           // var_dump($arr);die;

           #1排序
          $arr=SortByASCII($arr);

          #2签名
          $sign=jinyifu_getSign($arr,$this->passway->passageway_key);
          $arr['sign']=$sign;
          // echo $sign;die;
          #3参数
          $params=base64_encode(json_encode($arr));
          #4请求字符串
          $urls='https://hydra.scjinepay.com/jk/BranchMerchAction_add?params='.urlencode($params);
          // echo $urls;
          #请求
          $res=curl_post($urls);
          // var_dump($res);die;
          $res=json_decode($res,true);
          $result=base64_decode($res['params']);
          $result=json_decode($result,true);
          // var_dump($result);die;
          if($result['resCode']=='00')
            $res=MemberNet::where(['net_member_id'=>$this->member->member_id])->setField($this->passway->passageway_no, $result['merchId']);
          return $result;
      }



        /**
      *  @version ronghe / Api 融合支付商户入网接口
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-25 14:36:05
      *  @param   $member=要入网的会员   ☆☆☆::使用中
      **/
      public function ronghe()
      {
          $memberAlso=PassagewayItem::where(['item_group'=>$this->member->member_group_id,'item_passageway'=>$this->passway->passageway_id])->value('item_rate');

          $member=Member::get($this->param['uid']);
          $params=array(
            'companyname'=>$this->member->member_mobile,//商户名称
            'companycode'=>$this->membercard->card_member_id,//商户编码(由机构管理，保证唯一)
            'accountname'=>$this->membercard->card_name,//账户名
            'bankaccount'=>$this->membercard->card_bankno,//卡号
            'bank'=>$this->membercard->card_bank_address,//开户支行名称
            'accounttype'=>'1',//账户类型1=个人账户0=企业账户
            'bankcardtype'=>'1',//银行卡类型,默认1,1=储蓄卡2=信用卡
            'mobilephone'=>$this->member->member_mobile,//手机号
            'idcardno'=>$this->membercard->card_idcard,//身份证号
            'address'=>'1',//商户地址
          );

          $aes_params=AESencode($params,'xpsj69LRllld5Q74');

           $arr=array( 
                 'appid' => '400467885',//发送请求的公司id，由银联供应链综合服务平台统一分发
                 'method'      => '',//API接口名称
                 'format'  => 'json',//指定响应格式。默认json,目前支持格式为json
                 'data'          => $this->member->member_mobile,//业务数据经过AES加密后，进行urlsafe base64编码
                 'v'               => '2.0',//API协议版本，可选值：2.0
                 'timestamp'      => date("Y-m-d H:i:s",time()),//时间戳，格式为yyyy-MM-dd HH:mm:ss，时区为GMT+8，例如：2016-01-01 12:00:00
                 'city'           =>  "370100",//结算卡所在市编码
                 'bizTypes'                 => "4301" ,// 开通业务类型
                 '5001_fee'           => $memberAlso/100,//5001交易手续费例:0.0038  10000元交易手续费38（业务类型包含时必填）
                 '5001_tzAddFee'              => 2, //5001T0额外手续费例:2  提现额外收取2元提现费（业务类型包含时必填）
                 '4301_fee'         => $memberAlso/100, //4401交易手续费例:0.0038  10000元交易手续费38（业务类型包含时必填）
                 '4301_tzAddFee'   => 2,//4401T0额外手续费例:2  提现额外收取2元提现费（业务类型包含时必填）
           );
           // var_dump($arr);die;

           #1排序
          $arr=SortByASCII($arr);

          #2签名
          $sign=jinyifu_getSign($arr,$this->passway->passageway_key);
          $arr['sign']=$sign;
          // echo $sign;die;
          #3参数
          $params=base64_encode(json_encode($arr));
          #4请求字符串
          $urls='https://hydra.scjinepay.com/jk/BranchMerchAction_add?params='.urlencode($params);
          // echo $urls;
          #请求
          $res=curl_post($urls);
          // var_dump($res);die;
          $res=json_decode($res,true);
          $result=base64_decode($res['params']);
          $result=json_decode($result,true);
          // var_dump($result);die;
          if($result['resCode']=='00')
            $res=MemberNet::where(['net_member_id'=>$this->member->member_id])->setField($this->passway->passageway_no, $result['merchId']);
          return $result;
      }

      #0117 新商户注册（同步多渠道） 
      public function api0117_reg(){
        return config('weipay.check_name');
      }
 }