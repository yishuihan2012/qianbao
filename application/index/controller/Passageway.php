<?php
/**
 *  @version Passageway controller / 通道控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Passageway as Passageways;
use app\index\model\PassagewayItem;
use app\index\model\MemberGroup;
use app\index\model\Cashout;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;
use think\Db;

class Passageway extends Common{
	 #通道列表
	 public function index()
	 {
	 	 #查询通道列表分页
	 	 $passageway=Passageways::order('passageway_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
 		 $this->assign('button', ['text'=>'新增通道', 'link'=>url('/index/passageway/creat'), 'modal'=>'modal']);
 		 $this->assign('passageway_list', $passageway);
		 #渲染视图
		 return view('admin/passageway/index');
	 }

	 #通道对会员组税率调整
	 public function rate()
	 {
	  	 #获取通道
	  	 if(!Request::instance()->param('id'))
	  	 	 return '参数错误';
	 	 if(Request::instance()->isPost())
	 	 {
	 	 	 #获取提交的数据
	 	 	 $post=Request::instance()->post();
	 	 	 $result=true;
	 	 	 foreach ($post as $key => $value) {
	 	 		 #拆分Key键
	 	 	 	 $group_id=strrev(strstr(strrev($key),strrev('_'),true));
	 	 	 	 $key_fix=strtok($key,'_');
	 	 	 	 $field='item_'.$key_fix;
	 	 	 	 #查询库中是否存在本条数据
	 	 	 	 $passage=PassagewayItem::where(['item_passageway'=>Request::instance()->param('id'),'item_group'=>$group_id])->find();
	 	 	 	 if($passage){
	 	 	 	 	 #如果存在的话 对比一下之前和之后的值 如果不一致 则进行修改
	 	 	 	 	 if($passage[$field]!=$value)
	 	 	 	 	 	 PassagewayItem::where(['item_passageway'=>Request::instance()->param('id'),'item_group'=>$group_id])->setField($field, $value);
	 	 	 	 }else{
	 	 	 	 	 $data=array(
	 	 	 	 	 	'item_passageway'	=>Request::instance()->param('id'),
	 	 	 	 	 	'item_group'			=>$group_id,
	 	 	 	 	 	'item_rate'			=>$post['rate_'.$group_id],
	 	 	 	 	 	'item_also'			=>$post['also_'.$group_id]
	 	 	 	 	 );
	 	 	 	 	 $newpass=new PassagewayItem($data);
	 	 	 	 	 $result = $newpass->allowField(true)->save();
	 	 	 	 }
	 	 	 	 if(!$result)
	 	 	 	 	 continue;
	 	 	 }



		 	 $content = $result ? ['type'=>'success','msg'=>'税率调整成功'] : ['type'=>'warning','msg'=>'税率调整失败'];
		 	 Session::set('jump_msg', $content);
		 	 $this->redirect($this->history['0']);
	 	 }
	  	 #查询出当前通道对会员组的原始税率
	  	 $list=PassagewayItem::where('item_passageway',Request::instance()->param('id'))->select();
	  	 $this->assign('list', $list);
	  	 #查询出所有的用户组
	  	 $group=MemberGroup::all();
	  	 $this->assign('group', $group);
	  	 $this->assign('id', Request::instance()->param('id'));
	  	 return view('admin/passageway/rate');
	 }

	 #新增通道
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

	 	 			$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符
					$strlen = 52;
					while(5 > $strlen){
						$str .= $str;
						$strlen -= 52; 
					}
					$str = str_shuffle($str);
					$alert=substr($str,0,5);

					$_POST['passageway_no']=$alert;


	 	 		$sql="ALTER TABLE `wt_member_net` ADD `".$_POST['passageway_no']."` varchar(255) COMMENT '".$_POST['passageway_name']."'";
	 	 		Db::query($sql);



			 $passageway = new Passageways($_POST);
			 $result = $passageway->allowField(true)->save();
			 #数据是否提交成功
			 $content = $result ? ['type'=>'success','msg'=>'通道添加成功'] : ['type'=>'warning','msg'=>'通道添加失败'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('Passageway/index');
	 	 }
	 	 return view('admin/passageway/creat');
	 }


	 #修改通道
	 public function edit(Request $request)
	 {
	 	#获取到详细信息
		 $passageways = Passageways::get(Request::instance()->param('id'));
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
			 $Passageways =Passageways::get(Request::instance()->param('id'));
			 $result= $Passageways->allowField(true)->save($_POST);
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/Passageway/index');
		 }
		 #传递当前信息源去视图
		 $this->assign('passageways', $passageways);
		 return view('admin/passageway/edit');
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

	 #提现设置
	 public function cashout(){
	 	$data = Cashout::with('passageway')->where('cashout_passageway_id='.Request::instance()->param('id'))->find();

	 	if(empty($data)){
	 		$data=array(
	 			'cashout_passageway_id'=>Request::instance()->param('id'),
	 			'cashout_add_time'=>date("Y-m-d H:i:s",time())
	 		);
	 		$result= Cashout::insert($data);
	 	}elseif(Request::instance()->isPost()){
	 		if(Request::instance()->param('cashout_open')!=1){
	 			$_POST['cashout_open']=0;
	 		}
	 		$cashout =Cashout::get(Request::instance()->param('cashout_id'));

			 $result= $cashout->allowField(true)->save($_POST);
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/Passageway/index');die;
	 	}

	 	$data = Cashout::with('passageway')->where('cashout_passageway_id='.Request::instance()->param('id'))->find();

	 	$this->assign('data',$data);
	 	return view('admin/Passageway/cashout');
	 }
}
