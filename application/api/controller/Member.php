<?php
 /**
 *  @version Member controller / Api 会员接口
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-13 9:01:05
 *   @return 
 */
 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use think\Loader;
 use app\index\model\Member as Members;
 use app\index\model\MemberGroup;
 use app\index\model\MemberRelation;
 use app\index\model\MemberLogin;
 use app\index\model\System;
 use app\index\model\SmsCode;
 use app\index\model\MemberSuggestion;
 use app\index\model\MemberAccount;
 use app\index\model\MemberTeam;
 use app\index\model\MemberCreditcard;
 use app\index\model\MemberCashcard;
 use app\index\model\ChannelRate;
 use app\index\model\ChannelType;
 use app\index\model\MemberCert;
 use app\index\model\Passageway;
 use app\index\model\PassagewayItem;
 use app\index\model\Wallet;
 use app\index\model\Recomment;
 
 class Member 
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
                 $member=Members::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
                 if(!$member && !$this->error)
                       $this->error=317;
            }catch (\Exception $e) {
                 $this->error=317;
           }
      }

      /**
 	 *  @version info method / Api 会员基础信息 通用接口
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-13 09:03:05
 	 *  @param phone=手机号  
      **/ 
      public function info()
      {
           $member=Members::get($this->param['uid']);
           $data=array();
           $data['authState']=$member['member_cert'];
           $data['name']=$member['member_cert']==1 ? $member['member_nick'] : '';

           $data['membercard']=$member['member_cert']==1 ? $member->memberCert->cert_member_idcard : '';#card_preg()
           $data['phone']=$member['member_mobile'];
           $data['portrait']=$member['member_image'];

           $data['wallet_total_revenue']=$member->memberWallet->wallet_total_revenue;
           $data['wallet_fenrun']=$member->memberWallet->wallet_fenrun;
           $data['wallet_commission']=$member->memberWallet->wallet_commission;
           $data['wallet_amount']=$member->memberWallet->wallet_amount;


           $data['wallet_total_revenue']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_total_revenue), 0, -1));
           $data['wallet_fenrun']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_fenrun), 0, -1));
           $data['wallet_commission']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_commission), 0, -1));
           $data['wallet_amount']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_amount), 0, -1));

           $data['memberLevelId']=$member['member_group_id'];
           $data['memberLevelName']=$member->memberGroup->group_name;
           #查询信用卡绑定数量
           $data['numberOfCreditCard']=MemberCreditcard::where(['card_member_id'=>$this->param['uid'],'card_state'=>'1'])->count();
           $data['alipayBindState']=MemberAccount::where(['account_user'=>$this->param['uid'],'account_type'=>'Alipay'])->find() ? 1 : 0;
           $data['wechatBindState']=MemberAccount::where(['account_user'=>$this->param['uid'],'account_type'=>'Weipay'])->find() ? 1 : 0;
           #查询会员下级数量 TODO: 现在是直接下级数量 是否改成三级
           $data['subordinateNumber']=MemberRelation::where('relation_parent_id',$this->param['uid'])->count();
           $parent_id=MemberRelation::where(['relation_member_id'=>$this->param['uid']])->value('relation_parent_id');
           $data['parent']=$parent_id=='0' ? '' : Members::where('member_id',$parent_id)->value('member_nick');
           $data['parent_phone']=$parent_id=='0' ? '' : Members::where('member_id',$parent_id)->value('member_mobile');
           #查询用户储蓄卡
           $CashCard=MemberCashcard::where(['card_member_id'=>$this->param['uid'],'card_state'=>1])->find();
           $data['cashcardinfo']=$CashCard['card_bankname'].' 尾号'.substr($CashCard['card_bankno'], -4); 
           $data['hasmessage']=1; 
           return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$data];
      }

      /**
      *  @version bind_account method / Api 绑定第三方账号
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 09:22:05
      *  @param uid=会员ID token=登录令牌 thirdPartType=账号类型 Alipay/Weiapy ?  thirdPartToken=账号
      **/ 
      public function bind_account()
      {
           #验证器验证 验证参数合法性
           $validate = Loader::validate('Memberaccount');
           #如果验证不通过 返回错误代码 及提示信息
           if(!$validate->scene('bind')->check($this->param))
                 return ['code'=>322, 'msg'=>$validate->getError()];
           try {
                 $memberAccount=new MemberAccount;
                 #查找当前会员是否绑定过此账号
                 $memberAccount_info=$memberAccount->get(['account_user'=>$this->param['uid'],'account_type'=>$this->param['thirdPartType']]);
                 #如果绑定过此第三方账号 则抛出错误
                 if ($memberAccount_info)
                      return ['code'=>321];
                 $memberAccount->account_user  = $this->param['uid'];
                 $memberAccount->account_type  = $this->param['thirdPartType'];
                 #针对支付宝某些手机第一次授权出现 失败问题 判断账号是否为 2088开头
                 if($this->param['thirdPartType'] == "Alipay" && substr( $this->param['thirdPartToken'], 0, 4 ) !="2088")
                      return ['code'=>322];
                 $memberAccount->account_account  = $this->param['thirdPartToken'];
                 $memberAccount->account_info=isset($this->param['thirdPartUserInfo']) ? $this->param['thirdPartUserInfo'] : "";
                 if ($memberAccount->save()===false)
                      return ['code'=>322];
                 return ['code'=>200,"msg"=>"绑定成功~"];
           } catch (\Exception $e) {
                 return ['code'=>322];
           }
      }

      /**
      *  @version unbind_account method / Api 解绑第三方账户
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 09:22:05
      *  @param uid=会员ID token=登录令牌 thirdPartType=账号类型 Alipay/Weiapy 
      **/ 
      public function unbind_account()
      {
           #验证器验证 验证参数合法性
           $validate = Loader::validate('Memberaccount');
           #如果验证不通过 返回错误代码 及提示信息
           if(!$validate->scene('ubind')->check($this->param))
                 return ['code'=>322, 'msg'=>$validate->getError()];
           try {
                 $memberAccount=new MemberAccount;
                 $memberAccount_info=$memberAccount->where(['account_user'=>$this->param['uid'],'account_type'=>$this->param['thirdPartType']])->find();
                 if (!$memberAccount_info)
                      return ['code'=>360];
                 if ($memberAccount_info->delete()===false)
                      return ['code'=>360];
                 return ['code'=>200, 'msg'=>'解绑成功~'];
           } catch (\Exception $e) {
                 return ['code'=>360];
           }
      }

      /**
 	 *  @version edit_phone method / Api 更改手机号
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-14 15:06:05
 	 *  @param uid=会员ID token=登录令牌 smsCode=验证码 newPhone=新手机号
      **/ 
      public function edit_phone()
      {	
      	 if(!isset($this->param['smsCode']) || empty($this->param['smsCode']) || !isset($this->param['newPhone']) || empty($this->param['newPhone']) )
      	 	 return ['code'=>423] ;
           #验证码验证规则 读取本手机号最后一条没有使用的验证码 并且在系统设置的有效时间内
           $code_info=SmsCode::where(['sms_send'=>$this->param['newPhone'],'sms_log_state'=>1])->whereTime('sms_log_add_time', "-".System::getName('code_timeout').' minutes')->find();
           if(!$code_info || $code_info['sms_log_content']!=$this->param['smsCode'])
                 return ['code'=>404];
           #改变验证码使用状态
           $code_info->sms_log_state=2;
           $result=$code_info->save();
           #验证是否成功
           if(!$result)
                 return ['code'=>404];
           #判断原手机号和新手机号是否相同 首先获取会员信息
      	 $member_info=Members::hasWhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id',$this->param['uid'])->find();
         	 if(!$member_info)
      		 return ['code'=>423];
           if($member_info->memberLogin->login_account==$this->param['newPhone'])
      		 return ['code'=>427];
      	 #判断新手机号是否注册
      	 if(MemberLogin::phone_exit($this->param['newPhone']))
      	 	 return ['code'=>309];
      	 #检测通过 更改手机号
           Db::startTrans();
            #填写注册信息
            try{
            	  #获取会员信息
            	 $member=Members::get($member_info['member_id']);
            	 $memberlog=MemberLogin::get(['login_member_id'=>$member_info['member_id'],'login_token'=>$this->param['token']]);
            	 #如果实名认证的话 则不更改member_nick实名后的名字
            	 if($member['member_cert']!='1')
            	 	 $member->member_nick=$this->param['newPhone'];
            	 $member->member_mobile=$this->param['newPhone'];
            	 $memberlog->login_account=$this->param['newPhone'];
            	 if(!$member->save() || !$memberlog->save())
            	 {
            	 	 Db::rollback();
            	 	 return ['code'=>423];
            	 }
            	 Db::commit();
            	 return ['code'=>200, 'msg'=>'修改手机号成功~', 'data'=>''];
            } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>423,'msg'=>$e->getMessage()];
            }
      } 
      
      /**
 	 *  @version edit_avatar method / Api 更改会员头像
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-14 09:58:05
 	 *  @param uid=会员ID token=登录令牌 portraitUrl=图片地址  
      **/ 
      public function edit_avatar()
      {	
      	 if(!isset($this->param['portraitUrl']) || empty($this->param['portraitUrl']))
      	 	 return ['code'=>423] ;
           #获取当前会员信息
      	 $member_info=Members::hasWhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id',$this->param['uid'])->find();
      	 if(!$member_info)
      		 return ['code'=>359];
      	 $member=Members::get($member_info['member_id']);
      	 $member->member_image=$this->param['portraitUrl'];
      	 if($member->save()===false)
      		 return ['code'=>359];
      	 return ['code'=>200,'msg'=>'头像更改成功~'];
      }

      /**
 	 *  @version edit_pwd method / Api 更改登录密码
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-13 09:03:05
 	 *  @param uid=会员ID token=登录令牌 oldPwd=原密码 newPwd=新密码  
      **/ 
      public function edit_pwd()
      {
      	 if(!isset($this->param['oldPwd']) || empty($this->param['oldPwd']) || !isset($this->param['newPwd']) || empty($this->param['newPwd']))
      	 	 return ['code'=>423] ;
      	 #以uid和token去查询用户登录表信息
      	 $member=MemberLogin::where(['login_member_id'=>$this->param['uid'],'login_token'=>$this->param['token']])->find();
      	 if(!$member)
      	 	 return ['code'=>313] ;
      	 #对比旧密码是否相同
      	 if($member['login_pass']!=encryption($this->param['oldPwd'], $member['login_pass_salt']))
      	 	 return ['code'=>424];
      	 if($this->param['oldPwd']==$this->param['newPwd'])
      	 	 return ['code'=>319] ;  	 
          	 #随机密码salt
          	 $rand_salt=make_rand_code();
          	 #加密密码
          	 $pwd=encryption($this->param['newPwd'], $rand_salt);
          	 $member->login_pass=$pwd;
          	 $member->login_pass_salt=$rand_salt;
          	 if($member->save()===false)
          	 	 return ['code'=>313];
          	 $data=Members::member_info($this->param['token']);
          	 return ['code'=>200,'msg'=>'密码更改成功~','data'=>$data];
      }

      /**
      *  @version level method / Api 会员升级
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-13 09:03:05
      *  @param uid=会员ID token=登录令牌 targetLevelId=升级的目标会员级别  
      **/ 
      public function level()
      {
           if(!isset($this->param['targetLevelId']) || empty($this->param['targetLevelId']))
                 return ['code'=>448];
           $member=Members::get($this->param['uid']);
           $member_group=MemberGroup::get(['group_salt'=>$this->param['targetLevelId']]);
           if(!$member_group)
                 return ['code'=>449];
           #验证目标组是否可以升级
           if($member_group['group_level']!='1')
                 return ['code'=>450];
           #验证目标组是否可以通过付费方式升级
           if($member_group['group_level_type']!=-1 && $member_group['group_level_type']!=3)
                 return ['code'=>451];
           $currentgroup=MemberGroup::get($member['member_group_id']);
           #验证目标组是否比当前组级别高
           if($member_group['group_salt']<=$currentgroup['group_salt'])
                 return ['code'=>452];
           #计算差价
           $price_diff  =$member_group['group_level_money']-$currentgroup['group_level_money'];
           if($price_diff<0)
                 return ['code'=>453];
           $data['signedStr']=$price_diff;
           $data['money']=$price_diff;
           return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$data];
      }

      /**
      *  @version edit_pwd method / Api 绑定会员推送设备
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-13 09:03:05
      *  @param uid=会员ID token=登录令牌 registrationId 设备ID  
      **/ 
      public function bind_devince()
      {
           if(!isset($this->param['registrationId']) || empty($this->param['registrationId']))
                 return ['code'=>446];
           $member=Members::where('member_id', $this->param['uid'])->find();
           $member->member_token=$this->param['registrationId'];
           if($member->save()===false)
                 return ['code'=>447];
           return ['code'=>200, 'msg'=>'绑定成功~', 'data'=>'']; 
      }

      /**
 	 *  @version feedback method / Api 会员反馈
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-14 13:09:05
 	 *  @param uid=会员ID text=反馈内容  
      **/ 
      public function feedback()
      {
	    	 if(!isset($this->param['text']) || empty($this->param['text']))
	    	 	 return ['code'=>425];
	    	 $member=Members::get($this->param['uid']);
	    	 if(!$member)
	    	 	 return ['code'=>426];
	    	 $suggestion=new MemberSuggestion([
	    	 	 'suggestion_member_id' =>$this->param['uid'],
	    	 	 'suggestion_info'		  =>$this->param['text'],
	    	 ]);
	    	 if($suggestion->save()===false)
	    	  	 return ['code'=>426];
	    	 return ['code'=>200, 'msg'=>'信息反馈成功~','data'=>''];
      }

      /**       
      *  @version registerForOthers method / Api  替好友开通       
      *  @author $bill$(1270909623@qq.com)       *  @datetime    2017-12-15 11:58:05
      *  @param        
      **/       
      public function registerForOthers()
      {
           #验证parent_phone号码是否存在
           if(!phone_check($this->param['parent_phone']))
                 return ['code'=>428];
           #验证参数是否存在
           if(!phone_check($this->param['phone']))
                 return ['code'=>401];
           #手机验证码参数
           if(!isset($this->param['smsCode']) || empty($this->param['smsCode']))
                 return ['code'=>404];
           #验证码验证规则 读取本手机号最后一条没有使用的验证码 并且在系统设置的有效时间内            
           $code_info=SmsCode::where(['sms_send'=>$this->param['phone'],'sms_log_state'=>1])->whereTime('sms_log_add_time', "-".System::getName('code_timeout').' minutes')->find();
           if(!$code_info || $code_info['sms_log_content']!=$this->param['smsCode'])
                 return ['code'=>404];
           #改变验证码使用状态
           $code_info->sms_log_state=2;
           $result=$code_info->save();
           #验证是否成功            
           if(!$result)                  
                 return ['code'=>404];
           #检查用户(是否存在)            
           $member=MemberLogin::phone_exit($this->param['phone']);
           if($member)                  
                 return ['code'=>309];
           $parentmember=MemberLogin::phone_exit($this->param['parent_phone']);
           if(!$parentmember)                  
                 return ['code'=>428];
           Db::startTrans();             
           #填写注册信息             
           try{
                 #随机密码salt                  
                 $rand_salt=make_rand_code();                  
                 #加密密码
                 $pwd=encryption(substr($this->param['phone'], -6), $rand_salt);
                 #新增会员基本信息                  
                 $member_info= new Member([
                      'member_nick'=>$this->param['phone'],
                      'member_mobile'=>$this->param['phone'],
                      'member_group_id'=>System::getName('open_reg_membertype')]);
                 if($member_info->save()===false)
                 {
                      Db::rollback();                       
                      return ['code'=>300];                  
                 }
                 $token = get_token();                  
                 $member_login= new MemberLogin([
                      'login_member_id'=>$member_info->member_id,
                      'login_account'    =>$this->param['phone'],                       
                      'login_pass'  =>$pwd,                       
                      'login_pass_salt'  =>$rand_salt,
                      'login_token'         =>$token,                       
                      'login_attempts'   =>0,
                 ]);                  
                #用户推荐表信息处理                  
                 $meber_relation= new MemberRelation([
                      'relation_member_id'=>$member_info->member_id,
                       'relation_parent_id'  =>$parentmember['login_member_id'],
                      'relation_type'     =>6,//TODO 邀请方式                  
                 ]);
                 #初始化会员钱包信息                  
                 $member_wallet= new Wallet([
                      'wallet_member'=>$member_info->member_id,
                      'wallet_amount'=>0                  
                ]);                  
                 if(!$member_login->save() || !$meber_relation->save() || !$member_wallet->save())
                 {                       
                      Db::rollback();                       
                      return ['code'=>300];                  
                 }                  
                 Db::commit();
                 $data=Member::member_info($token);                  
                 return  ['code'=>200,'msg'=>'帮扶注册成功~','data'=>$data]; 
                 //请求成功             
           } catch (\Exception $e) {                  
                 Db::rollback();                 
                 return ['code'=>308,'msg'=>$e->getMessage()];             
           }       
      }

      /**
      *  @version get_team method / Api 我的团队
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 11:58:05
      *  @param uid=会员ID
      **/
      public function get_team()
      { 
           $group=MemberGroup::select();
           $data['totalChildAmount']=0;
           foreach ($group as $key => $value) {
             $data['list'][$key]['levelName']=$value['group_name'];
             $data['list'][$key]['levelId']=$value['group_id'];
             $data['list'][$key]['levelIcon']=$value['group_thumb'];
             $data['list'][$key]['childAmount']=0;
             $data['list'][$key]['grandChildAmount']=0;
             $MemberRelation_1rd=MemberRelation::where("relation_parent_id={$this->param['uid']}")->select();
             foreach ($MemberRelation_1rd as $k => $val) {
                $member[$k]=Members::with('membergroup')->where('member_id='.$val['relation_member_id'])->find();
                if($member[$k]['group_id']==$value['group_id']){
                  $data['list'][$key]['childAmount']+=1;
                } 

                $member_2rd[$k]=MemberRelation::where('relation_parent_id='.$member[$k]['member_id'])->select();
                foreach ($member_2rd[$k] as $k1 => $val1) {
                    $member_3rd[$k1]=Members::with('membergroup')->where('member_id='.$val1['relation_member_id'])->find();
                    if($member_3rd[$k1]['group_id']==$value['group_id']){
                    $data['list'][$key]['grandChildAmount']+=1;
                  } 
                }

             }

             $data['list'][$key]['grossChildAmount']=$data['list'][$key]['grandChildAmount']+$data['list'][$key]['childAmount'];
             #总人数
             $data['totalChildAmount']+=$data['list'][$key]['grossChildAmount'];
           }
          
         return ['code'=>200, 'msg'=>'信息反馈成功~','data'=>$data];
      }


      /**
      *  @version get_team_info method / Api 我的团队队员列表
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 11:58:05
      *  @param group_id 会员等级id  member_cert 认证状态 0全部 1已认证，2审核中，3未完善，4未通过
      **/
      public function get_team_info()
      { 
        if(!isset($this->param['group_id']) || empty($this->param['group_id']) || !isset($this->param['member_cert']))
            $this->error=314;

        $this->param['member_cert'] ? $this->param['member_cert'] : 0;
        $member_info=array();
        $MemberRelation_1rd=MemberRelation::with('memberp')->where("relation_parent_id={$this->param['uid']}")->select();
        foreach ($MemberRelation_1rd as $key => $value) {
          if($value['member_cert']==$this->param['member_cert'] || $this->param['member_cert']==0){
              $member_1rd=Members::where('member_id='.$value['relation_member_id'])->field('member_id,member_image, member_mobile, member_creat_time, member_cert')->find();
              $member_info[]=$member_1rd;
              $MemberRelation_2rd=MemberRelation::with('memberp')->where('relation_parent_id='.$member_1rd['member_id'])->select();
              if(!empty($MemberRelation_2rd)){
                foreach ($MemberRelation_2rd as $k => $val) {
                  if($val['member_cert']==$this->param['member_cert'] || $this->param['member_cert']==0){
                     $member_2rd=Members::where('member_id='.$val['relation_member_id'])->field('member_id,member_image, member_mobile, member_creat_time, member_cert')->find();
                     $member_info[]=$member_2rd;
                    }
                }
              }
              }
        }
        $data['totalChildAmount']=count($member_info);
        $data['list']=$member_info;
          
         return ['code'=>200, 'msg'=>'信息反馈成功~','data'=>$data];
      }



      /**
      *  @version get_upgrade_price method / Api 会员等级列表
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-16 11:58:05
      *  @param 
      **/
      public function get_upgrade_price()
      { 
            #获取用户组等级信息
            $membergroup=MemberGroup::select();
            foreach ($membergroup as $key => $value) {
                $data['group'][$key]['name']=$value['group_name'];
                $data['group'][$key]['group_salt']=$value['group_salt'];
                $data['group'][$key]['id']=$value['group_id'];
                $data['group'][$key]['group_level_money']=$value['group_level_money'];
                $data['group'][$key]['price_desc']='普通会员升级到此用户组需要的价格';
                $data['group'][$key]['group_url']=$value['group_url'];
                #获取每个用户等级的最低费率
                $passagewayItem=PassagewayItem::where('item_group='.$value['group_id'])->order('item_rate asc')->find();
                #获取对应费率的通道信息
                $passageway=Passageway::where('passageway_state=1 and passageway_id='.$passagewayItem['item_passageway'])->find();
                #如果不为空
                if(!empty($passageway)){
                  $data['group'][$key]['rate']='刷卡费率：'.$passagewayItem['item_rate'].'% 代还费率：'.$passagewayItem['item_also'].'%';
                  $data['group'][$key]['des']=$passageway['passageway_desc'];
                  $data['group'][$key]['icon']=$passageway['passageway_avatar'];
                }
            }
            $member=Members::where('member_id='.$this->param['uid'])->find();
            if(!$member)
              return ['code'=>314];

            $current=MemberGroup::where('group_id='.$member['member_group_id'])->find();

            if(!$current)
              return ['code'=>314];

            $data['current_salt']=$current['group_salt'];
            return ['code'=>200, 'msg'=>'信息反馈成功~','data'=>$data];
      }


      /**
   *  @version get_wallet method / Api 会员资产信息
   *  @author $bill$(755969423@qq.com)
   *  @datetime    2017-12-13 09:03:05
   *  @param   
      **/ 
      public function get_wallet()
      {
           $member=Members::get($this->param['uid']);
           $data=array();

           $data['wallet_total_revenue']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_total_revenue), 0, -1));
           $data['wallet_fenrun']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_fenrun), 0, -1));
           $data['wallet_commission']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_commission), 0, -1));
           $data['wallet_amount']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_amount), 0, -1));
           $data['wallet_invitation']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_invite), 0, -1));

           return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$data];
      }

<<<<<<< HEAD
      /**
    * 生成签名信息 获取授权登录信息
    * @return [type] [description]
    */
    public function get_sign()
    {
        $payment=new \app\index\controller\Alipay();
        $return=$payment->get_sign(); //转账
        return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$return];
    }
=======
        /**
   *  @version recomment_list method / Api 用户分润分佣明细列表
   *  @author $bill$(755969423@qq.com)
   *  @datetime    2017-12-25 09:03:05
   *  @param   
      **/ 
      public function recomment_list()
      {
          if(!isset($this->param['type']))
             $this->error=314;

          $this->param['type'] ? $this->param['type'] : 1;
           
          $recomment=Recomment::with('member')->where('recomment_member_id='.$this->param['uid'].' and recomment_type='.$this->param['type'])->select();
          if(!$recomment)
            return ['code'=>314];

           return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$recomment];
      }
>>>>>>> dev4
 }
