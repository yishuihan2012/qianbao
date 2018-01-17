<?php
namespace app\index\behavior;
use think\Request;
use luoyt\auth\Auth;
use think\Session;
class History
{
    public function run(&$params)
    {
        $request=Request::instance();
        $history=Session::has('history') ? Session::get('history') : [];            
        if($request->url()!=current($history) && !Request::instance()->isAjax())
            array_unshift($history,$request->url());
        if(count($history)>10)
            array_pop($history);
        //print_r($history);
        Session::set('history',$history);
    }
}
