<?php
/**
 * Order Model / 订单模型
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-09-29 17:14:23
 * @version $订单模型 Bill$
 */
namespace app\index\model;

use think\Db;
use think\Model;

class Order extends Model
{

    #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
    #protected $table = 'wt_order';

    #定义主键信息  可留空 默认主键
    protected $pk     = 'order_id';

    //初始化模型
    protected function initialize()
    {
           #需要调用`Model`的`initialize`方法
            parent::initialize();
    }
    
    public function member()
    {
        return $this->hasOne('Member', 'member_id', 'order_member', '', 'left')->bind('member_nick,member_id,member_image,member_mobile,member_creat_time')->setEagerlyType(0);
    }
   
    public function MemberCash()
    {
        return $this->belongsToMany('MemberCash');
    }
}
