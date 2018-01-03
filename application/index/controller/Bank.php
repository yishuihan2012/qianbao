<?php
/**
 *  @version Bank controller / 文章控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Bank as Banks;
use app\index\model\BankIdent;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Bank extends Common{
	 #银行列表
	 public function index()
	 {
	 	 #查询银行列表分页
	 	 $bank_list=Banks::order('bank_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
 		 $this->assign('button', ['text'=>'新增银行', 'remote'=>url('/index/bank/creat'), 'modal'=>'modal']);
 		 $this->assign('bank_list', $bank_list);
		 #渲染视图
		 return view('admin/bank/index');
	 }

	 #新增银行
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

			 $Banks = new Banks($_POST);
			 $result = $Banks->allowField(true)->save();
			 $content = ($result===false) ? ['type'=>'error','msg'=>'保存失败'] : ['type'=>'success','msg'=>'保存成功'];

			  Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/bank/index');die;
	 	 }
	 	 return view('admin/bank/creat');
	 }
	 //修改银行信息
	 public function bankSave(){
	 	if(Request::instance()->isPost()){
		 	 $Banks =Banks::get(Request::instance()->param('bank_id'));
		 	 // dump($_POST);die;
			 $result= $Banks->allowField(true)->save($_POST);
			 $content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			 Session::set('jump_msg', $content);
			 $this->redirect('bank/index');
			 exit;
		 }
		
		$info = Banks::get(Request::instance()->param('bank_id'));
		
		$this->assign("info",$info);
	
	 	 return view('admin/bank/bankSave');
	 }
	 public function bankRemove(){
	 	 $Banks = Banks::get(Request::instance()->param('id'));
		 $result = $Banks->delete(0);
		 #数据是否提交成功
		 $content = ($result===false) ? ['type'=>'error','msg'=>'删除失败'] : ['type'=>'success','msg'=>'文章删除成功'];
		 Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect('bank/index');
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



	 #银行卡号自动识别银行 识别银行总览
	 public function ident()
	 {
	 	 #查询可识别银行列表分页
	 	 $bank_list=BankIdent::order('ident_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
 		 $this->assign('bank_list', $bank_list);
		 #渲染视图
		 return view('admin/bank/ident');
	 }
}
