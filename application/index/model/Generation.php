<?php
/**
*  @version 代还计划模型
 * @author  $bill 755969423@qq.com
 * @time      2017-12-27 15:45
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class Generation extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_generation';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'generation_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'generation_add_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = 'generation_edit_time';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
      }

      #关联模型 一对多关联 (Reimbur) 关联还款卡表
      public function reimbur()
      {
           return $this->hasMany('Reimbur','reimbur_generation','generation_id')->setEagerlyType(0);
      }
       #关联模型 一对多关联 (creditcard) 关联还款卡表
      public function creditcard()
      {
           return $this->hasOne('MemberCreditcard','card_bankno','generation_card')->bind('card_bankicon,card_bankname')->setEagerlyType(0);
      }
      #关联模型 一对多关联 (generation_order) 关联文章分类表
      public function generationOrder()
      {
           return $this->hasOne('GenerationOrder','order_no','generation_id','','left')->join("wt_member m","wt_generation_order.order_member=m.member_id","left")->bind("order_type,order_card,order_money,order_pound,order_status,order_desc,order_time,member_nick,member_mobile");
      }
}
