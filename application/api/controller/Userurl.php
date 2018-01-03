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
use app\index\model\MemberNovice; 
use app\index\model\Passageway; 
use app\index\model\PassagewayItem; 
use app\index\model\MemberGroup; 
use app\index\model\MemberRelation; 
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
	  	return view("Userurl/repayment_history");
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [还款计划详情]
	 * @return   [type]
	 */
	public function repayment_plan_detail(){
		$this->checkToken();
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
		$Announcement=Announcement::all(['announcement_status'=>1]);
		$this->assign('announcement',$Announcement);
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
		$Announcement=Announcement::get($id);
		$this->assign('announcement',$Announcement);
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
		//取MemberCash内容
		$MemberCash=MemberCash::all(['cash_member_id'=>$this->param['uid'],'cash_state'=>1]);
		$data=[];
		//流水
		$i=0;
		//转存
		foreach ($MemberCash as $k => $v) {
			$data[$i]['number']=$i;
			//用于区分MemberCash和Withdraw
			$data[$i]['type']='MemberCash';
			$data[$i]['cash_amount']=$v['cash_amount'];
			$data[$i]['service_charge']=$v['service_charge'];
			$data[$i++]['cash_create_at']=$v['cash_create_at'];
		}
		//取withdraw内容
		$Withdraw=Withdraw::all(['withdraw_member'=>$this->param['uid'],'withdraw_state'=>12]);
		//转存
		foreach ($Withdraw as $k => $v) {
			$data[$i]['number']=$i;
			//用于区分MemberCash和Withdraw
			$data[$i]['type']='Withdraw';
			$data[$i]['withdraw_amount']=$v['withdraw_amount'];
			$data[$i]['withdraw_charge']=$v['withdraw_charge'];
			$data[$i]['withdraw_account']=substr($v['withdraw_account'],-4);
			$data[$i]['withdraw_charge']=$v['withdraw_charge'];
			$data[$i]['withdraw_add_time']=$v['withdraw_add_time'];
			$data[$i++]['withdraw_method']=$v['withdraw_method'];
		}
		//取CashOrder内容
		// $CashOrder=CashOrder::with('bankcard')->all(['order_member'=>$this->param['uid'],'order_state'=>2]);
		$CashOrder=CashOrder::with('membercreditcard')->where(['order_member'=>$this->param['uid'],'order_state'=>2])->select();
		//转存
		foreach ($CashOrder as $k => $v) {
			$data[$i]['number']=$i;
			//用于区分MemberCash和Withdraw
			$data[$i]['type']='CashOrder';
			$data[$i]['order_money']=$v['order_money'];
			$data[$i]['order_charge']=$v['order_charge'];
			$data[$i]['card_bankname']=$v['card_bankname'];
			$data[$i]['order_creditcard']=substr($v['order_creditcard'],-4);
			$data[$i++]['order_update_time']=$v['order_update_time'];
		}
		// var_dump($data);die;
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
  	 #获取所有通道
  	#获取所有税率
  	$also=PassagewayItem::haswhere('passageway',['passageway_state'=>1])->select();


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
   		#还款列表
   		$repaymentList = MemberNovice::list(1);
   		$this->assign("repaymentList",$repaymentList);
   		#收款列表
   		$receivablesList = MemberNovice::list();
   		$this->assign("receivablesList",$receivablesList);
    	return view("api/logic/web_freshman_guide");
  }
  #信用卡说明
  public function card_description(){
  	return view("api/logic/card_description");
  }
}