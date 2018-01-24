<?php
/** 
*  @version 管理员控制器
 * @author  $bill 755969423@qq.com
 * @time      2017-11-22 16:06
 * @return  index 管理员列表
 */
namespace app\index\controller;

use think\Config;
use think\Request;
use think\Session;
use think\View;
use app\index\model\Member;
use app\index\model\System;
use app\index\model\Adminster as Adminsters;
use app\index\model\AuthGroup as AuthGroups;
use app\index\model\AuthGroupAccess as AuthGroupAccesss;
class Adminster extends Common {
	 #管理员列表
	 public function index(Request $params)
	 {
	 	// halt($this->adminster['adminster_group_id']);
		 $query=[];
		 $where=[];
		 $groups=[];
		 $params=Request::instance()->param();
		 !empty($params['login']) ? $where['adminster_login']=["like","%".$params['login']."%"] : $params['login']="";
		 !empty($params['email']) ? $where['adminster_email']=["like","%".$params['email']."%"] : $params['email']="";
		 !empty($params['state']) ? $where['adminster_state']=$params['state'] :  $params['state']="";
		 !empty($params['group']) ? $groups['group_id']=$params['group'] : $params['group']="";
		 $params['page']=Request::instance()->param('page') ? : 1;
		 #运营商用户组
		 if($this->admin['adminster_group_id']==5){
		 	$where['adminster_user_id']=['in',$this->admin['children']];
		 }
		 $adminster_list= new Adminsters();
		 $adminster_list= $adminster_list->with('profile')->where($groups)->where($where)->order("adminster_id desc")->paginate(Config::get('page_size'), false, ['query'=>$params]);
		 $groupLists=AuthGroups::all();
		 $count = Adminsters::with('profile')->where($groups)->where($where)->count();
		 $this->assign("count",$count);
		 $this->assign('show',$adminster_list->render());
		 $this->assign('params',$params);
		 $this->assign('groupLists',$groupLists);
		 $this->assign('adminster_list',$adminster_list->toArray());
		 $this->assign('button', 
 		 	 [
 		 	 	 // ['text'=>'用户口令', 'link'=>url('/index/adminster/adminster_key'),'modal'=>'modal','icon'=>'tags','theme'=>'info'],
 		 		 ['text'=>'新增管理','link'=>url('/index/adminster/add')],
 		 	 ]);
		 return view('admin/adminster/index');
	 }
	 #管理员新增
	 public function add(){
		 if(Request::instance()->isPost()){
		 	$r=request()->param();
			 $code=make_rand_code();
			 
			 #运营商添加的用户强制为 运营商用户组
			 if($this->admin['adminster_group_id']==5){
			 	$r['login_group']=5;
			 }

			 $check_login_name=Adminsters::get(['adminster_login'=>Request::instance()->post('login_name')]);
			 if($check_login_name){
				 Session::set('jump_msg',['type'=>'warning','msg'=>'用户名已存在~请重试','data'=>'']);
				 $this->redirect($this->history['0']);
			 }
			 $check_login_email=Adminsters::get(['adminster_email'=>Request::instance()->post('login_email')]);
			 if($check_login_email){
			     	 Session::set('jump_msg',['type'=>'warning','msg'=>'邮箱已绑定其他账号~请更换','data'=>'']);
				 $this->redirect($this->history['0']);
			 }
			 $adminster=new Adminsters;
			 if($r['login_group']==5){
			 	if($r['adminster_user_id']){
			 		$admin=Adminsters::get(['adminster_user_id'=>$r['adminster_user_id']]);
			 		if($admin){
				     	Session::set('jump_msg',['type'=>'warning','msg'=>'该用户已被'.$admin['adminster_login'].'绑定，请更换用户或为'.$admin['adminster_login'].'解除绑定','data'=>'']);
						$this->redirect($this->history['0']);
			 		}else{
			 			$adminster->adminster_user_id=$r['adminster_user_id'];
			 		}
			 	}else{
			     	Session::set('jump_msg',['type'=>'warning','msg'=>'请选择该渠道商对应的用户','data'=>'']);
					$this->redirect($this->history['0']);
			 	}
			 }
			 $adminster->adminster_login=Request::instance()->post('login_name');
			 $adminster->adminster_pwd=encryption(Request::instance()->post('login_passwd'),$code);
			 $adminster->adminster_salt=$code;
			 $adminster->adminster_update_time=date('Y-m-d H:i:s');
			 $adminster->adminster_email=Request::instance()->post('login_email');
			 $authGroupAccesss=new AuthGroupAccesss;
			 $authGroupAccesss->group_id=$r['login_group']?$r['login_group']:Config::get('default_groups');
			 $adminster->profile=$authGroupAccesss;
			 if(false===$adminster->together('profile')->save()){
			      Session::set('jump_msg',['type'=>'warning','msg'=>'管理员添加失败~请重试','data'=>'']);
				 $this->redirect($this->history['0']);
			 }
			 Session::set('jump_msg',['type'=>'success','msg'=>'添加管理员成功','data'=>'']);
			 $this->redirect($this->history['1']);
		 }
		 return $this->get_form();
	 }
	 #管理员修改
	 public function edit(){
		 if(Request::instance()->isPost()){
			 $adminsters=Adminsters::get(Request::instance()->post('login_id'));
			 #判断是否重复
			 $check_exit=Adminsters::get(['adminster_id'=>['neq',Request::instance()->post('login_id')],'adminster_login'=>Request::instance()->post('login_name')]);
			 if($check_exit){
				 Session::set('jump_msg',['type'=>'warning','msg'=>'登录名已经存在，请重试~','data'=>'']);
				 $this->redirect($this->history['0']);
			 }
			 $check_exit=Adminsters::get(['adminster_id'=>['neq',Request::instance()->post('login_id')],'adminster_email'=>Request::instance()->post('login_email')]);
			 if($check_exit){
			      Session::set('jump_msg',['type'=>'warning','msg'=>'该邮箱已经绑定其他账号，请重新填写~','data'=>'']);
				 $this->redirect($this->history['0']);
			 }
			 if(Request::instance()->post('login_passwd')!=$adminsters->adminster_pwd)
				 $adminsters->adminster_pwd=encryption(Request::instance()->post('login_passwd'),$adminsters->adminster_salt);
			 $adminsters->adminster_email=Request::instance()->post('login_email');
			 $adminsters->adminster_login=Request::instance()->post('login_name');
			 $adminsters->adminster_user_id=Request::instance()->post('adminster_user_id');
			 $adminsters->profile->group_id=Request::instance()->post('login_group');
			 if(false===$adminsters->together('profile')->save()){
				 Session::set('jump_msg',['type'=>'warning','msg'=>'管理员修改失败~请重试','data'=>'']);
				 $this->redirect($this->history['0']);
			 }
			 Session::set('jump_msg',['type'=>'success','msg'=>'管理员更新成功','data'=>'']);
			 $this->redirect($this->history['1']);
		 }
		 return $this->get_form();
	 }
	 #信息表单
	 public function get_form(){
		 $data=[];
		 $information=[];
		 $information['action_text']="添加信息";
		 $information['action_link']=url("/index/adminster/add");
		 if(Request::instance()->param('id')){
			 $data['login_id']=Request::instance()->param('id');
			 $adminster_info=Adminsters::get(Request::instance()->param('id'));
			 $information['action_text']="更新信息";
			 $information['action_link']=url("/index/adminster/edit",['id'=>Request::instance()->param('id')]);
		 }
		 if(Request::instance()->has('login_name','post'))
		      $data['login_name']	=	Request::instance()->post['login_name'];
		 else if(isset($adminster_info) && !empty($adminster_info['adminster_login']))
			 $data['login_name']	=	$adminster_info['adminster_login'];
		 else
		      $data['login_name']	=	"";
		 if(Request::instance()->has('login_passwd','post'))
			 $data['login_passwd']	=	Request::instance()->post['login_passwd'];
		 else if(isset($adminster_info) && !empty($adminster_info['adminster_pwd']))
			 $data['login_passwd']	=	$adminster_info['adminster_pwd'];
		 else
			 $data['login_passwd']	=	"";
		 if(Request::instance()->has('login_group','post'))
			 $data['login_group']	=	Request::instance()->post['login_passwd'];
		 else if(isset($adminster_info) && !empty($adminster_info['profile']['group_id']))
			 $data['login_group']	=	$adminster_info['profile']['group_id'];
		 else
			 $data['login_group']	=	"";
		 if(Request::instance()->has('login_email','post'))
			 $data['login_email']	=	Request::instance()->post['login_email'];
		 else if(isset($adminster_info) && !empty($adminster_info['adminster_email']))
			 $data['login_email']	=	$adminster_info['adminster_email'];
		 else
			 $data['login_email']	=	"";
		 if(isset($adminster_info) && !empty($adminster_info['adminster_add_time']))
			 $data['adminster_add_time']	=	$adminster_info['adminster_add_time'];
		 else
			 $data['adminster_add_time']	=	"";
		 if(isset($adminster_info) && !empty($adminster_info['adminster_user_id']))
			 $data['adminster_user_id']	=	$adminster_info['adminster_user_id'];
		 else
			 $data['adminster_user_id']	=	"";

		 #获取用户组/子运营商用户
		 if($this->admin['adminster_group_id']==5){
			 $authGroups=AuthGroups::all(['id'=>5]);
			 $users=db('member')->alias('m')
			 	->join('member_group g','m.member_group_id=g.group_id')
			 	->where('m.member_id','in',$this->admin['children'])
			 	// ->where('g.group_visible=0')
			 	->select();
		 }else{
			 $authGroups=AuthGroups::all();
			 $users=db('member')->alias('m')
			 	->join('member_group g','m.member_group_id=g.group_id')
			 	// ->where('g.group_visible=0')
			 	->select();
		 }
		 $this->assign('users',$users);
		 $this->assign('data',$data);
    		 $this->assign('auth_groups',$authGroups);
		 $this->assign('information',$information);
    		 return view('admin/adminster/get_form');
	 }
	 #更改状态
	 public function change_state(){
	      if(!Request::instance()->isGet()){
		      Session::set('jump_msg',['type'=>'warning','msg'=>'错误的请求信息！']);
			 $this->redirect('Adminster/index');
		 }
		 $data=Request::instance()->param();
		 $adminster=new Adminsters;
		 $adminster_info=$adminster::get($data['id']);
		 $adminster_info->adminster_state=$adminster_info->adminster_state*-1;
		 if(false===$adminster_info->save()){
			 Session::set('jump_msg',['type'=>'warning','msg'=>'状态信息更新失败！请重试~']);
			 $this->redirect('Adminster/index');
		 }
		 Session::set('jump_msg',['type'=>'success','msg'=>'状态信息已更新~']);
		 $this->redirect('Adminster/index');
	 }
	 #获取用户组用户
	 public function lists(){
		 if(Request::instance()->param('group_id'))
			 $adminster=Adminsters::hasWhere('profile',['group_id'=>['neq',Request::instance()->param('group_id')]])->select();
		 else
			 $adminster=Adminsters::hasWhere('profile')->select();
		 $this->assign('adminster',$adminster);
		 $this->assign('group_id',Request::instance()->param('group_id'));
		 return view('admin/adminster/list');
	 }
	 #更改用户组
	 public function change_group(){
		 if(!Request::instance()->isAjax() || !Request::instance()->has('adminster_id','post') || !Request::instance()->has('group_id','post'))
			 exit(json_encode(['code'=>'104','msg'=>'非法请求~','data'=>[]]));
		 $authGroupAccesss=new AuthGroupAccesss;
		 foreach(Request::instance()->post('adminster_id/a') as $key=>$val){
			 $adminsterAuth=$authGroupAccesss::get($val);
			 $adminsterAuth->group_id=Request::instance()->post('group_id');
			 $adminsterAuth->save();
		 }
		 echo json_encode(['code'=>200,'msg'=>'','data'=>[]]);
	 }
	 // public function adminster_key(){
	 // 	if(Request::instance()->isPost()){

	 // 		 $result = Config::set("adminster_key",$_POST['adminster_key']);
	 // 		 dump($result);die;
	 // 		 $content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
		// 	 Session::set('jump_msg', $content);
		// 	 #重定向控制器 跳转到列表页
		// 	 $this->redirect("adminster/index");
	 // 	}
	 // 	$this->assign("adminster_key",Config::get('adminster_key'));
	 // 	return view("admin/adminster/adminster_key");
	 // }
}
