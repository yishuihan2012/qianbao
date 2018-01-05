<?php
/**
 *  @version MemberWithDraw controller / 会员提现申请 提现管理
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-15 11:57:05
 *   @return 
 */

 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use think\Loader;
 use app\index\model\MemberLogin;
 use app\index\model\System;
 use app\index\model\Member;
 use app\index\model\MemberAccount as MemberAccounts;
 use app\index\model\MemberCert as MemberCerts;
 use app\index\model\MemberCreditcard;
 use app\index\model\MemberCashcard;
 use app\index\model\Wallet;
 use app\index\model\WalletLog;
 use app\index\model\Withdraw as Withdraws;
 use app\index\model\SmsCode as SmsCodes;

 class MemberWithDraw 
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
                 if(!$member && !$this->error)
                       $this->error=350;
            }catch (\Exception $e) {
                 $this->error=317;
           }
      }

      /**
      *  @version withdraw method / Api 会员提现接口 申请提现 目前支持支付宝
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 11:58:05
      *  @param     ☆☆☆::使用中
      **/
      public function withdraw()
      {
           #验证器验证 验证参数合法性
           $validate = Loader::validate('Memberwithdraw');
           #如果验证不通过 返回错误代码 及提示信0息
           if(!$validate->check($this->param))
                 return ['code'=>323, 'msg'=>$validate->getError()];
           #验证会员有没有实名认证 提现需实名才可以
           $member=Member::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
           #查询出收款账号
           $memberAccount=MemberAccounts::get(['account_type'=>$this->param['thirdPartType'],'account_user'=>$this->param['uid']]);
           if(!$memberAccount)
                return ['code'=>370];
           if($member['member_cert']!='1')
                 return ['code'=>366];
           #查找用户实名信息 和计算卡信息 信息不符将驳回提现
           $member_cert=MemberCerts::get(['cert_member_id'=>$member['member_id']]);
           $membercashcard=MemberCashcard::get(['card_member_id'=>$member['member_id']]);
           $memberwallet=Wallet::get(['wallet_member'=>$member['member_id']]);
           if(empty($member_cert) || empty($membercashcard))
                 return ['code'=>367];
           #判断是否满足后台设置的最小提现额 或者该通道的最小提现额
           if($this->param['money']<System::getName('min_withdrawals'))
                 return ['code'=>368];
           #判断用户钱包状态是否正常
           if($memberwallet['wallet_state']!='2')
                 return ['code'=>336];
           #判断用户可提现余额是否足够提现的金额和手续费总额
           if($memberwallet['wallet_amount']<$this->param['money'])
                 return ['code'=>357];
           $charge=0; //默认手续费为0
           $total=0;//总扣款默认值
           $prac=0;//实际扣款
           #判断用户是否需要交纳提现费
           if($this->param['money']>System::getName('min_poundage'))
                 $charge=$this->param['money']*System::getName('poundage');
           #查看余额是否够支付手续费
           $yue=$memberwallet['wallet_amount']-$this->param['money'];
           if($yue>=$charge)
           {
                 $total=$this->param['money']+$charge;
                 $prac=$this->param['money'];
           }else{
                 $total=$memberwallet['wallet_amount'];
                 $prac=$memberwallet['wallet_amount']-$charge;
           }
           $meyyue=$memberwallet['wallet_amount']-$total;
           #是否需要审核？
           $need=$this->param['money']>System::getName('examine') ? '0' : '1';
           Db::startTrans();
           try{
                 #用户钱包信息更改~
                 $memberwallet->wallet_amount=$meyyue;
                 $memberwallet->wallet_total_withdraw=$memberwallet['wallet_total_withdraw']+$total;  
                 #写入提现审核表
                 $withdraws=new Withdraws;
                 $withdraws->withdraw_no = make_order();
                 $withdraws->withdraw_member = $member['member_id'];
                 $withdraws->withdraw_method = $this->param['thirdPartType'];
                 $withdraws->withdraw_name = $member->memberCert->cert_member_name;
                 $withdraws->withdraw_account  = $memberAccount->account_account;//toDo查询出收款账号
                 $withdraws->withdraw_amount = $prac;
                 $withdraws->withdraw_charge = $charge;
                 $withdraws->withdraw_total_money= $total;
                 $return="";
                 #小额提现
                 if($this->param['money']<System::getName('examine'))
                 {
                      $withdraws->withdraw_state  = 12;
                      $withdraws->withdraw_option = 0;
                      $withdraws->withdraw_bak  = "免审核提现";
                      if ($withdraws->save()===false) {
                           Db::rollback();
                           return ['code'=>371];
                      }
                      $payMethod="\app\index\controller\\".$this->param['thirdPartType'];
                      $payment=new $payMethod();
                      $return=$payment->transfer($withdraws); //转账
                      if ($return['code'] != "200") {
                           Db::rollback(); //未保存回滚数据
                           return ['code'=>371,'msg'=>$return['msg']]; //提现失败
                      }
                      $message="您的提现已经处理,请查收~";
                 }else{
                      $withdraws->withdraw_state  = 11;
                      $withdraws->withdraw_bak  = "需审核提现";  //备注信息
                      $withdraws->save();
                      $message="您的提现申请已经提交,等待审核~";
                 }            
                 $content=[];
                 $content['type']=2;
                 $content['item']=$message;
                 #写入提现记录 提现申请 订单表
                 $drawlog=new WalletLog([
                      'log_wallet_id' =>$memberwallet['wallet_id'],
                      'log_wallet_amount'=>$total,
                      'log_wallet_type'    =>2,
                      'log_relation_id'     =>$withdraws->withdraw_id,
                      'log_relation_type' =>2,
                      'log_form'              =>'会员提现',
                      'log_desc'  =>'申请提现: 申请金额:'.$this->param['money']."元,手续费:".$charge."元,实际到账:".$prac."元。"
                 ]);
                 if($drawlog->save()===false || $memberwallet->save()===false)
                 {
                      Db::rollback();
                      return ['code'=>371];
                 }
                 Db::commit();
/*                 if($member->member_device) //非空 
                      message_push('提现申请',$content,$member->member_id,$member->member_device,1);*/
                 return ['code'=>200,'msg'=>$message,'data'=>sprintf("%.2f",substr(sprintf("%.3f", $meyyue), 0, -1))];
           } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>371,'msg'=>$e->getMessage()];
           }
      }


 }
