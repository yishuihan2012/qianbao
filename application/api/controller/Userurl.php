<?php

namespace app\api\controller;
use think\Db;
use think\Config;
use think\Loader;
use think\Request;
use think\Controller;
use app\index\model\CustomerService;
use app\api\controller as con;
use app\index\model\Share;
use app\index\model\Page;
use app\index\model\Generalize;
use app\index\model\Member as Members;
use app\index\model\MemberCash;
use app\index\model\Withdraw;
use app\index\model\CashOrder;
use app\index\model\Exclusive;
use app\index\model\Recomment;
use app\index\model\Announcement;
use app\index\model\Notice;
use app\index\model\MemberNovice; 
use app\index\model\Passageway; 
use app\index\model\PassagewayItem; 
use app\index\model\MemberGroup; 
use app\index\model\MemberRelation; 
use app\index\model\CreditCard;
use app\index\model\MemberCreditcard;
use app\index\model\MemberCashcard;
use app\index\model\Generation;
use app\index\model\GenerationOrder;
use app\index\model\System;
use app\index\model\NoviceClass as NoviceClasss; 
use app\index\model\MemberCert;
use app\index\model\MemberNet;
use app\index\model\Reimbur;
use app\index\model\Appversion; 
use app\index\model\Commission as Commissions; 
use app\index\model\SmsCode; 
use app\index\model\ArticleCategory;
use app\index\model\Article;
use app\index\model\WalletLog;
use app\api\controller\Huilianjinchuang;
use app\index\model\ServiceItemList;
use app\index\model\MemberCreditPas;
/**
 *  此处放置一些固定的web地址
 */
class Userurl extends Controller
{
      protected $param;
      public $error=0;
      
      //验证token
      protected function checkToken(){
       $this->param=request()->param();
        try{
             if(!isset($this->param['uid']) || empty($this->param['uid']) || !isset($this->param['token']) ||empty($this->param['token'])){
                echo '<li style="margin-top:13rem;text-align:center;list-style:none;font-size:1.4rem;color:#999;">当前登录已过期，请重新登录</li>';die;
             }
                 
                   // $this->error=314;
             #查找到当前用户
             $member=Members::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
             if(!$member && !$this->error){
                echo '<li style="margin-top:13rem;text-align:center;list-style:none;font-size:1.4rem;color:#999;">当前登录已过期，请重新登录</li>';die;
             }
        }catch (\Exception $e) {
            echo '<li style="margin-top:13rem;text-align:center;list-style:none;font-size:1.4rem;color:#999;">当前登录已过期，请重新登录</li>';die;
              $this->assign('msg','当前登录已过期，请重新登录');
             // $this->error=317;
        }
        if($this->error){
            $msg=Config::get('response.'.$this->error) ? Config::get('response.'.$this->error) : "系统错误~";
                echo "<li style='margin-top:13rem;text-align:center;list-style:none;font-size:1.4rem;color:#999;'>{$msg}}</li>";die;
            // exit(json_encode(['code'=>$this->error, 'msg'=>$msg, 'data'=>[]]));
        }
        $this->assign('uid',$this->param['uid']);
        $this->assign('token',$this->param['token']);
      }

      #专属二维码列表
    public function exclusive_code(){
        $this->assign("name",System::getName("sitename"));
        $list = Exclusive::all();
        $this->assign("list",$list);
        return view("api/logic/share_code_list");
    }
    #取现现成功页面
    public function calllback_success(){
        // $request = $this->param;
    $request=file_get_contents("php://input");
    if(empty($request)){
       $data['result'] = -1;
      $this->assign('data',$data);
      return view("Userurl/calllback_success");
    }
        $data    = CashOrder::where(['order_thead_no' => $request['transNo']])->find();
        if ($request['status'] == '00') {
            $data['order_card']        = substr($data['order_card'], -4);
            $data['order_money'] = number_format($data['order_money'], 2);
            $data['result']           = 1;
        } elseif(empty($request)) {
            $data['result'] = -1;
        }elseif(!empty($request) && $request['status'] != '00'){
            $data['result'] = 0;
        }

      $this->assign('data',$data);
        return view("Userurl/calllback_success");
    }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:01:55+0800
     * @version  [专属二维码]
     * @return   [type]
     */
    public function exclusive_code_detail(){
        $this->checkToken();
        //获取当前手机号
        $tel=Members::get($this->param['uid']);
        $tel=$tel->member_mobile;
        //推广连接
        $url='http://'.$_SERVER['HTTP_HOST'].'/api/userurl/register/recomment/'.$tel;
        //背景图片ID
        $exclusive_id=$this->param['exclusive_id'];
        //调取数据库url
        $img=db('qrcode')->where(['qrcode_member_id'=>$this->param['uid'],'qrcode_exclusive_id'=>$exclusive_id])->find();
            // dump($img);die;
        if(!$img || !$img['qrcode_url']){
            $imgurl='autoimg/qrcode_'.$exclusive_id.'_'.$tel.'.png';
            //若未生成过
            if(!is_file($imgurl)){
                Vendor('phpqrcode.phpqrcode');
                $QRcode=new \QRcode();
                //生成二维码
                $QRcode->png($url, 'autoimg/qrcode'.$tel.'.png','H',8,5);
                $qrurl=ROOT_PATH.'public/autoimg/qrcode'.$tel.'.png';
                $logourl=ROOT_PATH.'public/static/images/logo.png';
                // 二维码加入logo
                 $QR = imagecreatefromstring(file_get_contents($qrurl)); 
                 $logo = imagecreatefromstring(file_get_contents($logourl)); 
                 $logo_width = imagesx($logo);
                 $logo_height = imagesy($logo);
                 #动态计算取中心点 让你丫不居中
                 $qr_width = imagesx($QR);
                 $scale=0.18;
                 $logo_line=$scale*$qr_width;
                 $xy=$qr_width*0.5-$logo_line*0.5;
                 imagecopyresampled( $QR,$logo, $xy, $xy, 0, 0, $logo_line, $logo_line, $logo_width, $logo_height); 
                imagepng($QR, 'autoimg/qrcode'.$tel.'.png'); 
                // 背景
                $bg_url=Exclusive::get($exclusive_id);
                $bg_url=$bg_url->exclusive_thumb;
                $bg_url=preg_replace('/\\\\/', '/', $bg_url);
                $bg_url=ROOT_PATH.'public'.$bg_url;
                // $bg=ROOT_PATH.'public\static\images\exclusice_code_bg.png';
                //合成专属二维码
                 $bg = imagecreatefromstring(file_get_contents($bg_url)); 
                 $QR_width = imagesx($QR);//二维码图片宽度 
                 $QR_height = imagesy($QR);//二维码图片高度 
                 imagecopyresampled( $bg,$QR, 240, 710, 0, 0, 280, 280, $QR_width, $QR_height); 
                imagejpeg($bg, $imgurl,65); 
            }
            if(!$img){
                db('qrcode')->insert(['qrcode_member_id'=>$this->param['uid'],'qrcode_exclusive_id'=>$exclusive_id,'qrcode_url'=>$imgurl]);
            }else{
                db('qrcode')->where(['qrcode_member_id'=>$this->param['uid'],'qrcode_exclusive_id'=>$exclusive_id])->update(['qrcode_url'=>$imgurl]);
            }
        }else{
            $imgurl=$img['qrcode_url'];
        }
        //返回图片地址
        $url='http://'.$_SERVER['HTTP_HOST'].'/'.$imgurl;
        $this->assign('url',$url);
        return view("Userurl/exclusive_code_detail");
    }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [用户还款计划]
     * @return   [type]
     */

