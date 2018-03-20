<?php
/**
*  @version 分佣模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-24 09:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class Commission extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_article';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'commission_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'commission_creat_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = false;
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }

      #一对一关联用户表
      public function member()
      {
           return $this->hasOne('Member', 'member_id', 'commission_member_id','','left')->bind('member_nick');
      }
      #一对一关联用户表
      public function members()
      {
           return $this->hasOne('Member', 'member_id', 'commission_childen_member','','left')->field('member_id,member_nick as nick')->bind('nick');
      }
      #一对一关联用户表
      public function cashorder()
      {
           return $this->hasOne('CashOrder', 'order_id', 'commission_from','','left')->bind('order_money');
      }

}
