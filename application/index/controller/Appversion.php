<?php
/**
 *  @version Article controller / 版本升级控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Generalize as Generalizes;
use app\index\model\Share ;
use app\index\model\Exclusive;
use app\index\model\Appversion as Appversions;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Appversion extends Common{
	//app版本升级列表
	public function index(){
		$Appversions=Appversions::order("version_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		$this->assign("count",Appversions::count());
		$this->assign('button',['text'=>'新增版本号', 'link'=>url('/index/appversion/creat'), 'modal'=>'modal']);
	 	 $this->assign('Appversions',$Appversions);
		return view("admin/appversion/index");
	}
	#添加app版本号
	public function creat(){
		if(Request::instance()->isPost())
	 	 {
	 	 	 $where['version_type'] = $_POST['version_type'];
	 	 	 $data['version_state'] = 0;
	 	 	 $_POST['version_state'] = 1;

	 		 $Appversions = new Appversions($_POST);
	 		 $result = Appversions::where($where)->update($data);
			 $result = $Appversions->allowField(true)->save();
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'添加失败'] : ['type'=>'success','msg'=>'添加成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/appversion/index');die;
	 	 }
		return view("admin/appversion/creat");
	}
	#删除版本号
	public function remove(){
		$id = input("id");
	 	//删除操作
	 	$delete =  Appversions::where(["version_id" => $id])->delete();
	 	// dump(Appversions::getLastsql());die;
 		$content = ($delete===false) ? ['type'=>'error','msg'=>'操作失败'] : ['type'=>'success','msg'=>'操作成功'];
 		Session::set('jump_msg', $content);
		#重定向控制器 跳转到列表页
		$this->redirect('/index/appversion/index');die;
	}
}