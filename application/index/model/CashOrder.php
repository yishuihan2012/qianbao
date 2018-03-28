<?php
 /**
 * @version  CashOrder Model 快捷支付订单模型
 * @author  $bill 755969423@qq.com
 * @time      2017-12-20 10:13
 * @return  
 */
 namespace app\index\model;
 use think\Db;
 use think\Model;
 use app\index\model\System;

 class CashOrder extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_member_account';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'order_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'order_add_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = 'order_update_time';
      #定义返回数据类型
      protected $resultSetType = 'collection';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }
      #关联模型 一对一关联 (bankcard) 银行卡
      public function membercreditcard()
      {
           return $this->hasOne('MemberCreditcard','card_bankno','order_creditcard')->bind('card_bankname')->setEagerlyType(0);
      }

      #关联模型 一对一关联 (passageway) 银行卡
      public function passageway()
      {
           return $this->hasOne('Passageway','passageway_id','order_passway')->bind("passageway_name,passageway_no")->setEagerlyType(0);
      }

      #关联模型 一对一关联 (member) 会员模型
      public function member()
      {
           return $this->hasOne('Member','member_id','order_member','','left')->bind("member_nick,member_mobile,member_group_id")->setEagerlyType(0);
      }
       /**
       *  @version getMemberCash method /  实例方法 返回会员的套现总额 订单总额
       *  @author $GongKe$ (755969423@qq.com)  Time  2018-1-17 13:27
       */
      public static function getMemberCash(int $memberId) : float
      {
           return self::where(['order_member'=>$memberId])->sum('order_money');
      }

}
