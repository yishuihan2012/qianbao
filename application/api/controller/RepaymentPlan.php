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
           $this->param['billMoney']=5000;
           $this->param['cashCount']=30;
           $this->param['startDate']="2018-01-01";
           $this->param['endDate']="2018-01-20";
           #定义一个虚拟税率  
           $also="0.0035";
           #定义代扣费
           $daikou=0.5;
           //$total_money=$this->param['billMoney']+$this->param['billMoney']*$also+$this->param['cashCount']*$daikou;
           #定义一个空数组, 用于存放最后的结果集 方便写入数据库
           $data=array();
           #判断总账单是否小于某个值,否则不执行, 比如还款10块20块的 执行没有必要,浪费资源
           if($this->param['billMoney']<100)
                 return ['code'=>470];
           #总账单除以消费次数得到每次消费AVG平均值  如果平均值小于某个值 则不进行还款  也是浪费资源
           if($this->param['billMoney']/$this->param['cashCount'] <50)
                 return ['code'=>471,'msg'=>'单次还款额太小啦,请调整次数到'.intval($this->param['billMoney']/50).'次以下~'];
           #计算开始还款日期到最后还款日期之间的间隔天数
           $days=days_between_dates($this->param['startDate'],$this->param['endDate'])+1;
           #取得开始日期与结束日期之间的所有日期 并且打乱顺序
           $date=prDates($this->param['startDate'],$this->param['endDate']);
           #如果总还款次数小于日期间隔天数 则随机日期 每天消费一次 并且保证不重复;
           if($this->param['cashCount']<=$days)
           {
                 #打乱日期顺序
                 shuffle($date);
                 #消费几次就取几个随机日期
                 $randDate=array_slice($date,0,$this->param['cashCount']);
                 #循环消费日期 拼接随机的消费小时和分钟 人工消费模拟 早8点-晚7点 24小时制
                 foreach ($randDate as $key => $value) {
                      $data[$key]['time']=$value." ".get_hours().":".get_minites();
                      $data[$key]['endtime']=$value." 20:".get_minites();
                 }
                 //取得每天消费多少钱
                 $result=new \app\api\controller\GetPlan();
                 $res=$result->splitReward($this->param['billMoney'],$this->param['cashCount'],$this->param['billMoney']/$this->param['cashCount']+100,$this->param['billMoney']/$this->param['cashCount']-100);
                 #循环消费数组 关联到日期数组  阙值为0.1元 为保证四舍五入后还可以足够额度
                 sort($data);
                 foreach ($res as $key => $value) {
                      $data[$key]['xf_money']=substr(sprintf("%.3f",($value/10)/(1-$also)+$daikou),0,-1)+1;
                      $data[$key]['hk_money']=substr(sprintf("%.2f", ($value/10+0.1)),0,-1);
                      $data[$key]['range']=substr(sprintf("%.3f", ($value/10)* $also),0,-1)+0.01;
                      $data[$key]['daikou']=$daikou;
                 }
                 dump($data);
           }
           if($this->param['cashCount']>$days)
           {
                 $result=$this->get_day_count($this->param['cashCount'],$days);
                 dump($result);
                 exit;
           }
            dump($randDate);
            exit();


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



       /**
       *  @version get_day_count controller / method 获取每天消费几次
       *  @author $bill$(755969423@qq.com)
       *   @datetime    2017-12-27 16:21:05
       *   @return 
       */
      public function get_day_count($num,$day){
           if($day <=$num) {
                 $vs = floor($num / $day);
                 $svgnum = $vs * $day;
                 $surnum = $num - $svgnum;
                 $arr = [];
                 for ($i = 0; $i < $day; $i++) {
                      $arr[$i] = $vs;
                 }
                 for ($i = 0; $i < $surnum; $i++) {
                      $arr[$i]+=1;
                 }
           }else if($day >$num){
                 for ($i=0; $i < $num ; $i++) { 
                      $arr[$i]=1;
                 }
           }
           return $arr;
      }
 }