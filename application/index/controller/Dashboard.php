<?php
/**
 * Dashboard controller / 仪表盘控制器
 * @authors GongKe(755969423@qq.com)
 * @date    2017-10-11 18:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Dashboard extends Common
{

    //-------------------------------------------------------

    			#仪表盘(index)

    //-------------------------------------------------------
    public function index()
    {







        return view('admin/dashboard/index');
    }

}

