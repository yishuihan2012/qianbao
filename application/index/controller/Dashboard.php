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
        // $memberId=44;
        //  $family=find_relation($memberId);
         // array_shift($family);
        //  halt($family);
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
       $group_id=db('auth_group_access')->where('uid',$adminster['id'])->value('group_id');
       //运营商登录
        if($adminster['adminster_user_id'] && $group_id==5){
            $where['order_member']=["in",$this->admin['children']];
            $whereMember['member_id']=["in",$this->admin['children']];
        }
        //总体数据
    	$data=[
            'count'          => Member::where($whereMember)->count(),#当前用户总数量
    		'cert'           => Member::where(array_merge($whereMember,['member_cert'=>1]))->count(),#当前用户总数量
    		'Todaycount'     => Member::where($whereMember)->whereTime('member_creat_time','d')->count(),#今日用户数量
            'CashOrdercount' => CashOrder::where(array_merge($where,['order_state'=>2]))->count(),//当前套现总数量
            'GenerationOrdercount' => GenerationOrder::where(array_merge($where,['order_status'=>2]))->count(),//当前还款总数量
            'CashOrderSum' => CashOrder::where(array_merge($where,['order_state'=>2]))->sum('order_money'),//当前套现总数量
            'GenerationOrderSum' => GenerationOrder::where(array_merge($where,['order_status'=>2]))->sum('order_money'),//当前还款总数量
    	];
        $membergroup =new  MemberGroup();
        $membergrouplist = $membergroup->select();
        foreach ($membergrouplist as $key => $value) {
           $membergrouplist[$key]['membergroupcount'] = Member::where(['member_group_id'=>$value['group_id']])->where($whereMember)->count();
        }
       
       #通道数据
        $passway=db('passageway')->where('passageway_state',1)->select();
        foreach ($passway as $k => $v) {
            $where=[];
            //运营商筛选
            if($adminster['adminster_user_id'] && $group_id==5){
                $where['order_member']=["in",$adminster['children']];
            }
            if($v['passageway_also']==1){
                $where['order_passway']=$v['passageway_id'];
                $where['order_state']=2;
                //套现通道
                $passway[$k]['todaysum']=CashOrder::where($where)->whereTime('order_add_time','today')->sum('order_money');
                $passway[$k]['yesterdaysum']=CashOrder::where($where)->whereTime('order_add_time','yesterday')->sum('order_money');
                $passway[$k]['weeksum']=CashOrder::where($where)->whereTime('order_add_time','week')->sum('order_money');
                $passway[$k]['monthsum']=CashOrder::where($where)->whereTime('order_add_time','month')->sum('order_money');
                $passway[$k]['allsum']=CashOrder::where($where)->sum('order_money');
            }else{
                $where['order_passway_id']=$v['passageway_id'];
                $where['order_status']=2;
                //代还通道
                $passway[$k]['todaysum']=db('generation_order')->where($where)->whereTime('order_add_time','today')->sum('order_money');
                $passway[$k]['yesterdaysum']=db('generation_order')->where($where)->whereTime('order_add_time','yesterday')->sum('order_money');
                $passway[$k]['weeksum']=db('generation_order')->where($where)->whereTime('order_add_time','week')->sum('order_money');
                $passway[$k]['monthsum']=db('generation_order')->where($where)->whereTime('order_add_time','month')->sum('order_money');
                $passway[$k]['allsum']=db('generation_order')->where($where)->sum('order_money');
            }
        }
        $this->assign('data',$data);
    	$this->assign('passway',$passway);
        $this->assign('membergrouplist',$membergrouplist);
        return view('admin/dashboard/index');

    }

}