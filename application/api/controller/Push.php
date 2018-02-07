<?php
/*
 * @Author: John 
 * @Date: 2017-10-19 19:03:05
 * @Last Modified by: John
 * @Last Modified time: 2017-12-29 13:54:38
 */
namespace app\api\controller;

use think\Config;
use JPush\Client as JPushs;
use app\index\model\System;
class Push
{
    protected $client;
    protected $platform="all";
    protected $audience;
    protected $message_title="1";
    protected $sms_message;
    protected $options;
    protected $cid;
    protected $tags=[];
    protected $message_type=2;
    protected $message_info_type;
    protected $message_info_item;
    protected $message_sort_desc;
    protected $registration_id="";
    protected $group;
    
    public function __construct()
    {
        try{
            // $this->client = new JPushs(Config::get('jpush.api_key'), Config::get('jpush.api_master'));
            $this->client =new JPushs(System::getName('jpush_api_key'),System::getName('jpush_api_master'));
            $this->options = [
                // sendno: 表示推送序号，纯粹用来作为 API 调用标识，
                // API 返回时被原样返回，以方便 API 调用方匹配请求与返回
                // 这里设置为 100 仅作为示例
                // 'sendno' => 100,
                // time_to_live: 表示离线消息保留时长(秒)，
                // 推送当前用户不在线时，为该用户保留多长时间的离线消息，以便其上线时再次推送。
                // 默认 86400 （1 天），最长 10 天。设置为 0 表示不保留离线消息，只有推送当前在线的用户可以收到
                // 这里设置为 1 仅作为示例
                'time_to_live' => 1,
                // apns_production: 表示APNs是否生产环境，
                // True 表示推送生产环境，False 表示要推送开发环境；如果不指定则默认为推送生产环境
                'apns_production' => false,
                // big_push_duration: 表示定速推送时长(分钟)，又名缓慢推送，把原本尽可能快的推送速度，降低下来，
                // 给定的 n 分钟内，均匀地向这次推送的目标用户推送。最大值为1400.未设置则不是定速推送
                // 这里设置为 1 仅作为示例
                // 'big_push_duration' => 1
            ];   
        }catch(\think\Exception $e){
            
        }
    }
    #推送平台设置
    public function set_platform($platform){
        $this->platform = $platform;
    }
    #推送设备指定
    public function set_audience($audience){
        $this->audience = $audience;
    }
    #消息内容体。是被推送到客户端的内容。与 notification 一起二者必须有其一，可以二者并存
    public function set_message_title($message_title){
        $this->message_title = $message_title;
    }
    #设置消息类型
    public function set_message_type($message_type){
        $this->message_type = $message_type;
    }
    #设置消息简述
    public function set_message_sort_desc($message_sort_desc){
        $this->message_sort_desc = $message_sort_desc;
    }
    
    #设置消息info类型
    public function set_message_info_type($message_info_type){
            $this->message_info_type = $message_info_type;
    }
    #设置消息item值
    public function set_message_info_item($message_info_item){
        $this->message_info_item = $message_info_item;
    }
    #短信渠道补充送达内容体
    public function set_sms_message($sms_message){
        $this->sms_message = $sms_message;
    }
    #推送参数
    public function set_options($options){
       $this->options = $options;
    }
    #用于防止 api 调用端重试造成服务端的重复推送而定义的一个标识符。
    public function set_cid($cid){
        $this->cid = $cid;
    } 
    #设置推送信息的标签
    public function set_tags($tags){
            $this->tags = $tags;
    } 
    #添加推送信息的标签
    public function add_tags($register,$tags){
            $this->client->device()->addTags($register,$tags);
    } 
    #移除推送信息的标签
    public function remove_tags($register,$tags){
            $this->client->device()->removeTags($register,$tags);
    } 
    #设置推送信息的标签
    public function set_registration_id($registration_id){
        $this->registration_id = $registration_id;
    } 
    #为一个标签添加设备
    public function update_device_tag($register,$tags){
        $result = $this->client->device()->isDeviceInTag($register,$tags);
        $exit = $result['body']['result'] ? 'true' : 'false';
        if(!$exit){ //不存在则添加
            $this->client->device()->addDevicesToTag($register,$tags);
        }
        return $result;
    } 
    #为一个标签添加设备
    public function get_tags(){
        return $this->client->device()->getTags();
    }
    #为一个标签移除设备
    public function del_device_tag($register,$tags){
            $result = $this->client->device()->isDeviceInTag($register, $tags);
            $exit = $result['body']['result'] ? 'true' : 'false';
            if($exit){ //不存在则添加
                $this->client->device()->removeDevicesFromTag($tags, $register);
            }
    }
    #单条推送
    private function push($client){
        try {
            #extras拼接
            $extras = [
                'type'  =>$this->message_type,
                'info'  =>[
                    'type'  =>$this->message_type,
                    'item'  =>$this->message_info_item,
                ]
            ];
            $push = $client->push();
            $push->setPlatform($this->platform);
            if($this->tags){
                $push->addTag($this->tags);
            }
            if($this->registration_id){
                $push->addRegistrationId($this->registration_id);
            }
            if(!empty($this->group)){
                $push->addAllAudience();
            }
            $push->setNotificationAlert($this->message_title);
            $push->iosNotification($this->message_title, [
                    // 'sound' => 'sound.caf', //音频文件
                    'badge' => '+1', //角标
                    // 'content-available' => false, //表示推送唤醒
                    // 'mutable-content' => true, //表示通知扩展
                    // 'category' => 'jiguang', //设置 APNs payload 中的 'category' 字段值
                    'extras' => $extras,
            ]);
            $push->androidNotification($this->message_sort_desc, [
                    'alert' =>$this->message_title,
                    'title' => $this->message_title,
                    'builder_id' => 2,
                    'extras' => $extras,
            ]);
            $push->message($this->message_title, [
                    'title' => $this->message_title,
                    // 'content_type' => 'text',
                    'extras' =>$extras
            ]);
            $push->options($this->options);
            // print_r($push);
            // die();
            $result = $push->send();
            return $result; 
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            return $e;
        }
    }
    #单推
    public function sign_push(){
        return $this->push($this->client);
    }
    #标签推送
    public function tag_push(){
        return $this->push($this->client);
    }
}
