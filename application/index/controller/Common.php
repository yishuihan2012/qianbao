<?php
 namespace app\index\controller;
 use think\Controller;
 use think\Session;
 use think\Request;
 use think\View;
 use think\Config;
 use app\index\model\System;
 class Common extends Controller
 {  
      protected $jump_msg;
      protected $_post;
      public $history;
      public function __construct(){
      parent::__construct();
      if(!Session::has('adminster'))
           $this->redirect('Login/index');
      $this->history=Session::get('history');
      $data=[
           'id'             => Session::get('adminster.id'),
           'name'       => Session::get('adminster.adminster_login'),
           'email'       => Session::get('adminster.adminster_email'),
           'last_login'=> Session::get('adminster.adminster_update_time'),
           'state'       => Session::get('adminster.adminster_state'),
           'title'         => System::GetName('sitename')
      ];
      #权限判断
      if(Session::has('jump_msg'))
           $data['jump_msg']=Session::get('jump_msg');
           Session::delete('jump_msg');
           View::share($data);
      }
 }
