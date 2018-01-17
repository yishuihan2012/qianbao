<?php
/**
*  @version 权限组模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-22 16:06
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class AuthGroup extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'ft_auth_group';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'id';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }
      #关联模型 一对多关联 (AuthGroupAccess) 所有属于用户组的管理员
      public function comments()
      {
           return $this->hasMany('AuthGroupAccess','group_id','id');
      }
}
