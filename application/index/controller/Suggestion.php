<?php
/**
 *  @version Suggestion controller / 反馈控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\MemberSuggestion;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Suggestion extends Common{
	 #会员反馈
	 public function index()
	 {
	 	$lists=MemberSuggestion::paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	// $lists=MemberSuggestion::get(1);
	 	// var_dump($lists->member());die;
	 	$this->assign('lists', $lists);
	 	$this->assign('count', count($lists));
		 #渲染视图
		return view('admin/suggestion/index');
	 }

	 public function edit()
	 {
	 	$data=MemberSuggestion::get(Request::instance()->param('id'),'member');
	 	if(Request::instance()->isPost())
	 	{
	 		$suggestion=MemberSuggestion::get(Request::instance()->param('id'));
	 		$result= $suggestion->allowField(true)->save($_POST);
	 		$content = ($result===false) ? ['type'=>'error','msg'=>'文章修改失败'] : ['type'=>'success','msg'=>'文章修改成功'];
			Session::set('jump_msg', $content);
			#重定向控制器 跳转到列表页
			$this->redirect($this->history['1']);
	 	}
	 	// var_dump($data);die;
	 	$this->assign('data', $data);
		 #渲染视图
		return view('admin/suggestion/edit');
	 }


	 public function remove()
	 {
	 	 $MemberSuggestion = MemberSuggestion::get(Request::instance()->param('id'));
		 $result = $MemberSuggestion->delete();
		 #数据是否提交成功
		 $content = ($result===false) ? ['type'=>'error','msg'=>'删除失败'] : ['type'=>'success','msg'=>'删除成功'];
		 Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect('index');
	 }

}
