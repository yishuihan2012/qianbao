<?php
namespace app\index\behavior;
use think\Request;
use luoyt\auth\Auth;
use think\Session;
use think\Controller;
class MenuAuthCheck extends Controller
{
    public function run(&$params)
    {
        $request=Request::instance();
        $auth = new Auth();
        $controller=$request->controller();
        $action=$request->action();
        if(Session::has('adminster')){
           if(!$auth->check($controller . '/' . $action, Session::get('adminster.id'))){
                 $history=Session::get('history');
                 Session::set('jump_msg',['type'=>'warning','msg'=>'没有相关权限！','data'=>'']);
                 $this->redirect($history['1']);
           }
        }
    }
}
