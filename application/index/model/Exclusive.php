<?php
/**
 * @version 转述二维码列表
 * @author  $bill 755969423@qq.com
 * @time      2017-11-24 09:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class Exclusive extends Model{
	#定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
	#protected $table = 'wt_article';
	#定义主键信息  可留空 默认主键
	protected $pk 	 = 'exclusive_id';
	#定义自动写入时间字段开启 格式为时间格式
	protected $autoWriteTimestamp = 'datetime';
	#定义时间戳字段名 信息添加时间
	protected $createTime = 'exclusive_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = false;
	// protected $updateTime = 'article_update_time';
	#初始化模型
	protected function initialize()
	{
	   #需要调用父类的`initialize`方法
	   parent::initialize();
	   #TODO:自定义的初始化
	}
	/**
	 * [remove 删除专属二维码列表]
	 * @return [type] [description]
	 */
	public static function remove($data){
		$where['exclusive_id'] = $data;
		$info = Db::table("wt_exclusive")->where($where)->find();
		if(Db::table("wt_exclusive")->where($where)->delete()){
			@unlink(".".$info['exclusive_thumb']);
			return true;
		}else{
			return false;
		}
	}
}