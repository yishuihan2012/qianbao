<?php
/**
 *  @version ArticleCategory controller / 文章分类器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-25 10:16:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Article as Articles;
use app\index\model\ArticleCategory as ArticleCategorys;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class ArticleCategory extends Common{
	 #分类列表
	 public function index()
	 {
	 	 $array=ArticleCategorys::all();
	 	 $tree = $this->getTree($array,'0');
	 	 if($tree)
	 	 	$tree=collection($tree)->toArray();
 		 $this->assign('tree', $tree);

 		 $this->assign('button', 
 		 	 [
 		 		 ['text'=>'新增分类', 'link'=>url('category_creat')],
 		 	 ]);
		 #渲染视图
		 return view('admin/article_category/index');
	 }

	 #新增分类
	 public function category_creat()
	 {
	 	 if(Request::instance()->isPost())
	 	 {
			 #验证器验证 触发Add事件验证
			 #$validate = Loader::validate('VillageValidate');
			 #如果验证不通过
			 #if(!$validate->check($_POST)){
			      #绑定表单值
				 #$this->assign('category', Request::instance()->Post());
				 #传回错误信息
				 #$this->assign('errormsg', $validate->getError());
				 #加载提交视图
				 #return view('admin/category/creat');
				 #exit;
			 #}
			 #验证器验证成功
			 $articlecategory = new ArticleCategorys($_POST);
			 $result = $articlecategory->allowField(true)->together('article')->save($_POST);
			 #数据是否提交成功
			 $content = $result ? ['type'=>'success','msg'=>'分类添加成功'] : ['type'=>'warning','msg'=>'分类添加失败'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('index');
	 	 }
	 	 #获取到所有一级分类
 		 $category_list=ArticleCategorys::where('category_parent','0')->select();
	 	 $this->assign('category_list', $category_list);
 		 $this->assign('button', ['text'=>'返回列表', 'link'=>$this->history['2'],'icon'=>'arrow-left']);
	 	 return view('admin/article_category/category_creat');
	 }

	 #修改分类
	 public function category_edit(Request $request)
	 {

	 	 #获取到所有一级分类
 		 $category_list=ArticleCategorys::where('category_parent','0')->select();
 		 $category=ArticleCategorys::get(Request::instance()->param('id'));
		 #提交更改信息
		 if(Request::instance()->isPost())
		 {
			 #验证器验证 触发Add事件验证
			 #$validate = Loader::validate('VillageValidate');
			 #如果验证不通过
			 #if(!$validate->check($_POST)){
				 #传递当前信息源去视图
				 #$this->assign('category', $categoryArray);
	 	 		 #$this->assign('category_list', $category_list);
		 		 #$this->assign('secend_category', $secend_category);
			      #传回错误信息
			      #$this->assign('errormsg', $validate->getError());
			      #加载提交视图
			      #return view('admin/category/edit');
			    	 #exit;
			 #}
			 $ArticleCategorys =ArticleCategorys::get(Request::instance()->param('id'));
			 $result= $ArticleCategorys->allowField(true)->together('article')->save($_POST);
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'分类修改失败'] : ['type'=>'success','msg'=>'分类修改成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('index');
		 }
		 #传递当前信息源去视图
	 	 $this->assign('category_list', $category_list);
	 	 $this->assign('category', $category);
	 	 $this->assign('button', ['text'=>'返回列表', 'link'=>$this->history['2'],'icon'=>'arrow-left']);
		 return view('admin/article_category/category_creat');
	 }

	 #删除分类
	 public function remove()
	 {
	 	 $ArticleCategorys = ArticleCategorys::get(Request::instance()->param('id'));
		 $result = $ArticleCategorys->delete();
		 #数据是否提交成功
		 $content = ($result===false) ? ['type'=>'error','msg'=>'分类删除失败'] : ['type'=>'success','msg'=>'分类删除成功'];
		 Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect('index');
	 }

	 #获取一级分类下的二级分类
	 public function getTree($data, $pId){
	 	 $tree = '';
	 	 foreach($data as $k => $v){
	   	 	 if($v['category_parent'] == $pId){         //父亲找到儿子
	    		 	 $v['category_parent'] =self::getTree($data, $v['category_id']);
	    			 $tree[] = $v;
	    			 //unset($data[$k]);
	   	 	 }
		 }
		 return $tree;
	 }
}
