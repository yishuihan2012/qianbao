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
	#新手列表首页
	public function index(){

		$NoviceClasss=NoviceClasss::where(array())->order('novice_class_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	
 		 	 	$this->assign('button',['text'=>'新增新手指引分类', 'link'=>url('/index/novice_class/creat'), 'modal'=>'modal']);
 		 
		$this->assign("list",$NoviceClasss);
		return view("/admin/noviceClass/index");
	}
	#添加新手指引分类列表
	public function creat(){
		return view("/admin/noviceClass/creat")
	}
}