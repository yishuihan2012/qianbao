<?php
/*
 * @Author: John 
 * @Date: 2017-12-28 15:49:35 
 * @Last Modified by: John
 * @Last Modified time: 2017-12-29 09:20:10
 */
namespace app\api\controller;

use think\Config;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

class Dayusms
{
    protected $client;
    protected $sendSms;
    protected $phone;
    protected $code;
    public function __construct()
    {
        try{
            $this->client  = new Client([
                'accessKeyId'    => Config::get("OSS.accessKeyId"),
                'accessKeySecret' => Config::get("OSS.accessKeySecret"),
            ]);
            $this->sendSms = new SendSms;
        }catch(\think\Exception $e){
            print_r($e->getMessage());
        }
    }
    #设置手机信息
    public function set_phone($phone){
        $this->phone = $phone;
    }
    #设置code信息
    public function set_code($code){
            $this->code = $code;
    }
    #发送验证码
    public function send_vertification_code(){        
        try{
            $this->sendSms->setPhoneNumbers($this->phone);
            $this->sendSms->setSignName(Config::get('default_title'));
            $this->sendSms->setTemplateCode(Config::get('sms_template.normal_vert_code')); //模板
            $this->sendSms->setTemplateParam(['code' => $this->code]);
            $this->sendSms->setOutId('fangtou_vertification_code');
            $result = $this->client->execute($this->sendSms);
            if($result->Code=="OK"){
                return ['code'=>200,'msg'=>$result->Message];
            }else{
                //错误代码 
                if($result->Code=='isv.BUSINESS_LIMIT_CONTROL'){             //业务限流
                    $msg = "您的短信发送过于频繁，请稍后再试";
                }else{
                    $msg = $result->Message;
                }
                //余额不足
                throw new \think\Exception($msg);
            }
        }catch(\think\Exception $error){
            throw new \think\Exception($error->getMessage());
        }
    }
    #注册验证码模板
    public function send_register_code(){        
        try{
            $this->sendSms->setPhoneNumbers($this->phone);
            $this->sendSms->setSignName(Config::get('default_title'));
            $this->sendSms->setTemplateCode(Config::get('sms_template.register_vert_code')); //模板
            $this->sendSms->setTemplateParam(['code' => $this->code,'product'=>Config::get('default_title')]);
            $this->sendSms->setOutId('fangtou_register_code');
            $result = $this->client->execute($this->sendSms);
            if($result->Code=="OK"){
                return ['code'=>200,'msg'=>$result->Message];
            }else{
                //错误代码 
                if($result->Code=='isv.BUSINESS_LIMIT_CONTROL'){             //业务限流
                    $msg = "您的短信发送过于频繁，请稍后再试";
                }else{
                    $msg = $result->Message;
                }
                throw new \think\Exception($msg);
            }
        }catch(\think\Exception $error){
            throw new \think\Exception($error->getMessage());
        }
    }
}
