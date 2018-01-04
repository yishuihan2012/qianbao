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
 use app\index\model\MemberGroup;
 use app\index\model\Generation;
 use app\index\model\GenerationOrder;
 use app\index\model\Reimbur;
 use app\index\model\MemberCert as MemberCerts;
 use app\index\model\MemberCreditcard;
 use app\index\model\PassagewayItem;
 use app\index\model\Passageway;
 class RepaymentPlan 
 {
      public $error;
      protected $param;
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
      //创建还款计划
      public function creatPlan()
      {
        // 测试数据
           // $this->param['uid']=16;
           // $this->param['token']=16;
           // $this->param['cardId']=12;
           // $this->param['billMoney']=5000;
           // $this->param['payCount']=12;
           // $this->param['startDate']="2018-01-01";
           // $this->param['endDate']="2018-01-03";
           // $this->param['passageway']=8;
           #获取需要参数
          $member_info=MemberCerts::where('cert_member_id='.$this->param['uid'])->find();
          if(empty($member_info)){
            $this->error=356;
          }
          // print_r($member_info);die;
          $card_info=MemberCreditcard::where('card_id='.$this->param['cardId'])->find();
          if(empty($member_info)){
            $this->error=473;
          }
          #获取后台费率
          $member_group_id=Member::where(['member_id'=>$this->param['uid']])->value('member_group_id');
          $rate=PassagewayItem::where(['item_passageway'=>$this->param['passageway'],'item_group'=>$member_group_id])->find();
           #定义税率  
           $also=($rate->item_also)/100;
           #定义代扣费
           $daikou=($rate->item_charges)*100;
           //$total_money=$this->param['billMoney']+$this->param['billMoney']*$also+$this->param['payCount']*$daikou;
           #定义一个空数组, 用于存放最后的结果集 方便写入数据库
           $data=array();
           #判断总账单是否小于某个值,否则不执行, 比如还款10块20块的 执行没有必要,浪费资源
           if($this->param['billMoney']<100)
                 exit(json_encode(['code'=>111,'msg'=>'还款金额不能低于100元']));
           #总账单除以消费次数得到每次消费AVG平均值  如果平均值小于某个值 则不进行还款  也是浪费资源
           if($this->param['billMoney']/$this->param['payCount'] <50)
                  exit(json_encode(['code'=>111,'msg'=>'单次还款额太小啦,请调整次数到'.intval($this->param['billMoney']/50).'次以下~']));
           //判断卡号是否在计划内
           $plan=Generation::where(['generation_card'=>$card_info->card_bankno,'generation_state'=>1])->find();
           if($plan){
                exit(json_encode(['code'=>111,'msg'=>'此卡已经在还款计划内，请先删除原计划再重新制定计划。']));
           }
           Db::startTrans();
           try
           {

                 #计算开始还款日期到最后还款日期之间的间隔天数
                 $days=days_between_dates($this->param['startDate'],$this->param['endDate'])+1;
                 #取得开始日期与结束日期之间的所有日期 并且打乱顺序
                 $date=prDates($this->param['startDate'],$this->param['endDate']);
                 #如果总还款次数小于日期间隔天数 则随机日期 每天消费一次 并且保证不重复;
                 if($this->param['payCount']<=$days)
                 {
                       #打乱日期顺序
                       shuffle($date);
                       #消费几次就取几个随机日期
                       $randDate=array_slice($date,0,$this->param['payCount']);
                       #循环消费日期 拼接随机的消费小时和分钟 人工消费模拟 早8点-晚7点 24小时制
                       foreach ($randDate as $key => $value) {
                            $data[$key]['time']=$value." ".get_hours().":".get_minites();
                            $data[$key]['endtime']=$value." 20:".get_minites();
                       }
                       //取得每天消费多少钱
                       $result=new \app\api\controller\GetPlan();
                       $res=$result->splitReward($this->param['billMoney'],$this->param['payCount'],$this->param['billMoney']/$this->param['payCount']+100,$this->param['billMoney']/$this->param['payCount']-100);
                       #循环消费数组 关联到日期数组  阙值为0.1元 为保证四舍五入后还可以足够额度
                       sort($data);
                       foreach ($res as $key => $value) {
                            $xiaofei=substr(sprintf("%.2f",(($value/10)+0.1)/(1-$also)+$daikou),0,-1);
                            $data[$key]['xf_money']=$xiaofei;
                            $data[$key]['dz_money']=round($xiaofei-$xiaofei*$also-$daikou,1, PHP_ROUND_HALF_DOWN);
                            $data[$key]['range']=substr(sprintf("%.3f", ($value/10)* $also)+0.01,0,-1);
                            $data[$key]['daikou']=$daikou;
                       }
                       //写入主计划表
                        $Generation_result=new Generation([
                             'generation_no'          =>uniqid(),//TODO 生成随机代号
                             'generation_count'     =>$this->param['payCount'],
                             'generation_member'    =>$this->param['uid'],
                             'generation_card'      =>$card_info->card_bankno,
                             'generation_total'      =>$this->param['billMoney'],
                             'generation_left'        =>$this->param['billMoney'],
                             'generation_pound'   =>$this->param['billMoney']*$also+$rate->item_charges,
                             'generation_start'     =>$this->param['startDate'],
                             'generation_end'      =>$this->param['endDate'],
                        ]);
                        if($Generation_result->save()!==false)
                        {
                             //写入还款卡表
                             $reimbur_result=new Reimbur([
                                   'reimbur_generation'   =>$Generation_result->generation_id,
                                   'reimbur_card'             =>$card_info->card_bankno,
                             ]); 
                             //循环数据 
                             $list=array();
                             $lists=array();
                             // print_r($data);die;
                             for ($i=0; $i <count($data) ; $i++) { 
                             // foreach ($data as $key => $value) {
                                   $list[]=array(
                                        'order_no'       =>$Generation_result->generation_id,
                                        'order_member'   =>$this->param['uid'],
                                        'order_type'     =>1,
                                        'order_card'     =>$card_info->card_bankno,
                                        'order_money'    =>$data[$i]['xf_money'],
                                        'order_pound'    =>$data[$i]['range'],
                                        'order_desc'     =>'自动代还消费~',
                                        'order_time'     =>$data[$i]['time'],
                                        'order_passageway'=>$this->param['passageway'],
                                   );
                                   $lists[]=array(
                                        'order_no'         =>$Generation_result->generation_id,
                                        'order_member'     =>$this->param['uid'],
                                        'order_type'       =>2,
                                        'order_card'       =>$card_info->card_bankno,
                                        'order_money'      =>$data[$i]['dz_money'],
                                        'order_pound'      =>0,
                                        'order_desc'       =>'自动代还还款~',
                                        'order_time'       =>$data[$i]['endtime'],
                                        'order_passageway'=>$this->param['passageway'],
                                   );
                             }
                             // var_dump($lists);die;
                             //写入定时任务表
                             $Generation_order=new GenerationOrder();

                             $order_result=$Generation_order->saveAll($list);

                             $order_result1=$Generation_order->saveAll($lists);

                             if($order_result && $order_result1 && $reimbur_result->save()!==false)
                             { 
                                   Db::commit();

                                   exit(json_encode(['code'=>200, 'msg'=> '计划创建成功~','data'=>['repaymentScheduleId'=>$Generation_result->generation_id,'repaymentScheduleUrl'=>$_SERVER['SERVER_NAME'].'/api/Userurl/repayment_plan_detail/order_no/'.$Generation_result->generation_id]]));
                             }else{
                                   Db::rollback();
                                   return ['code'=>472];      
                             }
                        }
                      
                 }
                 if($this->param['payCount']>$days)
                 {
                       #计算出每天消费几次 总和等于总消费次数
                       $result=$this->get_day_count($this->param['payCount'],$days);
                       #计算出每天总消费金额 再加上手续费
                       $dayM=new \app\api\controller\GetPlan();
                       $dayMoney=$dayM->splitReward($this->param['billMoney'],$days,$this->param['billMoney']/$days*1.3,$this->param['billMoney']/$days*0.7);
                       foreach ($date as $key => $value) {
                            $CurrentMoney=$dayMoney[$key]/10;
                            $CurrentCount=$result[$key];//当天总消费次数
                            $data[$key]['count']=$CurrentCount;
                            $data[$key]['countMoney']=round($CurrentMoney,2);//当天总还款额
                            $data[$key]['endtime']=$value." 20:".get_minites();
                            //计算出平均每天每次需还款多少钱
                            $everyCountMoney=$dayM->splitReward($CurrentMoney,$CurrentCount,$CurrentMoney/$CurrentCount*1.3,$CurrentMoney/$CurrentCount*0.7);
                            foreach ($everyCountMoney as $k => $v) {
                                 $xiaofei=substr(sprintf("%.2f",(($v/10)+0.1)/(1-$also)+$daikou),0,-1);
                                 $data[$key]['list'][$k]['time']=$date[$key]." ".get_hours().":".get_minites();
                                 $data[$key]['list'][$k]['xf_money']=$xiaofei;
                                 $data[$key]['list'][$k]['range']=substr(sprintf("%.3f", ($v/10)* $also),0,-1)+0.01;
                                 $data[$key]['list'][$k]['daikou']=$daikou;
                                 $data[$key]['list'][$k]['dz_money']=round($xiaofei-$xiaofei*$also-$daikou,1, PHP_ROUND_HALF_DOWN);
                            }
                       }
                        //写入主计划表
                        $Generation_result=new Generation([
                             'generation_no'          =>uniqid(),//TODO 生成随机代号
                             'generation_count'     =>$this->param['payCount'],
                             'generation_member'    =>$this->param['uid'],
                             'generation_card'      =>$card_info->card_bankno,
                             'generation_total'      =>$this->param['billMoney'],
                             'generation_left'        =>$this->param['billMoney'],
                             'generation_pound'   =>$this->param['billMoney']*$also+$rate->item_charges,
                             'generation_start'     =>$this->param['startDate'],
                             'generation_end'      =>$this->param['endDate'],
                        ]);
                        if($Generation_result->save()!==false)
                        {
                             //写入还款卡表
                             $reimbur_result=new Reimbur([
                                   'reimbur_generation'   =>$Generation_result->generation_id,
                                   'reimbur_card'             =>$card_info->card_bankno,
                             ]); 
                             //循环数据 
                             $list=array();
                             $lists=array();
                             // print_r($data);die;
                             foreach ($data as $key => $value) {
                                  
                                   $lists[]=array(
                                        'order_no'         =>$Generation_result->generation_id,
                                        'order_member'     =>$this->param['uid'],
                                        'order_type'       =>2,
                                        'order_card'       =>$card_info->card_bankno,
                                        'order_money'      =>$value['countMoney'],
                                        'order_pound'      =>0,
                                        'order_desc'       =>'自动代还还款~',
                                        'order_time'       =>$value['endtime'],
                                        'order_passageway'=>$this->param['passageway'],
                                   );
                                   foreach ($value['list'] as $k => $v) {
                                         $list[]=array(
                                              'order_no'       =>$Generation_result->generation_id,
                                              'order_member'   =>$this->param['uid'],
                                              'order_type'     =>1,
                                              'order_card'     =>$card_info->card_bankno,
                                              'order_money'    =>$v['xf_money'],
                                              'order_pound'    =>$v['range'],
                                              'order_desc'     =>'自动代还消费~',
                                              'order_time'     =>$v['time'],
                                              'order_passageway'=>$this->param['passageway'],
                                         );
                                   }
                             }
                             //写入定时任务表
                             $Generation_order=new GenerationOrder();

                             $order_result=$Generation_order->saveAll($list);

                             $order_result1=$Generation_order->saveAll($lists);

                             if($order_result && $order_result1 && $reimbur_result->save()!==false)
                             { 
                                   Db::commit();

                                   exit(json_encode(['code'=>200, 'msg'=> '计划创建成功~','data'=>['repaymentScheduleId'=>$Generation_result->generation_id,'repaymentScheduleUrl'=>$_SERVER['SERVER_NAME'].'/api/Userurl/repayment_plan_detail/order_no/'.$Generation_result->generation_id]]));
                             }else{
                                   Db::rollback();
                                   return ['code'=>472];      
                             }
                        }
                 }
                 
                  #判断信用卡是否存在 状态是否正常 是否签约报备
                 /*$money=$this->param['billMoney'];#获取要还款的账单金额
                 $payCount=$this->param['payCount'];#刷卡消费次数
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
                 $avg=$this->param['billMoney']/$this->param['payCount'];
                 //取得总共需要多少手续费
                 $total_changr=$this->param['billMoney']*$passway['also']+$this->param['payCount']*$passway['holding'];
                 //计算最低需要多少余额
                 $total_avg=$avg+$total_changr;*/
                 //判断可用余额是否足够这些 如果不够的话 则计划失败

                 //计算余额最低不能小于多少钱 取Avg+手续费
                 //如果可用余额不足 则不进行代还

           } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>308,'msg'=>$e->getMessage()];
           }
          
      }
      //自动执行任务
       public function action_repay_plan(){
          $where['order_status']='1';
          $where['order_time']=array('lt',date('Y-m-d H:i:s',time()));
          $list=GenerationOrder::where($where)->select();
          if($list){
              foreach ($list as $k => $v) {
                $value=$v->toArray();
                if($value['order_type']==1){ //消费
                    $this->payBindCard($value);
                }else if($value['order_type']==2){//提现
                    $this->transferApply($value);
                }
            }
          }
      }
      //7绑卡支付
        //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payBindCard
        public function payBindCard($pay){
          #1获取费率
          $member_group_id=Member::where(['member_id'=>$pay['order_member']])->value('member_group_id');
          $rate=PassagewayItem::where(['item_passageway'=>$pay['order_passageway'],'item_group'=>$member_group_id])->find();
          $also=($rate->item_also)/100;
          $daikou=($rate->item_charges)*100;
          #2获取通道信息
          $merch=Passageway::where(['passageway_id'=>$pay['order_passageway']])->find();
          // print_r($merch->passageway_mech);die;
          #3获取银行卡信息
          $card_info=MemberCreditcard::where(['card_bankno'=>$pay['order_card']])->find();
          #4获取用户信息
          $member=Member::where(['member_id'=>$pay['order_member']])->find();
          print_r($member);die;
          // print_r($pay);die;
          $params=array(
            'mchNo'=>$merch->passageway_mech, //机构号 必填  由平台统一分配 16
            'userNo'=>$pay['ro_userno'],  //平台用户标识  必填  平台下发用户标识  32
            'payCardId'=>$card_info->bindId, //支付卡签约ID 必填  支付签约ID，传入签约返回的平台签约ID  32
            'notifyUrl'=>$_SERVER['SERVER_NAME'].'/Api/Repaymentplan/payCallback',  //异步通知地址  可填  异步回调地址，为空时不起推送  200
            'orderNo'=>uniqid(), //订单流水号 必填  机构订单流水号，需唯一 64
            'orderTime'=>date('YmdHis',time()+60),  //订单时间  必填  格式：yyyyMMddHHmmss 14
            'goodsName'=>'虚拟商品',  //商品名称  必填    50
            'orderDesc'=>C('SYSTEM_TITLE').'米刷信用卡还款', //订单描述  必填    50
            'clientIp'=>$_SERVER['REMOTE_ADDR'],  //终端IP  必填  格式：127.0.0.1  20
            'orderAmt'=>$pay['order_money']*100, //交易金额  必填  单位：分  整型(9,0)
            'feeRatio'=>$also,  //交易费率  必填  需与用户入网信息保持一致  数值(5,2)
            'feeAmt'=>$daikou, //交易单笔手续费   需与用户入网信息保持一致  整型(4,0)
          );  
          print_r($params);die;
          // $params,$mechid,$url,$iv,$secretkey,$signkey,$type=0
          $income=repay_request($params,$merch->passageway_mech,'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payBindCard',$merch->iv,$merch->secretkey,$merch->signkey);
          if($income['code']=='200'){
            $arr['back_tradeNo']=$income['tradeNo'];
            $arr['back_statusDesc']=$income['statusDesc'];
            $arr['back_status']=$income['status'];
            $arr['order_status']='2';
          }else{
            $arr['back_statusDesc']=$income['message'];
            $arr['back_status']='FAIL';
            $arr['order_status']='-1';
          }
          //添加执行记录
          GenerationOrder::where(['order_id'=>$pay['order_id']])->update($arr);
        }
        //8:支付回调
        public function payCallback(){
          $data = file_get_contents("php://input");
             if ($data['code'] == 0) {
                  $datas = AESdecrypt($result['payload'],$this->iv,$this->secretkey);
                  $datas = trim($datas);
                  $datas = substr($datas, 0, strpos($datas, '}') + 1);
                  $resul = json_decode($datas, true);
                  $arr['back_status']=$resul['status'];
                  $arr['back_statusDesc']=$resul['statusDesc'];
                  if($resul['status']=="SUCCESS"){
                    $arr['order_status']='2';
                  }else{
                    $arr['order_status']='-1';
                  }
              }
              GenerationOrder::where(['order_id'=>$pay['order_id']])->update($arr);
              if($resul['status']=="SUCCESS"){
                echo "success";die;
              }
        }
        //9状态查询 unfinished
        //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payResultQuery
        public function payResultQuery(){
          $params=array(
            'mchNo'=>$this->mechid, //机构号 必填  由平台统一分配
            'userNo'=>'123',  //平台用户标识  必填  平台下发用户标识
            'orderNo'=>'',  //订单流水号 必填  机构订单流水号，需唯一
            'tradeNo'=>'',  //平台流水号 必填  绑卡支付返回的流水号
            'tradeDate'=>'',  //交易日期  可填  格式：yyyyMMdd为空时，仅查询仅3日内的交易数据；传入指定日期，可以查询更早前的数据
          );
          $income=$this->repay_request($params,'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payResultQuery');
          var_dump($income);die;
        }
        //10.余额提现
        //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/transferApply
        public function transferApply($get){
          // print_r($get);die;
          $user_merch_info=M('repay_user_merch')->where(['rm_userNo'=>$get['rc_userno']])->find();
          $card_merch_info=M('repay_card_sign')->where(['rs_number'=>$get['rc_settlecardno']])->find();
          $orderTime=date('YmdHis',time()+60);
          $money=$get['rc_depositamt'];
          $params=array(
            'mchNo'=>$this->mechid, //机构号 必填  由平台统一分配 16
            'userNo'=>$card_merch_info['rs_userno'],  //平台用户标识  必填  平台下发用户标识  32
            'settleBindId'=>$card_merch_info['rs_bindid'],  //提现卡签约ID 必填  提现结算的卡，传入签约返回的平台签约ID  32
            'notifyUrl'=>HOST.'/index.php?s=/Api/Repaycredit/cashCallback',// 异步通知地址  可填  异步通知的目标地址,为空时平台不发起推送  200
            'orderNo'=>A('Api/Jyf')->createOrderId(), //提现流水号 必填  机构订单流水号，需唯一 64
            'orderTime'=>$orderTime,//  提现时间点 必填  格式：yyyyMMddHHmmss 14
            'depositAmt'=>(int)$money*100,  //提现金额  必填  单位：分  整型(9,0)
            'feeRatio'=>$user_merch_info['rm_drawfeeratio'],  //提现费率  必填  需与用户入网信息保持一致  数值(5,2)
            'feeAmt'=>$user_merch_info['rm_drawfeeamt'],//提现单笔手续费   需与用户入网信息保持一致  整型(4,0)
          );
          $income=repay_request($params,'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/transferApply');
          if($income['code']=='200'){
            $arr['back_tradeNo']=$income['orderNo'];
            $arr['back_status']=$income['status'];
            $arr['back_statusDesc']=$income['statusDesc'];
            $arr['order_status']='2';
          }else{
            $arr['back_status']='FAIL';
            $arr['back_statusDesc']=$income['message'];
            $arr['order_status']='-1';
            
          }
          M('repay_cash')->where(['rc_id'=>$get['rc_id']])->save($arr);
        }
        //提现回调
        public function cashCallback(){
          $data = file_get_contents("php://input");
              $result = json_decode($data, true);
              if ($result['code'] == 0) {
                 $datas = AESdecrypt($result['payload'],$this->iv,$this->secretkey);
                  $datas = trim($datas);
                  $datas = substr($datas, 0, strpos($datas, '}') + 1);
                  $resul = json_decode($datas, true);
                  $arr['back_status']=$resul['status'];
                  $arr['back_statusDesc']=$resul['statusDesc'];
                  if($resul['status']=="SUCCESS"){
                    $arr['order_status']='2';
                  }else{
                    $arr['order_status']='-1';
                  }
              }
              GenerationOrder::where(['order_id'=>$pay['order_id']])->update($arr);
              if($resul['status']=="SUCCESS"){
                echo "success";die;
              }
        }
        //提现状态查询 unfinished
        //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/transferQuery
        public function transferQuery(){
          $user_merch_info=M('repay_user_merch')->where(['rm_mercUserNo'=>$post['mercUserNo']])->find();
          $params=array(
            'mchNo'=>$this->mechid, //机构号 必填
            'userNo'=>$user_merch_info['rs_userno'],  //平台用户标识  必填
            'orderNo'=>$orderNo,  //订单流水号 必填
            'depositNo'=>$depositNo,  //平台流水号 必填
            'depositDate'=>$depositDate,  //交易日期  可填
          );
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