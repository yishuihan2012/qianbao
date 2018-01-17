<?php
/**
 *  @version Sms controller / Api 会员登录
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-08 10:13:05
 *   @return 
 */
 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
use app\index\model\SmsCode;
use app\index\model\System;

 class Sms
 {
      protected $param;
      public $error;
      public function __construct($param)
      {
        	 $this->param=$param;
      }

      /**
 	 *  @version send method / Api 发送短信
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-08 11:19:05
 	 *  @param phone=手机号 / 发送的对象 
      **/
      public function send(Request $Request)
      {	
      	 #验证手机/发送对象是否存在
      	 if(!phone_check($this->param['phone']))
      	 	 return ['code'=>401];
           #随机一个验证码
           $code=verify_code(System::getName('code_number'));
           #设定短信内容
           $message="您本次操作的验证码为".$code."，请尽快使用。有效期为".System::getName('code_timeout')."分钟。";
           $log=new SmsCode([
                 'sms_log_content'=>$code,
                 'sms_log_state'    =>1,
                 'sms_log_type'     =>'验证码',
                 'sms_send'          =>$this->param['phone']
           ]);
           $sms_result=$log->save();
           if(!$sms_result)
                 return['code'=>303]; 
           $result=send_sms($this->param['phone'], $message);
           #如果发送成功,记录发送记录表
           if(!$result)
                 return ['code'=>303];
           return ['code'=>200,'msg'=>'验证码发送成功~'];
      }

      /**
        *  @version check method / Api 验证码验证
        *  @author $bill$(755969423@qq.com)
        *  @datetime    2017-12-11 11:29:05
        *  @param phone=手机号 / 发送的对象 
        *  @param code =验证码 / 要检验的验证码
      **/
      public function check(Request $Request)
      {
           if(!phone_check($this->param['phone']))
                 return ['code'=>401];
           if(!isset($this->param['code']) || empty($this->param['code']))
                 return ['code'=>404];
           #验证码验证规则 读取本手机号最后一条没有使用的验证码 并且在系统设置的有效时间内
           $code_info=SmsCode::where(['sms_send'=>$this->param['phone'],'sms_log_state'=>1])->whereTime('sms_log_add_time', "-".System::getName('code_timeout').' minutes')->find();
           if(!$code_info || $code_info['sms_log_content']!=$this->param['code'])
                 return ['code'=>404];
           #改变验证码使用状态
           $code_info->sms_log_state=2;
           $result=$code_info->save();
           #验证是否成功
           if(!$result)
                 return ['code'=>404];
           return ['code'=>200,'msg'=>'验证码验证成功~'];
      }
           
 }