    public function repayment_plan_list(){
        $this->checkToken();
        #全部
        $order=GenerationOrder::where(['order_member'=>$this->param['uid']])->select();

        #已执行
        $order2=GenerationOrder::where(['order_member'=>$this->param['uid'],'order_status'=>2])->select();

        #未执行
        $order1=GenerationOrder::where(['order_member'=>$this->param['uid'],'order_status'=>1])->select();
        
        $this->assign('order',$order);
        $this->assign('order2',$order2);
        $this->assign('order1',$order1);
        return view("Userurl/repayment_plan_list");
    }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [还款计划已完成列表]
     * @return   [type]
     */
    public function repayment_history(){
        $this->checkToken();
        #进行中
        // $this->param['uid']=16;
        $generation=Generation::with('creditcard')->where(['generation_member'=>$this->param['uid'],'generation_state'=>2])->order('generation_add_time','desc')->select();
        foreach ($generation as $key => $value) {
            //判断自动执行表 是否全部完成执行 取未执行的计划
            $haventDone=GenerationOrder::where(['order_no'=>$value['generation_id'],'order_status'=>1])->find();
            if(!$haventDone){
                //若全部完成执行 更改主表计划执行状态
                Generation::where(['generation_member'=>$this->param['uid'],'generation_id'=>$value['generation_id']])->update(['generation_state'=>3]);
                unset($generation['$key']);
                continue;
            }else{
                $generation[$key]['generation_card']=substr($value['generation_card'], -4);
                $generation[$key]['count']=GenerationOrder::where(['order_no'=>$value['generation_id']])->count();
            }
        }

        #待确认 不需要了
        // $generation1=Generation::with('creditcard')->where(['generation_member'=>$this->param['uid'],'generation_state'=>1])->select();
        // foreach ($generation1 as $key => $value) {
        //      $generation1[$key]['generation_card']=substr($value['generation_card'], -4);
        //      $generation1[$key]['count']=GenerationOrder::where(['order_no'=>$value['generation_id']])->count();
        // }

        #完成
        $generation3=Generation::with('creditcard')->where(['generation_member'=>$this->param['uid'],'generation_state'=>['in','3,4']])->order('generation_add_time','desc')->select();
        foreach ($generation3 as $key => $value) {
                $generation3[$key]['generation_card']=substr($value['generation_card'], -4);
                $generation3[$key]['count']=GenerationOrder::where(['order_no'=>$value['generation_id']])->count();
        }
        // var_dump($generation);die;
        $this->assign('generation',$generation);
        $this->assign('generation3',$generation3);
        return view("Userurl/repayment_history");
    }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [还款计划创建 #1]
     * @version  [还款计划创建下一步后显示的详情页]
     * @return   [type]
     */
    public function repayment_plan_create_detail(){
        // $this->checkToken();
        // $order_no=$this->param['order_no'];
        $order_no=input('order_no');
        $data=explode('_', $order_no);
        // print_r($data);die;
        $this->param['uid']=$param['uid']=$data[0];
        $this->param['cardId']=$param['cardId']=$data[1];
        $this->param['billMoney']=$param['billMoney']=$data[2];
        $this->param['payCount']=$param['payCount']=$data[3];
        $this->param['startDate']=$param['startDate']=$data[4];
        $this->param['endDate']=$param['endDate']=$data[5];
        $this->param['passageway']=$param['passageway']=$data[6];
        #1判断当前通道当前卡用户有没有入网和签约
        // 获取通道信息
       $passageway=Passageway::get($param['passageway']);
       $members=Members::haswhere('memberLogin','')->where(['member_id'=>$param['uid']])->find();
       if(!$passageway || !$members){
            $this->assign('data','获取数据失败，请重试。');
            return view("Userurl/show_error");
       }
       $passageway_rate=$passageway->passageway_rate;
       $passageway_income=$passageway->passageway_income;
       // var_dump($passageway->toArray());die;
       //判断是否签约
       $MemberCreditcard=MemberCreditcard::where(['card_id'=>$param['cardId']])->find();
       //判断哪个通道
       if($passageway['passageway_method']=='income'){  //暂时这么判断是汇联金创还是米刷

            // $member_net=MemberNet::where(['net_member_id'=>$param['uid']])->find();
            $has=MemberCreditPas::where(['member_credit_pas_creditid'=>$this->param['cardId'],'member_credit_pas_pasid'=>$this->param['passageway']])->find();
            // var_dump($has->toArray());die;
            if(!$has){ //信用卡有没有签约
                $MemberCreditPas=new MemberCreditPas;
                $res=$MemberCreditPas->save(['member_credit_pas_creditid'=>$this->param['cardId'],'member_credit_pas_pasid'=>$this->param['passageway']]);
                if(!$res){
                    $this->assign('data','商户入网失败，请重试。');
                    return view("Userurl/show_error");die;
                }
            }
            if(!$has['member_credit_pas_info']){//判断有没有入网
                $huilian=new Huilianjinchuang();
                $res=$huilian->income($this->param['passageway'],$this->param['cardId']);
                if(!$res){
                    $this->assign('data','商户入网失败，请重试。');
                    return view("Userurl/show_error");die;
                }
            }
            if(!$has['member_credit_pas_smsseq']){ //判断有没有签协议
                return redirect('Userurl/signed_huilian', ['passageway_id' =>$param['passageway'],'cardId'=>$param['cardId'],'order_no'=>$order_no]);
            } 
            // if(!$MemberCreditcard['huilian_income']){
            //     return redirect('Userurl/signed_huilian', ['passageway_id' =>$param['passageway'],'cardId'=>$param['cardId'],'order_no'=>$order_no]);
            // }
       }else{
           // 判断是否入网
           $member_net=MemberNet::where(['net_member_id'=>$param['uid']])->find();
           if(!$member_net[$passageway->passageway_no]){ //没有入网
               // 重定向到签约页面
               return redirect('Userurl/signed', ['passageway_id' =>$param['passageway'],'cardId'=>$param['cardId'],'order_no'=>$order_no]);
           }
           //判断是否签约
           if(!$MemberCreditcard['bindId'] || strlen($MemberCreditcard['bindId'])<20 || $MemberCreditcard['bindStatus']!='01'){ //未绑定
                //重定向到签约
                 return redirect('Userurl/signed', ['passageway_id' =>$param['passageway'],'cardId'=>$param['cardId'],'order_no'=>$order_no]);
           }
        }
       // ***************************************2生成计划**************************************************
       #2生成计划
          #卡详情
          $card_info=MemberCreditcard::where('card_id='.$this->param['cardId'])->find();
          if(!$card_info){
              $this->assign('data','获取数据失败，请重试。');
              return view("Userurl/show_error");
          }
          #获取后台费率
          $member_group_id=Members::where(['member_id'=>$this->param['uid']])->value('member_group_id');
          $rate=PassagewayItem::where(['item_passageway'=>$this->param['passageway'],'item_group'=>$member_group_id])->find();
           #定义税率  
           $also=($rate->item_also)/100;
           #定义代扣费
           $daikou=($rate->item_charges)/100;
           #获取代付费率
           $item_qfalso=($rate->item_qfalso)/100;
           #获取代付定额
           $item_qffix=($rate->item_qffix)/100;
          //如果还款次数小于天数
          $days=days_between_dates($this->param['startDate'],$this->param['endDate'])+1;
          $date=prDates($this->param['startDate'],$this->param['endDate']);
          if($this->param['payCount']<$days){
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
                $this->assign('data','生成计划失败，请重试');
                return view("Userurl/show_error");
          }
          //写入还款卡表
           $reimbur_result=new Reimbur([
                 'reimbur_generation'   =>$Generation_result->generation_id,
                 'reimbur_card'             =>$card_info->card_bankno,
           ]); 
           if(!$reimbur_result->save()){
                Db::rollback();
                $this->assign('data','生成计划失败，请重试');
                return view("Userurl/show_error");
           }
          ####################################
          #3确定每天实际还款金额
          $day_pay_real_money=$this->get_random_money($days,$this->param['billMoney'],$is_int=1);
          // print_r($day_pay_real_money);
          #4确定每天还款次数
          $day_pay_count=$this->get_day_count($this->param['payCount'],$days);
          #5计算出每天实际刷卡金额，和实际到账金额
          $Generation_order_insert=[];
           $generation_pound = 0;
          for ($i=0; $i <count($date) ; $i++) { 
              $day_real_get_money=0;
              //刷卡信息
              #计算每次需要刷卡的理论金额
              #还款也有手续费，计算出每次需要还款金额,$passageway->passageway_qf_rate,$passageway->passageway_qf_rate
              $day_pay_money[$i]=$this->get_need_pay($item_qfalso,$item_qffix,$day_pay_real_money[$i]);
              // print_r($day_pay_money[$i]);die;
              $each_pay_money=$this->get_random_money($day_pay_count[$i],$day_pay_money[$i],$is_int=1);
              #计算每次刷卡的时间
              $each_pay_time=$this->get_random_time($date[$i],$day_pay_count[$i]);

              foreach ($each_pay_money as $k => $each_money) {
                  //获取每次实际需要支付金额
                  $real_each_pay_money=$this->get_need_pay($also,$daikou,$each_money);
                  //获取每次实际到账金额
                  $real_each_get=$this->get_real_money($also,$daikou,$real_each_pay_money,$passageway_rate,$passageway_income);

                  $plan[$i]['pay'][$k]=$Generation_order_insert[]=array(
                      'order_no'       =>$Generation_result->generation_id,
                      'order_member'   =>$this->param['uid'],
                      'order_type'     =>1,
                      'order_card'     =>$card_info->card_bankno,
                      'order_money'    =>$real_each_pay_money,
                      'order_pound'    =>$real_each_get['fee'],
                      'order_real_get' =>$real_each_get['money'],
                      'order_platform_fee'=>$real_each_get['plantform_fee'],
                      'order_passageway_fee'=>$real_each_get['passageway_fee'],
                      'passageway_rate'=>$passageway_rate,
                      'passageway_fix'=>$passageway_income,
                      'user_fix'=>$daikou,
                      'user_rate'=>$also*100,
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
              //获取代还每次实际到账金额
              $real_qf_get=$this->get_real_money($item_qfalso,$item_qffix,$day_real_get_money,$passageway->passageway_qf_rate,$passageway->passageway_qf_fix);
              // print_r($real_qf_get);die;
              //提现信息
              $plan[$i]['cash']=$Generation_order_insert[]=array(
                  'order_no'         =>$Generation_result->generation_id,
                  'order_member'     =>$this->param['uid'],
                  'order_type'       =>2,
                  'order_card'       =>$card_info->card_bankno,
                  'order_money'      =>$day_real_get_money,//每天实际打回的金额
                  'order_pound'      =>$real_qf_get['fee'],

                  'order_real_get' =>$real_qf_get['money'],
                  'order_platform_fee'=>$real_qf_get['plantform_fee'],
                  'order_passageway_fee'=>$real_qf_get['passageway_fee'],
                  'passageway_rate'=>$passageway->passageway_qf_rate,
                  'passageway_fix'=> $passageway->passageway_qf_fix,
                  'user_fix'=>$item_qffix,
                  'user_rate'=>$item_qfalso*100,
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
        
          #写入计划表数据
          $Generation_order=new GenerationOrder();
          $order_result=$Generation_order->saveAll($Generation_order_insert);

         if($order_result!==false)
         { 
               Db::commit();
         }else{
               Db::rollback();
               $this->assign('data','生成计划失败，请重试');
               return view("Userurl/show_error");
         }
        $order_no=$Generation_result->generation_id;
        // *************************************展示计划****************************************************
        $order=array();
        //主计划
        $generation=Generation::with('creditcard')->where(['generation_id'=>$order_no])->find();
        //执行计划表
        $order=GenerationOrder::where(['order_no'=>$order_no])->order('order_time','asc')->select();
        foreach ($order as $key => $value) {
            $value=$value->toArray();
            // print_r($value);die;
            $list[$key]=$value;
            $list[$key]['day_time']=date("m月d日",strtotime($value['order_time']));
            $list[$key]['current_time']=date("H:i",strtotime($value['order_time']));
        }
        $data=[];
        //以日期为键
        foreach ($list as $key => $value) {
            $data[$value['day_time']][]=$value;
        }
        //手续费
        $order_pound=0;
        // print_r($data);die;
        //处理每日累计金额
        foreach($data as $k=>$v){
                $data[$k]['pay']=0;
                $data[$k]['get']=0;
            foreach ($v as $key => $vv) {
                if($vv['order_type']==1){
                  $data[$k]['pay']+=$vv['order_money'];
                }else if($vv['order_type']==2){
                  $data[$k]['get']+=$vv['order_real_get'];
                }
                $order_pound+=$vv['order_pound'];
            }
        }
        $this->assign('uid',$param['uid']);
        $this->assign('token',$members['memberLogin']['login_token']);
        $this->assign('order_pound',$order_pound);
        $this->assign('generation',$generation);
        $this->assign('order',$data);
        return view("Userurl/repayment_plan_create_detail");
    }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [还款计划创建 #2]
     * @version  [用户还款计划确认提交页]
     * param   $id  为generation表主键 generation_id
     * @return   [type]
     */
    public function repayment_plan_confirm($id){
        $this->checkToken();
        $GenerationOrder=GenerationOrder::order('order_time')->where(['order_no'=>$id,'order_type' => 2])->find();
        $GenerationOrder['order_money'] = GenerationOrder::order('order_time')->where(['order_no'=>$id,'order_id'=>["<",$GenerationOrder['order_id']]])->sum('order_money');
        $creaditcard=MemberCreditcard::where('card_bankno',$GenerationOrder->order_card)->find();
        $this->assign('generationorder',$GenerationOrder);
        $this->assign('creaditcard',$creaditcard);
        return view("Userurl/repayment_plan_confirm");
    }
      //确认执行还款计划
      //$id  为generation表主键 generation_id
      public function confirmPlan($id){
        $this->checkToken();
        $res=Generation::update(['generation_id'=>$id,'generation_state'=>2]);
        return json_encode($res ? ['code'=>200] : ['code'=>472,'msg'=>get_status_text(472)]);
      }
      #还款计划提交成功提示页
      #@version  [还款计划创建 #3]
      #
      public function repayment_plan_success(){
        return view("Userurl/repayment_plan_success");
      }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [还款计划详情]
     * @return   [type]
     */

    public function repayment_plan_detail(){
        #1获取参数判断需不需要去签约
        // $this->checkToken();
        $order_no=input('order_no');
       #33展示计划页面
        $order=array();
        //主计划
        $generation=Generation::with('creditcard')->where(['generation_id'=>$order_no])->find();
        //执行计划表
        $order=GenerationOrder::where(['order_no'=>$order_no])->order('order_time','asc')->select();
        if(!$order){
            echo '<li style="margin-top:13rem;text-align:center;list-style:none;font-size:1.4rem;color:#999;">暂无计划详情</li>';die;
        }
        $is_first=0;
        foreach ($order as $key => $value) {
            $value=$value->toArray();
            $list[$key]=$value;
            $list[$key]['day_time']=date("m月d日",strtotime($value['order_time']));
            $list[$key]['current_time']=date("H:i",strtotime($value['order_time']));
            if($value['order_status']=='-1' && $is_first==0 && $generation['generation_state']==2){//失败
                $list[$key]['is_first']=1;
                $is_first=1;
            }
            
        }
        // print_r($list);die;
        $data=[];
        //以日期为键
        foreach ($list as $key => $value) {
            $data[$value['day_time']][]=$value;
        }
        //手续费
        $order_pound=0;
        // print_r($data);die;
        //处理每日累计金额
        foreach($data as $k=>$v){
                $data[$k]['pay']=0;
                $data[$k]['get']=0;
            foreach ($v as $key => $vv) {
                if($vv['order_type']==1){
                  $data[$k]['pay']+=$vv['order_money'];
                }else if($vv['order_type']==2){
                  $data[$k]['get']+=$vv['order_real_get'];
                }
                $order_pound+=$vv['order_pound'];
            }
        }
        // print_r($data);die;
        $this->assign('order_pound',$order_pound);
        $this->assign('generation',$generation);
        $this->assign('order',$data);
        return view("Userurl/repayment_plan_detail");
    }
    //根据开始时间结束时间随机每天刷卡时间---有问题
      public function get_random_time($day,$count,$begin=8,$end=12){
        //如果日期为今天，刷卡时间大于当前小时
        $now_h=date('Y-m-d',time());
        if($day==$now_h){
           if($now_h<8){
               $begin =8;
           }else{
               $begin=date('H',time())+1;
           }
        }
        $last=$begin;
         $step=floor(($end-$begin)/$count)-1;
         for ($i=0; $i <$count ; $i++) { 
            // $time[$i]=$day.' '.get_hours($last,$last+$step).':'.get_minites();
               $time[$i]=$day.' '.get_hours($begin,$end).':'.get_minites();
            // $time[$i]['time']=$day.' '.get_hours($last,$last+$step).':'.get_minites();
            // $time[$i]['begin']=$last;
            // $time[$i]['end']=$last+$step;
            $last=$last+$step+1;
         }
         sort($time);
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
      public function get_real_money($rate,$fix,$pay,$passageway_rate,$passageway_income){
         //费率向上取整  0,1  0 
          $return['fee']=ceil($pay*100*$rate+$fix*100)/100;
          $return['passageway_fee']=ceil($pay*100*$passageway_rate/100+$passageway_income*100)/100;
          $return['plantform_fee']=$return['fee']-$return['passageway_fee'];
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
        rsort($arr);
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
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [消息]
     * @return   [type]
     */
     public function notify(){
         $this->checkToken();
        $count=Notice::where(['notice_recieve'=>$this->param['uid'],'notice_status'=>0])->count();
        $this->assign('count',$count);
        return view("Userurl/notify");
     }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [平台公告列表]
     * @return   [type]
     */
    public function notify_list(){
        $this->checkToken();
        $notice=Notice::where(['notice_announcement_id'=>["<>",'']])->order('notice_createtime desc')->select();
        if(!$notice){
            return view("Userurl/no_data");
        }
        $this->assign('notice',$notice);
        return view("Userurl/notify_list");
    }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-26T16:29:39+0800
     * @version  [version]
     * @param    [type]                   $id [announcement_id]
     * @return   [type]                       [平台公告详情]
     */
    public function notify_list_detail($id){
        $this->checkToken();
        $notice=Notice::get($id);
        $notice->save(['notice_status'=>1]);
        $this->assign('notice',$notice);
        return view("Userurl/notify_list_detail");
    }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [动账交易列表]
     * @return   [type]
     */
    public function deal_list(){
      $this->checkToken();
        $notice=Notice::where(['notice_recieve'=>$this->param['uid']])->order('notice_createtime desc')->select();
        if(!$notice){
            return view("Userurl/no_data");
        }
        $this->assign('notice',$notice);
        return view("Userurl/deal_lists");
    }
    // public function deal_list(){
    //     // $this->checkToken();
    //     $this->param['uid']=input("uid");
    //     $page = empty(input("page"))?1:input("page");
    //     if($_POST){
    //         $start = ($page-1)*10;
    //         $CashOrder=CashOrder::with("passageway")->where(['order_member'=>$this->param['uid'],'order_money' => ['<>' , 0]])->order('order_id desc')->limit($start,10)->select();

    //         foreach ($CashOrder as $key => $value) {
    //             $CashOrder[$key]["bank_ons"] = substr($value['order_creditcard'], -4);
    //             $CashOrder[$key]['add_time'] = date("m-d H:s",strtotime($value['order_add_time']));
    //         }
    //         echo json_encode(["data" => $CashOrder, "page" => $page+1]);die;
    //     }
    //     $CashOrder=CashOrder::with("passageway")->where(['order_member'=>$this->param['uid'],'order_money' => ['<>' , 0]])->order('order_id desc')->limit(0,10)->select();
    //     $count = CashOrder::where(['order_member'=>$this->param['uid'],'order_money' => ['<>' , 0]])->order('order_id desc')->count();
    //         $pages = ceil($count/10);
    //         #截取银行卡号
    //     foreach ($CashOrder as $key => $value) {
    //         $CashOrder[$key]["bank_ons"] = substr($value['order_creditcard'], -4);
    //         $CashOrder[$key]['add_time'] = date("m-d H:s",strtotime($value['order_add_time']));
    //     }
    //     if(!$CashOrder){
    //         return view("Userurl/no_data");
    //     }
    //     $this->assign("pages",$pages);
    //     $this->assign('data',$CashOrder);
    //     return view("Userurl/deal_list");
    // }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [平台福利列表]
     * @return   [type]
     */
    public function welfare_list(){
        $this->checkToken();
        //取Recomment内容
        $Recomment=Recomment::all(['recomment_member_id'=>$this->param['uid']]);
        $data=[];
        //转存
        foreach ($Recomment as $k => $v) {
            $data[$k]['recomment_money']=$v['recomment_money'];
            $data[$k]['recomment_desc']=$v['recomment_desc'];
            $data[$k]['recomment_creat_time']=$v['recomment_creat_time'];
        }
        if(!$data){
            return view("Userurl/no_data");
        }
        //Todo 对应事件数据 被推荐用户  操作
        $this->assign('data',$data);
        return view("Userurl/welfare_list");
    }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [注册页面]
     * @return   [type]
     */
    public function register(){
        $this->param=request()->param();
        //是否携带手机号
        if(!isset($this->param['recomment']))
            return 'miss telephone number';
        $recomment=$this->param['recomment'];
        //手机号格式
        if(!preg_match('/1\d{10}/', $recomment))
            return 'incorrect telephone number';
        $recommentid=Members::get(['member_mobile'=>$recomment]);
        //该手机号是否存在
        if(!$recommentid)
            return 'recomment telephone isnt exist';
        $this->assign('tel',$recomment);
        return view("Userurl/register");
    }
    /**
     * @Author   Star(794633291@qq.com)
     * @DateTime 2017-12-25T14:10:55+0800
     * @version  [下载页面]
     * @return   [type]
     */
    public function download(){
        $data['android_url']=Appversion::where(['version_type'=>'android','version_state'=>1])->value('version_link');
        $data['ios_url']=Appversion::where(['version_type'=>'ios','version_state'=>1])->value('version_link');
        $this->assign('data',$data);
        return view("Userurl/download");
    }

  /**
   * @Author   杨成志(3115317085@qq.com)
   * @DateTime 2017-12-25T14:01:55+0800
   * @version  [用户注册协议]
   * @return   [type]
   */
  public function web_user_register_protocol(){
    //查询用户协议相关信息
    $page_type = Page::pageInfo(3);
    $this->assign("page_content",$page_type['page_content']);
    return view("api/logic/web_user_register_protocol");
  }
  /**
   * @Author   杨成志(3115317085@qq.com)
   * @DateTime 2017-12-25T14:10:55+0800
   * @version  [推广素材库]
   * @return   [type]
   */
  public function web_marketing_media_library(){
    $this->assign("name",System::getName("sitename"));
    $generalizelist =  Generalize::generalizelist();
    $this->assign("generalizelist",$generalizelist);
    return view("api/logic/web_marketing_media_library");
  }
  /**
   * @Author   杨成志(3115317085@qq.com)
   * @DateTime 2017-12-25T14:10:55+0800
   * @version  [联系客服]
   * @return   [type]
   */
  public function web_contact_us(){
    //客服qq信息
    $qqInfo = CustomerService::customerinfo("QQ");
    $this->assign("qqInfo",$qqInfo);
    //客服微信信息
    $wxInfo = CustomerService::customerinfo("微信");
    $this->assign("wxInfo",$wxInfo);
    //客服电话信息
    $phoneInfo = CustomerService::customerinfo("电话");
    $this->assign("phoneInfo",$phoneInfo);

    
    $server['working_hours'] =  System::getName('working_hours');
    $server['phone'] = System::getName('contact_tel');//公司联系电话
    $this->assign("server",$server);
    return $this->fetch("api/logic/web_contact_us");
  }
  /**
   * @Author   杨成志(3115317085@qq.com)
   * @DateTime 2017-12-25T14:10:55+0800
   * @version  [复制图片增加次数]
   * @return   [type]
   */
  public function save_generalizenum(){
    $id = input("id");
    $save = Generalize::generalizenum($id);
    if($save){
      return json_encode(1);
    }else{
      return json_encode(0);
    }
  }
  /**
   * @Author   杨成志(3115317085@qq.com)
   * [share_link_list 分享注册邀请链接列表]
   * @return [type] [description]
   */
  public function share_link_list(){
    $this->checkToken();
    $phone=Members::get($this->param['uid']);
    $phone=$phone->member_mobile;
    $url='http://'.$_SERVER['HTTP_HOST'].'/api/userurl/gotoregister/recomment/'.$phone;
    $this->assign('url',$url);
    $list = Share::all();
    $this->assign("name",System::getName("sitename"));
    $this->assign("list",$list);
    return view("api/logic/share_link_list");
  }
  /**
   * @Author   杨成志(3115317085@qq.com)
   * [share_link 推广分享页]
   * @return [type] [description]
   */
  public function share_link(){
    $this->checkToken();
    $phone=Members::get($this->param['uid']);
    $phone=$phone->member_mobile;
    $url='http://'.$_SERVER['HTTP_HOST'].'/api/userurl/register/recomment/'.$phone;
    $this->assign('url',$url);
    return view("Userurl/share_link");
  }
  #分享的注册页 只有一个按钮的那个
  public function gotoregister(){
    $this->param=request()->param();
    #鑫鑫钱柜ngix报错 改用id查询src 兼容以前链接
    if(isset($this->param['share_thumb'])){
        $share_thumb=preg_replace('/~/', '/', $this->param['share_thumb']);
    }else{
        $Share=Share::get($this->param['share_id']);
        $share_thumb=$Share->share_thumb;
    }
    $url='http://'.$_SERVER['HTTP_HOST'].'/api/userurl/register/recomment/'.$this->param['recomment'];
    $this->assign('url',$url);
    $this->assign('share_thumb',$share_thumb);
    return view("Userurl/gotoregister");
  }
  #费率说明
  public function my_rates(){
    // $this->param['uid']=26;

    // $passageway=Passageway::where(['passageway_state'=>1,'passageway_also'=>1])->select();
    // var_dump($passageway);die;
     #获取所有通道
    #获取所有税率
    // $also=PassagewayItem::haswhere('passageway',['passageway_state'=>1])->select();
    $also=db('passageway')->where(['passageway_state'=>1])->order('passageway_also')->select();
    foreach ($also as $k => $v) {
        $also[$k]['details']=db('passageway_item')->alias('i')
            ->join('member_group g','i.item_group=g.group_id')
            ->where('i.item_passageway',$v['passageway_id'])->select();
    }
    $this->assign('also',$also);
    return view("Userurl/my_rates");
  }
  #盈利模式说明
  public function explain(){
        $description=Article::hasWhere('articleCategorys',['category_name'=>'盈利模式说明'])->join("wt_article_data","data_article=article_id")->field("data_text")->find();
        $this->assign('data',$description);
        return view("Userurl/explain");
  }
  #关于我们
  public function about_us(){
    $data=Page::get(1);
    $server['weixin']=CustomerService::where('service_title','微信')->find();
    #资格证书
    $datas = Page::get(4);
    $this->assign("datas",$datas);
    $server['qq']=CustomerService::where('service_title','QQ')->find();

    $server['tel']=CustomerService::where('service_title','电话')->find();
    $server['company_address'] = System::getName('company_address');
    $server['working_hours'] = System::getName('working_hours');
    $server['phone'] = System::getName('contact_tel');//公司联系电话
    $this->assign('data', $data);
    $this->assign('server', $server);
    return view("Userurl/about_us");
  }
  /**
   * [web_freshman_guide 新手指引]
   * @return [type] [description]
   */
   public function web_freshman_guide(){
        // $class = NoviceClasss::all();
        // #还款列表
        // foreach ($class as $key => $value) {
        //  $class[$key]['repaymentList'] = MemberNovice::lists($value['novice_class_id']);
        // }
        // $this->assign("class",$class);
        return view("api/logic/web_freshman_guide");
  }
  /**
  *@version [instructicons_detail 新手指引详情页面]
  *@author 杨成志 【3115317085@qq.com】
  */
  public function instructions_detail(){
    $title = input('title');
    $this->assign("title",$title);
    $info = MemberNovice::where(['novice_name' => $title])->find();
    $this->assign("info",$info);
    return view('api/logic/instructions_detail');
  }
  #信用卡说明
  public function card_description(){
    $CreditCard = new CreditCard();
    $list = $CreditCard->where(['bank_passageway_id'=>Request::instance()->param('id')])->select();
    $this->assign('list',$list);
    return view("api/logic/card_description");
  }
  /**
  *@version particulars 收支明细
  *@author 杨成志 （3115317085@qq.com）
  */
  public function particulars($month=null){
        if(!$month)$month=date('Y-m');
      //月初
      $monthstart=strtotime($month);
      //月末
      $monthend=strtotime(date('Y-m',strtotime('+1 month',strtotime($month))));
      $data=[];
        $data['in']=0;
        $data['out']=0;
        //手动下滑获取数据
        if($_POST){
            $page = isset($_POST['page'])?$_POST['page']:1;
            #获取收支明细数据
            $result = WalletLog::pages(input('uid'),$page,$data,$month,$monthstart,$monthend);
            $list = $result['list'];
            exit(json_encode($list));
        }
       $this->checkToken();
      #获取收支明细数据
        $result = WalletLog::pages($this->param['uid'],1,$data,$month,$monthstart,$monthend);
        //用户id
        $this->assign("uid",$this->param['uid']);
        #当前总共多少页
        $this->assign("allpage" , $result['allpage']);
        $this->assign('data' , $result['data']);
        $this->assign('list' , $result['list']);
        return view("Userurl/particulars");
  }
  #账单详情
  # log_id  wallet_log_id
  public function bills_detail($log_id){
    $this->checkToken();
    $wallet_log=db('wallet_log')->where('log_id',$log_id)->find();
    switch ($wallet_log['log_relation_type']){
        //分润分佣
        case 1:
            $commission=db('commission')->alias('c')
                ->join('member m','c.commission_childen_member=m.member_id')
                ->where('c.commission_id',$wallet_log['log_relation_id'])
                ->find();
            if($commission){
                $tel=$commission['member_mobile'];
                $commission['member_mobile']=substr($tel,0,3).'****'.substr($tel,7);
                $this->assign('commission',$commission);
            }
            break;
        //提现操作
        case 2:
            $withdraw=db('withdraw')->where('withdraw_id',$wallet_log['log_relation_id'])->find();
            if($withdraw)$withdraw['info']=state_info($withdraw['withdraw_state']);
            $this->assign('withdraw',$withdraw);
            break;
            //推荐红包
        case 5:
            $recomment=db('recomment')->alias('r')
                ->join('member m','r.recomment_children_member=m.member_id')
                ->where('r.recomment_id',$wallet_log['log_relation_id'])
                ->find();
            if($recomment){
                $tel=$recomment['member_mobile'];
                $recomment['member_mobile']=substr($tel,0,3).'****'.substr($tel,7);
                $this->assign('recomment',$recomment);
            }
        default:
            # code...
            break;
    }
    $this->assign('wallet_log',$wallet_log);
    return view("Userurl/bills_detail");
  }
  # 荣邦 开通快捷支付不返回html时 调用本页面 
  # memberId 用户id passwayId 通道id
  # treatycode 协议号 smsseq 短信验证码流水号
  public function passway_rongbang_openpay($memberId,$passwayId,$treatycode,$smsseq){
    if(request()->ispost()){
        $authcode=request()->param()['authcode'];
        if($authcode){
            $Membernets=new con\Membernets($memberId,$passwayId);
            $result=$Membernets->rongbang_confirm_openpay($treatycode,$smsseq,$authcode);
            return is_array($result) ? 1 : $result;
          }else{
            return 2;
          }
    }
    $this->assign('memberId',$memberId);
    $this->assign('passwayId',$passwayId);
    $this->assign('treatycode',$treatycode);
    $this->assign('smsseq',$smsseq);
    return view("Userurl/passway_rongbang_openpay");
  }
  # 荣邦 申请快捷支付订单不返回html时 调用本页面 
  # memberId 用户id passwayId 通道id
  # ordercode 订单号 card_id 卡号

  public function passway_rongbang_pay($memberId,$passwayId,$ordercode,$card_id){
    if(request()->ispost()){
        $authcode=request()->param()['authcode'];
        if($authcode){
            $Membernets=new con\Membernets($memberId,$passwayId);
            $result=$Membernets->rongbang_confirm_pay($ordercode,$card_id,$authcode);
            return is_array($result) ? 1 : $result;
          }else{
            return 2;
          }
    }
    #用户信息
    $info = Members::with("memberCashcard")->where(['member_id' => $memberId])->find();
    $money=db('cash_order')->where('order_thead_no',$ordercode)->value('order_money');
    $creditcard = MemberCreditcard::where(['card_id' => $card_id])->find();
    $this->assign("creditcard",$creditcard);
    $this->assign("member_info",$info);
    $this->assign('memberId',$memberId);
    $this->assign('money',$money);
    $this->assign('passwayId',$passwayId);
    $this->assign('ordercode',$ordercode);
    $this->assign('card_id',$card_id);
    return view("Userurl/passway_rongbang_pay");
  }

  #荣邦支付回调
  public function passway_rongbang_paycallback(){
    $param=request()->param();
    #荣邦通道键位
    $passageway_no=db('passageway')->column("passageway_id,passageway_no");
    $userinfo=db('member_net')->where('net_member_id',$param['member_id'])
        ->value($passageway_no[$param['passageway_id']]);
    // $userinfo="402628739,1756961550411722,9abphqw0bcz2vr3r,zeo3qvxb0nwuobk5619hss25h1dsremg,许成成11599";
    #信息顺序 0、appid 1、companycode 2、secretkey 3、session 4、companyname
      $userinfo=explode(',', $userinfo);
    $key=$userinfo[2];
    // return rongbang_aes_decode($key,rongbang_aes($key,$param['test']));
    $data = rongbang_aes_decode($key,$param['Data']);
    // var_dump($data);die;
    if($data=='err'){
        #失败日志
        trace("rongbang_aes_decode_err");
    }else{
        $data=json_decode($data,1);
            $cash_order=CashOrder::where('order_no',$param['order_no'])->find();
        if($data['respcode']==2){
          #查看是否有该订单的分润记录
          $hasCommission=db('commission')->where(['commission_from'=>$cash_order->order_id,'commission_type'=>1])->count();
            #仅对待支付状态下的订单进行更新并分润
            if($cash_order->order_state!=2 && $hasCommission == 0){
                $cash_order->order_state=2;
                //分润
                $fenrun= new con\Commission();
                $fenrun_result=$fenrun->MemberFenRun($param['member_id'],$cash_order->order_money,$param['passageway_id'],1,'快捷支付手续费分润',$cash_order->order_id);
            $member=Members::get($param['member_id']);
            $passway=Passageway::get($param['passageway_id']);
            $passwayitem=PassagewayItem::get(['item_group'=>$member->member_group_id,'item_passageway'=>$passway->passageway_id]);
          if($fenrun_result['code']=="200"){
            $cash_order->order_fen=$fenrun_result['leftmoney'];
            $cash_order->order_buckle=$passwayitem->item_charges/100;
            $cash_order->order_platform=$cash_order->order_charge-($cash_order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges/100-$passway->passageway_income;
          }else{
            $cash_order->order_fen=-1;
            $cash_order->order_buckle=$passwayitem->item_charges/100;
            $cash_order->order_platform=$cash_order->order_charge-($cash_order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges/100-$passway->passageway_income;
          }
          $cash_order->save();
            }else{
                trace("rongbang_repeat_request,not fenrun");
            }
        }else{
            $cash_order->order_state=-1;
            $cash_order->order_desc.=$data['respmsg'];
            $cash_order->save();
        }
    }
    //按文档要求返回
    return json_encode(['message'=>'ok','response'=>'00']);
  }
  #为无需短信确认的情况 直接显示一个成功页面
  public function passway_success(){
    return view("Userurl/passway_success");
  }
  # 开通快捷支付 前台回调成功页面
  public function passway_open_success(){
    return view("Userurl/passway_open_success");
  }
  #取消还款计划【整体】
  public function cancel_repayment($generation_id){
    $membernet=new con\Membernet();
    return json_encode($membernet->cancle_plan($generation_id));
  }
  //重新执行某个计划
  public function reset_one_repayment($plan_id){
        $membernet=new con\Membernet();
        $res=$membernet->action_single_plan($plan_id);
        echo $res;die;
  }
  //代还，用户签约界面
  public function signed($passageway_id,$cardId,$order_no){
        #信用卡信息
        $data['MemberCreditcard']=$MemberCreditcard=MemberCreditcard::where(['card_id'=>$cardId])->find();
        #通道信息
        $data['passageway']=$passageway=Passageway::get($passageway_id);
        #通道入网信息
        $member_net=MemberNet::where(['net_member_id'=>$MemberCreditcard['card_member_id']])->find();
        #用户基本信息
        $data['Members']=$Members=Members::haswhere('memberLogin','')->where(['member_id'=>$MemberCreditcard['card_member_id']])->find();
        #登录信息
        // if(!$MemberCreditcard || !$passageway || $member_net){
        //  exit('获取信息失败');
        // }
        $this->assign('order_no',$order_no);
        $this->assign('passageway_id',$passageway_id);
        $this->assign('data',$data);
        return view("Userurl/signed");
  }
  public function signed_huilian($passageway_id,$cardId,$order_no){
      #信用卡信息
      $data['MemberCreditcard']=$MemberCreditcard=MemberCreditcard::where(['card_id'=>$cardId])->find();
      #通道信息
      $data['passageway']=$passageway=Passageway::get($passageway_id);
      #通道入网信息
      $member_net=MemberNet::where(['net_member_id'=>$MemberCreditcard['card_member_id']])->find();
      #用户基本信息
      $data['Members']=$Members=Members::haswhere('memberLogin','')->where(['member_id'=>$MemberCreditcard['card_member_id']])->find();
      $data['merId']=$data['Members']['memberNet'][$data['passageway']['passageway_no']];
      // var_dump($data['merId']);die;
      #登录信息
      // if(!$MemberCreditcard || !$passageway || $member_net){
      //  exit('获取信息失败');
      // }
      $this->assign('order_no',$order_no);
      $this->assign('passageway_id',$passageway_id);
      $this->assign('data',$data);
      return view("Userurl/signed_huilian");
  }
  #金易付验证码页面
  public function jinyifu($memberId,$passagewayId,$cardId,$price){

    if(request()->ispost()){
        // var_dump(Request::instance()->post('passagewayId'));die;
        $jinyifu=new \app\index\controller\CashOut(Request::instance()->post('memberId'),Request::instance()->post('passwayId'),Request::instance()->post('cardId'));
        $res=$jinyifu->jinyifu_pay(Request::instance()->post());

        return $res;
    }
    //$member=Members::with('memberCashcard,memberCreditcard')->where(['member_id'=>$memberId,'card_id'])->find();
    $info=Members::haswhere('memberCreditcard',['card_id'=>$cardId])->where(['member_id'=>$memberId])->find();
    // //var_dump($info::getLastSql());
    // dump($info->memberCreditcard->card_bankname);
    // dump($info->memberCashcard->card_bankname);
    // die;

    $this->assign('info',$info);
    $this->assign('price',$price);
    $this->assign('passagewayId',$passagewayId);

    return view("Userurl/jinyifu");
  }

  #金易付发送验证码
  public function jinyifu_sms(){
    // var_dump(Request::instance()->param('phone'));die;
     #验证手机/发送对象是否存在
         if(!phone_check(Request::instance()->param('phone')))
             return ['code'=>401];
           #随机一个验证码
           $code=verify_code(System::getName('code_number'));
           // var_dump($code);die;
           #设定短信内容
           $message="您本次操作的验证码为".$code."，请尽快使用。有效期为".System::getName('code_timeout')."分钟。";
           $log=new SmsCode([
                 'sms_log_content'=>$code,
                 'sms_log_state'    =>1,
                 'sms_log_type'     =>'验证码',
                 'sms_send'          =>Request::instance()->param('phone')
           ]);
           $sms_result=$log->save();
           if(!$sms_result)
                 return['code'=>303]; 
           $result=send_sms(Request::instance()->param('phone'), $message);
           #如果发送成功,记录发送记录表
           if(!$result)
                 return ['code'=>303];
           return ['code'=>200,'msg'=>'验证码发送成功~'];
  }

  #H5有积分前台通知地址
  public function H5youjifen($tradeNo){

    //   echo "666";
    //   // echo str_pad("",4096);
    // {//断开连接的代码    
    //     $size=ob_get_length();    
    //     header("Content-Length: $size");  //告诉浏览器数据长度,浏览器接收到此长度数据后就不再接收数据  
    //     header("Connection: Close");      //告诉浏览器关闭当前连接,即为短连接  
    //     ob_flush();    
    //     flush();    
    // }    
      sleep(3);
      // var_dump(132);die;
    $order=CashOrder::get(['order_no'=>$tradeNo]);
    $passway=Passageway::get($order->order_passway);
    $member=Members::get($order->order_member);

    #查询订单是否成功
    $url="http://kjnq.jct8.com/quickpay/Query/".$passway->passageway_mech."/".$tradeNo;
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    $data = curl_exec($curl);     //返回api的json对象
    //关闭URL请求
    curl_close($curl);
    $return=json_decode($data,true);
    // var_dump($return);die;
    if($return['code']=='00'){
      if(isset($return['payStatus']) && $return['payStatus']=='Y'){
         $passwayitem=PassagewayItem::get(['item_group'=>$member->member_group_id,'item_passageway'=>$passway->passageway_id]);
         $Commission_info=Commissions::where(['commission_from'=>$order->order_id,'commission_type'=>1])->find();
         if(!$Commission_info){
                $fenrun= new \app\api\controller\Commission();
                $fenrun_result=$fenrun->MemberFenRun($order->order_member,$order->order_money,$order->order_passway,1,'套现手续费分润',$order->order_id);
         }else{
            $fenrun_result['code']=-1;
         }
         $order->order_state=2;

      }else{
        $order->order_state=-1;
         $fenrun_result['code']=-1;
      }

    }elseif($return['code']=='01'){
      $order->order_state=-1;
       $fenrun_result['code']=-1;
    }

     if($fenrun_result['code']=="200")
     {
         $order->order_fen=$fenrun_result['leftmoney'];
         $order->order_buckle=$passwayitem->item_charges/100;
         $order->order_platform=$order->order_charge-($order->order_money*$passway->passageway_rate/100)+$passwayitem->item_charges-$passway->passageway_income;
     }
        else    
     {
             $order->order_fen=-1;
     }

      $res = $order->save();
     if ($res) {
      $this->assign('data',$fenrun_result);
      $this->assign('url',$url);
        return view("Userurl/H5youjifen");
     }
  }

  public function parents($phone){
    if(is_numeric($phone)){
      $member=Members::where(['member_mobile'=>$phone])->find();
    }else{
      $member=Members::where(['member_nick'=>$phone])->find();
    }
    if(!$member){
      echo "没有用户信息";die;
    }
    $parent_id=MemberRelation::where(['relation_member_id'=>$member['member_id']])->value('relation_parent_id');
    if($parent_id==0){
      echo '没有上级';die;
    }
    if(!$parent_id && $parent_id!=0){
      echo "MemberRelation 缺少用户数据";die;
    }
    $parent=Members::where(['member_id'=>$parent_id])->find();

    echo "姓名：".$parent['member_nick'].'<p>';
    echo "手机号：".$parent['member_mobile'].'<p>';
    if($parent['member_cert']==1)echo "已实名"; else echo "未实名";    


  }
  /**
  * @version credit_card 推广模块子类页面
  * @author 杨成志 （3115317085@qq.com）
  */
  public function credit_card(){
    //获取父类名称
    $wheres['list_id'] = input("parent_id");
    $info=ServiceItemList::where($wheres)->order("list_id desc")->find();
    $this->assign("info",$info);
    $where['list_parent_id'] = input("parent_id");
    $list=ServiceItemList::where($where)->order("list_id desc")->select();
    $this->assign("list",$list);
    return view("Userurl/credit_card");
  }


    public function get_team($id){
      $parent=$this->get($id);
        // 输出Excel文件头，可把user.csv换成你要的文件名  
  header('Content-Type: application/vnd.ms-excel');  
  header('Content-Disposition: attachment;filename="user.csv"');  
  header('Cache-Control: max-age=0');     
  // 从数据库中获取数据，为了节省内存，不要把数据一次性读到内存，从句柄中一行一行读即可      
  // 打开PHP文件句柄，php://output 表示直接输出到浏览器  
 $fp = fopen('php://output', 'a');    
 // 输出Excel列名信息  
 $head = array('姓名');  
 // foreach ($head as $i => $v) {  
     // CSV的Excel支持GBK编码，一定要转换，否则乱码  
     $head[0] = iconv('utf-8', 'gbk', $head[0]);  
 // }    
 // 将数据通过fputcsv写到文件句柄  
 fputcsv($fp, $head);   
 // 计数器   
 // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小  
   
 // 逐行取出数据，不浪费内存  
 foreach ($parent as $key => $value) {
         ob_flush();  
         flush();  
         $row[0] = iconv('utf-8', 'gbk', $value['member_nick']);  
     fputcsv($fp, $row);  
 }

      
    }
    public function get($id){
        $parent=MemberRelation::with('member')->where(['relation_parent_id'=>$id])->select();
        $data=[];
        foreach ($parent as $key => $value) {
            $re=$this->get($value['relation_member_id']);

              $data=array_merge($data,$re);

        }
              $data=array_merge($data,$parent);
        return $data;
      }
      /**
       * 订单支付
       * @return [type] [description]
       */
    public function order_pay($passageway_id,$card_info,$price,$out_trade_no){
        $data['price']=$price;
        $data['out_trade_no']=$out_trade_no;
        $this->assign('card_info',$card_info);
        $this->assign('data',$data);
        return view("Userurl/order_pay");
    }
}