<?php
/**
*  @version  MemberLogin Model 会员登录模型
 * @author  $bill 755969423@qq.com
 * @time      2017-12-04 10:13
 * @return  
 */
 namespace app\index\model;
 use think\Db;
 use think\Model;
 use app\index\model\System;

 class MemberLogin extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_member_login';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'login_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'login_create_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = 'login_update_time';
      #定义返回数据类型
      protected $resultSetType = 'collection';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }

      #定义反向关联模型 关联会员表
      public function member()
      {
           return $this->belongsTo('Member', 'login_member_id', 'member_id');
      }

      #静态方法 查询手机号是否注册
      public static function phone_exit($phone)
      {
           $member=self::with('member')->where(['login_account'=>$phone])->find();
           return $member ? $member : false;
      }
      
      #注册事件 监听更改时间 当尝试次数大于系统设置的次数 则锁定lock
      /*protected static function init()
      {
           MemberLogin::event('after_update', function ($memberLogin) {
                 if($memberLogin->login_attempts>=System::getName('is_locking'))
                      return ['code'=>312];
           });
      }*/

      
}
