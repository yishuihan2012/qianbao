<?php
/**
*  @version 用户模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-24 09:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class MemberSuggestion extends Model{
      
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }
      #关联模型 一对一关联 (Member) 关联用户信息
      public function member()
      {
           return $this->hasOne('Member','suggestion_member_id','suggestion_id');
      }
}
