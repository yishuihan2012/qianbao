<?php
/**
 *  @version Passageway controller / 费率编码控制器
 *  @author 杨成志(3115317085@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Passageway as Passageways;
use app\index\model\PassagewayItem;
use app\index\model\PassagewayRate as  PassagewayRates;
use app\index\model\MemberGroup;
use app\index\model\Cashout;
use app\index\model\CreditCard;
use app\index\model\Member;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;
use think\Db;

class PassagewayRate extends Common{
	/**
	* @version index 费率编码列表
	* @author 杨成志（3115317085@qq.com）
	*/
	public function index(){
		$list = PassagewayRates::with("passageway")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		$this->assign('button', ['text'=>'新增费率编码', 'link'=>url('/index/passageway_rate/creat'), 'modal'=>'modal']);
		$this->assign('list',$list);
		return view("admin/passageway_rate/index");
	}
	/**
	* @version creat 创建编码
	*@author 杨成志（3115317085@qq.com）
	*/
	public function creat(){
		// dump(1);die;
		if(Request::instance()->isPost())
	 	 {
	 		 $PassagewayRates = new PassagewayRates($_POST);
			 $result = $PassagewayRates->allowField(true)->save();
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'添加失败'] : ['type'=>'success','msg'=>'添加成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/passageway_rate/index');die;
	 	 }
		$passageway = PassagewayS::all();
		$this->assign("passageway",$passageway);
		return view("admin/passageway_rate/creat");
	}
	/**
	* @version creat 删除编码
	* @author 杨成志（3115317085@qq.com）
	*/
	public function remove(){
		$PassagewayRates = PassagewayRates::get(Request::instance()->param('id'));
		 $result = PassagewayRates::where(['rate_id' => Request::instance()->param('id')])->delete();
		 #数据是否提交成功
		 $content = ($result===false) ? ['type'=>'error','msg'=>'操作失败'] : ['type'=>'success','msg'=>'操作成功'];
		 Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect('/index/passageway_rate/index');
	}

}