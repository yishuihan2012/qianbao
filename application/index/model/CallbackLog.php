<?php
/**
 * WalletLog Model / 钱包日志模型
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-09-29 17:14:23
 * @version $钱包日志模型 Bill$
 */
namespace app\index\model;

use think\Db;
use think\Model;

class CallbackLog extends Model{

        #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
	//protected $table = 'ft_wallet';

	  protected $pk 	 = 'callback_id';
    #定义自动写入时间字段开启 格式为时间格式
    // protected $autoWriteTimestamp = 'datetime';
    #定义时间戳字段名 信息添加时间
    // protected $createTime = 'callback_time';
    //初始化模型
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

        //TODO:自定义的初始化

    }
}
