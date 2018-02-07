<?php
/**
 *  @version Article controller / 分润分佣控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Earning as Earnings;
use app\index\model\MemberRelation;
use app\index\model\Member;
use think\Controller;
use app\index\model\System;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

 class Earning extends Common{
	 #文章列表
	 public function index()
	 {

	 }

      /**
 	 *  @version statistics method / Api 分润分佣计算分发
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-19 10:17:05
 	 *  @param $type='类型 1=分佣 2=分润' $member='交易/触发会员' , $money='交易额'
      **/ 
      public function statistics(/*$type,*/$member=29,$money='980')
      {
      	 $count=0;
      	 #因为不管是分佣还是分润 , 只分发到三级关系,  故:优先查询出交易会员的三级邀请人信息,然后再进行后续操作
      	 $member_father=MemberRelation::where('relation_member_id',$member)->value('relation_parent_id');
      	 #如果,他的直接上级没有,为平台的话 ，则所有分佣为平台  分润计算平台的就可以 是否满足三级关系
      	 if($member_father!='0' && !empty($member_father))
      	 {	
      	 	 $count=1;
      	 	 $member_grandad=MemberRelation::where('relation_member_id',$member_father)->value('relation_parent_id');
      	 	 if($member_grandad!='0' && !empty($member_grandad))
      	 	 {
      	 	 	 $count=2;
      	 	 	 $member_grandfather=MemberRelation::where('relation_member_id',$member_grandad)->value('relation_parent_id');
      	 	 	 if($member_grandfather!='0' && !empty($member_grandfather))
      	 	 	 	 $count=3;
      	 	 }
      	 }
      	 #取得分佣的百分比所对应的金额
      	 $total=$money*(System::getName('direct_rate')/100);
      	 #平台获得分佣
      	 $platmoney=0;
      	 #直接上级获得的分佣
      	 $member_father_money=0;
      	 #间接上级获得的分佣
      	 $member_grandad_money=0;
      	 #三级获得的分佣
      	 $member_grandfather_money=0;
      	 #如果没有上级 则平台获得全部利润
      	 if($count==0)
      		 $platmoney=$total;
      	 #如果只有直接上级  则直接上级和平台共分利润
      	 if($count==1)
      	 {
      	 	 #算出直接上级得到的分佣
      	 	 $member_father_money=$total*(System::getName('direct_total')/100);
      	 }
      	 #如果有两级上级  则上级按照比例获取利润
      	 if($count==2)
      	 {

      	 }
      	 #如果有三级上级 则所有三级上级按照比例获得利润 最多三级
      	 if($count==3)
      	 {

      	 }
 	echo $total;
      }


 }
