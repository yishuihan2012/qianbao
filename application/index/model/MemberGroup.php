<?php
/**
 * MemberGroup Model / 会员用户组模型
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-09-29 17:14:23
 * @version $会员用户组模型 Bill$
 */
namespace app\index\model;

use think\Db;
use think\Model;

class MemberGroup extends Model{

    #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
    #protected $table = 'ft_member_login';

    #定义主键信息  可留空 默认主键
    protected $pk 	 = 'group_id';

    #定义自动写入时间字段开启 格式为时间格式
    protected $autoWriteTimestamp = 'datetime';

    #定义时间戳字段名 信息添加时间
    protected $createTime = 'group_add_time';

    #定义时间戳字段名 信息修改时间
    protected $updateTime = 'group_update_time';
    
    #初始化模型
    protected function initialize()
    {
        #需要调用`Model`的`initialize`方法
        parent::initialize();

        #TODO:自定义的初始化
    }

    #相对的模型关联(Member) 一对一关联
    public function member()
    {
        return $this->hasOne('Member', 'member_group_id', 'group_id');   
    }

    #相对的模型关联(channelrate) 一对一关联
    public function channelrate()
    {
        return $this->hasOne('ChannelRate', 'channel_level', 'group_id');   
    }

    //-------------------------------------------------------

    			#红包权限(packet_type)

    //-------------------------------------------------------
   public function packet()
    {

        return $this->belongsTo('PacketType', 'group_packet', 'type_id')->bind('type_name');
        
    }



    //-------------------------------------------------------

    			#新增会员(member_creat)

    //-------------------------------------------------------




    


}