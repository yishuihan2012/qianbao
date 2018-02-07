<?php
/**
 *  @version Article controller / 新手指引分类
 *  @author $杨成志$(3115317085@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Article as Articles;
use app\index\model\ArticleCategory as ArticleCategorys;
use app\index\model\MemberNovice; 
use app\index\model\NoviceClass as NoviceClasss; 
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class NoviceClass extends Common{
	#新手列表分类首页
	public function index(){

		$NoviceClasss=NoviceClasss::where(array())->order('novice_class_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		$count = NoviceClasss::where(array())->count();
		$this->assign("count",$count);
 		$this->assign('button',['text'=>'新增新手指引分类', 'link'=>url('/index/novice_class/creat'), 'modal'=>'modal']);
 		 
		$this->assign("list",$NoviceClasss);
		return view("/admin/noviceClass/index");
	}
	#添加新手指引分类列表
	public function creat(){
		 if(Request::instance()->isPost())
	 	 {
		 	$NoviceClasss = new NoviceClasss($_POST);
			$result = $NoviceClasss->allowField(true)->save();
			 #数据是否提交成功
			$content = $result ? ['type'=>'success','msg'=>'添加成功'] : ['type'=>'warning','msg'=>'添加失败'];
			Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			$this->redirect('NoviceClass/index');
		 }
		 return view("/admin/noviceClass/creat");
	}
	#删除新手指引分类列表
	public function remove(){
		$content = array();

		if(!MemberNovice::where(["novice_class" => Request::instance()->param('id')])->find()){
			$result= NoviceClasss::destroy(Request::instance()->param('id'));
			$content = ($result===false) ? ['type'=>'error','msg'=>'删除失败'] : ['type'=>'success','msg'=>'删除成功'];
			
		}else{
			$content =  ['type'=>'error','msg'=>'请删除下级分类'];
		}
			Session::set('jump_msg', $content);
		$this->redirect('novice_class/index');
	}
}