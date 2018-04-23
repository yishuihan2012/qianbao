<?php
 /**
 * @version Member Model  通道绑卡收费记录
 * @author  $bill 755969423@qq.com
 * @time      2017-11-24 09:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;

class PassagewayBind extends Model{
      #定义时间戳字段名 信息添加时间
      protected $autoWriteTimestamp = 'datetime';
      protected $createTime = 'bind_createtime';
     
}