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
use app\index\model\Member;
use app\index\model\CashOrder;
use app\index\model\MemberGroup;
use app\index\model\GenerationOrder;
use app\index\model\Generation;
class Dashboard extends Common
{

    //-------------------------------------------------------

    			#仪表盘(index)

    //-------------------------------------------------------
    public function index()
    {
    	$data=[
    		'count'          => Member::count(),#当前用户总数量
    		'Todaycount'     => Member::whereTime('member_creat_time','d')->count(),#今日用户数量
            'CashOrdercount' => CashOrder::count(),//当前套现总数量
    	];
        $membergroup =new  MemberGroup();
        $membergrouplist = $membergroup->select();
        foreach ($membergrouplist as $key => $value) {
           $membergrouplist[$key]['membergroupcount'] = Member::where(['member_group_id'=>$value['group_id']])->count();
        }
        $where['generation_state'] = ['<>',1];
        $this->assign('GenerationOrdercount', Generation::with("member,creditcard")->count());
    	$this->assign('data',$data);
        $this->assign('membergrouplist',$membergrouplist);
        return view('admin/dashboard/index');

    }

}