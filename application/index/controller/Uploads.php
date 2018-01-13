<?php
/**
 *  @version Uploads controller / 图片上传控制器logo
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
 use think\Db;
use app\index\model\Withdraw;
use app\index\model\MemberGroup;
use app\index\model\WalletLog;
use app\index\model\CashOrder;
use app\index\model\Recomment;
use app\index\model\Member;
use app\index\model\Wallet;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Uploads extends Common{
	 public function index()
	 {
	 	$group=MemberGroup::select();
	 	$this->assign('group',$group);

	 	if(Request::instance()->isPost())
	 	 {

	 	 	 $data['group_thumb']=$_POST['img'];
	 	 	 $data['group_id']=$_POST['group_id'];
			 $MemberGroup =MemberGroup::get(Request::instance()->param('group_id'));
			 $result= $MemberGroup->allowField(true)->save($data);
			 // var_dump($result);die;
			 #数据是否提交成功
			 $content = $result ? ['type'=>'success','msg'=>'修改成功'] : ['type'=>'warning','msg'=>'修改失败'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('index/index');
	 	 }
	 	
		 #渲染视图
		return view('admin/uploads/index');
	 }

	  public function logo()
	 {	 	
		 #渲染视图
		return view('admin/uploads/logo');
	 }
}
