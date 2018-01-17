<?php
namespace app\index\behavior;
use think\Request;
use luoyt\auth\Auth;
use think\Session;
use think\Controller;
use app\index\model\AuthRule;
class Crumbs extends Controller
{
    public function run(&$params)
    {
      #获取请求
      $request=Request::instance();
      #获取当前执行的控制器
      $controller=$request->controller();
      #获取当前执行的方法
      $action=$request->action();
      $methodPost=$controller."/".$action;
      #查询到当前执行的控制器方法的定义名
      $name=AuthRule::where(['name'=>$methodPost])->field('title')->find();
      if($action=='index')
        $crumbs['first']=array('action'=>$methodPost, 'title'=>$name->title);
      else{
        #查询到列表页 默认为index方法
        $methodPostS=$controller."/index";
        $secend=AuthRule::where(['name'=>$methodPostS])->field('title')->find();
        #如果查询到的话
        if($secend){
          $crumbs['first']=array('action'=>$methodPostS, 'title'=>$secend->title);
          $crumbs['secend']=array('action'=>$methodPost, 'title'=>$name->title);
        }else{
          $crumbs['first']=array('action'=>$methodPost, 'title'=>$name->title);
        }
      }
      $this->assign('crumbs', $crumbs);
    }
}
