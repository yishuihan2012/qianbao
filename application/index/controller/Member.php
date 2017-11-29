<?php
/**
 *  @version Article controller / 文章控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Member as Members;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Member extends Common{
	 #会员列表
	 public function index()
	 {
	 	$where="";
	 	$member=Members::where($where)->order('member_creat_time','desc')->paginate();
	 	$this->assign('member', $member);
	 	$members=Members::get(1);
	 	var_dump($members->membercertification['certification_creat_time']);die;
		#渲染视图
		return view('admin/member/index');
	 }

}
