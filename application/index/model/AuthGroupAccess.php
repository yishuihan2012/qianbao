<?php
/**
*  @version 权限组角色模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-22 16:06
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class AuthGroupAccess extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'ft_auth_group_access';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'uid';
      #初始化模型
      protected function initialize()
      {
            #需要调用`Model`的`initialize`方法
            parent::initialize();
            #TODO:自定义的初始化
      }
      #关联模型 管理员表 一对一关联
      public function profile()
      {
            return $this->hasOne('Adminster','adminster_id','uid')->field('adminster_id,adminster_login');
      }
      #关联模型 关联权限组 反向关联
      public function auth_group()
      {
            return $this->belongsTo('AuthGroup','id','uid');
      }
}
