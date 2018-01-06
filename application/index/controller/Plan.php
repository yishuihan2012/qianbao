<?php
/**
 *  @version Passageway controller / 还款计划列表
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Passageway as Passageways;
use app\index\model\PassagewayItem;
use app\index\model\MemberGroup;
use app\index\model\Cashout;
use app\index\model\CreditCard;
use app\index\model\Generation;
use app\index\model\GenerationOrder;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;
use think\Db;

class Plan extends Common{
	#还款计划列表
	public function index(){
		$r=request()->param();
	 	 #搜索条件
	 	$data = $this->memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
	 	 //注册时间
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['Member.member_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		#身份证查询
		 if( request()->param('cert_member_idcard')){
			$where['MemberCert.cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
		}else{
			$r['cert_member_idcard'] = '';
		}
		#待确认条件
		$where['generation_state'] = ['<>',1];
		$data = GenerationOrder::list($where);
		$this->assign("list",$data['list']);
		$this->assign("count",$data['count']);
		$this->assign("r",$r);
		$member_group=MemberGroup::all();
		$this->assign('member_group', $member_group);
		return view("/admin/plan/index");
	}
	#还款详情
	public function info(){
		$where['order_id'] = input('id');
		$info = GenerationOrder::info($where);
		$this->assign("info",$info);
		return view("/admin/pLan/info");
	}
	#失败还款计划
	public function fail(){
		$r=request()->param();
	 	 #搜索条件
	 	$data = $this->memberwhere($r);
	 	$r = $data['r'];
	 	$where = $data['where'];
	 	 //注册时间
		if(request()->param('beginTime') && request()->param('endTime')){
			$endTime=strtotime(request()->param('endTime'))+24*3600;
			$where['Member.member_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
		}
		#身份证查询
		 if( request()->param('cert_member_idcard')){
			$where['MemberCert.cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
		}else{
			$r['cert_member_idcard'] = '';
		}
		#失败条件
		$where['generation_state'] = -1;
		$data = GenerationOrder::list($where);
		$this->assign("list",$data['list']);
		$this->assign("count",$data['count']);
		$this->assign("r",$r);
		$member_group=MemberGroup::all();
		$this->assign('member_group', $member_group);
		return view("/admin/plan/fail");
	}
	#查询条件
	public function memberwhere($r){
       $where=array();
       //手机号
       if(!empty($r['member_mobile'] )) {
        $where['Member.member_mobile']=["like","%".$r['member_mobile']."%"];
       }else{
        $r['member_mobile']='';
       }
       //昵称
       if(!empty($r['member_nick']) ){
        $where['Member.member_nick']=["like","%".$r['member_nick']."%"];
       }else{
        $r['member_nick']='';
       }
       //是否实名
       if(!empty($r['member_cert'])){
        $where['Member.member_cert'] = $r['member_cert']==2?0:1;
       }else{
        $r['member_cert']='';
       }

       //会员等级
       if(!empty($r['member_group_id'])){
        $where['Member.member_group_id'] = $r['member_group_id'];
       }else{
        $r['member_group_id']='';
       }
       
       return ['r'=>$r, 'where' => $where];
    }
}