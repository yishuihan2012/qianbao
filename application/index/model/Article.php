<?php
/**
*  @version 文章模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-24 09:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class Article extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_article';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'article_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'article_add_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = 'article_update_time';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }
      #关联模型 一对一关联 (ArticleData) 关联文章内容表
      public function articleData()
      {
           return $this->hasOne('ArticleData','data_article','article_id')->withfield('data_article,data_text')->bind('data_article,data_text')->setEagerlyType(0);
      }
      #关联模型 一对一关联 (ArticleCategory) 关联文章分类表
      public function articleCategory()
      {
           return $this->hasOne('ArticleCategory','category_id','article_category')->bind('category_name')->setEagerlyType(0);
      }
}
