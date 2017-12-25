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
use app\index\model\MemberLogin;
use app\index\model\System;
use think\Model;

 class Login extends Model
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
 	 *  @param phone=手机号  pwd=密码 
      **/
      public function login()
      {	
        var_dump(123);die;
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
           return ['code'=>200,'msg'=>'登录成功~'];
      }


 }
