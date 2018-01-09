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
        $site_name=System::GetName('sitename');
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
        if(!$data['login_key'] || Config::get('adminster_key')!=$data['login_key']){
            Session::set('jump_msg',['type'=>'warning','msg'=>'请填写正确的口令信息！']);
            $this->redirect('Login/index');
        }
        //密码验证
        $adminster=Adminster::is_exit($data['login_name'],$data['login_passwd']);

        if($adminster['code']!=200){
            Session::set('jump_msg',['type'=>'warning','msg'=>$adminster['msg']]);
            $this->redirect('Login/index');
        }
        Session::set('jump_msg',['type'=>'success','msg'=>$adminster['msg']]);
        Session::set('adminster',[
            'id'=>$adminster['data']['adminster_id'],
            'adminster_login' =>$adminster['data']['adminster_login'],
            'adminster_email'   =>$adminster['data']['adminster_email'],
            'adminster_update_time'=>$adminster['data']['adminster_update_time'],
            'adminster_state'=>$adminster['data']['adminster_state'],
        ]);
        $this->redirect('Dashboard/index');
    }
    public function logout(){
        Session::delete('adminster');
        Session::set('jump_msg',['type'=>'success','msg'=>"您已成功退出~"]);
        $this->redirect('Login/index');
    }
}
