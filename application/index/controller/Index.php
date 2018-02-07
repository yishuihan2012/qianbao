<?php
namespace app\index\controller;
use think\Db;
use think\Session;
class Index extends Common
{
    public function index()
    {
          $this->redirect('Dashboard/index');
    }
    public function help(){
    	return view('admin/index/help');
    }
}
