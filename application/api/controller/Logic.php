<?php

namespace app\api\controller;
use think\Db;
use think\Controller;
use app\index\model\CustomerService;
use app\index\model\Share;
use app\index\model\Page;
use app\index\model\Generalize;
/**
 *  此处放置一些固定的web地址
 */
class Logic extends Controller
{
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
  }
}