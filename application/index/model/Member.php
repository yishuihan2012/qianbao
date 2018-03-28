<?php
 /**
 *  @version Member Model  会员模型
 * @author  $GongKe$  755969423@qq.com
 * @time      2017-11-24 09:20
 */
 namespace app\index\model;
 use think\{Db, Model, Config};
 use think\ErrorException;

 class Member extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_member';
      #定义主键信息  可留空 默认主键
      protected $pk    = 'member_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'member_creat_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = 'member_update_time';
      #定义返回数据类型
      protected $resultSetType = 'collection';

      protected function initialize()
      {
           parent::initialize();
           #TODO:自定义的初始化
      }

       /**
       *  @version getChild method /  实例方法 获取会员的直接下级信息   @datetime    2018-1-17 13:27
       *  @author $GongKe$ (755969423@qq.com) @return  返回会员的下级基本信息 
       */
      public static function getChild(int $memberId) : array
      {
            return self::haswhere('memberRelation',['relation_parent_id'=>$memberId])->column('member_id,member_nick,member_mobile,member_image,member_cert,member_creat_time');
            // return self::haswhere('memberRelation',['relation_parent_id'=>$memberId])->field('member_id,member_nick,member_mobile,member_image,member_cert,member_creat_time')->select();
      }

      #关联模型 一对一关联 (MemberCertification) 用户实名表
      public function membercert()
      {
           return $this->hasOne('MemberCert','cert_member_id','member_id')->bind('cert_member_name,cert_member_idcard,IdPositiveImgUrl,IdNegativeImgUrl,IdPortraitImgUrl');
      }

      #关联模型 一对一关联 (MemberLogin) 用户登录表
      public function memberLogin()
      {
           return $this->hasOne('MemberLogin','login_member_id','member_id')->bind('login_state,login_account,login_token,login_attempts')->setEagerlyType(0);
      }

      #关联模型 一对一关联 (MemberSuggestion) 用户反馈表
      public function membersuggestion()
      {
           return $this->belongsTo('MemberSuggestion','suggestion_id','suggestion_member_id');
      }

      #关联模型 一对一关联 (MemberGroup) 用户等级表
      public function membergroup()
      {
           return $this->hasOne('MemberGroup','group_id','member_group_id')->bind('group_name,group_id,group_salt');
      }

      #关联模型 一对一关联 (MemberRelation) 用户推荐表
      public function memberRelation()
      {
           return $this->hasOne('MemberRelation','relation_member_id','member_id')->bind('relation_member_id,relation_parent_id')->setEagerlyType(0);
      }

      #关联模型 一对一关联 (memberWallet) 用户钱包表
      public function memberWallet()
      {
           return $this->hasOne('Wallet','wallet_member','member_id')->setEagerlyType(0);
      }

      #关联模型 一对多关联 (Upgrade) 用户升级订单表
      public function memberUpgrade()
      {
           return $this->hasMany('Upgrade','upgrade_id','member_id')->bind('upgrade_state,upgrade_update_time');
      }
      
      public function memberCashcard(){
        return $this->hasOne("MemberCashcard","card_member_id","member_id")->bind("card_bankno,card_idcard,card_name");
      } 

      #信用卡
      public function memberCreditcard()
      {
        return $this->hasOne("MemberCreditcard","card_member_id","member_id")->bind("card_id,card_bankno,card_phone");
      } 



      /**
       *  @version member_info method /返回会员信息
       *  @author $bill$(755969423@qq.com)
       *  @datetime    2017-12-13 10:00:05
       *  @param $token 会员token值
      **/
      public static function member_info($token)
      {
           $info = self::with('memberLogin')->where('login_token',$token)->find();
           $data=array(
                 'uid'=>$info['member_id'],
                 'token'=>$info['login_token']
            );
            return $info;
      }

      public function memberLevel()
      {
           return $this->belongsTo('memberLevel','level_member_id','member_id');
      }

      public function recomment()
      {
           return $this->hasOne('Recomment','recomment_member_id','member_id');
      }
      public function membernet()
      {
           return $this->hasOne('MemberNet','net_member_id','member_id');
      }
}
