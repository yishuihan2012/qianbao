<?php

namespace app\api\controller;
use think\Db;
use think\Controller;
 use app\index\model\CustomerService;
/**
 *  此处放置一些固定的web地址
 */
class Logic extends Controller
{
  /**
   * @Author   Star(794633291@qq.com)
   * @DateTime 2017-12-25T14:01:55+0800
   * @version  [用户注册协议]
   * @return   [type]
   */
  public function web_user_register_protocol(){
    return view("api/logic/web_user_register_protocol");
  }
  /**
   * @Author   Star(794633291@qq.com)
   * @DateTime 2017-12-25T14:10:55+0800
   * @version  [推广素材库]
   * @return   [type]
   */
  public function web_marketing_media_library(){
    return view("api/logic/web_marketing_media_library");
  }
  /**
   * @Author   Star(794633291@qq.com)
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
   * @Author   Star(794633291@qq.com)
   * @DateTime 2017-12-25T14:10:55+0800
   * @version  [还款计划详情]
   * @return   [type]
   */
  public function repayment_plan_detail(){
    return $this->fetch();
  }
}