<?php
/**
*  @version 有效推荐人模型
 * @author  yishuihan 1015571416@qq.com
 * @time      2018-01-08 18:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class MemberRecommend extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_article';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'recommend_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'recommend_time';
      #定义时间戳字段名 信息修改时间
      // protected $updateTime = false;
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }

      #一对一关联用户表
      public function member()
    {
        return $this->hasOne('Member', 'member_id', 'recommend_member_id')->bind('member_mobile');
    }
}
