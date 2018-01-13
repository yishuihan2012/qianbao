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
        //数据获取展示
        $member['count']=Member::count();#当前用户总数量
        $member['today']=Member::whereTime('member_creat_time','d')->count();#今日用户数量
        $member['cert']=Member::where('member_cert','1')->count();#实名认证的总会员数
        $member['group']=MemberGroup::field('group_id,group_name')->select();
        foreach ($member['group'] as $key => $value) {
           $member['group'][$key]['memberCount'] = Member::where(['member_group_id'=>$value['group_id']])->count();
        }
        $this->assign('member',$member);


       $adminster=session('adminster');
       $where=[];
       $whereMember=[];
        if($adminster['adminster_user_id']){
            $where['order_root']=$adminster['adminster_user_id'];
            $whereMember['member_root']=$adminster['adminster_user_id'];
        }
        //总体数据
    	$data=[
    		'count'          => Member::where($whereMember)->count(),#当前用户总数量
    		'Todaycount'     => Member::where($whereMember)->whereTime('member_creat_time','d')->count(),#今日用户数量
            'CashOrdercount' => CashOrder::where($where)->count(),//当前套现总数量
            'GenerationOrdercount' => Generation::where($where)->count(),//当前还款总数量
            'CashOrderSum' => CashOrder::where($where)->sum('order_money'),//当前套现总数量
            'GenerationOrderSum' => Generation::where($where)->sum('generation_total'),//当前还款总数量
    	];
        $membergroup =new  MemberGroup();
        $membergrouplist = $membergroup->select();
        foreach ($membergrouplist as $key => $value) {
           $membergrouplist[$key]['membergroupcount'] = Member::where(['member_group_id'=>$value['group_id']])->count();
        }
       
       #通道数据
        $passway=db('passageway')->where('passageway_state',1)->select();
        foreach ($passway as $k => $v) {
            if($v['passageway_also']==1){
                $where=[
                    'order_passway'=>$v['passageway_id'],
                ];
                if($adminster['adminster_user_id']){
                    $where['order_root']=$adminster['adminster_user_id'];
                }
                //套现通道
                $passway[$k]['todaysum']=CashOrder::where($where)->whereTime('order_update_time','today')->sum('order_money');
                $passway[$k]['yesterdaysum']=CashOrder::where(['order_passway'=>$v['passageway_id']])->whereTime('order_update_time','yesterday')->sum('order_money');
                $passway[$k]['weeksum']=CashOrder::where(['order_passway'=>$v['passageway_id']])->whereTime('order_update_time','week')->sum('order_money');
                $passway[$k]['monthsum']=CashOrder::where(['order_passway'=>$v['passageway_id']])->whereTime('order_update_time','month')->sum('order_money');
                $passway[$k]['allsum']=CashOrder::where(['order_passway'=>$v['passageway_id']])->sum('order_money');
            }else{
                //代还通道
                $passway[$k]['todaysum']=db('generation')->where(['generation_passway_id'=>$v['passageway_id']])->whereTime('generation_add_time','today')->sum('generation_total');
                $passway[$k]['yesterdaysum']=db('generation')->where(['generation_passway_id'=>$v['passageway_id']])->whereTime('generation_add_time','yesterday')->sum('generation_total');
                $passway[$k]['weeksum']=db('generation')->where(['generation_passway_id'=>$v['passageway_id']])->whereTime('generation_add_time','week')->sum('generation_total');
                $passway[$k]['monthsum']=db('generation')->where(['generation_passway_id'=>$v['passageway_id']])->whereTime('generation_add_time','month')->sum('generation_total');
                $passway[$k]['allsum']=db('generation')->where(['generation_passway_id'=>$v['passageway_id']])->sum('generation_total');
            }
        }
        $this->assign('data',$data);
    	$this->assign('passway',$passway);
        $this->assign('membergrouplist',$membergrouplist);
        return view('admin/dashboard/index');

    }

}