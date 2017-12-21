<?php
/**
 *  @version Article controller / 文章控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Generalize as Generalizes;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Generalize extends Common{
	 #素材列表
	 public function index()
	 {
	 	 $generalize=Generalizes::paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);

	 	 $this->assign('button', 
 		 	 [
 		 		 ['text'=>'新增素材', 'link'=>url('/index/generalize/creat'), 'modal'=>'modal'],
 		 	 ]);
	 	 $this->assign('generalize',$generalize);
		 #渲染视图
		 return view('admin/Generalize/index');
	 }

	 #添加素材
	 public function creat(){
	 	if(Request::instance()->isPost()){
	 		var_dump($_POST);die;
	 		 $Generalizes = new Generalizes($_POST);
			 
			 $result = $Generalizes->allowField(true)->save();
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'添加失败'] : ['type'=>'success','msg'=>'添加成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/Generalize/index');die;
	 	}

	 	 #渲染视图
		 return view('admin/Generalize/creat');
	 }

}
