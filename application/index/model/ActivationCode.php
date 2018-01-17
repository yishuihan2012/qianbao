<?php
/**
*  @version 管理员模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-22 16:06
 * @return  is_exit 用户是否存在 (@param $login_name 用户名,$login_passwd 密码)
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class ActivationCode extends Model{
	 #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
	 protected $table = 'wt_activation_code';
	 #定义主键信息  可留空 默认主键
	 protected $pk 	 = 'activation_code_id';
      #初始化模型
      protected function initialize()
      {
       	 #需要调用父类的`initialize`方法
        	 parent::initialize();
        	 #TODO:自定义的初始化
      }
	 #关联模型
	 public  function adminster()
	 {
	 	 return $this->hasOne('Adminster','adminster_id','activation_code_for')->bind('group_id')->setEagerlyType(0);
	 }
	#关联模型
	public  function groups()
	 {
	 	 return $this->hasOne('MemberGroup','group_id','activation_code_level')->bind('group_id')->setEagerlyType(0);
	 }
}
