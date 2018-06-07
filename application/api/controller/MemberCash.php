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
           #判断当前时间是否在交易时间范围内
           if(date('H:i:s')<$passway->cashout->cashout_begintime || date('H:i:s')>$passway->cashout->cashout_endtime)
                 return ['code'=>457,'msg'=>'该通道交易时间段为'.$passway->cashout->cashout_begintime.' - '.$passway->cashout->cashout_endtime];

           #获取用户信用卡信息
          $member_card=MemberCreditcard::get(['card_id'=>$this->param['cardid'],'card_member_id'=>$this->param['uid']]);
          // dump($passway);die;
          // if($passway['passageway_true_name'] == 'YilPayjf'){
              $where['passageway_true_name'] = $passway['passageway_true_name'];
              $where['card_name'] = array("like","%".mb_substr($member_card['card_bankname'],0,2)."%");
              $money = $this->bank_limit($where);
              if(!$money)
                 return ['code' => 450 , 'msg' => "此通道不支持该银行取现"];
              if($this->param['money'] < $money['bank_single'])
                 return ['code' => 458 , 'msg' => "取现金额不能小于该银行的最小金额".$money['bank_single']."元"];
              if($this->param['money'] > $money['bank_one_day'])
                 return ['code' => 459 , 'msg' => "取现金额不能大于该银行的最大金额".$money['bank_one_day']."元"];
          // }
               // die;
           if(empty($member_card))
                return ['code'=>442];
           $method=$passway->cashout->cashout_method;
           // return ['code'=>442,'msg'=>'123','data'=>$method];
           $cashObject=new CashOut($this->param['uid'],$this->param['passwayid'],$this->param['cardid']);
           if ($cashObject->error)
                return ['code'=>$cashObject->error];
              // var_dump($method);die;
           $DaoLong=$cashObject->$method(get_plantform_pinyin().$member->member_mobile.make_rand_code(),$this->param['money']);
           if($DaoLong['code']==200){
                return ["code" => 200, "msg" =>"请求成功" , "data" =>$DaoLong['data'] ];
           }
           return $DaoLong;
      }
      #获取银行的限额
      public function bank_limit($where){
              $credit_card = db("CreditCard")->where($where)->find();
              if(empty($credit_card)){
                return false;
              }
              $arr = array();
              if($credit_card['bank_attrbute'] == '百'){
                $arr['bank_single'] = $credit_card['bank_single']*100;
                $arr['bank_one_day'] = $credit_card['bank_one_day']*100;
              }elseif($credit_card['bank_attrbute'] == "千"){
                $arr['bank_single'] = $credit_card['bank_single'] * 1000;
                $arr['bank_one_day'] = $credit_card['bank_one_day'] *1000;
              }else{
                $arr['bank_single'] = $credit_card['bank_single'] * 10000;
                $arr['bank_one_day'] = $credit_card['bank_one_day'] *10000;
              }
              return $arr;
      }

 }
