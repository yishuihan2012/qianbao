<?php
/**
 *  @version Article controller / 文章控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Generalize as Generalizes;
use app\index\model\Share ;
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
	 /**
	  * @Author  杨成志(3115317085@qq.com)
	  * [edit 素材详情]
	  * @return [type] [description]
	  */
	public function edit(){
		$id = input("id");
		$info = Generalizes::edits($id);
		$this->assign("info",$info);	 	
		return view("admin/Generalize/edit");
	}
	 /**
	  * @Author   杨成志(3115317085@qq.com)
	  * [edit 删除素材]
	  * @return [type] [description]
	  */
	public  function remove(){
	 	$id = input("id");
	 	//删除操作
	 	$delete = Generalizes::remove($id);

 		$content = ($delete===false) ? ['type'=>'error','msg'=>'操作失败'] : ['type'=>'success','msg'=>'操作成功'];
 		Session::set('jump_msg', $content);
		#重定向控制器 跳转到列表页
		$this->redirect('/index/Generalize/index');die;
	}
	 /**
	  * [share 分享链接列表]
	  * @return [type] [description]
	  */
	public function share(){
		$share = Share::paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		$this->assign('button', 
 		 	 [
 		 		 ['text'=>'新增分享', 'link'=>url('/index/generalize/shareCreat'), 'modal'=>'modal'],
 		 	 ]);
		$this->assign("share",$share);
		#渲染视图
	 	return view("admin/Generalize/share");
	}
	/**
	 * [shareCreat 添加分享列表]
	 * @return [type] [description]
	 */
	public function shareCreat(){
		if(Request::instance()->isPost()){
	 		
	 		 $Share = new Share($_POST);
			 
			 $result = $Share->allowField(true)->save();
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'添加失败'] : ['type'=>'success','msg'=>'添加成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/Generalize/share');die;
	 	}
		return view('admin/Generalize/shareCreat');
	}
	/**
	 * [shareRemove 删除分享操作]
	 * @return [type] [description]
	 */
	public function shareRemove(){
		$id = input("id");
		$delete = Share::remove($id);
		$content = ($delete===false) ? ['type'=>'error','msg'=>'操作失败'] : ['type'=>'success','msg'=>'操作成功'];
 		Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect('/index/Generalize/share');die;
	}
}
