<?php
 /**
 * SmsLog Model / 短信发送记录表
 * @authors John (1414210199@qq.com)
 * @date    2017-10-19 15:50:23
 * @version 信息日志表 Bill$
 */
 namespace app\index\model;
 use think\Db;
 use think\Model;

 class SmsCode extends Model{
     #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
     protected $table = 'wt_sys_code';
     #定义主键信息  可留空 默认主键
     protected $pk     = 'sms_log_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'sms_log_add_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = false;
     #初始化模型
     protected function initialize()
     {
         #需要调用`Model`的`initialize`方法
         parent::initialize();
         #TODO:自定义的初始化
     }

}
