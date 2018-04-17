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
 use app\index\model\Recomment;
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
 use app\index\model\SystemBank;
 use app\index\model\RegionProvince;
 use app\index\model\RegionCity;
 use app\index\model\RegionArea;
 use app\index\model\Passageway;
 class MemberCert 
 {
      protected $param;
      public $error;
      public function __construct($param)
      {
        	 $this->param=$param;
            // $card_validate=BankCertNew(['bankCardNo'=>$this->param['card_bankno'], 'identityNo'=>$this->param['card_idcard'], 'mobileNo'=>$this->param['card_phone'], 'name'=>$this->param['card_name']]);
           // $card_validate=BankCert_Java($this->param['card_bankno'],$this->param['card_idcard'],$this->param['card_name'],$this->param['card_phone']);
          // halt($card_validate);

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
           #去实名认证库查找当前条件的信息
           $cert_where=MemberCashcard::get(['card_idcard'=>$this->param['card_idcard']]);
           if($cert_where)
                 return ['code'=>601];

            $cert_card_bankno=MemberCashcard::get(['card_bankno'=>$this->param['card_bankno']]);
           if($cert_card_bankno)
                 return ['code'=>602];
           #判断用户是否超过实名认证的最大次数 超过后将不予受理
           //if($member['member_check_count']>=)

           $bankInfo=SystemBank::get($this->param['card_bank_addressId']);//查找出银行支行名称和联行号
           if(!$bankInfo)
                 return ['code'=>430];
           #银行卡实名验证
           // $card_validate=BankCert($this->param['card_bankno'],$this->param['card_phone'],$this->param['card_idcard'],$this->param['card_name']);
           // return ['code'=>200,'msg'=>'实名认证成功~', 'data'=>$card_validate];
           // 老接口
           // if($card_validate['error_code']!=0){
           //   return ['code'=>351,'msg'=>$card_validate['reason']];
           // }
           // $state=$card_validate['result']['result']=='T' ? '1' : '0';
           //如果实名认证返回状态不等于T 则进行加1次验证
           //TODOif($card_validate['result']['result']=='T' && $card_validate['result']['result']!='P')
           // 新接口
           $card_validate=BankCert_Java($this->param['card_bankno'],$this->param['card_idcard'],$this->param['card_name'],$this->param['card_phone']);
          if(isset($card_validate['data']['identType']) && $card_validate['data']['identType']!='借记卡')
            return ['code'=>351,'msg'=>'认证失败:只能验证储蓄卡'];
          if($card_validate['info']=='交易成功 认证不一致，发卡行不支持此交易')
            return ['code'=>351,'msg'=>'该卡尚未开通无卡支付，请联系发卡行开通'];
          if($card_validate['code']!=200)
              return ['code'=>351,'msg'=>$card_validate['info']];
          $state=1;
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
                'card_bank_area' => '',//$this->param['card_bank_area'],
                'card_bank_address' => $bankInfo['bank_name'],
                'card_bank_lang'   => $bankInfo['bank_code'],
                'card_state'          => $state,
                'card_return'        =>json_encode($card_validate),
           ]);
           if($member_cashcard->save()===false)
                return ['code'=>350];
           try {
                 // if($card_validate['result']['result']=='F')
                 //      return ['code'=>352,'msg'=>$card_validate['result']['message']];
                 // if($card_validate['result']['result']=='N')
                 //      return ['code'=>353,'msg'=>$card_validate['result']['message']];
                 // #写入到实名认证表
                 // if($card_validate['result']['result']=='T' && $card_validate['result']['result']!='P')
                 // {
                      $member_certs_array=[
                           'cert_member_id' =>$this->param['uid'],
                           'cert_card_id'       =>$member_cashcard->card_id,
                           'cert_member_name' => $this->param['card_name'],
                           'cert_member_idcard' => $this->param['card_idcard'],
                      ];
                      //如果最后上传照片，也一起更新
                      if(isset($this->param['IdPositiveImgUrl'])){
                          $member_certs_array['IdPositiveImgUrl']=$this->param['IdPositiveImgUrl'];
                      }else{
                          $member_certs_array['IdPositiveImgUrl']='';
                      }
                      if(isset($this->param['IdNegativeImgUrl'])){
                          $member_certs_array['IdNegativeImgUrl']=$this->param['IdNegativeImgUrl'];
                      }else{
                          $member_certs_array['IdNegativeImgUrl']='';
                      }
                      if(isset($this->param['IdPortraitImgUrl'])){
                          $member_certs_array['IdPortraitImgUrl']=$this->param['IdPortraitImgUrl'];
                      }else{
                          $member_certs_array['IdPortraitImgUrl']='';
                      }
                      $member_certs=new MemberCerts($member_certs_array);
                      #更改数据表
                      $member_result=new Member;
                      $result=$member_result->where(['member_id'=>$this->param['uid']])->update(['member_cert'=>'1','member_nick'=>$this->param['card_name']]);
                      if($result===false || $member_certs->save()===false)
                      {
                            Db::rollback();
                            return ['code'=>350];
                      }
                      #实名认证成功返回上级红包
                      // $parent_member_id=MemberRelation::where(['relation_member_id'=>$this->param['uid']])->value('relation_parent_id');
                      // if($parent_member_id!=0 && System::getName('is_havecert_redpackets') )
                      // {
                      //       $realname_wallet=rand(System::getName('realname_min'),System::getName('realname_max')); //实名红包金额
                      //       $wallet=Wallet::get(['wallet_member'=>$parent_member_id]);
                      //       if(!$wallet)
                      //       {
                      //            Db::rollback();
                      //            return ['code'=>350,'上级用户钱包信息未找到~'];
                      //       }
                      //      $wallet->wallet_amount=$wallet->wallet_amount+$realname_wallet;
                      //      $wallet->wallet_total_revenue=$wallet->wallet_total_revenue+$realname_wallet;
                      //      $wallet->wallet_invite=$wallet->wallet_invite+$realname_wallet;
                      //      #添加到推荐红包表
                      //       $recomment=new Recomment([
                      //            'recomment_member_id'=>$parent_member_id,
                      //            'recomment_children_member'=>$this->param['uid'],
                      //            'recomment_money'=>$realname_wallet,
                      //            'recomment_desc'=>'推荐下级'.$this->param['card_name'].'注册并实名认证成功',
                      //       ]);
                      //       if($recomment->save()){
                      //          $wallet_log=new WalletLog([
                      //                'log_wallet_id'          =>$wallet['wallet_id'],
                      //                'log_wallet_amount'  =>$realname_wallet,
                      //                'log_wallet_type' =>1,
                      //                'log_relation_id'  =>$recomment->recomment_id,
                      //                'log_relation_type' => 5,
                      //                'log_form' => '邀请红包',
                      //                'log_desc' => '邀请好友'.$this->param['card_name'].'注册并完成实名认证,获得红包'.$realname_wallet."元",
                      //          ]);
                      //        }
                      //      if($wallet->save()===false || $wallet_log->save()===false )
                      //      {
                      //            Db::rollback();
                      //            return ['code'=>350,'上级钱包余额更新失败~'];
                      //      }
                      // }
                      Db::commit();
                      return ['code'=>200,'msg'=>'实名认证成功~', 'data'=>''];
                 // }
           } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>350,'msg'=>$e->getMessage()];
           }
      }
          /**
       *  @version validation method / Api 验证     @datetime    2018-02-03 21:24
       *  @author $bill$(755969423@qq.com)     @param     ☆☆☆::使用中  新的四元素认证实名接口
      **/
      public function validationNew()
      {
           #验证器验证 验证参数合法性
           $validate = Loader::validate('Membervalidation');
           #如果验证不通过 返回错误代码 及提示信息
           if(!$validate->scene('creat')->check($this->param))
                 return ['code'=>350, 'msg'=>$validate->getError()];
           #查询当前用户信息 查看是否实名过
           $member=Member::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
           #如果用户已经实名 或者绑定已了一张结算卡 则不进行实名认证
           if($member['member_cert'])
           {
                 $member_cert=MemberCerts::get(['cert_member_id'=>$member['member_id']]);
                 $member_cashcard=MemberCashcard::get(['card_member_id'=>$member['member_id'],'card_state'=>1]);
                 if($member_cert && $member_cashcard) return ['code'=>355];
           }
           #去实名认证库查找当前条件的信息
           $cert_where=MemberCashcard::get(['card_idcard'=>$this->param['card_idcard']]);
           if($cert_where)  return ['code'=>601];
           $cert_card_bankno=MemberCashcard::get(['card_bankno'=>$this->param['card_bankno']]);
           if($cert_card_bankno) return ['code'=>602];

           // $card_validate=BankCertNew(['bankCardNo'=>$this->param['card_bankno'], 'identityNo'=>$this->param['card_idcard'], 'mobileNo'=>$this->param['card_phone'], 'name'=>$this->param['card_name']]);

           $card_validate=BankCert_Java($this->param['card_bankno'],$this->param['card_idcard'],$this->param['card_name'],$this->param['card_phone']);

          if(isset($card_validate['data']['identType']) && $card_validate['data']['identType']!='借记卡')
            return ['code'=>351,'msg'=>'认证失败:只能验证储蓄卡'];
          if($card_validate['info']=='交易成功 认证不一致，发卡行不支持此交易')
            return ['code'=>351,'msg'=>'该卡尚未开通无卡支付，请联系发卡行开通'];
          if($card_validate['code']!=200)
              return ['code'=>351,'msg'=>$card_validate['info']];
           // if($card_validate['code']!=0000) return ['code'=>351, 'msg'=>'实名认证失败。'];
           // if($card_validate['data']['resultCode']!='R001')  return ['code'=>351, 'msg'=>'认证信息不匹配或您的储蓄卡尚未开通无卡支付功能，请联系发卡行。'];
           // if(!isset($card_validate['data']['bankCardBin'])){
           //       return ['code'=>351, 'msg'=>'识别银行失败，请换卡重试。'];
           // }
           // if(isset($card_validate['data']['bankCardBin']) && $card_validate['data']['bankCardBin']['cardTy']!='D')
           //    return ['code'=>351, 'msg'=>'认证失败:请更换一个储蓄卡完成实名认证~'];
           Db::startTrans();
           try{
                #写入认证表
                $card_bankname=$card_validate['data']['bankCardBin']['bankName'];
                if($num=strpos($card_bankname,'(')){
                    $card_bankname=substr($card_bankname,0,$num);
                }
                $member_cashcard=new MemberCashcard([
                     'card_member_id'=>$this->param['uid'],
                     'card_bankno'=>$this->param['card_bankno'],
                     'card_name'  =>$this->param['card_name'],
                     'card_idcard' =>$this->param['card_idcard'],
                     'card_phone' =>$this->param['card_phone'],
                     'card_bankname' =>$card_bankname,
                     //'card_bank_province' =>$this->param['card_bank_province'],
                     //'card_bank_city'   => $this->param['card_bank_city'],
                     //'card_bank_area' => '',//$this->param['card_bank_area'],
                     //'card_bank_address' => $bankInfo['bank_name'],
                     //'card_bank_lang'   => $bankInfo['bank_code'],
                     'card_ident'          =>$card_validate['data']['bankCardBin']['id'] ?? 0,
                     'card_rname'          =>$card_validate['data']['bankCardBin']['cardName'] ?? 0 ,
                     'card_type'          =>$card_validate['data']['bankCardBin']['cardTy'] ?? 0,
                     'card_bankid'        => $card_validate['data']['bankCardBin']['bankId'] ?? 0,
                     'card_binstart'       => $card_validate['data']['bankCardBin']['binStat'] ?? 0,
                     'card_channel'       => $card_validate['data']['channel'],
                     'card_state'          => '1',
                     'card_return'        =>json_encode($card_validate),
                ]);
                if(! $member_cashcard->save()) return ['code'=>350];
                $member_certs_array=[
                     'cert_member_id' =>$this->param['uid'],
                     'cert_card_id'       =>$member_cashcard->card_id,
                     'cert_member_name' => $this->param['card_name'],
                     'cert_member_idcard' => $this->param['card_idcard'],
                ];
                //如果最后上传照片，也一起更新
                $member_certs_array['IdPositiveImgUrl']=$this->param['IdPositiveImgUrl'] ?? '';
                $member_certs_array['IdNegativeImgUrl']=$this->param['IdNegativeImgUrl'] ?? '';
                $member_certs_array['IdPortraitImgUrl']=$this->param['IdPortraitImgUrl'] ?? '';
                $member_certs=new MemberCerts($member_certs_array);
                //更新会员表
                $member_result=new Member;
                $result=$member_result->where(['member_id'=>$this->param['uid']])->update(['member_cert'=>'1','member_nick'=>$this->param['card_name']]);
                if($result===false || $member_certs->save()===false)
                {
                     Db::rollback();
                     return ['code'=>350];
                }
                //实名认证成功返回上级红包
                // $parent_member_id=MemberRelation::where(['relation_member_id'=>$this->param['uid']])->value('relation_parent_id');
                // if($parent_member_id!=0 && System::getName('is_havecert_redpackets') )
                // {
                //      $realname_wallet=rand(System::getName('realname_min'),System::getName('realname_max')); //实名红包金额
                //      $wallet=Wallet::get(['wallet_member'=>$parent_member_id]);
                //      if(!$wallet)
                //      {
                //            Db::rollback();
                //            return ['code'=>350,'上级用户钱包信息未找到~'];
                //      }
                //      $wallet->wallet_amount=$wallet->wallet_amount+$realname_wallet;
                //      $wallet->wallet_total_revenue=$wallet->wallet_total_revenue+$realname_wallet;
                //      $wallet->wallet_invite=$wallet->wallet_invite+$realname_wallet;
                //      //添加到推荐红包表
                //      $recomment=new Recomment([
                //            'recomment_member_id'=>$parent_member_id,
                //            'recomment_children_member'=>$this->param['uid'],
                //            'recomment_money'=>$realname_wallet,
                //            'recomment_desc'=>'推荐下级'.$this->param['card_name'].'注册并实名认证成功',
                //      ]);
                //      if($recomment->save()){
                //            $wallet_log=new WalletLog([
                //                 'log_wallet_id'          =>$wallet['wallet_id'],
                //                 'log_wallet_amount'  =>$realname_wallet,
                //                 'log_wallet_type' =>1,
                //                 'log_relation_id'  =>$recomment->recomment_id,
                //                 'log_relation_type' => 5,
                //                 'log_form' => '邀请红包',
                //                 'log_desc' => '邀请好友'.$this->param['card_name'].'注册并完成实名认证,获得红包'.$realname_wallet."元",
                //            ]);
                //       }
                //      if($wallet->save()===false || !$wallet_log->save() )
                //      {
                //            Db::rollback();
                //            return ['code'=>350,'上级钱包余额更新失败~'];
                //      }
                // }
                Db::commit();
                return ['code'=>200,'msg'=>'实名认证成功~', 'data'=>''];
           }catch(\Exception $e){
                Db::rollback();
                return ['code'=>350,'msg'=>$e->getMessage()];
           }
      }
      /**
      *  @version get_card_info method /获取绑定储蓄卡信息
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 11:58:05
      *  @param     ☆☆☆::使用中
      **/
      public function get_card_info()
      {
            $cert=MemberCashcard::where(['card_member_id'=>$this->param['uid']])->find();
            $cert['province_name']=RegionProvince::where(['id'=>$cert['card_bank_province']])->value('Name')?RegionProvince::where(['id'=>$cert['card_bank_province']])->value('Name'):'';
            $cert['city_name']=RegionCity::where(['id'=>$cert['card_bank_city']])->value('Name')?RegionCity::where(['id'=>$cert['card_bank_city']])->value('Name'):'';
            $cert['area_name']=RegionArea::where(['id'=>$cert['card_bank_area']])->value('name')?RegionArea::where(['id'=>$cert['card_bank_area']])->value('name'):'';
            $cert['card_bank_addressId']=SystemBank::where(['bank_code'=>$cert['card_bank_lang']])->value('id');
            if(!$cert)
                 return ['code'=>356];
            return ['code'=>200,'msg'=>'获取储蓄卡信息成功~', 'data'=>$cert];
      }

      /**
      *  @version cert_photo method /  Api 验证
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

           $bankInfo=SystemBank::get($this->param['card_bank_addressId']);
           if(!$bankInfo)
                 return ['code'=>430];
           #银行卡实名验证
               #如果银行卡号与手机号有变动执行认证操作
          if($cashcard['card_bankno']!=$this->param['card_bankno'] || $cashcard['card_phone']!=$this->param['card_phone']){

             // $card_validate=BankCert($this->param['card_bankno'],$this->param['card_phone'],$cashcard['card_idcard'],$cashcard['card_name']);
             // if($card_validate['error_code']!=0){
             //    return ['code'=>351,'msg'=>$card_validate['reason']];
             // }
             // $state=$card_validate['result']['result']=='T' ? '1' : '0';
             // if($card_validate['result']['result']=='F')
             //       return ['code'=>352, 'msg'=>$card_validate['result']['message']];
             // if($card_validate['result']['result']=='N')
             //       return ['code'=>353, 'msg'=>$card_validate['result']['message']];
           $card_validate=BankCert_Java($this->param['card_bankno'],$this->param['card_idcard'],$this->param['card_name'],$this->param['card_phone']);
          if(isset($card_validate['data']['identType']) && $card_validate['data']['identType']!='借记卡')
            return ['code'=>351,'msg'=>'认证失败:只能验证储蓄卡'];
          if($card_validate['info']=='交易成功 认证不一致，发卡行不支持此交易')
            return ['code'=>351,'msg'=>'该卡尚未开通无卡支付，请联系发卡行开通'];
          if($card_validate['code']!=200)
              return ['code'=>351,'msg'=>$card_validate['info']];
            $state=1;
            $card=array(
             'card_bankno'=>$this->param['card_bankno'],
             'card_phone'=>$this->param['card_phone'],
             'card_bank_province'=>$this->param['card_bank_province'],
             'card_bank_city'=>$this->param['card_bank_city'],
             'card_bank_area'=>$this->param['card_bank_addressId'],
             'card_bank_address' => $bankInfo['bank_name'],
             'card_bank_lang'   => $bankInfo['bank_code'],
             'card_bankname'=>$this->param['card_bankname'],
             'card_state'          => $state,
             'card_return'        =>json_encode($card_validate),
             );
          }else{

            #如果手机号和银行卡号都没有变动直接修改储蓄卡信息
            $card=array(
                 'card_bankno'=>$this->param['card_bankno'],
                 'card_phone'=>$this->param['card_phone'],
                 'card_bank_province'=>$this->param['card_bank_province'],
                 'card_bank_city'=>$this->param['card_bank_city'],
                 'card_bank_area'=>$this->param['card_bank_addressId'],
                 'card_bank_address' => $bankInfo['bank_name'],
                 'card_bank_lang'   => $bankInfo['bank_code'],
                 'card_bankname'=>$this->param['card_bankname'],
           );
          }
              #修改入网信息M02修改结算卡信息
            // $passageway=Passageway::where(['passageway_state'=>1,'passageway_also'=>1])->select();
            // foreach ($passageway as $key => $value) {
            //     $membernetObject=new Membernetsedit($this->param['uid'],$value['passageway_id'],'M02',$this->param['card_bankno'],$this->param['card_phone'],$cashcard['card_idcard']);
            //     $change=$membernetObject->quickNet();
            // }
            // #返回00为修改成功
            // if($membernetObject['respCode']!='00')
            //     return ['code'=>435];

            $bank_info=$bankInfo['bank_name'].' 尾号'.substr($this->param['card_bankno'], -4);

           $result=MemberCashcard::where('card_member_id='.$this->param['uid'])->update($card);
           if($result===false)
                 return ['code'=>435];
           return ['code'=>200,'msg'=>'更换储蓄卡成功~', 'data'=>$bank_info];
      }

      /**
      *  @version validation change_validationNew / Api 更换储蓄卡     @datetime    2018-02-03 21:16
      *  @author $bill$(755969423@qq.com)   @param  新的四元素认证接口    ☆☆☆::使用中
      **/
      public function change_validationNew()
      {
           #验证器验证 验证参数合法性
           $validate = Loader::validate('Membervalidation');
           #如果验证不通过 返回错误代码 及提示信息
           if(!$validate->scene('edit')->check($this->param))
                 return ['code'=>322, 'msg'=>$validate->getError()];
           #验证用户是否绑定储蓄卡
           $cashcard=MemberCashcard::where('card_member_id='.$this->param['uid'])->find();
           if(!$cashcard) return ['code'=>435];
           // $card_validate=BankCertNew(['bankCardNo'=>$this->param['card_bankno'], 'identityNo'=>$this->param['card_idcard'], 'mobileNo'=>$this->param['card_phone'], 'name'=>$this->param['card_name']]);
           // if($card_validate['code']!=0000) return ['code'=>351, 'msg'=>'实名认证失败。'];
           // if($card_validate['data']['resultCode']!='R001')  return ['code'=>351, 'msg'=>'认证信息不匹配或您的储蓄卡尚未开通无卡支付功能，请联系发卡行。'];
           // if($card_validate['data']['bankCardBin']['cardTy']!='D')  return ['code'=>351, 'msg'=>'认证失败:请更换一个储蓄卡完成实名认证~'];
           //  $card_bankname=$card_validate['data']['bankCardBin']['bankName'];
           //  if($num=strpos($card_bankname,'(')){
           //      $card_bankname=substr($card_bankname,0,$num);
           //  }
           $card_validate=BankCert_Java($this->param['card_bankno'],$this->param['card_idcard'],$this->param['card_name'],$this->param['card_phone']);
          if(isset($card_validate['data']['identType']) && $card_validate['data']['identType']!='借记卡')
            return ['code'=>351,'msg'=>'认证失败:只能验证储蓄卡'];
          if($card_validate['info']=='交易成功 认证不一致，发卡行不支持此交易')
            return ['code'=>351,'msg'=>'该卡尚未开通无卡支付，请联系发卡行开通'];
          if($card_validate['code']!=200)
              return ['code'=>351,'msg'=>$card_validate['info']];
          $card_bankname=isset($card_validate['data']['identBankName']) ? $card_validate['data']['identBankName'] : '';
           $card=array(
                'card_bankno'=>$this->param['card_bankno'],
                'card_phone'=>$this->param['card_phone'],
                //'card_bank_province'=>$this->param['card_bank_province'],
                //'card_bank_city'=>$this->param['card_bank_city'],
                //'card_bank_area'=>$this->param['card_bank_addressId'],
                //'card_bank_address' => $bankInfo['bank_name'],
                //'card_bank_lang'   => $bankInfo['bank_code'],
                'card_bankname'=> $card_bankname,
                'card_state'          => '1',
                'card_return'        =>json_encode($card_validate),
           );
           $result=MemberCashcard::where(['card_member_id'=>$this->param['uid']])->update($card);
           $bank_info=$result['card_bankname'].' 尾号'.substr($this->param['card_bankno'], -4);
           if($result===false) return ['code'=>435];
           return ['code'=>200,'msg'=>'更换储蓄卡成功~', 'data'=>$bank_info];
      }

 }
