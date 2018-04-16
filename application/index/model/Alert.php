<?php
 /**
 * @version Member Model  app升级模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-24 09:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;

class Alert extends Model{
      #定义时间戳字段名 信息添加时间
      protected $autoWriteTimestamp = 'datetime';
      protected $createTime = 'alert_createtime';
     
}