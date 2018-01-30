<?php
/**
 *  @version Register controller / Api 会员注册
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-08 13:28:05
 *   @return 
 */
 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use app\index\model\System;
 use app\index\model\MemberLogin;
 use app\index\model\Member;
 use app\index\model\Wallet;
 use app\index\model\MemberRelation;
 use app\index\model\MemberTeam;
 use app\index\model\MemberNet;

 use app\index\model\SmsCode;

 class Register
 {
      protected $param;
      public $error;
      public function __construct($param)
      {
        	 $this->param=$param;
      }
 	
      /**
       *  @version Register method / Api 注册会员
       *  @author $bill$(755969423@qq.com)
       *  @datetime    2017-12-08 13:31:05
       *  @param phone=手机号  pwd=密码  parent_phone=邀请手机号   ☆☆☆::使用中
      **/
      public function register(Request $Request)
      {
           #验证参数是否存在
           if(!phone_check($this->param['phone']))
             	 return ['code'=>401];
           #验证密码
           if(!isset($this->param['pwd']) || empty($this->param['pwd']))
             	 return ['code'=>402];
           #手机验证码参数
           if(!isset($this->param['smsCode']) || empty($this->param['smsCode']))
                 return ['code'=>404];
           #验证码验证规则 读取本手机号最后一条没有使用的验证码 并且在系统设置的有效时间内
           $code_info=SmsCode::where(['sms_send'=>$this->param['phone'],'sms_log_state'=>1])->whereTime('sms_log_add_time', "-".System::getName('code_timeout').' minutes')->order('sms_log_id','desc')->find();
           if(!$code_info || $code_info['sms_log_content']!=$this->param['smsCode'])
                 return ['code'=>404];
           #改变验证码使用状态
           $code_info->sms_log_state=2;
           $result=$code_info->save();
           #验证是否成功
           if(!$result)
                 return ['code'=>404];
           #检查用户名(是否存在)
           $member=MemberLogin::phone_exit($this->param['phone']);
           if($member)
             	 return ['code'=>309];
           Db::startTrans();
            #填写注册信息
            try{
            	 #随机密码salt
            	 $rand_salt=make_rand_code();
            	 #加密密码
            	 $pwd=encryption($this->param['pwd'], $rand_salt);
            	 #新增会员基本信息
            	 $member_info= new Member([
            	 	 'member_nick'=>$this->param['phone'],
            	 	 'member_mobile'=>$this->param['phone'],
            	 	 'member_group_id'=>System::getName('open_reg_membertype'),
                 'member_image'=>System::getName('system_url').'/static/images/logo.png',
                 'member_root'=>0,
            	 ]);
            	 if(!$member_info->save())
            	 {
            	 	 Db::rollback();
            	 	 return ['code'=>300];
            	 }
                 $token = get_token();
            	 $member_login= new MemberLogin([
            	 	 'login_member_id'  => $member_info->member_id,
            	 	 'login_account'	  => $this->param['phone'],
            	 	 'login_pass'		    => $pwd,
            	 	 'login_pass_salt'  => $rand_salt,
                 'login_token'      => $token,
            	 	 'login_attempts'	  => 0,
            	 ]);
            	 #用户推荐表信息处理
            	 $parent=0;
            	 #检测邀请人手机号是否必填
            	 if(System::getName('is_have_recommend'))
            	 {
            	 	 if(!phone_check($this->param['phone']))
            	 	 {
            	 	 	 Db::rollback();
             	 	      return ['code'=>310];
             	      }
            	 }
            	 #验证是否有邀请手机号 并且邀请手机号是否存在
            	 if(isset($this->param['parent_phone']) && !empty($this->param['parent_phone']) && preg_mobile($this->param['parent_phone']))
            	 {
                   $parent_result=MemberLogin::phone_exit($this->param['parent_phone']);
                   #用手机号去查询会员信息
                   // $parent = $parent_result ? $parent_result['login_member_id'] : 0;
                   #TODO 系统设置里是否开启必须邀请人
                   // Member::update(['member_id'=>$member_info->member_id,'member_root'=>find_root($parent)]);
            	 	 if(!$parent_result)
            	 	 {
             	 	      Db::rollback();
             	 	      return ['code'=>311];
            	 	 }
            	 }

            	 $meber_relation= new MemberRelation([
            	 	 'relation_member_id'=>$member_info->member_id,
            	 	 'relation_parent_id'	 =>$parent,
            	 	 'relation_type'		 =>1,//TODO 邀请方式
            	 ]);
            	 #初始化会员钱包信息
            	 $member_wallet= new Wallet([
            	 	 'wallet_member'=>$member_info->member_id,
            	 	 'wallet_amount'=>0
            	 ]);

               #初始化会员团队信息
               $member_team=new MemberTeam([
                'team_name'=>$member_info->member_nick,
                'team_member_id'=>$member_info->member_id,
               ]);

               #初始化会员入网信息
               $MemberNet=new MemberNet([
                'net_member_id'=>$member_info->member_id,
               ]);
               
            	 if( !$member_login->save() || !$meber_relation->save() || !$member_wallet->save() || !$member_team->save() || !$MemberNet->save())
            	 {
            	 	 Db::rollback();
            	 	 return ['code'=>300];
            	 }

               

            	 Db::commit();
                 $data=Member::member_info($token);
            	 return ['code'=>200,'msg'=>'注册成功~','data'=>$data]; //请求成功
            } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>308,'msg'=>$e->getMessage()];
            }
      }
      
      /**
      *  @version register_phone method / Api 验证手机号是否注册
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-13 09:03:05
      *  @param phone=手机号  ☆☆☆::使用中
      **/
      public function register_phone()
      {
           #验证参数是否存在
           if(!phone_check($this->param['phone']))
                 return ['code'=>401];
           #查找账号
           $memberLogin=MemberLogin::phone_exit($this->param['phone']);
           #能否查找到手机号码
           if($memberLogin)
                 return ['code'=>309];
           return ['code'=>200,'msg'=>'本手机号尚未注册~'];
      }

 }