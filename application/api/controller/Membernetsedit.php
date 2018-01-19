<?php
/**
 *  @version MemberNetedit controller / Api 会员修改入网接口信息
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-25 15:31:05
 *   @return 
 */
 namespace app\api\controller;
 use app\index\model\Member as Members;
 use app\index\model\MemberCert as MemberCerts;
 use app\index\model\MemberCashcard;
 use app\index\model\Passageway;
 use app\index\model\System;
 use app\index\model\MemberNet;
 use app\index\model\PassagewayItem;

 class Membernetsedit{ 
      public $error;
      private $member; //会员信息
      private $membercert; //会员认证信息
      private $membercard; //会员结算卡信息
      private $passway; //通道信息
      private $membernet; //入网信息
      private $modifyType; //入网信息
      private $accountno; //银行卡
      private $phone; //手机号
      function __construct($memberId,$passwayId='',$modifyType='M03',$accountno='',$phone=''){
           try{
                 #根据memberId获取会员信息和会员的实名认证信息还有会员银行卡信息
                 $this->member=Members::get($memberId);
                 if(! $this->member)
                      $this->error=314;
                 if($this->member->member_cert!='1')
                      $this->error=356;
                 $this->membercert=MemberCerts::get(['cert_member_id'=>$memberId]);
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

                    #获取入网信息
                 $this->membernet=MemberNet::get(['net_member_id'=>$memberId]);
                 if(!$this->passway)
                      $this->error=454; 

                  $this->modifyType=$modifyType;
                  $this->accountno=$accountno;
                  $this->phone=$phone;
                  $this->identityCard=$identityCard;
           }catch (\Exception $e) {
                 $this->error=460; //TODO 更改错误码 入网失败错误码
           }
      }

      /**
      *  @version quickNet / Api 修改快捷支付商户入网接口
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-25 14:36:05
      *  @param   $member=要入网的会员   ☆☆☆::使用中
      **/
      public function quickNet()
      {

        $memberAlso=PassagewayItem::where(['item_group'=>$this->member->member_group_id,'item_passageway'=>$this->passway->passageway_id])->value('item_rate');
        if($this->modifyType=='M02'){//修改结算卡信息
            $arr=array( 
                 'merchno'     =>$this->membernet->quick023,//商户签约时，分配给商家的唯一标识。
                 'modifyType'  =>$this->modifyType, 
                 'bankno'    =>  $this->membercard->card_bank_lang,//开户行支行联行号
                 'bankName'    =>  $this->membercard->card_bank_address,//开户行支行联行号
                 'accountno'   =>   $this->accountno,
                 'accountType'   =>  '1',
                 'mobile'   =>  $this->phone,
                 'identityCard'   =>  $this->membercard->card_idcard,
                 'version'            => "v1.2",//接口固定版本号
           );
        }
        if($this->modifyType=='M03'){//修改费率信息
          $arr=array( 
                 'merchno'      => $this->membernet->quick023,//商户签约时，分配给商家的唯一标识。
                 'modifyType'   => $this->modifyType, 
                 'd0Rate'       => $memberAlso/10,//小数点后四位，例如0.0035 D0费率
                 't1Rate'       => System::getName('charge_t1'), //小数点后四位，例如0.0035
                 'quickFixed'   => System::getName('charge_max'),//封顶值 10000为不封顶
                 'version'      => "v1.2",//接口固定版本号
           );
        }
        $param=get_signature($arr,$this->passway->passageway_key);
        //dump($param);
        $result=curl_post("http://api.ekbuyclub.com:6001/quick.do?m=modifymerch",'post',$param,'Content-Type: application/x-www-form-urlencoded; charset=gbk');
        $data=json_decode(mb_convert_encoding($result, 'utf-8', 'GBK,UTF-8,ASCII'),true);
        var_dump($data);die;
        return $data;
      } 

     /**
      *  @version mishua / Api 米刷支付商户入网接口
      *  @author $bill$(928791694@qq.com)
      *  @datetime    2017-12-25 14:36:05
      *  @param   $member=要入网的会员   ☆☆☆::使用中
      **/
    function mishuadaihuan()
    {
      $memberAlso=PassagewayItem::where(['item_group'=>$this->member->member_group_id,'item_passageway'=>$this->passway->passageway_id])->find();
      $params=array(
        'versionNo'=>'1',//接口版本号 必填  值固定为1
        'mchNo'=>$this->passway->passageway_mech, //mchNo 商户号 必填  由米刷统一分配
        'userNo'=>$this->membernet->LkYQJ, //用户标识,下级机构对用户身份唯一标识。
        'userName'=>$this->membercard->card_name,//姓名
        'userCertId'=>$this->membercard->card_idcard,//身份证号  必填  注册后不可修改
        'userPhone'=>$this->phone,
        'feeRatio'=>$memberAlso['item_also']*10, //交易费率  必填  单位：千分位。如交易费率为0.005时,需传入5.0
        'feeAmt'=>$memberAlso['item_charges'],//单笔交易手续费  必填  单位：分。如机构无单笔手续费，可传入0
        'drawFeeRatio'=>'0',//提现费率
        'drawFeeAmt'=>'0',//单笔提现易手续费
      );
      $url='http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/updateMerchant';
      $income=repay_request($params, $this->passway->passageway_mech, $url, $this->passway->iv, $this->passway->secretkey, $this->passway->signkey);
      if($income['code']==200)
        return true;

      return $income['msg'];
    }



        /**
      *  @version jinyifu / Api 修改金易付商户入网接口
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-25 14:36:05
      *  @param   $member=要入网的会员   ☆☆☆::使用中
      **/
      public function jinyifu()
      {
          $memberAlso=PassagewayItem::where(['item_group'=>$this->member->member_group_id,'item_passageway'=>$this->passway->passageway_id])->find();
           $arr=array( 
                 'branchId' => $this->passway->passageway_mech,//机构号
                 'merchId'  => $this->membernet->PtKWJ,//商户号
                 'lpName'      => $this->membercard->card_name,//法人姓名
                 'lpCertNo'  => $this->membercard->card_idcard,//法人身份证
                 'merchName'          => $this->member->member_mobile,//商户名称
                 'accNo'               => $this->membercard->card_bankno,//必须为法人本人卡号
                 'telNo'      => $this->member->member_mobile,//商户手机号
                 'city'           => "370100",//结算卡所在市编码
                 'bizTypes'                 => "4301" ,// 开通业务类型
                 '5001_fee'           => $memberAlso['item_rate']/100,//5001交易手续费例:0.0038  10000元交易手续费38（业务类型包含时必填）
                 '5001_tzAddFee'              => $memberAlso['item_charges']/100, //5001T0额外手续费例:2  提现额外收取2元提现费（业务类型包含时必填）
                 '4301_fee'         => $memberAlso['item_rate']/100, //4401交易手续费例:0.0038  10000元交易手续费38（业务类型包含时必填）
                 '4301_tzAddFee'   => $memberAlso['item_charges']/100,//4401T0额外手续费例:2  提现额外收取2元提现费（业务类型包含时必填）
           );


            #1排序
          $arr=SortByASCII($arr);

          #2签名
          $sign=jinyifu_getSign($arr,$this->passway->passageway_key);
          $arr['sign']=$sign;
          // echo $sign;die;
          #3参数
          $params=base64_encode(json_encode($arr));
          #4请求字符串
          $urls='https://hydra.scjinepay.com/jk/BranchMerchAction_update?params='.urlencode($params);
          // echo $urls;
          #请求
          $res=curl_post($urls);

          $res=json_decode($res,true);
          $result=base64_decode($res['params']);
          $result=json_decode($result,true);
           if($result['resCode']=="00")
           return true;
      } 




      #荣邦 1.4.3.根据邀请码，修改商户费率与D0费率
      public function rongbangnet(){
        $memberAlso=PassagewayItem::where(['item_group'=>$this->member->member_group_id,'item_passageway'=>$this->passway->passageway_id])->find();
        //传入费率对应的在荣邦的编码
        $rate_code=db('passageway_rate')->where(['rate_rate'=>$memberAlso['item_rate'],'rate_passway_id'=>$this->passway->passageway_id])->find();
        if($rate_code){
          $userinfo=db('member_net')->where('net_member_id',$this->member->member_id)->value($this->passway->passageway_no);
          $userinfo=explode(',', $userinfo);
          $arr=array(
            #公司ID
            'companyid'   =>$userinfo[0],
            #商户名称
            // 'membername'   =>$userinfo[4],
            #邀请码(费率套餐代码)
            'ratecode'   =>$rate_code['rate_code'],
            // 'ratecode'   =>902429,
          );
          // var_dump($arr);die;
          // $data=rongbang_curl(rongbang_foruser($this->member,$this->passway),$arr,'masget.pay.compay.router.samename.update');
          $data=rongbang_curl($this->passway,$arr,'masget.pay.compay.router.samename.update');
          var_dump($data);die;
          if($data['ret']==0){
            return true;
            return $data['data'];
          }else{
            return $data['message'];
          }
        }else{
          return '该费率'.$memberAlso['item_rate'].'无对应的套餐编码';
        }
      }
 }