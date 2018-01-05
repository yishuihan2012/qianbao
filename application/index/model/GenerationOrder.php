<?php
/**
*  @version 代还 => 还款明细订单模型
 * @author  $bill 755969423@qq.com
 * @time      2017-12-27 15:45
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;
use think\Request;
class GenerationOrder extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_generation_order';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'order_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'order_add_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = 'order_edit_time';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
      }
      
      #关联模型 一对一关联 (Generation) 反向关联计划表
      public function generation()
      {
           return $this->belongsTo('Generation','generation_id','order_no')->setEagerlyType(0);
      }
      #获取还款列表
      public static function list(){
          $list = Db::view("GenerationOrder")
            ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
            dump($list);

      }

}
