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


 class Repaymentplan 
 {
      public $error;
      protected $param;
      private $member;//会员
     /* public function __construct($param)
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
      }*/

      public function creatPlan()
      {
           $result=new \app\api\controller\GetPlan();
           $res=$result->splitReward('5000','10','600','300');
           dump($res);
            #判断信用卡是否存在 状态是否正常 是否签约报备
           /*$money=$this->param['billMoney'];#获取要还款的账单金额
           $cashCount=$this->param['cashCount'];#刷卡消费次数
           $startDate=$this->param['startDate'];#计划执行日
           $endDate=$this->param['endDate'];#计划结束日期

           #获取通道信息
           $passway=PassageWay($this->param['passwayId']);
           #判断该通道是否可以代还  如果可以的话 查询出该代还通道的费率和代扣费
           if($passway->passageway_also!='1' || $passway->passageway_state!='1')
                 return ['code'=>496];
           //判断是否必须入网才可以进行代还设置 并且检查会员是否入网TODO:

           //取到该通道的税率和代扣费
           $passway['also']=0.0035; //税率 需在后台读取 TODO
           $passway['holding']=3; //固定值 需在后台取 TODO
           //计算平均每次需要还款多少钱 取AVG平均值 
           $avg=$this->param['billMoney']/$this->param['cashCount'];
           //取得总共需要多少手续费
           $total_changr=$this->param['billMoney']*$passway['also']+$this->param['cashCount']*$passway['holding'];
           //计算最低需要多少余额
           $total_avg=$avg+$total_changr;*/
           //判断可用余额是否足够这些 如果不够的话 则计划失败

           //计算余额最低不能小于多少钱 取Avg+手续费
           //如果可用余额不足 则不进行代还


           
      }


 }