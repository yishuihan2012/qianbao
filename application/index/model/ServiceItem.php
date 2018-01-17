<?php
/**
*  @version 自定义模块模型
 * @author  $bill 755969423@qq.com
 * @time      2017-12-14 17:29
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class ServiceItem extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_service_item';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'item_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'item_add_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = false;
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }
      #关联模型 一对一关联 (ArticleData) 关联文章内容表
      public function itemList()
      {
           return $this->hasOne('ServiceItemList','list_item_id','item_id');
      }
      
      #删除模块
      public static function remove($data = 0){
        $where['item_id'] = $data;
        //查询图片地址
        $url = Db::table("wt_service_item")->where($where)->find();
        //图片是多图分割成数组
        //删除数据
        if(Db::table("wt_service_item")->where($where)->delete()){
            @unlink(".".$url['item_icon']);
          return true;
        }else{
          return false;
        }
      }

      public static function info($data){
        $where['item_id'] = $data;
        //查询图片地址
        return $info = Db::table("wt_service_item")->where($where)->find();
      }

      public static function saves($data = ''){
         $where['item_id'] = $data['item_id'];
        //查询图片地址
        $info = Db::table("wt_service_item")->where($where)->find();
        if($data['item_icon']){
          @unlink(".".$info['item_icon']);
        }
        return Db::table("wt_service_item")->where($where)->update($data);
      }
}
