<?php
namespace app\index\controller;
use think\Db;
use think\Session;
class Index extends Common
{
    public function index()
    {
          return view("admin/dashboard/index");
    }
}
