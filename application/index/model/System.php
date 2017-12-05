<?php
/**
*  @version  System Model 系统配置模型
 * @author  $bill 755969423@qq.com
 * @time      2017-12-04 10:13
 * @return  getName 获取key=$key的值 第二个参数为真时,返回整条数据  setName 设置key=$key的值
 */
namespace app\index\model;
use think\Db;
use think\Model;

class System extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_system';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'system_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'system_creat_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = 'system_update_time';
      #定义返回数据类型
      protected $resultSetType = 'collection';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }

      #获取配置表中 system_key=$key 的值, 默认返回值 第二个参数为真时 返回整条数据
      public static function getName($key, $is=false)
      {
           $array = self::where('system_key', $key)->find();
           return $is ? $array : $array['system_val'];
      }

      #设置配置表中 system_key=$key 的值
      public static function setName($key, $value)
      {
           return self::update(['system_val' => $value], ['system_key' => $key]);
      }

}
