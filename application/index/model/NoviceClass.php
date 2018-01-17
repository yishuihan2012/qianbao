<?php
/**
 * Notice Model / 新手指引分类模型
 * @authors 杨成志 (3115317085@qq.com)
 * @date    2017-09-29 17:14:23
 * @version $订单模型 Bill$
 */
namespace app\index\model;

use think\Db;
use think\Model;

class NoviceClass extends Model
{

    #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
    #protected $table = 'wt_order';

    #定义主键信息  可留空 默认主键
    protected $pk     = 'novice_class_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'novice_class_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = false;
    //初始化模型
    protected function initialize()
    {
           #需要调用`Model`的`initialize`方法
            parent::initialize();
    }
}