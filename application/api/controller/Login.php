<?php
 /**
 *  @version Login controller / Api 会员登录
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-08 10:13:05
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
 use app\index\model\SmsCode as SmsCodes;

 class Login 
 {
      protected $param;
      public $error;
      public function __construct($param)
      {
        	 $this->param=$param;
      }

      /**
 	 *  @version Login method / Api 登录方法
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-08 11:19:05
 	 *  @param phone=手机号  pwd=密码     ☆☆☆::使用中
      **/
      public function login()
      {	
      	 #验证参数是否存在
      	 if(!phone_check($this->param['phone']))
      	 	 return ['code'=>401];
      	 #验证密码
      	 if(!isset($this->param['pwd']) || empty($this->param['pwd']))
      	 	 return ['code'=>402];
      	 #查找账号
      	 $memberLogin=MemberLogin::phone_exit($this->param['phone']);
      	 #能否查找到手机号码
      	 if(!$memberLogin)
      	 	 return ['code'=>403];
      	 #验证最大尝试次数 TODO: 验证多久时间内最大的登录次数
           if($memberLogin['login_attempts']>=System::getName('is_locking'))
           {
                 #判断当前登录时间是否距离上一次锁定时间超出系统设定时间
                 $timeout=floor(abs(time()-strtotime($memberLogin['login_update_time']))/60);
                 if( $timeout < System::getName('is_locking_time'))
                      return ['code'=>312];
                 $memberSetInc=MemberLogin::where(['login_account'=>$this->param['phone']])->setField('login_attempts',0);
           }
      	 #如果手机号存在的话 对比密码信息 TODO 密码加密算法 非对称加密
           if ($memberLogin['login_pass']!=encryption($this->param['pwd'], $memberLogin['login_pass_salt'])) {
           	 $memberSetInc=MemberLogin::where(['login_account'=>$this->param['phone']])->setInc('login_attempts');
                 $memberTime=MemberLogin::where(['login_account'=>$this->param['phone']])->setField('login_update_time',date('Y-m-d H:i:s',time()));
                 return ['code'=>302];
           }
           #验证账号异常
           if($memberLogin['login_state']!='1')
                 return ['code'=>318];
               //dump($memberLogin['login_token']);
           $data=Member::member_info($memberLogin['login_token']);
           return ['code'=>200,'msg'=>'登录成功~', 'data'=>$data];
      }

      /**
      *  @version find_pwd method / Api 找回密码
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-13 09:25:05
      *  @param phone=手机号  smscode=验证码  new_pwd=新密码     ☆☆☆::使用中
      **/
      public function find_pwd()
      {
          //return ['code'=>505, 'data'=>$this->param['smsCode'].$this->param['phone']];
           #验证参数是否存在
           if(!phone_check($this->param['phone']))
               return ['code'=>401];
           #手机验证码参数
           if(!isset($this->param['smsCode']) || empty($this->param['smsCode']))
                 return ['code'=>404];
           #验证码验证规则 读取本手机号最后一条没有使用的验证码 并且在系统设置的有效时间内
           $code_info=SmsCodes::where(['sms_send'=>$this->param['phone'],'sms_log_state'=>1])->whereTime('sms_log_add_time', "-".System::getName('code_timeout').' minutes')->order('sms_log_id','desc')->find();
           //return ['code'=>600, 'data'=>SmsCodes::getLastSql()];
           if(!$code_info || ($code_info['sms_log_content']!=$this->param['smsCode']))
                 return ['code'=>404];
           #改变验证码使用状态
           $code_info->sms_log_state=2;
           #验证是否成功
           if($code_info->save()===false)
                 return ['code'=>429];
           #检查用户名(是否存在)
           $member=MemberLogin::phone_exit($this->param['phone']);
           if(!$member)
                 return ['code'=>304];
           if(encryption($this->param['new_pwd'], $member['login_pass_salt'])==$member['login_pass'])
                 return ['code'=>319];
           Db::startTrans();
           try {
                 #随机密码salt
                 $rand_salt=make_rand_code();
                 #加密密码
                 $pwd=encryption($this->param['new_pwd'], $rand_salt);
                 #修改会员登录信息和token
                 $token = get_token();
                 $member_login= new MemberLogin();
                 $result=$member_login->save([
                    'login_pass'  =>$pwd,
                    'login_pass_salt' => $rand_salt,
                    'login_token'   => $token
                  ],['login_account' =>$this->param['phone']]);
                 if(!$result){
                      Db::rollback();
                      return ['code'=>313];
                 }
                 Db::commit();
                 $data=Member::member_info($token);
                 return ['code'=>200, 'msg'=>'密码更改成功~', 'data'=>$data];
           } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>308,'msg'=>$e->getMessage()];
           }
      }

 }
