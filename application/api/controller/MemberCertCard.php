<?php
/**
 *  @version MemberCertCard controller / Api 会员信用卡
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-16 13:50:05
 *   @return 
 */

 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use think\Loader;
 use app\index\model\MemberLogin;
 use app\index\model\System;
 use app\index\model\Member;
 use app\index\model\MemberCert as MemberCerts;
 use app\index\model\MemberCreditcard;
 use app\index\model\MemberCashcard;
 use app\index\model\SmsCode as SmsCodes;

 class MemberCertCard 
 {
      protected $param;
      public $error;
      public $name;
      public $idcard;
      public function __construct($param)
      {
        	 $this->param=$param;
            try{
                 if(!isset($this->param['uid']) || empty($this->param['uid']) || !isset($this->param['token']) ||empty($this->param['token']))
                       $this->error=314;
                 #查找到当前用户
                 $member=Member::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
                 if($member['member_cert']!='1')
                      $this->error=356;
                 if(empty($member))
                       $this->error=314;
                 #查找实名认证信息
                 $member_cert=MemberCerts::get(['cert_member_id'=>$member['member_id']]);
                 if(empty($member_cert) && !$this->error )
                      $this->error=356;
                 $this->name=$member_cert['cert_member_name'];
                 $this->idcard=$member_cert['cert_member_idcard'];
            }catch (\Exception $e) {
                 $this->error=317;
           }
      }

      /**
      *  @version addition_card method / Api 绑定信用卡
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 09:22:05
      *  @param uid=会员ID token=登录令牌 
          'creditCardNo:信用卡卡号',  'cvv:信用卡背面的3位cvv数字',  'expireDate:有效期',
          'billDate:账单日，两位数字，如1号->01',  'deadline:最后还款日',  'isRemind:是否提醒，0表示不提醒，1表示提醒，\ 当关闭提醒时，下方的日期选择将隐藏',
          'remindDate:提醒日，数据格式和账单日相同'
      **/ 
      public function addition_card()
      {
           #验证器验证 验证参数合法性
           $validate = Loader::validate('Memberadditioncard');
           #如果验证不通过 返回错误代码 及提示信息
           if(!$validate->check($this->param))
                 return ['code'=>436, 'msg'=>$validate->getError()];
           #验证码验证规则 读取本手机号最后一条没有使用的验证码 并且在系统设置的有效时间内
           $code_info=SmsCodes::where(['sms_send'=>$this->param['phone'],'sms_log_state'=>1])->whereTime('sms_log_add_time', "-".System::getName('code_timeout').' minutes')->find();
           if(!$code_info || ($code_info['sms_log_content']!=$this->param['smsCode']))
                 return ['code'=>404];
           #改变验证码使用状态
           $code_info->sms_log_state=2;
           $result=$code_info->save();
           #验证是否成功
           if(!$result)
                 return ['code'=>404];
           #查询当前卡有没有绑定过
           $creditcard=MemberCreditcard::get(['card_bankno'=>$this->param['creditCardNo'],'card_phone'=>$this->param['phone'],'card_name'=>$this->name,'card_idcard'=>$this->idcard]);
           if($creditcard)
           {  
                 if($creditcard['card_state']=='1')
                      return ['code'=>437];
                 return ['code'=>438] ;
           } 
           #信用卡有效状态校验
           $card_validate=BankCert($this->param['creditCardNo'],$this->param['phone'],$this->idcard,$this->name);
           if($card_validate['reason']!='成功')
                 return ['code'=>351];
           $state=$card_validate['result']['result']=='T' ? '1' : '0';
           if($card_validate['result']['result']=='P')
                 return ['code'=>440];
           #写入信用卡表
           $member_cashcard=new MemberCreditcard([
                'card_member_id'=>$this->param['uid'],
                'card_bankno'=>$this->param['creditCardNo'],
                'card_name'  =>$this->name,
                'card_idcard' =>$this->idcard,
                'card_phone' =>$this->param['phone'],
                'card_bankname' => $this->param['bank_name'],
                'card_Ident'  => $this->param['cvv'],
                'card_expireDate' => $this->param['expireDate'],
                'card_billDate'   => $this->param['billDate'],
                'card_deadline' => $this->param['deadline'],
                'card_isRemind' => $this->param['isRemind'],
                'card_remindDate' => $this->param['remindDate'] ? $this->param['remindDate'] : $this->param['billDate'],
                'card_state'  => $state,
                'card_return' =>json_encode($card_validate),
           ]);
           if($member_cashcard->save()===false)
                 return ['code'=>436];
           if($card_validate['result']['result']=='F')
                return ['code'=>439];
           if($card_validate['result']['result']=='N')
                return ['code'=>353];
           return ['code'=>200, 'msg'=>'信用卡绑定成功~', 'data'=>''];
      }
 
      /**
      *  @version addition_card method / Api 解绑信用卡
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 09:22:05
      *  @param uid=会员ID token=登录令牌  creditCardId='信用卡ID'
      **/ 
      public function ubind_card()
      {
           #判断参数是否存在
           if(!isset($this->param['creditCardId']) || empty($this->param['creditCardId']))
                 return ['code'=>441];
           #查找到当前信用卡
           $cert_card=MemberCreditcard::get($this->param['creditCardId']);
           if(empty($cert_card))
                 return ['code'=>442];
           #查找出会员的实名信息
           $member_cert=MemberCerts::get(['cert_member_id'=>$this->param['uid']]);
           #进行和当前会员信息比对
           if($cert_card['card_name']!=$member_cert['cert_member_name'] ||  $cert_card['card_idcard']!=$member_cert['cert_member_idcard'])
                 return ['code'=>443];
           if($cert_card->delete()===false)
                 return ['code'=>444];
           return ['code'=>200, 'msg'=>'解绑成功~', 'data'=>''];
      }

      /**
      *  @version get_card_list method / Api 获取信用卡列表
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 09:22:05
      *  @param uid=会员ID token=登录令牌
      **/ 
      public function get_card_list()
      {
           #判断参数是否存在
           if(!isset($this->param['uid']) || empty($this->param['uid']) || !isset($this->param['token']) ||empty($this->param['token']))
                $this->error=314;
           #查找到当前用户信用卡列表
           $data=MemberCreditcard::where('card_member_id='.$this->param['uid'])->select();
           if(empty($data))
                 return ['code'=>314];

           return ['code'=>200, 'msg'=>'获取信用卡列表成功~', 'data'=>$data];
      }
 }