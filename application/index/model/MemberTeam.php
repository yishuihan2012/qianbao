<?php
 /**
 * MemberTeam Model / 团队表模型
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-12-16 17:14:23
 * @version $会员组表模型 Bill$
 */
 namespace app\index\model;
 use think\Db;
 use think\Model;

 class MemberTeam extends Model{

        #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
        #protected $table = 'ft_member_login';
        #定义主键信息  可留空 默认主键
        protected $pk 	 = 'team_id';
         #定义自动写入时间字段开启 格式为时间格式
        protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
        protected $createTime = 'team_creat_time';
        #定义时间戳字段名 信息修改时间
        protected $updateTime = false;
        #定义返回数据类型
        protected $resultSetType = 'collection';
        #初始化模型
        protected function initialize()
        {
            #需要调用父类的`initialize`方法
            parent::initialize();
            #TODO:自定义的初始化
        }

}
