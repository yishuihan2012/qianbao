<?php
/**
 *  @version ServerModel controller / 自定义模块控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
 namespace app\index\controller;
 use app\index\model\ServiceItem;
 use app\index\model\ServiceItemList;
 use think\Controller;
 use think\Request;
 use think\Session;
 use think\Config;
 use think\Loader;

 class ServerModel extends Common
 {
	 #自定义模块列表 
	 public function index(){
		 $list = ServiceItem::paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		 $this->assign('list', $list);
		 $this->assign('button', ['text'=>'新增模块', 'link'=>url('/index/server_model/add_model'), 'modal'=>'modal']);
		 #渲染视图
		 return view('admin/server/services_item');
	 }

	 #增加自定义模块
	 public function add_model(){
		 if(Request::instance()->isPost()){
		 	 $ServiceItem = new ServiceItem($_POST);
			 $result = $ServiceItem->allowField(true)->save();
			 $content = ($result===false) ? ['type'=>'error','msg'=>'保存失败'] : ['type'=>'success','msg'=>'保存成功'];
			 Session::set('jump_msg', $content);
			 $this->redirect('server_model/index');
		 }
		 #渲染视图
		 return view('admin/server/add_model');
	 }

	 #编辑自定义模块
	 public function edit_model(){
		 if($_POST){
			 $result = ServiceItem::saves(input());
			 $content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			 Session::set('jump_msg', $content);
			 $this->redirect('ServerModel/index');
		 }
		 $id = input("item_id");
		 $info = ServiceItem::info($id);
		 $this->assign("info",$info);
		 return view('admin/server/edit_service_item');
	 }

	 //删除自定义模块
	 public function del_model(){
		 $id = input("item_id");
		 $find = ServiceItemList::info($id);
		 if($find){
			 $content = ['type'=>'error','msg'=>'请删除下级子类'] ;
			 print_r($content);
		      Session::set('jump_msg', $content);
		      $this->redirect('ServerModel/index');
		      return ;
		 }
		 $delete = ServiceItem::remove($id);
		 $content = ($delete===false) ? ['type'=>'error','msg'=>'删除失败'] : ['type'=>'success','msg'=>'删除成功'];
		 Session::set('jump_msg', $content);
		 $this->redirect('ServerModel/index');
	 }



	 #自定义模块服务列表 
	 public function service_list(){
		 $ServiceItemList=new ServiceItemList();
		 $list=ServiceItemList::with('serverItem')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		 $this->assign('button', ['text'=>'新增服务', 'link'=>url('/index/server_model/add_service')]);
		 $this->assign('list',$list);
		 return view('admin/server/service_list');
	 }

	 #查看自定义模块服务列表 
	 public function show_service(){
		 if(Request::instance()->isPost()){
		 	 $ServiceItemList =ServiceItemList::get(Request::instance()->param('list_id'));
			 $result= $ServiceItemList->allowField(true)->save($_POST);
			 $content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			 Session::set('jump_msg', $content);
			 $this->redirect('server_model/service_list');
			 exit;
		 }
		 $data=ServiceItemList::where('list_id='.Request::instance()->param('list_id'))->find();
		 $service=ServiceItem::all();
		 $this->assign('service', $service);
		 $this->assign('data', $data);
		 #渲染视图
		 return view('admin/server/show_service');
	 }

	 #增加自定义模块服务列表 
	 public function add_service(){

		 if(Request::instance()->isPost()){

		 	 $ServiceItemList = new ServiceItemList($_POST);
			 $result = $ServiceItemList->allowField(true)->save();
			 $content = ($result===false) ? ['type'=>'error','msg'=>'保存失败'] : ['type'=>'success','msg'=>'保存成功'];
			 Session::set('jump_msg', $content);
			 $this->redirect('server_model/service_list');
		 }
		 $service=ServiceItem::all();
		 $this->assign('service', $service);
		 #渲染视图
		 return view('admin/server/add_service');
	 }

	 #删除自定义模块服务列表 
	 public function del_service()
	 {
		 $result= ServiceItemList::destroy(Request::instance()->param('list_id'));
		 $content = ($result===false) ? ['type'=>'error','msg'=>'删除失败'] : ['type'=>'success','msg'=>'删除成功'];
		 Session::set('jump_msg', $content);
		 $this->redirect('server_model/service_list');
	 }

 }
