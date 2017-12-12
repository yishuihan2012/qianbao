<?php
/**
 *  @version Member controller / 会员基本信息控制器
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
	 	 #获取会员列表 
	 	 $member_list=Members::with('memberLogin,membergroup')->order('member_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	 $this->assign('member_list', $member_list);
		 #渲染视图
		 return view('admin/member/index');
	 }

	 #会员详细信息
	 public function info(Request $request)
	 {
	 	 if(!$request->param('id'))
	 	 {
			 Session::set('jump_msg', ['type'=>'error','msg'=>'参数错误']);
			 $this->redirect($this->history['1']);
	 	 }
	 	 #查询到当前会员的基本信息
	 	 $member_info=Members::with('memberLogin')->find($request->param('id'));
	 	 // var_dump($member_info);die;
	 	 $this->assign('member_info', $member_info);
	 	 return view('admin/member/info');
	 }

}