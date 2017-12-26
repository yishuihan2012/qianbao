<?php
 /**
 * @version  MemberCas Model 用户提现列表
 * @author  $bill 755969423@qq.com
 * @time      2017-12-04 10:13
 * @return  
 */
 namespace app\index\model;
 use think\Db;
 use think\Model;
 use app\index\model\System;

 class MemberCash extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_member_account';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'cash_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'cash_create_at';
      #定义返回数据类型
      protected $resultSetType = 'collection';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }


}
