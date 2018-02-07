<?php
/**
 *  @version Common controller / 基类控制器
 *  @author $GongKe$ (755969423@qq.com)
 *   @datetime    2018-01-17 12:00:05
 *   @return 
 */
 namespace app\index\controller;
 use app\index\model\System;
 use think\{Controller, Session, Request, View, Config};
 class Common extends Controller
 {  
      public $history;
      protected $jump_msg;
      protected $_post;
      protected $admin;
      public function __construct(){
           parent::__construct();
           if(!Session::has('adminster'))
                $this->redirect('Login/index');
           $this->history=Session::get('history');
           $data=[
                'id'             => Session::get('adminster.id'),
                'name'        => Session::get('adminster.adminster_login'),
                'email'         => Session::get('adminster.adminster_email'),
                'last_login'    => Session::get('adminster.adminster_update_time'),
                'state'          => Session::get('adminster.adminster_state'),
                'title'            => System::GetName('sitename')
           ];
           $this->admin=session('adminster');
           $this->assign('admin', $this->admin);
           #权限判断
           if(Session::has('jump_msg'))
                $data['jump_msg']=Session::get('jump_msg');
           Session::delete('jump_msg');
           View::share($data);
      }
 }
