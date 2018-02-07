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
use app\index\model\Exclusive ;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Generalize extends Common{

	 #素材列表
	 public function index()
	 {
	 	 $generalize=Generalizes::order("generalize_id desc")->paginate(12, false, ['query'=>Request::instance()->param()]);
	 	 $count=Generalizes::count();
	 	 $this->assign("count",$count);
	 	 $this->assign('button',['text'=>'新增素材', 'link'=>url('/index/generalize/creat'), 'modal'=>'modal']);
	 	 $this->assign('generalize',$generalize);
		 #渲染视图
		 return view('admin/Generalize/index');
	 }

	 #添加素材
	 public function creat()
	 {
	 	 if(Request::instance()->isPost())
	 	 {
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
	 	if($_POST){
	 		
	 		$result = Generalizes::saves(input());
	 		$content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/Generalize/index');die;
	 	}
		 $id = input("id");
		 // print_r(input());die;
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
	  * @Author   杨成志(3115317085@qq.com)
	  * [share 分享链接列表]
	  * @return [type] [description]
	  */
	public function share(){
		$share = Share::order("share_id desc")->paginate(12, false, ['query'=>Request::instance()->param()]);
		$count = Share::count();
		$this->assign("count",$count);
		$this->assign('button', 
 		 	 [
 		 		 ['text'=>'新增分享', 'link'=>url('/index/generalize/shareCreat'), 'modal'=>'modal'],
 		 	 ]);
		$this->assign("share",$share);
		#渲染视图
	 	return view("admin/Generalize/share");
	}
	/**
	 * @Author   杨成志(3115317085@qq.com)
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
	 * @Author   杨成志(3115317085@qq.com)
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
	/**
	* @author 杨成志（3115317085@qq.com）
	* @version shareedit 编辑注册邀请链接
	*
	*/
	public function shareedit(){
		if($_POST){
	 		if(empty($_POST['share_thumb'])){
	 			unset($_POST['share_thumb']);
	 		}
	 		$Share =Share::get(Request::instance()->param('share_id'));
			$result= $Share->allowField(true)->save($_POST);
	 		$content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/Generalize/share');die;
	 	}
		 $id = input("id");
		 $info = Share::where(["share_id" => $id])->find();
		 $this->assign("info",$info);
		 return view("admin/Generalize/shareedit");
	}
	/**
	* @author 杨成志（3115317085@qq.com)
	* @version exclusive_list 专属二维码列表
	*
	*/
	public function exclusive_list(){
		$Exclusive = Exclusive::order("exclusive_id desc")->paginate(12, false, ['query'=>Request::instance()->param()]);
		$count = Exclusive::order("exclusive_id desc")->count();
		$this->assign("count",$count);
		$this->assign('button', 
 		 	 [
 		 		 ['text'=>'新增专属', 'link'=>url('/index/generalize/exclusiveCreat'), 'modal'=>'modal'],
 		 	 ]);
		$this->assign("Exclusive",$Exclusive);
		#渲染视图
		return view("admin/Generalize/exclusive_list");
	}
	/**
	*@version exclusiveCreat 新增专属
	*@author 杨成志（3115317085@qq.com）
	*/
	#新增专属
	public function exclusiveCreat(){
		if(Request::instance()->isPost()){
	 		 $Exclusive = new Exclusive($_POST);
			 $result = $Exclusive->allowField(true)->save();
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'添加失败'] : ['type'=>'success','msg'=>'添加成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/Generalize/exclusive_list');
	 	}
		return view("admin/Generalize/exclusiveCreat");
	}
	/**
	*@version exclusiveedit 编辑专属详情
	*@author 杨成志（3115317085@qq.com）
	*/
	public function exclusiveedit(){
		if($_POST){	 		
	 		if(empty($_POST['exclusive_thumb'])){
	 			unset($_POST['exclusive_thumb']);
	 		}
	 		// dump($_POST['exclusive_thumb']);die;
	 		$Share =Exclusive::get(Request::instance()->param('exclusive_id'));
			$result= $Share->allowField(true)->save($_POST);
	 		$content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			$this->redirect('/index/Generalize/exclusive_list');die;
	 	}
		 $id = input("id");
		
		 $info = Exclusive::where(["Exclusive_id" => $id])->find();

		 $this->assign("info",$info);

		 return view("admin/Generalize/exclusiveedit");
	}
	/**
	*@version del_exclusive 删除专属详情
	*@author 杨成志 （3115317085@qq.com）
	*/
	public function del_exclusive(){
		$id = input("id");
		
		$delete = Exclusive::remove($id);
		$content = ($delete===false) ? ['type'=>'error','msg'=>'操作失败'] : ['type'=>'success','msg'=>'操作成功'];
 		Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect('/index/Generalize/exclusive_list');die;
	}
	/**
	*@version removeimg 删除字段多图一一删除
	*@author 杨成志（3115317085@qq.com ）
	*/
	public function removeimg(){
		#查出数据表列里的多图值
		$where['generalize_id'] = input("generalize_id");
		$generalize = Generalizes::where($where)->find();
		#把多图转换成数组
		$imgarr = explode( "#",$generalize['generalize_thumb']);
		#获取要删除图片的key值
		$imgkey = input("key");
		#删除图片
		@unlink(".".$imgarr[$imgkey]);
		unset($imgarr[$imgkey]);
		$data['generalize_thumb'] = implode("#", $imgarr);
		#修改数据库字段
		$result = Generalizes::where($where)->update($data);
		if($result){
			exit(json_encode(array("code"=>200,"msg"=>"操作成功","data" => $data['generalize_thumb'])));
		}else{
			exit(json_encode(array("code"=>400,"msg"=>"操作失败")));
		}
	}
}
