<?php
/**
 * Admin Model / 后台管理员模型
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-09-29 17:14:23
 * @version $管理员模型 Bill$
 */
namespace app\index\model;

use think\Db;
use think\Model;
use think\Config;

class AuthGroupAccess extends Model{
	#定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
	protected $table = 'ft_auth_group_access';
	#定义主键信息  可留空 默认主键
	protected $pk 	 = 'uid';
    //初始化模型
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
    public function profile()
    {
        return $this->hasOne('Adminster','adminster_id','uid')->field('adminster_id,adminster_login');
    }
    public function auth_group()
    {
        return $this->belongsTo('AuthGroup','id','uid');
    }
}
