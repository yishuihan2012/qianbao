<?php
/**
 *  @version MemberNetedit controller / Api 会员修改入网接口信息
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-25 15:31:05
 *   @return 
 */
 namespace app\api\controller;
 use app\index\model\Member as Members;
 use app\index\model\MemberCert;
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
      private $identityCard; //身份证
      function __construct($memberId,$passwayId='',$modifyType='M03',$accountno='',$phone='',$identityCard=''){
           try{
                 #根据memberId获取会员信息和会员的实名认证信息还有会员银行卡信息
                 $this->member=Members::get($memberId);
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
                 'identityCard'   =>  $this->identityCard,
                 'version'            => "v1.2",//接口固定版本号
           );
        }
        if($this->modifyType=='M03'){//修改费率信息
          $arr=array( 
                 'merchno'      => $this->membernet->quick023,//商户签约时，分配给商家的唯一标识。
                 'modifyType'   => $this->modifyType, 
                 'd0Rate'       => $memberAlso/100,//小数点后四位，例如0.0035 D0费率
                 't1Rate'       => System::getName('charge_t1'), //小数点后四位，例如0.0035
                 'quickFixed'   => System::getName('charge_max'),//封顶值 10000为不封顶
                 'version'      => "v1.2",//接口固定版本号
           );
        }
           
        //dump($arr);
        $param=get_signature($arr,$this->passway->passageway_key);
        //dump($param);
        $result=curl_post("http://api.ekbuyclub.com:6001/quick.do?m=modifymerch",'post',$param,'Content-Type: application/x-www-form-urlencoded; charset=gbk');
        $data=json_decode(mb_convert_encoding($result, 'utf-8', 'GBK,UTF-8,ASCII'),true);
        return $data;
      } 
 }