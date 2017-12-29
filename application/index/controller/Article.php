<?php
/**
 *  @version Article controller / 文章控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Article as Articles;
use app\index\model\ArticleCategory as ArticleCategorys;
use app\index\model\MemberNovice; 
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Article extends Common{
	 #文章列表
	 public function index()
	 {
	 	 $where=array();
	 	 $son_category=array();
	 	 if(Request::instance()->isGet() && Request::instance()->param('article_parent')!='')
	 	 {
	 	 	 Request::instance()->param('article_parent')=='0' ? :  $where['article_parent']=Request::instance()->param('article_parent');
	 	 	 Request::instance()->param('article_category')=='0' ? : $where['article_category']=Request::instance()->param('article_category');
	 	 }
	 	 #查询文章列表分页
	 	 $article_list=Articles::where($where)->order('article_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	 #统计文章信息
	 	 $count['count_size']=Articles::where($where)->order('article_id','desc')->count();
	 	 #获取到所有一级分类
 		 $category_list=ArticleCategorys::where('category_parent','0')->select();
 		 #查询出当前一级分类的二级分类
 		 if(isset($where['article_parent']) && $where['article_parent'])
 		 	$son_category=ArticleCategorys::where('category_parent',$where['article_parent'])->select();
 		 $this->assign('button', 
 		 	 [
 		 	 	 ['text'=>'分类管理', 'link'=>url('/index/article_category/index'),'icon'=>'tags','theme'=>'info'],
 		 		 ['text'=>'新增文章', 'link'=>url('/index/article/creat')],
 		 	 ]);
 		 $this->assign('category_list', $category_list);
 		 $this->assign('son_category', $son_category);
 		 $this->assign('article_list', $article_list);
 		 $this->assign('count', $count);
 		 $this->assign('where', $where);
		 #渲染视图
		 return view('admin/article/index');
	 }

	 #新增文章
	 public function creat()
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
			 $article = new Articles($_POST);
			 $article->articleData = ['data_text' => $_POST['data_text']];
			 $result = $article->allowField(true)->together('articleData')->save();
			 #数据是否提交成功
			 $content = $result ? ['type'=>'success','msg'=>'文章添加成功'] : ['type'=>'warning','msg'=>'文章添加失败'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('Article/index');
	 	 }
	 	 #获取到所有一级分类
 		 $category_list=ArticleCategorys::where('category_parent','0')->select();
	 	 $this->assign('category_list', $category_list);
 		 $this->assign('button', ['text'=>'返回列表', 'link'=>$this->history['2'],'icon'=>'arrow-left']);
	 	 return view('admin/article/creat');
	 }

	 #修改文章
	 public function edit(Request $request)
	 {
		 #获取到详细信息
		 $articleArray = Articles::get(Request::instance()->param('id'),'articleData');
	 	 #获取到所有一级分类
 		 $category_list=ArticleCategorys::where('category_parent','0')->select();
 		 #获取到所有二级分类
 		 $secend_category=ArticleCategorys::where('category_parent', $articleArray['article_parent'])->select();
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
			 $article =Articles::get(Request::instance()->param('id'));
			 $result= $article->allowField(true)->save($_POST);
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'文章修改失败'] : ['type'=>'success','msg'=>'文章修改成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect($this->history['1']);
		 }
		 #传递当前信息源去视图
		 $this->assign('article', $articleArray);
	 	 $this->assign('category_list', $category_list);
	 	 $this->assign('secend_category', $secend_category);
	 	 $this->assign('button', ['text'=>'返回列表', 'link'=>$this->history['2'],'icon'=>'arrow-left']);
		 return view('admin/article/creat');
	 }

	 #删除文章
	 public function remove()
	 {
	 	 $article = Articles::get(Request::instance()->param('id'));
		 $result = $article->delete();
		 #数据是否提交成功
		 $content = ($result===false) ? ['type'=>'error','msg'=>'文章删除失败'] : ['type'=>'success','msg'=>'文章删除成功'];
		 Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect($this->history['1']);
	 }

	 #获取一级分类下的二级分类
	 public function getCategory()
	 {
	 	$category_list=ArticleCategorys::where('category_parent', Request::instance()->param('id'))->select();
	 	echo json_encode($category_list);
	 }
	 #新手指引
	 public function memberNovice(){

	 	 $MemberNovice=MemberNovice::paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
	 	 $this->assign('button', ['text'=>'新增文章', 'link'=>url('/index/article/noviceCreat')]);
	 	 $this->assign('MemberNovice',$MemberNovice);
	 	
	 	 return view('admin/article/memberNovice');
	 }
	#新增新手指引
	public function noviceCreat(){
	 	if(Request::instance()->isPost()){

	 		 $MemberNovice = new MemberNovice($_POST);
			 $result = $MemberNovice->allowField(true)->save();

			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'添加失败'] : ['type'=>'success','msg'=>'添加成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/article/memberNovice');die;
	 	}
	 	return view('admin/article/noviceCreat');
	}
}
