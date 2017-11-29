<?php
/**
*  @version 实名认证模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-24 09:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class MemberCertification extends Model{

      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }
      #关联模型 一对一关联 (MemberCertification) 用户实名表
      public function member()
      {
           return $this->belongsTo('Member','member_id','certification_member_id');
      }
}
