<?php
 /**
 *  @version MemberCash controller / Api 会员取现
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-19 16:53:05
 *   @return 
 */
 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use app\index\model\Passageway;
 use app\index\model\Member;
 use app\index\model\Wallet;
 use app\index\model\Order;
 use app\index\model\MemberCert;
 use app\index\model\MemberCashout;
 use app\index\model\MemberCreditcard;
 use app\index\model\PassagewayItem;

 use  app\index\controller\CashOut;
 class MemberCash 
 {
      protected $param;
      public $error;
      public function __construct($param)
      {
           $this->param=$param;
                 try{
                 if(!isset($this->param['uid']) || empty($this->param['uid']) || !isset($this->param['token']) ||empty($this->param['token']))
                       $this->error=314;
                 #查找到当前用户
                 $member=Member::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
                 if($member['member_cert']!='1')
                      $this->error=356;
                 if(empty($member))
                       $this->error=314;
                 #查找实名认证信息
                 $member_cert=MemberCert::get(['cert_member_id'=>$member['member_id']]);
                 if(empty($member_cert) && !$this->error )
                      $this->error=356;
                 $this->name=$member_cert['cert_member_name'];
                 $this->idcard=$member_cert['cert_member_idcard'];
            }catch (\Exception $e) {
                 $this->error=317;
           }
      }

      /**
       *  @version cardcash method / Api 信用卡取现
       *  @author $bill$(755969423@qq.com)
       *  @datetime    2017-12-13 09:03:05
       *  @param $member=取现的会员  $token=令牌验证  $cardid=信用卡  $money 取现金额 $passwayid 通道ID
      **/ 
      public function cardcash()
      {
           #获取到用户的信息
           $member=Member::get($this->param['uid']);
           #获取用户实名认证信息
           $member_cert=MemberCert::get(['cert_member_id'=>$this->param['uid']]);
           #获取通道信息
           $passway=Passageway::get($this->param['passwayid']);
           if(empty($passway))
                 return ['code'=>454];
           #判断该通道是否支持提现 并提取出提现费率和提现类TODO:
           if($passway->cashout->cashout_open!='1')
                 return ['code'=>455];
           #判断该笔订单是否小于最小体现额
           if($this->param['money']<$passway->cashout->cashout_min)
                 return ['code'=>456];
           #判断该笔订单是否大于最大体现额
           if($this->param['money']>$passway->cashout->cashout_max)
                 return ['code'=>457];
           #获取用户信用卡信息
          $member_card=MemberCreditcard::get(['card_id'=>$this->param['cardid'],'card_member_id'=>$this->param['uid']]);
           if(empty($member_card))
                return ['code'=>442];
           $method=$passway->cashout->cashout_method;
           // return ['code'=>442,'msg'=>'123','data'=>$method];
           $cashObject=new CashOut($this->param['uid'],$this->param['passwayid'],$this->param['cardid']);
           if ($cashObject->error)
                return ['code'=>$cashObject->error];
              // var_dump($method);die;
           $DaoLong=$cashObject->$method(make_order(),$this->param['money']);
           // var_dump(123);die;
           return $DaoLong;
      }

 }
