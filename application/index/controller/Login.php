<?php
namespace app\index\controller;
use think\Db;
use think\Session;
use think\Request;
use think\Config;
use think\Controller;
use app\index\model\System;
use app\index\model\Adminster;
class Login extends Controller
{
    public function index()
    {
        $data=[];
        if(Session::has('jump_msg')){
            $data=['jump_msg'=>['type'=>Session::get('jump_msg.type'),'msg'=>Session::get('jump_msg.msg')]];
            Session::delete('jump_msg');
        }
        $site_name=System::getName('sitename');
        $this->assign('site_name',$site_name);
        return view('admin/login/index',$data);
    }
    //管理员登录
    public function do_login()
    {
        $request=Request::instance();
        if($request->method()!='POST'){
            Session::set('jump_msg',['type'=>'warning','msg'=>'错误的请求信息！']);
            $this->redirect('Login/index');
        }
        $data=$request->post();
        //口令检测
        if(!$data['login_key'] || System::getName('adminster_key')!=$data['login_key']){
            Session::set('jump_msg',['type'=>'warning','msg'=>'请填写正确的口令信息！']);
            $this->redirect('Login/index');
        }
        //密码验证
        $adminster=Adminster::is_exit($data['login_name'],$data['login_passwd']);

        if($adminster['code']!=200){
            Session::set('jump_msg',['type'=>'warning','msg'=>$adminster['msg']]);
            $this->redirect('Login/index');
        }

        $adminster_group_id=db('auth_group_access')->where('uid',$adminster['data']['adminster_id'])->value('group_id');
        #用户组ID 当用户为运营商的时候 调取其下三级子ID
        $children=[0];
        if($adminster_group_id==5){
            $children=$this->get_ids_all([$adminster['data']['adminster_user_id']]);
        }
        Session::set('jump_msg',['type'=>'success','msg'=>$adminster['msg']]);
        Session::set('adminster',[
            'id'=>$adminster['data']['adminster_id'],
            'adminster_login' =>$adminster['data']['adminster_login'],
            'adminster_email'   =>$adminster['data']['adminster_email'],
            'adminster_update_time'=>$adminster['data']['adminster_update_time'],
            'adminster_state'=>$adminster['data']['adminster_state'],
            'adminster_user_id'=>$adminster['data']['adminster_user_id'],
            'adminster_group_id'=>$adminster_group_id,
            'children'=>$children,
        ]);
        $this->redirect('Dashboard/index');
    }
    public function logout(){
        Session::delete('adminster');
        Session::set('jump_msg',['type'=>'success','msg'=>"您已成功退出~"]);
        $this->redirect('Login/index');
    }
    //获取运营商下三级的用户id
    private function get_ids($uid){
        if(!$uid)
            return [0];
        $level1=db('member_relation')->where('relation_parent_id',$uid)->column('relation_member_id');
        $level2=db('member_relation')->where('relation_parent_id','in',$level1)->column('relation_member_id');
        $level3=db('member_relation')->where('relation_parent_id','in',$level2)->column('relation_member_id');
        return array_merge($level1,$level2,$level3);
    }
    #递归获取所有下级用户ID
    private function get_ids_all($uid){
        if($uid){
            $users=db('member_relation')->where('relation_parent_id','in',$uid)->column('relation_member_id');
            return $users ? array_merge($users,$this->get_ids_all($users)) : [];
        }
    }
}
