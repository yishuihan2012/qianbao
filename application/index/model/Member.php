<?php
/**
*  @version Member Model  会员基本信息模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-24 09:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class Member extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_member';
      #定义主键信息  可留空 默认主键
      protected $pk    = 'member_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'member_creat_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = 'member_update_time';
      #定义返回数据类型
      protected $resultSetType = 'collection';
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }

      #关联模型 一对一关联 (MemberCertification) 用户实名表
      public function membercertification()
      {
           return $this->hasOne('MemberCertification','certification_member_id','member_id');
      }

      #关联模型 一对一关联 (MemberLogin) 用户登录表
      public function memberLogin()
      {
           return $this->hasOne('MemberLogin','login_member_id','member_id')->bind('login_state')->setEagerlyType(0);
      }

      #关联模型 一对一关联 (MemberSuggestion) 用户反馈表
      public function membersuggestion()
      {
           return $this->belongsTo('MemberSuggestion','suggestion_id','suggestion_member_id');
      }
}
