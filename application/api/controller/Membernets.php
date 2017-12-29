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
                 'companyname'    =>"山东联硕支付技术有限公司济南分公司（无积分快捷）",//$this->membercard->card_name.rand(1000,9999),
                 'companycode'     =>$this->passway->passageway_mech,
                 'accountname'      =>$this->membercard->card_name,
                 'bankaccount'       =>$this->membercard->card_bankno,
                 'bank'                   =>$this->membercard->card_bank_address,
                 "bankcode"          =>$this->membercard->card_bank_lang,
                 "accounttype"      =>"1",
                 "bankcardtype"    =>"1",
                 'mobilephone'      =>$this->membercard->card_phone,
                 'idcardno'            =>$this->membercard->card_idcard,
                 //'address'             =>
           );
           //dump($arr);
           $passParam=urlsafe_b64encode(AESencode(json_encode($arr),$this->passway->passageway_pwd_key,$this->passway->passageway_pwd_key));
           $array=array(
                 'appid'      =>$this->passway->passageway_mech, //APPID
                 'method'   =>"masget.webapi.com.subcompany.add",//进件接口
                 'format'     =>"json",//响应格式
                 'data'        =>$passParam,//请求报文加密
                 'v'             =>"2.0",//接口版本号
                 'session'  =>$this->passway->passageway_key,
                 'target_appid' =>$this->passway->passageway_mech,
                 'timestamp'  =>time()
            );
           ksort($array);//自然排序 
           $str="";
           foreach ($array as $key => $value)  //循环组成键值对
               $str.=$value;
           $signature=md5($this->passway->passageway_pwd_key.$str.$this->passway->passageway_pwd_key); //生成签名
           $str1="";
           foreach ($array as $key => $value)
                 $str1.=$key."=".$value."&";
           $str1.="sign=".$signature; //拼接请求体参数
           $getData=$this->passway->cashout->cashout_url."?".$str1;
           //dump($getData);exit;
           $curl = curl_init();
           curl_setopt($curl, CURLOPT_URL, $getData);
           curl_setopt($curl, CURLOPT_HEADER, 0);
           curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
           curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
           $result = curl_exec($curl);
           //$result=curl_post($this->passway->cashout->cashout_url,'post',$array);
           dump($result);

      }


 }