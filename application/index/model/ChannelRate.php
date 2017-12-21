<?php
/**
 * ChannelRate Model / 等级费率模型
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-12-16 17:14:23
 * @version $会员用户组模型 Bill$
 */
namespace app\index\model;

use think\Db;
use think\Model;

class ChannelRate extends Model{

    #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
    #protected $table = 'ft_member_login';

    #定义主键信息  可留空 默认主键
    protected $pk 	 = 'channel_id';

    #定义自动写入时间字段开启 格式为时间格式
    protected $autoWriteTimestamp = 'datetime';

    #定义时间戳字段名 信息添加时间
    protected $createTime = 'channel_creat_time';

    #定义时间戳字段名 信息修改时间
    protected $updateTime = 'channel_update_time';
    
    #初始化模型
    protected function initialize()
    {
        #需要调用`Model`的`initialize`方法
        parent::initialize();

        #TODO:自定义的初始化
    }

    #相对的模型关联(membergroup) 用户组一对一关联
    public function membergroup()
    {
        return $this->hasOne('MemberGroup', 'group_id', 'channel_level')->bind('group_name,group_id');  
    }

    #相对的模型关联(channeltype) 渠道类型表一对一关联
    public function channeltype()
    {
        return $this->hasOne('ChannelType', 'ct_id', 'channel_type')->bind('ct_name,ct_id,ct_des,ct_img,ct_status')->setEagerlyType(0);  
    }

    


}