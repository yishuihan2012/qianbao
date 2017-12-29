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


class Material extends Common {
    public function index(){
        echo "pppp";die;
        // return view('admin/material/index');
    }
}
