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
    protected $status=['1'=>'待执行','-1'=>'失败','2'=>'成功','3'=>'取消','4'=>'待查证','5'=>'已处理'];
    #还款计划列表
    public function index(){
        $r=input();
        $where=[];
         #搜索条件
        if(input('member'))
          $where['member_nick|member_mobile']=['like','%'.$r['member'].'%'];
         //注册时间
        wheretime($where,'generation_add_time');
        #信用卡号
        if( request()->param('generation_card')){
            $where['generation_card'] = ['like',"%".request()->param('generation_card')."%"];
        }else{
            $r['generation_card'] = '';
        }
        #代还计划号码
        if(input('generation_id'))
            $where['generation_id'] = input('generation_id');
        #计划状态查询
        $where['generation_state'] = array("<>",1);
        #计划订单列表
        if(request()->param('generation_state')){
            $where['generation_state'] = request()->param("generation_state");
        }else{
            $r['generation_state'] = '';
        }

        if(request()->param('generation_id')){
            $where['generation_id'] = request()->param("generation_id");
        }
        if(input('is_export')==1){
            $fp = fopen('php://output', 'a');
            #取数据
            $generation_list=db("generation")->alias('g')
                ->join('member m','m.member_id=g.generation_member')
                ->join('member_creditcard c','c.card_bankno=g.generation_card')
                ->where($where)
                ->order("generation_id desc")
                ->field('generation_id,member_nick,member_mobile,generation_no,generation_card,generation_total,generation_count,generation_has,generation_left,generation_pound,generation_start,generation_end,generation_state,generation_desc')
                ->select();

            $head=['ID','还款会员','手机号码','计划代号','需还信用卡','需还款总额','还款次数','已还款总额','剩余总额','手续费','开始还款日期','最后还款日期','计划状态','还款失败原因'];
            export_csv($head,$generation_list,$fp);
            return;
        }
        $data = Generation::with("member,creditcard")->where($where)->order("generation_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
        #还款总金额
        $sum = Generation::with("member,creditcard")->where($where)->sum("generation_total");
        $this->assign("sum",$sum);
        #剩余还款总额
        $surplussum = Generation::with("member,creditcard")->where($where)->sum("generation_total");
        $this->assign("surplussum",$surplussum);
        #还款总笔数
        $count_plan = Generation::with("member,creditcard")->where($where)->sum("generation_count");
        $this->assign("count_plan",$count_plan);
        #计算总条数
        $count = Generation::with("member,creditcard")->where($where)->count();
        //用户组
        $this->assign("member_group",MemberGroup::all());
        $this->assign("list",$data);
        $this->assign("count",$count);
        $this->assign("r",$r);
        return view("admin/plan/index");
    }
    /**
     * 代还订单详情
     */
    public function info(){
        if(input('order_id')){
            $info=db('generation_order')->alias('o')
                ->join('member m','o.order_member=m.member_id')
                ->join('passageway p','o.order_passageway=p.passageway_id')
                ->where('order_id',input('order_id'))
                ->find();
            $info['fenrun']=db('commission')
                ->where(['commission_from'=>input('order_id'),'commission_type'=>1])
                ->sum('commission_money');
            $info['yingli']=round($info['order_pound']-$info['order_platform_fee']-$info['fenrun'],2);
            $info['status']=$this->status[$info['order_status']];
            $this->assign('info',$info);
            return view("/admin/plan/info");
        }
    }
    #还款详情
    public function info2(){
        #从钱包日志跳转来 单条详情
        if(input('order_id')){
            $where['order_id'] = input('order_id');
        }else{
            #从菜单点击来 计划所有详情
            $where['order_no'] = input('id');
        }
        $list = GenerationOrder::with("passageway,member,memberCreditcard")->where($where)->order('order_time','asc')->select();
        $this->assign("list",$list);
        return view("/admin/plan/info");
    }
    #失败还款计划
    public function fail(){
        $r=input();
        #精确查询
        if(input('passway') && input('mobile')){
            $uid = db('member')->where('member_mobile',$r['mobile'])->value('member_id');
            $res = '不支持此通道';
            $passway = db('passageway')->where('passageway_id',$r['passway'])->find();
            if($passway['passageway_true_name']=='Misdh'){
                $class = new \app\api\controller\Membernet();
                $res = $class->accountQuery($uid,$passway['passageway_id']);
                if(isset($res['availableAmt']) && isset($res['usedAmt']) && isset($res['refundAmt'])){
                    $res = ($res['availableAmt']-$res['usedAmt']+$res['refundAmt'])/100;
                }else{
                    $res = '查询失败';
                }
                
            }elseif($passway['passageway_true_name']=='Yipayld'){
                $class = new \app\api\controller\Yipay();
                $res = $class->merch_remain($uid);
                if(isset($res['ableBalanceT0'])){
                    $res = $res['ableBalanceT0']/100;
                }else{
                    $res = '查询失败';
                }
                
            }
            return $res;
        }
        if(!input('beginTime')){
            $r['beginTime']=date('Y-m-d');
            $endTime=time();
        }else{
            $endTime=strtotime($r['beginTime'])+3600*24;
        }

        #消费成功的订单
        $pay_orders=db('generation_order')->alias('o')
            ->join('member m','o.order_member=m.member_id')
            ->join('passageway p','o.order_passageway=p.passageway_id')
            ->where('order_status',2)
            ->where('order_type',1)
            ->whereTime('order_time','between',[$r['beginTime'],$endTime])
            ->group('order_no')
            ->field('o.*,m.member_nick,m.member_mobile,p.passageway_name,sum(order_money) as sums')
            ->select();
        #目前为止还款的订单
        $back_orders=db('generation_order')
            ->where('order_type',2)
            ->whereTime('order_time','between',[$r['beginTime'],$endTime])
            ->column('*','order_no');
        $list=[];
        foreach ($pay_orders as $k => $v) {
            #确定有此还款(已执行过)
            if(isset($back_orders[$v['order_no']])){
                #还款状态非成功
                if($back_orders[$v['order_no']]['order_status']!=2){
                    $v['order_id']=$back_orders[$v['order_no']]['order_id'];
                    $list[]=$v;
                }
            }
        }
        usort($list,function($a,$b){
            if($a['sums']==$b['sums'])
                return 0;
            return $a['sums']>$b['sums'] ? -1 : 1;
        });
        $passway = db('Passageway')
            ->where('passageway_also',2)
            ->where('passageway_true_name','in','Misdh,Yipayld')
            ->select();
        // halt($list);
        $this->assign('passway',$passway);
        $this->assign('list',$list);
        $this->assign('r',$r);
        return view("admin/plan/fail");

        $r=request()->param();
         #搜索条件
        $data = memberwhere($r);
        $r = $data['r'];
        $where = $data['where'];
        $where['order_status'] = -1;
        if(request()->param('beginTime') && request()->param('endTime')){
            $endTime=strtotime(request()->param('endTime'))+24*3600;
            $where['order_time']=["between time",[request()->param('beginTime'),$endTime]];
        }else{
            $where['order_time']=["between time",[strtotime("-7 days"),time()]];
            $r['beginTime']=date('Y-m-d',strtotime("-7 days"));
            $r['endTime']=date('Y-m-d',time());
        }
        if(request()->param('order_money')!=''){
            $where['order_money'] = request()->param('order_money');
        }else{
            $r['order_money'] = ''; 
        }
        if(request()->param('order_no')!=''){
            $where['order_no'] = request()->param('order_no');
        }else{
            $r['order_no'] = '';
        }
        $list = GenerationOrder::with("passageway,member")->join("wt_generation","generation_id=order_no")->where($where)->where([])->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
        $this->assign('r',$r);
        $this->assign("list",$list);
        return view("admin/plan/fail");
    }
    #取消执行|继续执行还款计划
    public function order_status(){
        $where['order_id'] = request()->param("id");
        $data['order_status'] = request()->param("status");

        $result = GenerationOrder::where($where)->update($data);
        // $result  = GenerationOrder::where($where)->update($data);
        // if(!$result)
        //  die;
         #数据是否提交成功
         $content = ($result===false) ? ['type'=>'error','msg'=>'操作失败'] : ['type'=>'success','msg'=>'操作成功'];
         Session::set('jump_msg', $content);
         #重定向控制器 跳转到列表页
         $this->redirect("Plan/index");
    }

    /**
     * 还款订单列表
     */

    public function detail(){
        // $passageway=Passageways::where(['passageway_also'=>2,'passageway_state'=>1])->select();
        $passageway=db('passageway')->alias('p')
            ->join('generation_order o','p.passageway_id=o.order_passageway')
            ->group('p.passageway_id')
            ->column("p.passageway_id,p.passageway_name,p.passageway_also","passageway_id");
        $this->assign('passageway',$passageway);
        $where=$this->detail_search();
        #分润数据
        $cms=db('commission')->where(['commission_type'=>3])
            ->group('commission_from')
            ->column("commission_from,sum(commission_money) as sum");

        $list=model('generation_order')->alias('o')
            ->join('member m','o.order_member=m.member_id')
            ->join('generation g','g.generation_id=o.order_no')
            ->join('passageway p','o.order_passageway=p.passageway_id')
            ->join('member_creditcard c','o.order_card=c.card_bankno','left')
            ->where($where)
            ->where('g.generation_state','<>',1)
            ->order('order_id desc');
        if(input('is_export')==1){
            $fp = fopen('php://output', 'a');
            $type=['1'=>'消费','2'=>'还款'];
            #取数据
            $order_list=db('generation_order')->alias('o')
                ->join('member m','o.order_member=m.member_id')
                ->join('generation g','g.generation_id=o.order_no')
                ->join('passageway p','o.order_passageway=p.passageway_id')
                ->where($where)
                ->where('g.generation_state','<>',1)
                ->join('member_creditcard c','c.card_bankno=o.order_card','left')
                ->order("order_id desc")
                ->field('order_id,order_platform_no,member_nick,passageway_name,member_mobile,order_type,concat("`",order_card),card_bankname,order_money,order_real_get,order_pound,order_passageway_fee,order_platform_fee,order_platform_fee as order_fenrun,order_platform_fee as order_yingli,order_status,order_retry_count,back_statusDesc,order_desc,order_edit_time,order_add_time')
                ->select();
            foreach ($order_list as $k => $v) {
                $order_list[$k]['order_type']=$type[$v['order_type']];
                $order_list[$k]['order_status']=$this->status[$v['order_status']];
                $order_lists[$k]['order_fenrun']=isset($cms[$v['order_id']])?$cms[$v['order_id']]:0;
                $order_lists[$k]['order_yingli']=$v['order_platform_fee']-$order_lists[$k]['order_fenrun'];
            }
            $head=['ID','订单号','姓名','通道','手机号','订单类型','信用卡号','银行名称','订单金额','到账金额','订单手续费','成本手续费','结算','分润','盈利','订单状态','重新执行次数','执行结果','订单描述','订单更新时间','订单创建时间'];
            export_csv($head,$order_list,$fp);
            return;
        }

        $order_lists=clone $list;
        $order_lists->__construct();
        #对搜索结果数据进行缓存
        $order_data=cache($_SERVER['HTTP_HOST'].'order_data_cache'.md5(json_encode($where)));
        if(!$order_data){
            $order_data=$order_lists->field('o.order_id,o.order_type,o.order_money,o.order_pound,o.order_status,o.order_passageway_fee,o.order_platform_fee')->select();
            cache($_SERVER['HTTP_HOST'].'order_data_cache'.md5(json_encode($where)),$order_data,300);
        }
        #分页数据
        $order_lists=$list
            ->field('o.*,m.member_nick,p.passageway_name,c.card_bankname')
            ->paginate(Config::get('page_size'), false, ['query'=>input()]);
        foreach ($order_lists as $k => $v) {
             $order_lists[$k]['order_fenrun']=isset($cms[$v['order_id']])?$cms[$v['order_id']]:0;          
        }
        #统计数据
        $count=[
            #消费金额
            'order_cash_money'=>0,
            #还款金额
            'order_repay_money'=>0,
            #手续费之和
            'order_pound'=>0,
            #成本手续费之和
            'chengben'=>0,
            #结算费用
            'order_platform_fee'=>0,
            #全部三级分润金额
            'sanji'=>0,
            #订单数量
            'count_size'=>count($order_data),
        ];
        $this->assign('button', ['text'=>'新增还款', 'link'=>url('/index/plan/creat'), 'modal'=>'modal']);
        #默认值
        #全部订单状态时
        if(!input('order_status')){
            $order_ids=[];
            foreach ($order_data as $k => $v) {
                if($v['order_status']==2){
                    $count['order_pound']+=$v['order_pound'];
                    $count['chengben']+=$v['order_passageway_fee'];
                    $count['order_platform_fee']+=$v['order_platform_fee'];
                    $order_ids[]=$v['order_id'];
                    if($v['order_type']==1){
                        $count['order_cash_money']+=$v['order_money'];
                    }else{
                        $count['order_repay_money']+=$v['order_money']-$v['order_pound'];
                    }
                    $count['sanji']+=(isset($cms[$v['order_id']]) ? $cms[$v['order_id']] : 0);
                }
            }
            // $cms=db('commission')->where('commission_from','in',$order_ids)->where('commission_type',3)->group('commission_from')->column("commission_from,sum(commission_money) as sum");
            // $count['sanji']=array_sum($cms);
            $r['order_status']='';
        }elseif(input('order_status')==2){
            $count['chengben']=array_sum(array_column($order_data,'order_passageway_fee'));
            $count['order_platform_fee']=array_sum(array_column($order_data,'order_platform_fee'));
            $cms=db('commission')->where('commission_from','in',array_column($order_data, 'order_id'))->where('commission_type',3)->group('commission_from')->column("commission_from,sum(commission_money) as sum");
            #指定成功状态时
            $count['order_pound']=array_sum(array_column($order_data,'order_pound'));
            $count['sanji']=array_sum($cms);
            foreach ($order_data as $k => $v) {
                if($v['order_type']==1){
                    $count['order_cash_money']+=$v['order_money'];
                }else{
                    $count['order_repay_money']+=$v['order_money']-$v['order_pound'];
                }
            }
        }
        $count['fenrunhou']=round($count['order_platform_fee']-$count['sanji'],2);
        $this->assign('count',$count);
        $this->assign('list',$order_lists);
        return view("admin/plan/detail");
    }

    private function detail_search(){
        $r=input();
        $where=[];
        if(input('member'))
          $where['m.member_nick|m.member_mobile']=['like','%'.$r['member'].'%'];
        if(input('back_statusDesc'))
          $where['o.back_statusDesc']=['like','%'.$r['back_statusDesc'].'%'];
        if(input('order_platform_no'))
          $where['o.order_platform_no']=$r['order_platform_no'];
        if(input('order_card'))
          $where['o.order_card']=$r['order_card'];
        if(input('order_money'))
          $where['o.order_money']=$r['order_money'];
        if(input('order_id'))
          $where['o.order_id']=$r['order_id'];
        if(input('order_no'))
          $where['o.order_no']=$r['order_no'];
        if(input('order_status'))
          $where['o.order_status']=$r['order_status'];
        if(input('order_type'))
          $where['o.order_type']=$r['order_type'];
        if(input('passageway_id'))
          $where['o.order_passageway']=$r['passageway_id'];
        wheretime($where,'o.order_time');
        if(input('updatebeginTime') && input('updateendTime') && input('updatebeginTime')<=input('updateendTime')){
            $endTime=strtotime(input('updateendTime'))+24*3600;
            $where['o.order_edit_time']=["between time",[input('updatebeginTime'),$endTime]];
        }
        #默认当前月份
        if(!$where){
            $where['o.order_time']=["between time",[mktime(0,0,0,date('m'),1,date('Y')),strtotime(date('Y-m-d'))+3600*24]];
            $r['beginTime']=date('Y-m-d',mktime(0,0,0,date('m'),1,date('Y')));
            $r['endTime']=date('Y-m-d');
        }
        $this->assign('r',$r);
        return $where;
    }
    /**
     *  @version detail controller / 总还款列表详情
     *  @author $Mr.gao$(928791694@qq.com)
     *   @datetime    2017-02-27 09:34:05
     *   @return 
     */

     public function detail2(){

        $r=request()->param();
         #搜索条件
        $data = memberwhere($r);
        $r = $data['r'];
        $where = $data['where'];
        $where['order_status'] = -1;
        if(request()->param('beginTime') && request()->param('endTime')){
            $endTime=date("Y-m-d",strtotime(request()->param('endTime'))+24*3600);
            $where['order_time']=["between",[request()->param('beginTime'),$endTime]];
            $this->assign('beginTime',request()->param('beginTime'));
            $this->assign('endTime',request()->param('endTime'));
        }else{
            $where['order_time']=["between time",[strtotime("-7 days"),time()]];
            $r['beginTime']=date('Y-m-d',strtotime("-7 days"));
            $r['endTime']=date('Y-m-d',time());
        }
        if(request()->param('order_money')!=''){
            $where['order_money'] = request()->param('order_money');
        }else{
            $r['order_money'] = ''; 
        }

        if(request()->param('order_type')!=''){
            $where['order_type'] = request()->param('order_type');
        }else{
            $r['order_type'] = ''; 
        }

        if(request()->param('id')!=''){
            $where['order_id'] = request()->param('id');
            $r['order_id']=request()->param('id');
        }else{
            $r['order_id'] = '';
        }

        // if(request()->param('order_no')!=''){
        //  $where['order_no'] = request()->param('order_no');
        // }else{
        //  $r['order_no'] = '';
        // }

        #计划状态查询
        $where['order_status'] = array("<>",1);
        #计划订单列表
        if(request()->param('order_status')){
            $where['order_status'] = request()->param("order_status");
        }else{
            $r['order_status'] = '';
        }

        // $generation = db("Generation")->alias('w')->where('generation_state=1')->field('generation_id')->select();
        // $generation_id=array();
        // foreach ($generation as $key => $value) {
        //  $generation_id[]=$value['generation_id'];
        // }

        // $list = GenerationOrder::with("passageway,member,memberCreditcard")->where($where)->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
        // foreach ($list as $key => $value) {
        //  if(in_array($value->order_no, $generation_id)){
        //      unset($list[$key]);
        //  }
        // }

        $generation = Generation::where('generation_state!=1')->select();
        $generation_id=array();
        foreach ($generation as $key => $value) {
            $generation_id[]=$value['generation_id'];
        }
        $generation_id=implode(',', $generation_id);

        if(input('is_export')==1){
            $fp = fopen('php://output', 'a');
            $type=['1'=>'消费','2'=>'还款'];
            $status=['1'=>'待执行','-1'=>'失败','2'=>'成功','3'=>'取消','4'=>'待查证处理中'];
            #取数据
            $order_list=db("generation_order")->alias('o')
                ->join('passageway p','p.passageway_id=o.order_passageway')
                ->join('member m','m.member_id=o.order_member')
                ->join('member_creditcard c','c.card_bankno=o.order_card')
                ->where($where)
                ->where('order_no in ('.$generation_id.')')
                ->order("order_id desc")
                ->field('order_id,passageway_name,member_nick,member_mobile,order_type,concat("`",order_card),card_bankname,order_money,order_pound,order_status,order_retry_count,back_statusDesc,order_desc,order_time,order_add_time')
                ->select();
            foreach ($order_list as $k => $v) {
                $order_list[$k]['order_type']=$type[$v['order_type']];
                $order_list[$k]['order_status']=$status[$v['order_status']];
            }
            $head=['ID','通道','姓名','手机号','订单类型','信用卡号','银行名称','订单金额','订单手续费','订单状态','重新执行次数','执行结果','订单描述','订单执行时间','订单创建时间'];
            export_csv($head,$order_list,$fp);
            return;
        }

        //消费总金额
        $order['money']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=1')->where($where)->sum('order_money');
        //全部手续费
        $order['change']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=1')->where($where)->sum('order_pound');
        //成本手续费
        $order['chengben']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=1')->where($where)->sum('order_passageway_fee');
        //盈利分润
        $order['yingli']=$order['change']-$order['chengben'];
        //三级分润消耗
        $order['fen']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=1')->where($where)->sum('order_fen');
        //分润后平台盈利
        $order['fenrunhou']=$order['yingli']-$order['fen'];
        //消费笔数
        $order['consumption']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=1')->where($where)->count();
        //还款笔数
        $order['repayment']=GenerationOrder::with("passageway,member,memberCreditcard")->where('order_status=2 and order_type=2')->where($where)->count();


        $list=GenerationOrder::with("passageway,member,memberCreditcard")->where($where)->where('order_no in ('.$generation_id.')')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
        $count = GenerationOrder::with("passageway,member,memberCreditcard")->where($where)->where('order_no in ('.$generation_id.')')->count();

        $this->assign('r',$r);
        $this->assign("list",$list);
        $this->assign("count",$count);
        $this->assign("order",$order);

        return view("admin/plan/detail");
     }

      /**
     *  @version edit_status controller / 修改订单状态
     *  @author $Mr.gao$(928791694@qq.com)
     *   @datetime    2017-02-27 09:34:05
     *   @return 
     */

      public function edit_status(){
        $order_status = GenerationOrder::where(['order_id'=>request()->param('id')])->value('order_status');

        $this->assign("order_status",$order_status);
        $this->assign("id",request()->param('id'));
        if($_POST){
            $data=array(
                'order_status'=>request()->param('order_status')
            );
            $status=GenerationOrder::where(['order_id'=>request()->param('id')])->update($data);

            $content = ($status===false) ? ['type'=>'error','msg'=>'修改状态失败'] : ['type'=>'success','msg'=>'修改状态成功'];

            Session::set('jump_msg', $content);
            $this->redirect("plan/detail");
        }

        return view("admin/plan/edit_status");
      }
      /**
      *增加一条还款记录
      *
      */
      public function creat(){
        if(!empty(request()->param('order_id'))){
            $info = db("GenerationOrder")->where(['order_id'=>request()->param('order_id')])->find();
            // dump($info['order_type']);die;
            if($info['order_type'] != 2){
                $content =  ['type'=>'warning','msg'=>'不能增加消费计划！'] ;
            }else{
                unset($info['order_id']);
                $info['order_real_get'] = request()->param("money")-($info['order_money']-$info['order_real_get']);
                $info['order_money']  = request()->param("money");
                $info['order_platform_no'] = get_plantform_pinyin().time().make_rand_code();
                $info['order_time'] = date("Y-m-d H:i:s");
                $info['order_edit_time'] = date("Y-m-d H:i:s");
                $info['order_add_time'] = date("Y-m-d H:i:s");
                $info['order_status'] = 1;
                if($status = db("GenerationOrder")->insert($info)){
                     $content =  ['type'=>'success','msg'=>'新增成功'] ;
                }else{
                    $content =  ['type'=>'warning','msg'=>'新增失败'] ;
                }
                
            }
            Session::set('jump_msg', $content);
             $this->redirect('plan/detail');
        }
        
        return view("admin/plan/creat");
      }
   
}