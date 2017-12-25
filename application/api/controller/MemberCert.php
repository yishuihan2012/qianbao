<?php
/**
 *  @version MemberCert controller / Api 会员银行卡实名认证 四元素认证 储蓄卡 结算卡
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-15 11:57:05
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
 use app\index\model\MemberRelation;
 use app\index\model\SmsCode as SmsCodes;
 use app\index\model\Wallet;
 use app\index\model\WalletLog;

 class MemberCert 
 {
      protected $param;
      public $error;
      public function __construct($param)
      {
        	 $this->param=$param;
            try{
                 if(!isset($this->param['uid']) || empty($this->param['uid']) || !isset($this->param['token']) ||empty($this->param['token']))
                       $this->error=314;
                 #查找到当前用户
                 $member=Member::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
                 if(!$member && !$this->error)
                       $this->error=350;
            }catch (\Exception $e) {
                 $this->error=317;
           }
      }

      /**
 	 *  @version validation method / Api 验证
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-15 11:58:05
 	 *  @param     ☆☆☆::使用中
      **/
      public function validation()
      {	
           #验证器验证 验证参数合法性
           $validate = Loader::validate('Membervalidation');
           #如果验证不通过 返回错误代码 及提示信息
           if(!$validate->scene('creat')->check($this->param))
                 return ['code'=>350, 'msg'=>$validate->getError()];
           #验证码验证规则 读取本手机号最后一条没有使用的验证码 并且在系统设置的有效时间内
           $code_info=SmsCodes::where(['sms_send'=>$this->param['card_phone'],'sms_log_state'=>1])->whereTime('sms_log_add_time', "-".System::getName('code_timeout').' minutes')->order('sms_log_id','desc')->find();
           if(!$code_info || ($code_info['sms_log_content']!=$this->param['smsCode']))
                 return ['code'=>404];
           #改变验证码使用状态
           $code_info->sms_log_state=2;
           $result=$code_info->save();
           #验证是否成功
           if(!$result)
                 return ['code'=>404];
           #查询当前用户信息 查看是否实名过
           $member=Member::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
           #如果用户已经实名 或者绑定已了一张结算卡 则不进行实名认证
           if($member['member_cert'])
           {
                 $member_cert=MemberCerts::get('cert_member_id',$member['member_id']);
                 $member_cashcard=MemberCashcard::get(['card_member_id'=>$member['member_id'],'card_state'=>1]);
                 if($member_cert && $member_cashcard)
                      return ['code'=>355];
           }
           // #替换空格
           // $this->param['card_idcard']=str_replace(' ', '', $this->param['card_idcard']);
           // $this->param['card_bankno']=str_replace(' ', '', $this->param['card_bankno']);
           #去实名认证库查找当前条件的信息
           $cert_where=MemberCashcard::get(['card_bankno'=>$this->param['card_bankno'],'card_name'=>$this->param['card_name'],'card_idcard'=>$this->param['card_idcard'],'card_phone'=>$this->param['card_phone']]);
           if($cert_where)
           {
                 if($cert_where['card_state'])
                      return ['code'=>354];
                 else
                      return ['code'=>352]; 
           }
           #银行卡实名验证
           $card_validate=BankCert($this->param['card_bankno'],$this->param['card_phone'],$this->param['card_idcard'],$this->param['card_name']);
           if($card_validate['reason']!='成功')
                 return ['code'=>351];
           $state=$card_validate['result']['result']=='T' ? '1' : '0';
          Db::startTrans();
           #写入认证表
           $member_cashcard=new MemberCashcard([
                'card_member_id'=>$this->param['uid'],
                'card_bankno'=>$this->param['card_bankno'],
                'card_name'  =>$this->param['card_name'],
                'card_idcard' =>$this->param['card_idcard'],
                'card_phone' =>$this->param['card_phone'],
                'card_bankname' => $this->param['card_bankname'],
                'card_bank_province' =>$this->param['card_bank_province'],
                'card_bank_city'   => $this->param['card_bank_city'],
                'card_bank_area' => $this->param['card_bank_area'],
                'card_bank_address' => $this->param['card_bank_address'],
                //'card_bank_lang'   => $this->param['banklang'],
                'card_state'          => $state,
                'card_return'        =>json_encode($card_validate),
           ]);
           if($member_cashcard->save()===false)
                return ['code'=>350];
          
           try {
                 if($card_validate['result']['result']=='F')
                      return ['code'=>352];
                 if($card_validate['result']['result']=='N')
                      return ['code'=>353];
                 #写入到实名认证表
                 if($card_validate['result']['result']=='T' && $card_validate['result']['result']!='P')
                 {
                      $member_certs=new MemberCerts([
                           'cert_member_id' =>$this->param['uid'],
                           'cert_card_id'       =>$member_cashcard->card_id,
                           'cert_member_name' => $this->param['card_name'],
                           'cert_member_idcard' => $this->param['card_idcard']
                      ]);
                      #更改数据表
                      $member_result=new Member;
                      $result=$member_result->where(['member_id'=>$this->param['uid']])->update(['member_cert'=>'1','member_nick'=>$this->param['card_name']]);
                      if($result===false || $member_certs->save()===false)
                      {
                            Db::rollback();
                            return ['code'=>350];
                      }

                      #实名认证成功返回上级红包
                      $parent_member_id=MemberRelation::where('relation_member_id='.$this->param['uid'])->value('relation_parent_id');
                      if($parent_member_id!=0){
                          $system_wallet=System::where("system_key='realname_max' or system_key='realname_min'")->order('system_val desc')->field('system_val')->select();
                          $realname_wallet=mt_rand($system_wallet[0]['system_val'],$system_wallet[1]['system_val']);
                          $wallet=Wallet::where('wallet_member='.$parent_member_id)->find();
                          if(!$wallet)
                            Db::rollback();
                            return ['code'=>350];

                          $realname=$wallet['wallet_invite']+$realname_wallet;
                          $res=Wallet::where('wallet_member='.$parent_member_id)->update(['wallet_invite'=>$realname_wallet]);
                          if($res===false)
                             Db::rollback();
                             return ['code'=>350];

                          $wallet_log=new WalletLog([
                           'log_wallet_id' =>$wallet['wallet_id'],
                           'log_wallet_amount'       =>$realname_wallet,
                           'log_wallet_type' =>1,
                           'log_relation_type' => 5,
                           'log_form' => '邀请红包',
                           'log_desc' => '邀请好友注册并实名认证红包',
                           'log_add_time' =>date("Y-m-d H:i:s",time())
                           ]);
                          if($member_certs->save()===false)
                             Db::rollback();
                             return ['code'=>350];
                          
                      }


                      Db::commit();
                      return ['code'=>200,'msg'=>'实名认证成功~', 'data'=>''];
                 }
           } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>350,'msg'=>$e->getMessage()];
           }
      }

      /**
      *  @version cert_photo method / Api 验证
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 11:58:05
      *  @param     ☆☆☆::使用中
      **/
      public function cert_photo()
      {
           #验证器验证 验证参数合法性
           $validate = Loader::validate('Membercertphoto');
           #如果验证不通过 返回错误代码 及提示信息
           if(!$validate->check($this->param))
                 return ['code'=>435, 'msg'=>$validate->getError()];
           $member=Member::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
           if($member['member_cert']!='1')
                 return ['code'=>356] ;
           #查询实名认证表中的信息
           $member_cert=MemberCerts::get(['cert_member_id'=>$member['member_id']]);
           if(!$member_cert)
                 return ['code'=>356]; 
           #更新实名认证表信息
           Db::startTrans();
           try {
                 $member_cert->IdPositiveImgUrl=$this->param['IdPositiveImgUrl'];
                 $member_cert->IdNegativeImgUrl=$this->param['IdNegativeImgUrl'];
                 $member_cert->IdPortraitImgUrl=$this->param['IdPortraitImgUrl'];
                 if($member_cert->save()===false)
                 {  
                      Db::rollback();
                      return ['code'=>435];
                 }
                 Db::commit();
                 return ['code'=>200, 'msg'=>'上传成功~', 'data'=>''];
           } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>435];
           }
      }

      /**
      *  @version validation change_validation / Api 更换储蓄卡
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-18 11:58:05
      *  @param     ☆☆☆::使用中
      **/
      public function change_validation()
      {
       #验证器验证 验证参数合法性
       $validate = Loader::validate('Membervalidation');
       #如果验证不通过 返回错误代码 及提示信息
       if(!$validate->scene('edit')->check($this->param))
             return ['code'=>322, 'msg'=>$validate->getError()];
      #验证码验证规则 读取本手机号最后一条没有使用的验证码 并且在系统设置的有效时间内
       $code_info=SmsCodes::where(['sms_send'=>$this->param['card_phone'],'sms_log_state'=>1])->whereTime('sms_log_add_time', "-".System::getName('code_timeout').' minutes')->find();
       if(!$code_info || ($code_info['sms_log_content']!=$this->param['smsCode']))
             return ['code'=>404];
       #改变验证码使用状态
       $code_info->sms_log_state=2;
       $result=$code_info->save();
       #验证是否成功
       if(!$result)
             return ['code'=>404];

      #验证用户是否绑定储蓄卡
      $cashcard=MemberCashcard::where('card_member_id='.$this->param['uid'])->find();
      if(!$cashcard)
        return ['code'=>435];

      #银行卡实名验证
         $card_validate=BankCert($this->param['card_bankno'],$this->param['card_phone'],$cashcard['card_idcard'],$cashcard['card_name']);

         if($card_validate['reason']!='成功')
               return ['code'=>351];

        $state=$card_validate['result']['result']=='T' ? '1' : '0';

        $card=array(
          'card_bankno'=>$this->param['card_bankno'],
          'card_phone'=>$this->param['card_phone'],
          'card_bank_province'=>$this->param['card_bank_province'],
          'card_bank_city'=>$this->param['card_bank_city'],
          'card_bank_area'=>$this->param['card_bank_area'],
          'card_bank_address'=>$this->param['card_bank_address'],
          'card_bankname'=>$this->param['card_bankname'],
          'card_state'          => $state,
          'card_return'        =>json_encode($card_validate),
        );
        if($card_validate['result']['result']=='F')
            return ['code'=>352];
       if($card_validate['result']['result']=='N')
            return ['code'=>353];

        $result=MemberCashcard::where('card_member_id='.$this->param['uid'])->update($card);
        if(!$result)
          return ['code'=>435];

        return ['code'=>200,'msg'=>'更换储蓄卡成功~', 'data'=>''];

      }
 }
