<?php

namespace app\api\controller;
use think\Db;
use think\Config;
use think\Loader;
use think\Controller;
use app\index\model\Member as Members;
use app\index\model\MemberCash;
use app\index\model\Withdraw;
use app\index\model\CashOrder;
use app\index\model\Recomment;
use app\index\model\Announcement;
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
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:01:55+0800
	 * @version  [专属二维码]
	 * @return   [type]
	 */
	public function exclusive_code(){
		$this->checkToken();
		//获取当前手机号
		$tel=Members::get($this->param['uid'])->value('member_mobile');

		Vendor('phpqrcode.phpqrcode');
		//推广连接
		$url='http://gongke.iask.in:21339/api/userurl/register/recomment/'.$tel;
		$QRcode=new \QRcode();
		//生成二维码
		$QRcode->png($url, 'autoimg\qrcode'.$tel.'.png');
		$qrurl=ROOT_PATH.'public\autoimg\qrcode'.$tel.'.png';
		//背景
		$bg=ROOT_PATH.'public\static\images\exclusice_code_bg.png';
		//合成专属二维码
		 $QR = imagecreatefromstring(file_get_contents($qrurl)); 
		 $bg = imagecreatefromstring(file_get_contents($bg)); 
		 $QR_width = imagesx($QR);//二维码图片宽度 
		 $QR_height = imagesy($QR);//二维码图片高度 
		 $bg_width = imagesx($bg);
		 $bg_height = imagesy($bg);
		 $bg_qr_width = $QR_width / 5; 
		 $scale = $bg_width/$bg_qr_width; 
		 $bg_qr_height = $bg_height/$scale; 
		 //重新组合图片并调整大小 
		 imagecopyresampled( $bg,$QR, 210, 700, 0, 0, 255, 
		 255, $QR_width, $QR_height); 
		//输出图片 
		imagepng($bg, 'autoimg\qrcode'.$tel.'.png'); 
		$url='/autoimg\qrcode'.$tel.'.png';
		$this->assign('url',$url);
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [用户还款计划]
	 * @return   [type]
	 */
	public function repayment_plan_list(){
		$this->checkToken();
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [还款计划已完成列表]
	 * @return   [type]
	 */
	public function repayment_history(){
		$this->checkToken();
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [还款计划详情]
	 * @return   [type]
	 */
	public function repayment_plan_detail(){
		$this->checkToken();
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [消息]
	 * @return   [type]
	 */
	public function notify(){
		$this->checkToken();
		return $this->fetch();
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
		return $this->fetch();
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
		return $this->fetch();
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
		$CashOrder=CashOrder::with('bankcard')->where(['order_member'=>$this->param['uid'],'order_state'=>2])->select();
		//转存
		foreach ($CashOrder as $k => $v) {
			$data[$i]['number']=$i;
			//用于区分MemberCash和Withdraw
			$data[$i]['type']='CashOrder';
			$data[$i]['order_money']=$v['order_money'];
			$data[$i]['order_charge']=$v['order_charge'];
			$data[$i]['card_bank']=$v['card_bank'];
			$data[$i]['order_card']=substr($v['order_card'],-4);
			$data[$i++]['order_update_time']=$v['order_update_time'];
		}
		$this->assign('data',$data);
		return $this->fetch();
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
		return $this->fetch();
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
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [下载页面]
	 * @return   [type]
	 */
	public function download(){
		return $this->fetch();
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
   * [share_link_list 分享下载链接列表]
   * @return [type] [description]
   */
  public function share_link_list(){
    $list = Share::sharelist();
    $this->assign("list",$list);
    return view("api/logic/share_link_list");
  }
  /**
   * @Author   杨成志(3115317085@qq.com)
   * [share_link 分享下载链接]
   * @return [type] [description]
   */
  public function share_link(){
    return view("api/logic/share_link");
  }}