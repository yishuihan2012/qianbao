<?php
/**
 * RegionProvince Model / 省份管理
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-09-29 17:14:23
 * @version $省份模型 Bill$
 */
namespace app\index\model;

use think\Db;
use think\Model;
use think\Config;

class RegionProvince extends Model{

	#定义主键信息  可留空 默认主键
	protected $pk 	 = 'id';
    //初始化模型
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }

    //关联市
    public function comments()
    {
        return $this->hasMany('RegionCity','pro_id','id');
    }
}
