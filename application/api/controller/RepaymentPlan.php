<?php
 /**
 *  @version RepaymentPlan controller / Api 创建还款计划
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-27 16:21:05
 *   @return 
 */
 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use app\index\model\Member;
 use app\index\model\MemberCert;


 class RepaymentPlan 
 {
      protected $param;
      public $error;
      private $member;//会员
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
                 $this->member=$member;
            }catch (\Exception $e) {
                 $this->error=317;
           }
      }

      public function creatPlan()
      {
            #判断信用卡是否存在 状态是否正常 是否签约报备
           $money=$this->param['billMoney'];#获取要还款的账单金额
           $cashCount=$this->param['cashCount'];#刷卡消费次数
           $startDate=$this->param['startDate'];#计划执行日
           $endDate=$this->param['endDate'];#计划结束日期
           
      }


 }