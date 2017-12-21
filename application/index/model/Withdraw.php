<?php
/**
 * Withdraw Model / 提现模型
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-09-29 17:14:23
 * @version $提现模型 Bill$
 */
namespace app\index\model;

use think\Db;
use think\Model;

class Withdraw extends Model{

	#定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
	//protected $table = 'ft_withdraw';

	#定义主键信息  可留空 默认主键
	protected $pk 	 = 'withdraw_id';    

    //初始化模型
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

        //TODO:自定义的初始化

    }
  

    //-------------------------------------------------------
                #关联模型 关联管理员(adminster)
    //-------------------------------------------------------
    public function adminster()
    {
        return $this->hasOne('Adminster','adminster_id','withdraw_option');
    }
    //关联会员
    public function member()
    {
        return $this->hasOne('Member','member_id','withdraw_member');
    }
    public function wallet()
    {
        return $this->hasOne('Wallet','wallet_member','withdraw_member');
    }

}