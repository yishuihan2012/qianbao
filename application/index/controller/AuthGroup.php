<?php
/**
 *
 * @authors John(1160608332@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use think\Config;
use think\Request;
use think\Session;
use think\auth\Auth;
use app\index\model\AuthGroup as AuthGroups;
use app\index\model\AuthRule as AuthRules;
use app\index\model\AuthGroupAccess as AuthGroupAccesss;


class AuthGroup extends Common {
    public function index(){
      $auth_list= AuthGroups::where([])->paginate(Config::get('page_size'), false, [
                'page'=>input('param.page')?:1,
                'path'=>'/index/auth_group/index/page/[PAGE].html'
                ]);
      $this->assign('show',$auth_list->render());
			$this->assign('button',['text'=>'新增用户组','link'=>url('/index/auth_group/add')]);
      return view('admin/authgroup/index',['auth_list'=>$auth_list]);
    }
    public function edit(){
        if(Request::instance()->isPost()){
            $auth_groups=new AuthGroups;
            $auth_group=$auth_groups::get(Request::instance()->post('group_id'));
            $auth_group->title=Request::instance()->post('group_name');
            if(Request::instance()->has('group_rules','post'))
                  $auth_group->rules=implode(',',Request::instance()->post('group_rules/a'));
            //删除所有权限 更新权限
            if(false===$auth_group->save()){
                  Session::set('jump_msg',['type'=>'warning','msg'=>'信息更新失败~请重试','data'=>'']);
                  $this->redirect($this->history['0']);
            }
            Session::set('jump_msg',['type'=>'success','msg'=>'信息已更新','data'=>'']);
            $this->redirect($this->history['1']);
        }
        return $this->get_form();
    }
    public function add(){
      if(Request::instance()->isPost()){
        $auth_group=new AuthGroups;
        $auth_group->title=Request::instance()->post('group_name');
        if(Request::instance()->has('group_rules','post'))
              $auth_group->rules=implode(',',Request::instance()->post('group_rules/a'));
        //删除所有权限 更新权限
        if(false===$auth_group->save()){
              Session::set('jump_msg',['type'=>'warning','msg'=>'信息添加失败~请重试','data'=>'']);
              $this->redirect($this->history['0']);
        }
        Session::set('jump_msg',['type'=>'success','msg'=>'信息已添加','data'=>'']);
        $this->redirect($this->history['1']);
      }
      return $this->get_form();
    }
    public function get_form(){
        $data_info  =[];
        $auth_rules =[];
        $adminster  =[];
        $data_info['id']="";
        $information['action_text']="添加信息";
    		$information['action_link']=url("/index/auth_group/add");
        $information['add_link']  = "#";

        $auth_rules=AuthRules::all();
        if(Request::instance()->param('id')){
            $data_info['id']=Request::instance()->param('id');
            $auth_info=AuthGroups::get(Request::instance()->param('id'));
            $adminster=$auth_info->comments;
            $information['action_text']="更新信息";
            $information['action_link']=url("/index/auth_group/edit",['id'=>Request::instance()->param('id')]);
            $information['add_link']  = url('/index/adminster/lists',['group_id'=>Request::instance()->param('id')]);
        }
        if(Request::instance()->has('group_name','post')){
            $data_info['group_name']=Request::instance()->post('group_name');
        }else if(isset($auth_info) && !empty($auth_info['title'])){
            $data_info['group_name']=$auth_info['title'];
        }else{
            $data_info['group_name']="";
        }

        if(Request::instance()->has('group_rules','post')){
            $data_info['group_rules']=Request::instance()->post('group_rules');
        }else if(isset($auth_info) && !empty($auth_info['rules'])){
            $data_info['group_rules']=explode(',',$auth_info['rules']);
        }else{
            $data_info['group_rules']="";
        }
        $this->assign('info',$data_info);
        $this->assign('rules',$auth_rules);
        $this->assign('adminster',$adminster);
        $this->assign('information',$information);
        return view('admin/authgroup/get_form');
    }
    public function remove_adminster(){
      if(Request::instance()->isAjax() && Request::instance()->has('id','post') && Request::instance()->has('group_id','post')){
        $authGroupAccesss=new AuthGroupAccesss;
        $authAdminster=$authGroupAccesss::get(Request::instance()->post('id'));
        $authAdminster->group_id=Config::get('default_groups');
        $authAdminster->save();
        exit(json_encode(['code'=>200,'msg'=>'用户已经移除~','data'=>'']));
      }
      echo json_encode(['code'=>104,'msg'=>'非法请求~','data'=>'']);
    }
    public function change_state(){
        if(!Request::instance()->isGet()){
          Session::set('jump_msg',['type'=>'warning','msg'=>'错误的请求信息！']);
          $this->redirect('AuthGroup/index');
        }
        $data=Request::instance()->param();
        $auth_group=new AuthGroups;
        $auth_group_info=$auth_group::get($data['id']);
        $auth_group_info->status=$auth_group_info->status*-1;
        if(false===$auth_group_info->save()){
            Session::set('jump_msg',['type'=>'warning','msg'=>'状态信息更新失败！请重试~']);
            $this->redirect($this->history['0']);
        }
        Session::set('jump_msg',['type'=>'success','msg'=>'状态信息已更新~']);
        $this->redirect($this->history['1']);
    }
    //获取权限组list
    public function lists(){
      if(Request::instance()->param('group_id')){
            $authGroups=AuthGroups::all(['id'=>['neq',Request::instance()->param('group_id')]]);
      }else{
            $authGroups=AuthGroups::all();
      }
      $this->assign('authGroups',$authGroups);
      return view('/admin/authgroup/list');
    }
}
