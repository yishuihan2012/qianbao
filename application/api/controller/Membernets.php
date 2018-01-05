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
      *  @version rongbangnet / Api 荣邦快捷支付入网接口
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-25 14:36:05
      *  @param   $member=要入网的会员   ☆☆☆::使用中
      **/
      public function rongbangnet()
      {
           #定义请求报文组装
           $arr=array(
                 'companyname'    =>"商户名称已经存在",//$this->membercard->card_name.rand(1000,9999),山东联硕支付技术有限公司济南分公司（无积分快捷）
                 'companycode'     =>$this->passway->passageway_mech,
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
           #aes加密并且urlsafe base64编码
           $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->passway->passageway_key, json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), MCRYPT_MODE_CBC, $this->passway->passageway_key);
           $base64str= base64_encode($encrypted);
           $base64str = str_replace("+", "-",  $base64str);
           $passParam = str_replace("/","_",  $base64str);
           // $passParam = str_replace("=",".",  $passParam);

           $array=array(
                 'appid'      =>$this->passway->passageway_mech, //APPID
                 'method'   =>"masget.webapi.com.subcompany.add",//进件接口
                 'format'     =>"json",//响应格式
                 'data'        =>$passParam,//请求报文加密
                 'v'             =>"2.0",//接口版本号
                 'session'  =>$this->passway->passageway_key,
                 // 'target_appid' =>'400467885',
                 'timestamp'  =>time(),
            );
           #连接键值生成sign
           // $str='';
           // foreach ($array as $key => $value){
           //    $str.=$value;
           // }
           // $signature=md5('xpsj69LRllld5Q74'.trim($str).'xpsj69LRllld5Q74'); //生成签名
            #拼接请求参数
           // $str1="";
           // foreach ($array as $key => $value){
           //    $str1.=$key."=".$value."&";
           // }
           //$str1.="sign=".$signature; //拼接请求体参数
           $signature = md5($this->passway->passageway_key.$array['appid'].$passParam.$array['format'].$array['method'].$array['session'].$array['timestamp'].$array['v'].$this->passway->passageway_key);

          

           $str1="appid=".$array['appid']."&method=".$array['method']."&format=".$array['format']."&data=".$array['data']."&v=".$array['v']."&amp;timestamp=".$array['timestamp']."&session=".$array['session']."&sign=" .$signature;

           $getData="https://gw.masget.com:27373/openapi/rest?".$str1;

           $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $getData);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            $data = curl_exec($curl);
            $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
            curl_close($curl);
            return $data;
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

 }