<?php
/**
*  @version  SystemBank Model 支行 联行号模型
 * @author  $bill 755969423@qq.com
 * @time      2017-12-04 10:13
 * @return   查找某市区域内的联行号
 */
 namespace app\index\model;
 use think\Db;
 use think\Model;

 class SystemBank extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_system_bank';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'id';
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
