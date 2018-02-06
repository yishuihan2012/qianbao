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
 use app\index\model\MemberNet;
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
      public function creatPlan(){
           // $this->param['uid']=16;
           // $this->param['token']=16;
           // $this->param['cardId']=18;
           // $this->param['billMoney']=500;
           // $this->param['payCount']=1;
           // $this->param['startDate']="2018-01-06";
           // $this->param['endDate']="2018-01-06";
           // $this->param['passageway']=8;
           $session_name='repayment_data_'.$this->param['uid'];
           #1判断当前通道当前卡用户有没有入网和签约
           // 获取通道信息
           $passageway=Passageway::get($this->param['passageway']);
           // 判断是否入网
           $member_net=MemberNet::where(['net_member_id'=>$this->param['uid']])->find();
           if(!$member_net[$passageway->passageway_no]){ //没有入网
               // 重定向到签约页面
               session::push($session_name,json_encode($this->param));
               return redirect('Userurl/signed', ['passageway' =>$this->param['passageway'],'cardId'=>$this->param['cardId']]);
           }
           //判断是否签约
           $MemberCreditcard=MemberCreditcard::where(['card_id'=>$this->param['cardId']])->find();
           if(!$MemberCreditcard['bindId'] || $MemberCreditcard['bindStatus']!='01'){ //未绑定
                //重定向到签约
                 session::push($session_name,json_encode($this->param));
                 return redirect('Userurl/signed', ['passageway_id' =>$this->param['passageway'],'cardId'=>$this->param['cardId']]);
           }
           #2判断是否存在session
           if($data=session::get($session_name)){
              //获取到session,跳转到creatPlan_mishua方法
              return redirect('RepaymentPlan/creatPlan_mishua',json_decode($data),true);
           }else{
                exit('获取数据失败！');
           }

      }
      //创建计划

      public function creatPlan_mishua(){
        try {
       
          // 测试数据
           // $this->param['uid']=42;
           // $this->param['token']=16;
           // $this->param['cardId']=21;
           // $this->param['billMoney']=200;
           // $this->param['payCount']=1;
           // $this->param['startDate']="2018-01-25";
           // $this->param['endDate']="2018-01-27";
           // $this->param['passageway']=8;
           if($this->param['billMoney']/ $this->param['payCount']<200)
                return['code'=>477];//单笔还款金额太小，请减小还款次数
           #总账单除以消费次数得到每次消费AVG平均值  如果平均值小于某个值 则不进行还款  也是浪费资源
           if($this->param['billMoney']/$this->param['payCount'] >20000)
                  return['code'=>478];//单笔还款金额过大，请增加还款次数

           // $root_id=find_root($this->param['uid']);
           #0 获取参数数据
           if($this->param['endDate']<$this->param['startDate']){
              exit(json_encode(['code'=>111,'msg'=>'还款结束日期不能小于开始日期']));
              return['code'=>474]; //开始日期不能小于今天
           }
           if($this->param['startDate']<date('Y-m-d',time())){
               exit(json_encode(['code'=>111,'msg'=>'开始日期不能小于今天']));
               return ['code'=>475];//开始日期不能小于今天
           }
           // if(date('H',time())>19 && $this->param['startDate']==$this->param['endDate'] ){
           //     return ['code'=>476];//今天已超过还款时间，无法为您制定还款计划
           // }
           if($this->param['startDate']<$this->param['startDate']){
              exit(json_encode(['code'=>111,'msg'=>'还款结束日期不能小于开始日期']));
              return['code'=>474]; //开始日期不能小于今天
           }
           #获取需要参数
          $member_info=MemberCerts::where('cert_member_id='.$this->param['uid'])->find();
          if(empty($member_info)){
                exit(json_encode(['code'=>111,'msg'=>'当前登录已失效，请重新登录']));
                return ['code'=>317];//当前登录已失效，请重新登录
          }
          // print_r($member_info);die;
          #卡详情
          $card_info=MemberCreditcard::where('card_id='.$this->param['cardId'])->find();
          if(!$card_info){
              exit(json_encode(['code'=>111,'msg'=>'未获取到卡号信息']));
              return ['code'=>442];
          }
          #获取后台费率
          $member_group_id=Member::where(['member_id'=>$this->param['uid']])->value('member_group_id');
          $rate=PassagewayItem::where(['item_passageway'=>$this->param['passageway'],'item_group'=>$member_group_id])->find();
           #定义税率  
           $also=($rate->item_also)/100;
           #定义代扣费
           $daikou=($rate->item_charges)/100; 

          #1获取实际还款天数和还款日期
           if($this->param['startDate']==date('Y-m-d',time())){
               return['code'=>485];//开始还款日期必须大于今天
           }

           // if($this->param['startDate']==date('Y-m-d',time()) && date('H',time())>19){
           //    $days=days_between_dates($this->param['startDate'],$this->param['endDate']);
           //    $date=prDates(date('Y-m-d',strtotime($this->param['startDate'])+3600*24),$this->param['endDate']);
           // }else{
           //    $days=days_between_dates($this->param['startDate'],$this->param['endDate'])+1;
           //    $date=prDates($this->param['startDate'],$this->param['endDate']);
           // }
           //  if($days==0){
           //     return['code'=>480];//还款天数太短无法为您安排还款
           // }
           // echo $days;die;
           // print_r($days);die;
          //如果还款次数小于天数
          $days=days_between_dates($this->param['startDate'],$this->param['endDate'])+1;
          $date=prDates($this->param['startDate'],$this->param['endDate']);
          if($this->param['payCount']<$days){
          //       if($this->param['startDate']==date('Y-m-d',time()) && date('H',time())>12){
          //         $days=days_between_dates($this->param['startDate'],$this->param['endDate']);
          //         $date=prDates(date('Y-m-d',strtotime($this->param['startDate'])+3600*24),$this->param['endDate']);
          //       }else{
          //         $days=days_between_dates($this->param['startDate'],$this->param['endDate'])+1;
          //         $date=prDates($this->param['startDate'],$this->param['endDate']);
          //       }
          //      if($days==0){
          //          return['code'=>480];//还款天数太短无法为您安排还款
          //       }
          // }else{
          //      $days=days_between_dates($this->param['startDate'],$this->param['endDate'])+1;
               shuffle($date);
                #消费几次就取几个随机日期
               $date=array_slice($date,0,$this->param['payCount']);
               $days=$this->param['payCount'];
          }
          ########存入主表数据############################
          Db::startTrans();
           $Generation_result=new Generation([
               'generation_no'          =>uniqidNumber(),//TODO 生成随机代号
               'generation_count'     =>$this->param['payCount'],
               'generation_member'    =>$this->param['uid'],
               'generation_card'      =>$card_info->card_bankno,
               'generation_total'      =>$this->param['billMoney'],
               'generation_left'        =>$this->param['billMoney'],
               'generation_pound'   =>$this->param['billMoney']*$also+$daikou,
               'generation_start'     =>$this->param['startDate'],
               'generation_end'      =>$this->param['endDate'],
               'generation_passway_id'=>$this->param['passageway'],
          ]);
          if($Generation_result->save()==false){
              Db::rollback();
              return ['code'=>472]; 
          }
          //写入还款卡表
           $reimbur_result=new Reimbur([
                 'reimbur_generation'   =>$Generation_result->generation_id,
                 'reimbur_card'             =>$card_info->card_bankno,
           ]); 
           if(!$reimbur_result->save()){
                Db::rollback();
                return ['code'=>472]; 
           }
          ####################################
          #3确定每天还款金额
          $day_pay_money=$this->get_random_money($days,$this->param['billMoney'],$is_int=1);
          #4确定每天还款次数
          $day_pay_count=$this->get_day_count($this->param['payCount'],$days);
          #5计算出每天实际刷卡金额，和实际到账金额
          $Generation_order_insert=[];
           $generation_pound = 0;
          for ($i=0; $i <count($date) ; $i++) { 
              $day_real_get_money=0;
              // $plan[$i]['day_date']=$date[$i];
              // $plan[$i]['day_pay_money']=$day_pay_money[$i];
              // $plan[$i]['day_pay_count']=$day_pay_count[$i];
              //刷卡信息
              #计算每次需要刷卡的理论金额
              $each_pay_money=$this->get_random_money($day_pay_count[$i],$day_pay_money[$i],$is_int=1);
              #计算每次刷卡的时间
              $each_pay_time=$this->get_random_time($date[$i],$day_pay_count[$i]);

              foreach ($each_pay_money as $k => $each_money) {
                  //获取每次实际需要支付金额
                  $real_each_pay_money=$this->get_need_pay($also,$daikou,$each_money);
                  //获取每次实际到账金额
                  $real_each_get=$this->get_real_money($also,$daikou,$real_each_pay_money);
                  $plan[$i]['pay'][$k]=$Generation_order_insert[]=array(
                      'order_no'       =>$Generation_result->generation_id,
                      'order_member'   =>$this->param['uid'],
                      'order_type'     =>1,
                      'order_card'     =>$card_info->card_bankno,
                      'order_money'    =>$real_each_pay_money,
                      'order_pound'    =>$real_each_get['fee'],
                      // 'real_each_get'  =>$real_each_get['money'],
                      'order_desc'     =>'自动代还消费~',
                      'order_time'     =>$each_pay_time[$k],
                      'order_passageway'=>$this->param['passageway'],
                      'order_passway_id'=>$this->param['passageway'],
                      'order_platform_no'     =>uniqid(),
                      // 'order_root'=>$root_id,
                  );
                  $generation_pound += $real_each_get['fee'];
                $day_real_get_money+=$real_each_get['money'];
              }
              //提现信息
              $plan[$i]['cash']=$Generation_order_insert[]=array(
                  'order_no'         =>$Generation_result->generation_id,
                  'order_member'     =>$this->param['uid'],
                  'order_type'       =>2,
                  'order_card'       =>$card_info->card_bankno,
                  'order_money'      =>$day_real_get_money,//每天实际打回的金额
                  'order_pound'      =>0,
                  'order_desc'       =>'自动代还还款~',
                  'order_time'       =>$date[$i]." ".get_hours(15,16).":".get_minites(0,59),
                  'order_passageway'=>$this->param['passageway'],
                  'order_passway_id'=>$this->param['passageway'],
                  'order_platform_no'     =>uniqid(),
                  // 'order_root'=>$root_id,
              );

          }
          $Generation = new Generation();
          #修改手续费
          $ss = $Generation->where(['generation_id' => $Generation_result->generation_id])->update(['generation_pound' =>  $generation_pound]);
        
          // print_r($Generation_order_insert);die;
          #写入计划表数据
          $Generation_order=new GenerationOrder();
          $order_result=$Generation_order->saveAll($Generation_order_insert);

         if($order_result!==false)
         { 
               Db::commit();
               exit(json_encode(['code'=>200, 'msg'=> '计划创建成功~','data'=>['repaymentScheduleId'=>$Generation_result->generation_id,'repaymentScheduleUrl'=>$_SERVER['SERVER_NAME'].'/api/Userurl/repayment_plan_detail/order_no/'.$Generation_result->generation_id]]));
         }else{
               Db::rollback();
               return ['code'=>472];      
         }

        } catch (Exception $e) {
            echo  $e->getMessage.$e->getLine().$e->getFile();
        }
    }
      //根据开始时间结束时间随机每天刷卡时间---有问题
      public function get_random_time($day,$count,$begin=9,$end=14){
        //如果日期为今天，刷卡时间大于当前小时
        $now_h=date('Y-m-d',time());
        if($day==$now_h){
           if($now_h<8){
               $begin =9;
           }else{
               $begin=date('H',time())+1;
           }
        }
        $last=$begin;
         $step=floor(($end-$begin)/$count)-1;
         for ($i=0; $i <$count ; $i++) { 
            $time[$i]=$day.' '.get_hours($last,$last+$step).':'.get_minites();
            // $time[$i]['time']=$day.' '.get_hours($last,$last+$step).':'.get_minites();
            // $time[$i]['begin']=$last;
            // $time[$i]['end']=$last+$step;
            $last=$last+$step+1;
         }
         // print_r($time);die;
         return $time;
      }
      //根据还款金额获取需要支付的金额
      //传入单位元，转成分计算，再返回单位元
      public function get_need_pay($rate,$fix,$get){
           //遇到小数向上取整防止金额不够
          $money=ceil(($get*100+$fix*100)/(1-$rate));
          return $money/100;
      }
      //根据支付的金额获取实际到账金额
       //传入单位元，转成分计算，再返回单位元
      public function get_real_money($rate,$fix,$pay){
         //费率向上取整
          $return['fee']=ceil($pay*100*$rate+$fix*100)/100;
          $return['money']=$pay-$return['fee'];
          return $return;
      }
      //根据总金额和次数随机每次金额
      public function get_random_money($num,$money,$is_int=''){
        $count=$num;
        for ($i=0; $i <$num; $i++) { 
          if($i==$num-1){
            $arr[]=$money;
          }else{
            $avage=$money/$count;
            //判断奇偶，
            if($is_int){
              if($i%2==0){//偶数随机在平均值上
                $get=ceil(rand($avage,$avage*1.2));
              }else{//奇数随机在平均值下
                $get=ceil(rand($avage*0.8,$avage));
              }
            }else{
              if($i%2==0){//偶数随机在平均值上
                $get=ceil(rand($avage,$avage*1.2)).'.'.rand(0,99);
              }else{//奇数随机在平均值下
                $get=ceil(rand($avage*0.8,$avage)).'.'.rand(0,99);
              }
            }
            
            $int_num=intval($get);
            if(strlen($int_num)>2){
              $first=substr($int_num,-1,1);
              $second=substr($int_num,-2,1);
              $third=substr($int_num,-3,1);

              if($first==$second &&$first==$third){
                $this->get_random_money($num,$money);
              }
            }
            $count=$count-1;
            $money=$money-$get;
            $arr[]=$get;
          }
        }
        return $arr;
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
      #取消还款计划【整体】
      public function cancel_repayment($generation_id){
        
      }
       //创建还款计划
      public function creatPlan_old()
      {
        // 测试数据
           // $this->param['uid']=16;
           // $this->param['token']=16;
           // $this->param['cardId']=18;
           // $this->param['billMoney']=500;
           // $this->param['payCount']=1;
           // $this->param['startDate']="2018-01-06";
           // $this->param['endDate']="2018-01-06";
           // $this->param['passageway']=8;
           #1判断开始日期和结束日期
           //开始日期不能大于结束日期
           if($this->param['endDate']<$this->param['startDate']){
              exit(json_encode(['code'=>111,'msg'=>'还款结束日期不能小于开始日期']));
              return['code'=>474]; //开始日期不能小于今天
           }
           if($this->param['startDate']<date('Y-m-d',time())){
               return ['code'=>475];//开始日期不能小于今天
           }
           if(date('H',time())>20 && $this->param['startDate']==$this->param['endDate'] ){
               return ['code'=>476];//今天已超过还款时间，无法为您制定还款计划
           }
           #获取需要参数
          $member_info=MemberCerts::where('cert_member_id='.$this->param['uid'])->find();
          if(empty($member_info)){
                return ['code'=>317];//当前登录已失效，请重新登录
          }
          // print_r($member_info);die;
          #卡详情
          $card_info=MemberCreditcard::where('card_id='.$this->param['cardId'])->find();
          if(!$card_info){
              return ['code'=>442];
          }
          #获取后台费率
          $member_group_id=Member::where(['member_id'=>$this->param['uid']])->value('member_group_id');
          $rate=PassagewayItem::where(['item_passageway'=>$this->param['passageway'],'item_group'=>$member_group_id])->find();
           #定义税率  
           $also=($rate->item_also)/100;
           #定义代扣费
           $daikou=($rate->item_charges)/100; 
           //$total_money=$this->param['billMoney']+$this->param['billMoney']*$also+$this->param['payCount']*$daikou;

           #定义一个空数组, 用于存放最后的结果集 方便写入数据库
           $data=array();
           ###还款区间在200-20000之间
           #判断总账单是否小于某个值,否则不执行, 比如还款10块20块的 执行没有必要,浪费资源
           if($this->param['billMoney']/ $this->param['payCount']<200)
                return['code'=>477];//单笔还款金额太小，请减小还款次数
           #总账单除以消费次数得到每次消费AVG平均值  如果平均值小于某个值 则不进行还款  也是浪费资源
           if($this->param['billMoney']/$this->param['payCount'] >20000)
                  return['code'=>478];//单笔还款金额过大，请增加还款次数
           //判断卡号是否在计划内
           $plan=Generation::where(['generation_card'=>$card_info->card_bankno,'generation_state'=>2])->find();
           if($plan){
                //判断当前计划是否执行结束
                $notover=GenerationOrder::where(['order_no'=>$plan['generation_id'],'order_status'=>1])->find();
                if($notover){
                  return['code'=>479];//此卡已经在还款计划内，请先删除原计划再重新制定计划。
                }else{
                  //若没有未执行的则更新主计划表状态为3
                  Generation::update(['generation_id'=>$plan['generation_id'],'generation_state'=>3]);
                }
           }
           Db::startTrans();
           try
           {
                 #计算开始还款日期到最后还款日期之间的间隔天数
                 //如果制定计划时间为当天，且超过晚上8点，从第二天开始执行
                 if($this->param['startDate']==date('Y-m-d',time()) && date('H',time())>19){
                    $days=days_between_dates($this->param['startDate'],$this->param['endDate']);
                    $date=prDates(date('Y-m-d',strtotime($this->param['startDate'])+3600*24),$this->param['endDate']);
                 }else{
                    $days=days_between_dates($this->param['startDate'],$this->param['endDate'])+1;
                    $date=prDates($this->param['startDate'],$this->param['endDate']);
                 }
                  if($days==0){
                     return['code'=>478];//还款天数太短无法为您安排还款
                  }
                 // var_dump($date);die;
                 #取得开始日期与结束日期之间的所有日期 并且打乱顺序
                
                 #如果总还款次数小于日期间隔天数 则随机日期 每天消费一次 并且保证不重复;
                 if($this->param['payCount']<=$days)
                 {
                       #打乱日期顺序
                       shuffle($date);
                       #消费几次就取几个随机日期
                       $randDate=array_slice($date,0,$this->param['payCount']);
                       #循环消费日期 拼接随机的消费小时和分钟 人工消费模拟 早8点-晚7点 24小时制
                       // var_dump($randDate);die;
                       foreach ($randDate as $key => $value) {
                            //如果是今天，则执行时间从下个小时开始.
                            if($value==date('Y-m-d',time())){
                                $data[$key]['time']=$value." ".get_hours(date('H',time()),19).":".get_minites();
                            }else{
                                $data[$key]['time']=$value." ".get_hours().":".get_minites();
                            }
                            $data[$key]['endtime']=$value." 20:".get_minites(1,30);
                       }
                       //取得每天消费多少钱
                       $result=new \app\api\controller\GetPlan();
                       $res=$result->splitReward($this->param['billMoney'],$this->param['payCount'],$this->param['billMoney']/$this->param['payCount']+100,$this->param['billMoney']/$this->param['payCount']-100);
                       $res1=$result->get_random_money($this->param['billMoney'],$this->param['payCount'],1);
                       #循环消费数组 关联到日期数组  阙值为0.1元 为保证四舍五入后还可以足够额度
                       sort($data);
                       foreach ($res as $key => $value) {
                            $xiaofei=substr(sprintf("%.2f",(($value/10)+0.1)/(1-$also)+$daikou),0,-1);
                            $data[$key]['xf_money']=$xiaofei;
                            $data[$key]['dz_money']=round($xiaofei-$xiaofei*$also-$daikou,1, PHP_ROUND_HALF_DOWN);
                            $data[$key]['range']=substr(sprintf("%.3f", ($value/10)* $also)+$daikou,0,-1);
                            $data[$key]['daikou']=$daikou;
                       }
                       // print_r($data);die;
                       //写入主计划表
                        $Generation_result=new Generation([
                             'generation_no'          =>uniqidNumber(),//TODO 生成随机代号
                             'generation_count'     =>$this->param['payCount'],
                             'generation_member'    =>$this->param['uid'],
                             'generation_card'      =>$card_info->card_bankno,
                             'generation_total'      =>$this->param['billMoney'],
                             'generation_left'        =>$this->param['billMoney'],
                             'generation_pound'   =>$this->param['billMoney']*$also+$rate->item_charges,
                             'generation_start'     =>$this->param['startDate'],
                             'generation_end'      =>$this->param['endDate'],
                             'generation_passway_id'=>$this->param['passageway'],
                        ]);
                        // print_r($data);die;
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
                             // $root_id=find_root($this->param['uid']);
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
                                        'order_passway_id'=>$this->param['passageway'],
                                        // 'order_root'=>$root_id,
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
                                        'order_passway_id'=>$this->param['passageway'],
                                        // 'order_root'=>$root_id,
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
                       if($this->param['startDate']==date('Y-m-d',time()) && date('H',time())>12){
                          $days=days_between_dates($this->param['startDate'],$this->param['endDate']);
                          $date=prDates(date('Y-m-d',strtotime($this->param['startDate'])+3600*24),$this->param['endDate']);

                       }else{
                          $days=days_between_dates($this->param['startDate'],$this->param['endDate'])+1;
                          $date=prDates($this->param['startDate'],$this->param['endDate']);
                       }
                       if($days==0){
                           return['code'=>478];//还款天数太短无法为您安排还款
                        }
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
                            $data[$key]['endtime']=$value." 20:".get_minites(1,30);
                            //计算出平均每天每次需还款多少钱
                            $everyCountMoney=$dayM->splitReward($CurrentMoney,$CurrentCount,$CurrentMoney/$CurrentCount*1.3,$CurrentMoney/$CurrentCount*0.7);
                            foreach ($everyCountMoney as $k => $v) {
                                 $xiaofei=substr(sprintf("%.2f",(($v/10)+0.1)/(1-$also)+$daikou),0,-1);
                                 $data[$key]['list'][$k]['time']=$date[$key]." ".get_hours().":".get_minites();
                                 $data[$key]['list'][$k]['xf_money']=$xiaofei;
                                 $data[$key]['list'][$k]['range']=substr(sprintf("%.3f", ($v/10)* $also),0,-1)+$daikou;
                                 $data[$key]['list'][$k]['daikou']=$daikou;
                                 $data[$key]['list'][$k]['dz_money']=round($xiaofei-$xiaofei*$also-$daikou,1, PHP_ROUND_HALF_DOWN);
                            }
                       }
                       // print_r($data);die;
                        //写入主计划表
                        $Generation_result=new Generation([
                             'generation_no'          =>uniqidNumber(),//TODO 生成随机代号
                             'generation_count'     =>$this->param['payCount'],
                             'generation_member'    =>$this->param['uid'],
                             'generation_card'      =>$card_info->card_bankno,
                             'generation_total'      =>$this->param['billMoney'],
                             'generation_left'        =>$this->param['billMoney'],
                             'generation_pound'   =>$this->param['billMoney']*$also+$rate->item_charges,
                             'generation_start'     =>$this->param['startDate'],
                             'generation_end'      =>$this->param['endDate'],
                             'generation_passway_id'=>$this->param['passageway'],
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
                                        'order_passway_id'=>$this->param['passageway'],
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
                                              'order_passway_id'=>$this->param['passageway'],
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
                                   return ['code'=>200, 'msg'=> '计划创建成功~','data'=>['repaymentScheduleId'=>$Generation_result->generation_id,'repaymentScheduleUrl'=>$_SERVER['SERVER_NAME'].'/api/Userurl/repayment_plan_detail/order_no/'.$Generation_result->generation_id]];
                             }else{
                                   Db::rollback();
                                   return ['code'=>472];      
                             }
                        }else{
                             Db::rollback();
                             return ['code'=>472];      
                        }
                 }

           } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>308,'msg'=>$e->getMessage(),'data'=>[]];
           }
          
      }
 }