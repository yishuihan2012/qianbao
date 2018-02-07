<?php

namespace app\api\controller;
use think\Db;
use think\Config;
use think\Loader;
use think\Controller;
use app\index\model\CustomerService;
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
use app\index\model\Generation;
use app\index\model\GenerationOrder;
use app\index\model\NoviceClass as NoviceClasss; 
use app\index\model\MemberCert;
use app\index\model\MemberNet;
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
             if(!isset($this->param['uid']) || empty($this->param['uid']) || !isset($this->param['token']) ||empty($this->param['token']))
                   $this->error=314;
             #查找到当前用户
             $member=Members::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
             if(!$member && !$this->error)
                   $this->error=317;
        }catch (\Exception $e) {
             $this->error=317;
        }
        if($this->error){
			$msg=Config::get('response.'.$this->error) ? Config::get('response.'.$this->error) : "系统错误~";
            exit(json_encode(['code'=>$this->error, 'msg'=>$msg, 'data'=>'']));
        }
		$this->assign('uid',$this->param['uid']);
		$this->assign('token',$this->param['token']);
      }

      #专属二维码列表
	public function exclusive_code(){
	    $list = Exclusive::all();
	    $this->assign("list",$list);
	    return view("api/logic/share_code_list");
	}

	#套现成功页面
	public function calllback_success(){
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
		//若已经生成过
		if(!is_file('autoimg/qrcode_'.$exclusive_id.'_'.$tel.'.png')){
			Vendor('phpqrcode.phpqrcode');
			$QRcode=new \QRcode();
			//生成二维码
			$QRcode->png($url, 'autoimg/qrcode'.$tel.'.png',0,8);
			$qrurl=ROOT_PATH.'public/autoimg/qrcode'.$tel.'.png';
			$logourl=ROOT_PATH.'public/static/images/logo.png';
			// 二维码加入logo
			 $QR = imagecreatefromstring(file_get_contents($qrurl)); 
			 $logo = imagecreatefromstring(file_get_contents($logourl)); 
			 $logo_width = imagesx($logo);
			 $logo_height = imagesy($logo);
			 imagecopyresampled( $QR,$logo, 115, 115, 0, 0, 60, 60, $logo_width, $logo_height); 
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
			 imagecopyresampled( $bg,$QR, 240, 710, 0, 0, 296, 296, $QR_width, $QR_height); 
			imagejpeg($bg, 'autoimg/qrcode_'.$exclusive_id.'_'.$tel.'.png',65); 
		}
		//返回图片地址
		$url='http://'.$_SERVER['HTTP_HOST'].'/autoimg/qrcode_'.$exclusive_id.'_'.$tel.'.png';
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
		$generation=Generation::with('creditcard')->where(['generation_member'=>$this->param['uid'],'generation_state'=>2])->select();
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
		// 		$generation1[$key]['generation_card']=substr($value['generation_card'], -4);
		// 		$generation1[$key]['count']=GenerationOrder::where(['order_no'=>$value['generation_id']])->count();
		// }

		#完成
		$generation3=Generation::with('creditcard')->where(['generation_member'=>$this->param['uid'],'generation_state'=>3])->select();
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
		$this->checkToken();
		$order_no=$this->param['order_no'];
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
        		  $data[$k]['get']+=$vv['order_money'];
        		}
        		$order_pound+=$vv['order_pound'];
        	}
        }
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
		$GenerationOrder=GenerationOrder::order('order_money desc')->where('order_no',$id)->find();
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
		$order_no=$this->param['order_no'];//这个order_no是app传过来的参数合集
		$data=explode('_', $order_no);
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
       // 判断是否入网
       $member_net=MemberNet::where(['net_member_id'=>$param['uid']])->find();
       if(!$member_net[$passageway->passageway_no]){ //没有入网
           // 重定向到签约页面
           session::push($session_name,json_encode($param));
           return redirect('Userurl/signed', ['passageway' =>$param['passageway'],'cardId'=>$param['cardId'],'order_no'=>$order_no]);
       }
       //判断是否签约
       $MemberCreditcard=MemberCreditcard::where(['card_id'=>$param['cardId']])->find();
       if(!$MemberCreditcard['bindId'] || strlen($MemberCreditcard['bindId'])<20){ //未绑定
            //重定向到签约
             session::push($session_name,json_encode($param));
             return redirect('Userurl/signed', ['passageway_id' =>$param['passageway'],'cardId'=>$param['cardId'],'order_no'=>$order_no]);
       }
       #2判断是否存在session
       // if($data=session::get($session_name)){
       //    //获取到session,跳转到creatPlan_mishua方法
       //    return redirect('RepaymentPlan/creatPlan_mishua',json_decode($data,true));
       // }else{
       //      exit('获取数据失败！');
       // }
       // ***************************************2生成计划**************************************************
       #2生成计划
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
          //如果还款次数小于天数
          $days=days_between_dates($this->param['startDate'],$this->param['endDate'])+1;
          $date=prDates($this->param['startDate'],$this->param['endDate']);
          if($this->param['payCount']<$days){
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
        
          #写入计划表数据
          $Generation_order=new GenerationOrder();
          $order_result=$Generation_order->saveAll($Generation_order_insert);

         if($order_result!==false)
         { 
               Db::commit();
         }else{
               Db::rollback();
               return ['code'=>472];      
         }

        } catch (Exception $e) {
            echo  $e->getMessage.$e->getLine().$e->getFile();
        }
        $order_no=$Generation_result->generation_id;
        // *************************************展示计划****************************************************
       #33展示计划页面
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
        		  $data[$k]['get']+=$vv['order_money'];
        		}
        		$order_pound+=$vv['order_pound'];
        	}
        }
		$this->assign('order_pound',$order_pound);
		$this->assign('generation',$generation);
		$this->assign('order',$data);
	  	return view("Userurl/repayment_plan_detail");
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
		// $Announcement=Announcement::where(['announcement_status'=>1])->order('announcement_id desc')->select();
		$notice=Notice::where(['notice_recieve'=>$this->param['uid']])->order('notice_createtime desc')->select();
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
		// $this->checkToken();
		// $this->param['uid']=11;
		//取MemberCash内容
		$MemberCash=MemberCash::where(['cash_member_id'=>$this->param['uid'],'cash_state'=>1])->order('cash_create_at desc')->select();
		$data=[];
		//流水
		$i=0;
		//转存
		foreach ($MemberCash as $k => $v) {
			$data[$i]['number']=$i;
			//用于区分MemberCash和Withdraw
			$data[$i]['type']='MemberCash';
			$data[$i]['cash_amount']=sprintf("%.2f",substr(sprintf("%.3f", $v['cash_amount']), 0, -1));
			$data[$i]['service_charge']=sprintf("%.2f",substr(sprintf("%.3f", $v['service_charge']), 0, -1));
			$data[$i++]['cash_create_at']=$v['cash_create_at'];
		}
		//取withdraw内容
		$Withdraw=Withdraw::where(['withdraw_member'=>$this->param['uid'],'withdraw_state'=>12])->order('withdraw_add_time desc')->select();
		//转存
		foreach ($Withdraw as $k => $v) {
			$data[$i]['number']=$i;
			//用于区分MemberCash和Withdraw
			$data[$i]['type']='Withdraw';
			$data[$i]['withdraw_amount']=sprintf("%.2f",substr(sprintf("%.3f", $v['withdraw_amount']), 0, -1));
			$data[$i]['withdraw_charge']=sprintf("%.2f",substr(sprintf("%.3f", $v['withdraw_charge']), 0, -1));
			$data[$i]['withdraw_account']=substr($v['withdraw_account'],-4);
			$data[$i]['withdraw_charge']=$v['withdraw_charge'];
			$data[$i]['withdraw_add_time']=$v['withdraw_add_time'];
			$data[$i++]['withdraw_method']=$v['withdraw_method'];
		}
		//取CashOrder内容
		// $CashOrder=CashOrder::with('bankcard')->all(['order_member'=>$this->param['uid'],'order_state'=>2]);
		$CashOrder=CashOrder::with('membercreditcard')->where(['order_member'=>$this->param['uid'],'order_state'=>2])->order('order_add_time desc')->select();
		//转存
		foreach ($CashOrder as $k => $v) {
			$data[$i]['number']=$i;
			//用于区分MemberCash和Withdraw
			$data[$i]['type']='CashOrder';
			$data[$i]['order_money']=sprintf("%.2f",substr(sprintf("%.3f", $v['order_money']), 0, -1));
			$data[$i]['order_charge']=$v['order_charge'];
			$data[$i]['card_bankname']=$v['card_bankname'];
			$data[$i]['order_creditcard']=substr($v['order_creditcard'],-4);
			$data[$i++]['order_update_time']=$v['order_update_time'];
		}
		if(!$data){
			return view("Userurl/no_data");
		}
		$this->assign('data',$data);
	  	return view("Userurl/deal_list");
	}
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
  	$share_thumb=preg_replace('/~/', '/', $this->param['share_thumb']);
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
	  	return view("Userurl/explain");
  }
  #关于我们
  public function about_us(){
  	$data=Page::get(1);
  	$server['weixin']=CustomerService::where('service_title','微信')->find();

  	$server['qq']=CustomerService::where('service_title','QQ')->find();

  	$server['tel']=CustomerService::where('service_title','电话')->find();
  
  	$this->assign('data', $data);
  	$this->assign('server', $server);
  	return view("Userurl/about_us");
  }
  /**
   * [web_freshman_guide 新手指引]
   * @return [type] [description]
   */
   public function web_freshman_guide(){
   		$class = NoviceClasss::all();
   		#还款列表
   		foreach ($class as $key => $value) {
   			$class[$key]['repaymentList'] = MemberNovice::list($value['novice_class_id']);
   		}
   		$this->assign("class",$class);
    	return view("api/logic/web_freshman_guide");
  }
  #信用卡说明
  public function card_description(){
  	$CreditCard = new CreditCard();
  	$list = $CreditCard->select();
  	$this->assign('list',$list);
  	return view("api/logic/card_description");
  }
  #收支明细
  public function particulars($month=null){
	$this->checkToken();
	if(!$month)$month=date('Y-m');
	$month='2017-12';
	//月初
	$monthstart=strtotime($month);
	//月末
	$monthend=strtotime(date('Y-m',strtotime('+1 month',strtotime($month))));
  	//表头数据
  	$data=[];
  	$data['month']=$month;
  	$data['in']=0;
  	$data['out']=0;
  	$list=db('wallet_log')->alias('l')
  		->join('wallet w','l.log_wallet_id=w.wallet_id')
  		->where(['w.wallet_member'=>$this->param['uid']])
  		->where('log_add_time','between time',[$monthstart,$monthend])
  		->select();
	foreach ($list as $k => $v) {
		if($v['log_wallet_type']==1){
			$data['in']+=$v['log_wallet_amount'];
		}else{
			$data['out']+=$v['log_wallet_amount'];
		}
		switch ($v['log_relation_type']) {
			//提现操作
			case 2:
				$state=db('withdraw')->where('withdraw_id',$v['log_relation_id'])->value('withdraw_state');
				if($state)$list['k']['info']=state_info($state);
				break;
			
			default:
				# code...
				break;
		}
	}
  	$this->assign('data',$data);
  	$this->assign('list',$list);
  	return view("Userurl/particulars");
  }
  #账单详情
  # log_id  wallet_log_id
  public function bills_detail($log_id){
  	$this->checkToken();
  	$wallet_log=db('wallet_log')->where('log_id',$log_id)->find();
  	switch ($wallet_log['log_relation_type']) {
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
  #新版本查询 for安卓
  public function check_version($code){
  	if(isset($code)){
  		$version=db('system')->where('system_key','ad_version')->value('system_val');
  		if($code==$version){
  			return json_encode(['code'=>400]);
  		}else{
  			$url=$version=db('system')->where('system_key','ad_url')->value('system_val');
  			return json_encode(['code'=>200,'url'=>$url]);
  		}
  	}
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
  		// 	exit('获取信息失败');
  		// }
  		$this->assign('order_no',$order_no);
  		$this->assign('passageway_id',$passageway_id);
  		$this->assign('data',$data);
  		return view("Userurl/signed");
  }
}