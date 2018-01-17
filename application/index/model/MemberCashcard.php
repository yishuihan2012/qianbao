<?php
 /**
 * @version  MemberCascard Model 会员结算卡 储蓄卡模型
 * @author  $bill 755969423@qq.com
 * @time      2017-12-04 10:13
 * @return  
 */
 namespace app\index\model;
 use think\Db;
 use think\Model;
 use app\index\model\System;

 class MemberCashcard extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_member_account';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'card_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'card_add_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = 'card_update_time';
      #定义返回数据类型
      protected $resultSetType = 'collection';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }

      #关联模型 一对一关联 (province) 省表
      public function province()
      {
           return $this->hasOne('RegionProvince','id','card_bank_province')->bind('Name');
      }
      #关联模型 一对一关联 (city) 市
      public function city()
      {
           return $this->hasOne('RegionCity','id','card_bank_city')->bind('Name');
      }

      #关联模型 一对一关联 (area) 区
      public function area()
      {
           return $this->hasOne('RegionArea','id','card_bank_area')->bind('name');
      }


}
