<?php
/**
*  @version 用户模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-24 09:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class MemberSuggestion extends Model{
      protected $pk    = 'suggestion_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'suggestion_creat_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = false;
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }
      #关联模型 一对一关联 (Member) 关联用户信息
      public function member()
      {
           return $this->hasOne('Member','member_id','suggestion_member_id')->bind('member_nick,member_id,member_image,member_mobile,member_creat_time')->setEagerlyType(0);
      }
}
