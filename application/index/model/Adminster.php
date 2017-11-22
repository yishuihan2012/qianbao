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

class Adminster extends Model{
	#定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
	#protected $table = 'wt_adminster';
	#定义主键信息  可留空 默认主键
	protected $pk 	 = 'adminster_id';
    //初始化模型
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
		//获取器
		public static function is_exit($login_name,$login_passwd){

	      $adminster=Db::table('wt_adminster')->where(['adminster_login'=>$login_name])->find();

	      if(!$adminster)
	          return ['code'=>100,'msg'=>'用户名不存在！','data'=>''];

				if($adminster['adminster_state']!=1)
			      return ['code'=>102,'msg'=>'账户状态异常~','data'=>''];

	      if($adminster['adminster_pwd']!=encryption($login_passwd,$adminster['adminster_salt']))
	          return ['code'=>101,'msg'=>'密码错误！','data'=>''];

	      return ['code'=>200,'msg'=>'登录成功！','data'=>$adminster];
	  }
		public function profile()
		{
				return $this->hasOne('AuthGroupAccess','uid','adminster_id');
		}
		public function auth_group_access()
    {
        return $this->belongsTo('AuthGroupAccess','uid','adminster_id');
    }
}
