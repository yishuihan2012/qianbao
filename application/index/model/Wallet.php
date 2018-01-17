<?php
/**
 * Wallet Model / 钱包模型
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-09-29 17:14:23
 * @version $钱包模型 Bill$
 */
namespace app\index\model;

use think\Db;
use think\Model;

class Wallet extends Model{
    #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
    #protected $table = 'wt_wallet';
    #定义主键信息  可留空 默认主键
    protected $pk 	 = 'wallet_id';
    #定义自动写入时间字段开启 格式为时间格式
    protected $autoWriteTimestamp = 'datetime';
    #定义时间戳字段名 信息添加时间
    protected $createTime = 'wallet_add_time';
    #定义时间戳字段名 信息修改时间
    protected $updateTime = 'wallet_update_time';
    #初始化模型
    protected function initialize()
    {
        #需要调用父类的`initialize`方法
        parent::initialize();

        #TODO:自定义的初始化
    }

    #模型关联 关联钱包日志表(WalletLog) 一对多关联
    public function walletLog()
    {
        return $this->hasMany('WalletLog', 'log_wallet_id', 'wallet_id')->order('log_id', 'desc');
    }

    #相对的模型关联(member)
    public function member()
    {
        return $this->belongsTo('Member', 'wallet_member', 'member_id')->bind('member_id,member_nick')->setEagerlyType(0);
    }

   #静态方法 查询会员的钱包 返回array
    public static function getWallet($member_id)
    {
        return self::where('wallet_member', $member_id)->find();
    }

}
