<?php
/**
 *  @version Order controller / 订单控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use think\Db;
use app\index\model\Order as Orders;
use app\index\model\Withdraw;
use app\index\model\WalletLog;
use app\index\model\CashOrder;
use app\index\model\Recomment;
use app\index\model\Member;
use app\index\model\MemberGroup;
use app\index\model\Wallet;
use app\index\model\Upgrade;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;

class Order extends Common{
     #order列表
     public function index()
     {
        $r=request()->param();
         #搜索条件
        $data = memberwhere($r);
        $r = array_merge($r,$data['r']);
        $where = $data['where'];
         //订单创建时间
        $wheres = array();
        if(request()->param('beginTime') && request()->param('endTime')){
            $endTime=strtotime(request()->param('endTime'))+24*3600;
            $wheres['upgrade_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
        }
        #身份证查询
        if( request()->param('cert_member_idcard')){
            $wheres['m.cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
        }else{
            $r['cert_member_idcard'] = '';
        }

        #订单支付状态
        if(request()->param('upgrade_state')!=''){
            $wheres['upgrade_state'] = request()->param('upgrade_state');
        }else{
            $r['upgrade_state'] = '';
        }

        if(request()->param('upgrade_id')!=''){
            $wheres['upgrade_id'] = request()->param('upgrade_id');
        }

        if(input('is_export')==1){
            set_time_limit(0);
            $limit=20000;
            $max=100000;
            $i=intval(input('start_p')) ?? 0;
            $n=0;
            $fp = fopen('php://output', 'a');
            #算出乘数
            if($i)
                $i=($i-1)*$max/$limit;
            do{
                #取数据
                $order_lists=db("upgrade")->alias('o')
                    ->join('member m','o.upgrade_member_id=m.member_id')
                    ->join('member_cert c','c.cert_member_id=m.member_id','left')
                    ->where($where)
                    ->where($wheres)
                    ->order("upgrade_id desc")
                    ->field("member_nick,upgrade_type,concat('`',upgrade_no),upgrade_money,upgrade_commission,upgrade_state,upgrade_bak,member_creat_time")
                    ->limit($i*$limit,$limit)
                    ->select();
                    $i++;
                    // var_dump($order_lists);die;
                // halt($order_lists);
                $status=[
                    '0'=>'待支付',
                    '1'=>'已支付',
                ];
                $list=[];
                foreach ($order_lists as $k => $v) {
                    $order_lists[$k]['upgrade_state']=$status[$v['upgrade_state']];
                }
                $head=['用户名','升级方式','流水号','升级金额','分佣金额','支付状态','备注','创建时间'];
                export_csv($head,$order_lists,$fp);
                $count=count($order_lists);
                unset($order_lists);
                $n++;
            }while($count==$limit && $n<$max/$limit);
            return;
        }



        #支付类型
        $order_lists = Upgrade::haswhere('member',$where)
            ->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")
            ->where($wheres)->field('wt_member.member_nick')->order("upgrade_id desc")
            ->paginate(Config::get('page_size'),false, ['query'=>Request::instance()->param()]);

         #统计订单条数
        $count['count_size']=Upgrade::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->count();
         #升级总金额
        $count['upgrade_money'] = Upgrade::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->sum("upgrade_money");
         #升级未支付金额
          $count['upgrade_money_del'] = Upgrade::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where(array_merge($wheres,['upgrade_state'=>0]))->sum("upgrade_money");
          #升级已支付的金额
           $count['upgrade_money_yes'] = Upgrade::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where(array_merge($wheres,['upgrade_state'=>1]))->sum("upgrade_money");

        $count['upgrade_commission'] = Upgrade::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->sum("upgrade_commission");
        $this->assign('order_lists', $order_lists);
        $this->assign('count', $count);
        $this->assign('r', $r);
         #获取用户分组
        $member_group=MemberGroup::all();
        $this->assign('member_group', $member_group);
         #渲染视图
        return view('admin/order/index');
     }
     #订单详情
     public function edit(Request $request){

        if(!$request->param('id'))
         {
             Session::set('jump_msg', ['type'=>'error','msg'=>'参数错误']);
             $this->redirect($this->history['1']);
         }

         #查询到当前订单的基本信息
         $order_info=Upgrade::with('member')->find($request->param('id'));
         #升级前的用户组
         $front_group = MemberGroup::get($order_info['upgrade_before_group']);
         $this->assign("front_group",$front_group);
         #升级后的用户组
         $after_group = MemberGroup::get($order_info['upgrade_group_id']);
         $this->assign("after_group",$after_group);
         $this->assign('order_info', $order_info);
         return view('admin/order/edit');
     }

     #提现订单
     public function withdraw()
     {
        $r=request()->param();
         #搜索条件
        $data = memberwhere($r);
        $r = $data['r'];
        $where = $data['where'];
         //订单下单时间
        $wheres = array();
        wheretime($wheres,'withdraw_add_time');
        wheretime($wheres,'withdraw_update_time','beginTime2','endTime2');
        #提现状态
        if(input('withdraw_state'))
            $wheres['withdraw_state']=input('withdraw_state');
        #是否传id
        if(request()->param('withdraw_id')){
            $wheres['withdraw_id'] = request()->param('withdraw_id');
        }
        //管理员列表
        $admins=db('adminster')->column('adminster_id,adminster_login');
         // #查询订单列表分页
        $order_lists = Withdraw::haswhere('member',$where)
            ->where($wheres)
            ->order('withdraw_add_time desc')
            ->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
        //取出审批人姓名替换
        foreach ($order_lists as $k => $v) {
            if($v['withdraw_option']!=0)
                $order_lists[$k]['withdraw_option']=$admins[$v['withdraw_option']];
        }

         #操作成功金额
         $count['withdraw_amount'] = Withdraw::haswhere('member',$where)->where(array_merge($wheres,['withdraw_state'=>12]))->sum('withdraw_amount');
         #待审核金额
         $count['wait_amount'] = Withdraw::haswhere('member',$where)->where(array_merge($wheres,['withdraw_state'=>11]))->sum('withdraw_amount');
         #操作手续费
         $count['withdraw_charge'] = Withdraw::haswhere('member',$where)->where(array_merge($wheres,['withdraw_state'=>12]))->sum('withdraw_charge');
         $count['count_size']=Withdraw::haswhere('member',$where)->where($wheres)->count();
         $count['success_count']=Withdraw::haswhere('member',$where)->where(array_merge($wheres,['withdraw_state'=>12]))->count();
         $this->assign('order_lists', $order_lists);
         $this->assign('count', $count);
         #获取用户分组
        $member_group=MemberGroup::all();
        $this->assign('member_group', $member_group);
        $this->assign('r', $r);
         #渲染视图
        return view('admin/order/withdraw');
     }

      #提现订单详情
     public function showwithdraw(Request $request){
        if(!$request->param('id'))
         {
             Session::set('jump_msg', ['type'=>'error','msg'=>'参数错误']);
             $this->redirect($this->history['1']);
         }
         #查询到当前订单的基本信息
         $info=Withdraw::with('member,adminster')->find($request->param('id'));
         // var_dump($order_info);die;
         $this->assign('info', $info);
         return view('admin/order/showwithdraw');
     }
     #审核提现列表
     public function toexminewithdraw()
     {
        if(request()->isPost()){
            $param=request()->param();
            $Withdraw = Withdraw::get($param['withdraw_id']);
            $result=true;
            //审核通过

            if($param['withdraw_state']==12){
                //支付宝仅支持小数点后2位，数据库中存储的为小数点后4位，转换
                
              $Withdraw->withdraw_amount=round($Withdraw->withdraw_amount,2);
                //调用支付接口
              $payMethod="\app\index\controller\\".$Withdraw->withdraw_method;
              $payment=new $payMethod();
              $return=$payment->transfer($Withdraw); //转账

              if ($return['code'] != "200") {
                trace($return);
                 $content =  ['type'=>'warning','msg'=>$return['msg']];
                 Session::set('jump_msg', $content);
                 $this->redirect('order/withdraw');
              }else{
                $param['withdraw_option']=session('adminster.id');
                WalletLog::where(['log_relation_type'=>2,'log_relation_id'=>$param['withdraw_id']])->update(['log_status'=>1]);
                $Withdraw->allowField(['withdraw_state','withdraw_option'])->save($param);
                  $message="您的提现已经通过,请查收~";
                  jpush($Withdraw->withdraw_member,$message,$message,$message,4);
              }
            //审核不通过
            }else{
               Db::startTrans();
               try{
                    $Withdraw->withdraw_state=-12;
                    $Withdraw->withdraw_information=$param['withdraw_information'];
                    $Withdraw->withdraw_option=session('adminster.id');

                    //恢复用户钱包数据
                    $Wallet=Wallet::get(['wallet_member'=>$Withdraw->withdraw_member]);
                    $Wallet->wallet_total_withdraw=$Wallet['wallet_total_withdraw']-$Withdraw['withdraw_total_money'];
                    $Wallet->wallet_amount=$Wallet['wallet_amount']+$Withdraw['withdraw_total_money'];
                    //对钱包日志修改描述说明还有实时余额
                    $wallet_log=WalletLog::get(['log_wallet_id'=>$Wallet->wallet_id,'log_relation_type'=>2,'log_relation_id'=>$Withdraw->withdraw_id]);
                    // trace($wallet_log);
                    $wallet_log->log_desc="您的提现已驳回,驳回原因：".$param['withdraw_information'];
                    $wallet_log->log_balance=$Wallet->wallet_amount;
                    $wallet_log->log_status=3;
                    if($Wallet->save()===false || $Withdraw->save()===false || $wallet_log->save()===false){
                      Db::rollback();
                      $result=false;
                    }else{
                        Db::commit();
                        jpush($Withdraw->withdraw_member,$wallet_log->log_desc,$wallet_log->log_desc,$wallet_log->log_desc,4);
                    }
               } catch (\Exception $e) {
                     Db::rollback();
                trace($e->getMessage());
                     $result=false;
               }
            }


            $content = $result ? ['type'=>'success','msg'=>'审核成功'] : ['type'=>'warning','msg'=>'审核失败'];
            Session::set('jump_msg', $content);
            $this->redirect('http://'.$_SERVER['HTTP_HOST'].'/index/order/withdraw'.input('search'));
        }
        $this->assign("id",input("id"));
        return view("admin/order/toexminewithdraw");
     }


     #快捷支付订单 优化
     public function cash(){
        $r=request()->param();
        #根据搜索条件获取order_id
        $data = memberwhere($r);
        $r = array_merge($r,$data['r']);
        $where = $data['where'];
        if(input('order_id'))
            $where['order_id']=input('order_id');
        if(input('order_creditcard'))
            $where['order_creditcard']=['like',"%".input('order_creditcard')."%"];
        if(input('order_state'))
            $where['order_state']=input('order_state');
        #非成功状态的所有订单
        if(input('order_state')=='!2')
            $where['order_state']=['<>',2];
        if(input('passageway_id')){
            $where['order_passway']=input('passageway_id');
        }else{
            $r['passageway_id']='';
        }
        wheretime($where,'order_add_time');
        $passageway=db('passageway')->column("*","passageway_id");
        #共用数据
        $order_data=CashOrder::where($where)
            ->join('member m','wt_cash_order.order_member=m.member_id')
            ->order("order_id desc")
            ->column("order_id,order_no,order_name,order_card,order_creditcard,order_money,order_charge,order_passway,order_passway_profit,passageway_fix,user_fix,order_state,order_desc,order_add_time","order_id");

        // $cms=db('commission')->where('commission_from','in',array_column($order_data, 'order_id'))->where('commission_type',1)->group('commission_from')->column("commission_from,sum(commission_money) as sum");
        $cms=db('commission')->where('commission_from','in',function($q) use ($where){
                $q->table('wt_cash_order')->alias('o')
                    ->join('member m','wt_cash_order.order_member=m.member_id')
                    ->where($where)
                    ->field('order_id');
            })->where('commission_type',1)->group('commission_from')
            ->column("commission_from,sum(commission_money) as sum");
        
        if(input('is_export')==1){
            set_time_limit(0);
            $limit=20000;
            $max=100000;
            $i=intval(input('start_p')) ?? 0;
            $n=0;
            $fp = fopen('php://output', 'a');
            $status=['1'=>'待支付','2'=>'成功','-1'=>'失败','-2'=>'超时','3'=>'代付未成功'];
            #算出乘数
            if($i)
                $i=($i-1)*$max/$limit;
            do{
                #重组导出数据
                $list=[];
                foreach ($order_data as $k => $v) {
                    $order_fen=isset($cms[$v['order_id']])?$cms[$v['order_id']]:0;
                    $list[$k]=[];
                    $list[$k][]=$v['order_id'];
                    $list[$k][]="`".$v['order_no'];
                    $list[$k][]=$v['order_name'];
                    $list[$k][]="`".$v['order_card'];
                    $list[$k][]="`".$v['order_creditcard'];
                    $list[$k][]=$v['order_money'];
                    $list[$k][]=$v['order_charge']+$v['user_fix'];
                    $list[$k][]=$v['order_passway_profit']+$v['passageway_fix'];
                    $list[$k][]=$v['order_charge']+$v['user_fix']-$v['order_passway_profit']-$v['passageway_fix'];
                    $list[$k][]=$order_fen;
                    $list[$k][]=round($v['order_charge']+$v['user_fix']-$v['order_passway_profit']-$v['passageway_fix']-$order_fen,2);
                    $list[$k][]=$passageway[$v['order_passway']]['passageway_name'];
                    $list[$k][]=$status[$v['order_state']];
                    $list[$k][]=$v['order_desc'];
                    $list[$k][]=$v['order_add_time'];
                }
                    $i++;
                // halt($order_lists);
                $head=['#','交易流水号','刷卡人','结算卡','信用卡','总金额','刷卡手续费','成本手续费','结算金额','分润金额','盈利','通道','订单状态','备注','创建时间'];
                export_csv($head,$list,$fp);
                $count=count($list);
                unset($order_lists);
                $n++;
            }while($count==$limit && $n<$max/$limit);
            return;
        }
        #统计数据
        // $order_data=CashOrder::where($where)->order("order_id desc")->field('order_id,order_money,order_charge,order_passway_profit,order_buckle,order_state')->column("*","order_id");
        #分页数据
        $order_lists=CashOrder::where($where)
            ->join('member m','wt_cash_order.order_member=m.member_id')
            ->order("order_id desc")
            ->paginate(Config::get('page_size'), false, ['query'=>input()]);
        #分页数据补充
        foreach ($order_lists as $k => $v) {
             $order_lists[$k]['order_fen']=isset($cms[$v['order_id']])?$cms[$v['order_id']]:0;          
             $order_lists[$k]['yingli']=round($v['order_charge']+$v['user_fix']-$v['order_passway_profit']-$v['passageway_fix']-$order_lists[$k]['order_fen'],2);
        }
        #非成功状态 应该为0分润 即分润为0
        $count=[
            #代扣费之和
            'user_fix'=>array_sum(array_column($order_data,'user_fix')),
            #手续费之和
            // 'order_charge'=>array_sum(array_column($order_data,'order_charge')),
            'order_charge'=>0,
            #全部总金额
            'order_money'=>array_sum(array_column($order_data,'order_money')),
            #成本手续费之和
            // 'chengben'=>array_sum(array_column($order_data,'order_passway_profit')),
            'chengben'=>0,
            #全部三级分润金额
            'sanji'=>0,
            #订单数量
            'count_size'=>count($order_data),
        ];
        #默认值
        $count['order_money_yes']=0;
        #全部订单状态时
        if(!input('order_state')){
            $order_ids=[];
            foreach ($order_data as $k => $v) {
                if($v['order_state']==2){
                    $count['order_money_yes']+=$v['order_money'];
                    $count['order_charge']+=$v['order_charge']+$v['user_fix'];
                    $count['chengben']+=$v['order_passway_profit']+$v['passageway_fix'];
                    $order_ids[]=$v['order_id'];
                }
            }
            $where['order_state']=2;
            $cms=db('commission')->where('commission_from','in',function($q) use ($where){
                    $q->table('wt_cash_order')->alias('o')
                        ->join('member m','wt_cash_order.order_member=m.member_id')
                        ->where($where)
                        ->field('order_id');
                })->where('commission_type',1)->group('commission_from')
                ->column("commission_from,sum(commission_money) as sum");

            // $cms=db('commission')->where('commission_from','in',$order_ids)->where('commission_type',1)->group('commission_from')->column("commission_from,sum(commission_money) as sum");
            $count['sanji']=array_sum($cms);
            $r['order_state']='';
        }elseif(input('order_state')==2){
            // $cms=db('commission')->where('commission_from','in',array_column($order_data, 'order_id'))->where('commission_type',1)->group('commission_from')->column("commission_from,sum(commission_money) as sum");
            #指定成功状态时
            $count['order_money_yes']=$count['order_money'];
            $count['order_charge']=array_sum(array_column($order_data,'order_charge'))+array_sum(array_column($order_data,'user_fix'));
            $count['chengben']=array_sum(array_column($order_data,'order_passway_profit'))+array_sum(array_column($order_data,'passageway_fix'));
            $count['sanji']=array_sum($cms);
        }
        $count['chengben']=round($count['chengben'],2);
        $count['order_money_del']=$count['order_money']-$count['order_money_yes'];
        $count['yingli']=$count['order_charge']-$count['chengben'];

        $count['yingli']=round($count['yingli'],2);
        $count['fenrunhou']=$count['yingli']-$count['sanji'];
        $this->assign('order_lists', $order_lists);
        $this->assign('count', $count);
        $this->assign('passageway', $passageway);
        $member_group=MemberGroup::all();
        $this->assign('member_group', $member_group);
        $this->assign('r', $r);
         #渲染视图
        return view('admin/order/cash');
     }

      #快捷支付订单
     public function cash2(){
        $r=request()->param();
         #搜索条件
        $data = memberwhere($r);
        $r = $data['r'];
        $where = $data['where'];
        // var_dump($where);die;
         //注册时间
        $wheres = array();
        if(request()->param('beginTime') && request()->param('endTime')){
            $endTime=strtotime(request()->param('endTime'))+24*3600;
            $where['order_add_time']=["between time",[request()->param('beginTime'),$endTime]];
            $r['beginTime']=request()->param('beginTime');
            $r['endTime']=request()->param('endTime');
        }else{
            $r['beginTime']='';
            $r['endTime']='';
        }
        #身份证查询
        
        if( request()->param('order_creditcard')){
            $wheres['order_creditcard'] = ['like',"%".request()->param('order_creditcard')."%"];
        }else{
            $r['order_creditcard'] = '';
        }
        #订单状态
        if( request()->param('order_state')){
            $wheres['order_state'] = array("eq",request()->param('order_state'));

        }else{
            $r['order_state'] = '';
        }
        #通道
        if( request()->param('passageway_id')){
            $wheres['order_passway'] =  request()->param('passageway_id');
            $r['passageway_id'] = request()->param('passageway_id');
        }else{
            $r['passageway_id'] = '';
        }
        // var_dump($where);die;
        if(request()->param('order_id')){
            $wheres['order_id'] = request()->param('order_id');
        }
        if(input('is_export')==1){
            set_time_limit(0);
            $limit=20000;
            $max=100000;
            $i=intval(input('start_p')) ?? 0;
            $n=0;
            $fp = fopen('php://output', 'a');
            #算出乘数
            if($i)
                $i=($i-1)*$max/$limit;
            do{
                #取数据
                $order_lists=db("cash_order")->alias('o')
                    ->join('passageway p','o.order_passway=p.passageway_id')
                    ->join('member m','o.order_member=m.member_id')
                    ->join('member_cert c','c.cert_member_id=m.member_id','left')
                    ->where($where)
                    ->where($wheres)
                    ->order("order_id desc")
                    ->field('order_id,order_no,order_name,order_card,order_creditcard,order_money,order_charge,order_also,order_state,order_desc,order_add_time')
                    ->limit($i*$limit,$limit)
                    ->select();
                    $i++;
                // halt($order_lists);
                $status=[
                    '1'=>'待支付',
                    '-1'=>'失败',
                    '2'=>'成功',
                    '-2'=>'超时',
                ];
                $list=[];
                foreach ($order_lists as $k => $v) {
                    $order_lists[$k]['order_state']=$status[$v['order_state']];
                }
                $head=['#','交易流水号','用户名','结算卡','信用卡','总金额','手续费','费率','订单状态','备注','创建时间'];
                export_csv($head,$order_lists,$fp);
                $count=count($order_lists);
                unset($order_lists);
                $n++;
            }while($count==$limit && $n<$max/$limit);
            return;
        }
    
         // #查询订单列表分页
         $order_lists = CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->order("order_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
         // var_dump($order_lists);die;
         $count['chengben']=0;
         $count['yingli']=0;
         $count['sanji']=0;
         $count['fenrunhou']=0;
         $where1=array_merge($where,$wheres);
         $where1['order_state'] = array("eq",2);
         $list = CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where1)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->order("order_id desc")->select();

         foreach ($order_lists as $key => $value) {
             $order_lists[$key]['fenrun']=db('commission')->alias('c')
                ->where('commission_from='.$value['order_id'].' and commission_type=1')
                ->sum('commission_money');           
               #成本手续费
             // $count['chengben']+=$value['order_passway_profit'];

             $order_lists[$key]['yingli']=$value['order_charge']+$value['order_buckle']-$value['order_passway_profit'];

             // $count['yingli']+=$order_lists[$key]['yingli'];
             // $count['sanji']+=$order_lists[$key]['fenrun'];          
        }

        foreach ($list as $k => $order) {
            $list[$k]['fenrun']=db('commission')->alias('c')
                ->where('commission_from='.$order['order_id'].' and commission_type=1')
                ->sum('commission_money');  
            $count['chengben']+=$order['order_passway_profit'];
             $list[$k]['yingli']=$order['order_charge']+$order['order_buckle']-$order['order_passway_profit'];
             $count['yingli']+=$list[$k]['yingli'];
             $count['sanji']+=$list[$k]['fenrun'];          
        }
        $count['fenrunhou']=$count['yingli']-$count['sanji'];
         #统计订单条数
         $count['count_size']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->count();
         #交易总金额
         $count['order_money']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->sum("order_money");
          #交易成功金额
          $count['order_money_yes']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->where(['order_state'=>2])->sum("order_money");
         #交易未成功
          $count['order_money_del']=$count['order_money'] - $count['order_money_yes'];

         #交易总手续费
         $count['order_charge']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->where('order_state=2')->sum("order_charge");

         $this->assign('order_lists', $order_lists);
         $this->assign('count', $count);
        if(!Request::instance()->param('member_nick')){
            $where['member_nick']='';
         }
         if(!Request::instance()->param('member_mobile')){
            $where['member_mobile']='';
         }
        $passageway=db('passageway')->select();
        $this->assign('passageway', $passageway);
         $member_group=MemberGroup::all();
        $this->assign('member_group', $member_group);
        $this->assign('r', $r);
         #渲染视图
        return view('admin/order/cash');
     }
     #银行交易信息详情
     public function showcash(){
        $where['order_id'] = request()->param("id");
        $info =  CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->field("member_nick,member_mobile")->where($where)->find();
        $this->assign("info",$info);
        return view("admin/order/showcash");
     }
      #成功交易订单
     public function successCash(){
        $this->redirect("order/cash",['order_state'=>2]);
        $r=request()->param();
         #搜索条件
        $data = memberwhere($r);
        $r = $data['r'];
        $where = $data['where'];
         //注册时间
        $wheres = array();
        if(request()->param('beginTime') && request()->param('endTime')){
            $endTime=strtotime(request()->param('endTime'))+24*3600;
            $where['order_add_time']=["between time",[request()->param('beginTime'),$endTime]];
        }
        #身份证查询
        if( request()->param('order_creditcard')){
            $wheres['order_creditcard'] = ['like',"%".request()->param('order_creditcard')."%"];
        }else{
            $r['order_creditcard'] = '';
        }
        #订单状态
        if( request()->param('order_state')){
            $wheres['order_state'] = ['like',"%".request()->param('order_state')."%"];
        }else{
            $r['order_state'] = '';
        }
        $where['order_state'] = 2;
         // #查询订单列表分页
         $order_lists = CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->order("order_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
        
         #统计订单条数
         $count['count_size']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->count();
          #交易总金额
         $count['order_money']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->sum("order_money");
         #交易总手续费
         $count['order_charge']=CashOrder::with('passageway')->join('wt_member m',"m.member_id=wt_cash_order.order_member")->where($where)->join("wt_member_cert mc", "mc.cert_member_id=m.member_id","left")->where($wheres)->sum("order_charge");
             $this->assign('order_lists', $order_lists);
             $this->assign('count', $count);
        if(!Request::instance()->param('member_nick')){
            $where['member_nick']='';
         }
         if(!Request::instance()->param('member_mobile')){
            $where['member_mobile']='';
         }
         $member_group=MemberGroup::all();
        $this->assign('member_group', $member_group);
        $this->assign('r', $r);
         #渲染视图
        return view('admin/order/successCash');
     }
       #实名红包订单
     public function recomment(){
         // #查询订单列表分页
          #如果有查询条件
         $r=request()->param();
         #搜索条件
        $data = memberwhere($r);
        $r = $data['r'];
        $where = $data['where'];
         //注册时间
        $wheres = array();
        if(request()->param('beginTime') && request()->param('endTime')){
            $endTime=strtotime(request()->param('endTime'))+24*3600;
            $wheres['recomment_creat_time']=["between time",[request()->param('beginTime'),$endTime]];
        }
        #身份证查询
         if( request()->param('cert_member_idcard')){
            $wheres['m.cert_member_idcard'] = ['like',"%".request()->param('cert_member_idcard')."%"];
        }else{
            $r['cert_member_idcard'] = '';
        }

         // #查询订单列表分页
         $order_lists = Recomment::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->order("recomment_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
         foreach ($order_lists as $key => $value) {
                $order_lists[$key]['recomment_member_name']=Member::where(['member_id'=>$value['recomment_member_id']])->value('member_nick');
                $order_lists[$key]['recomment_children_name']=Member::where(['member_id'=>$value['recomment_children_member']])->value('member_nick');
         }
         $countmoney=Recomment::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->sum('recomment_money');
         #统计订单条数
         $count['count_size']=Recomment::haswhere('member',$where)->join("wt_member_cert m", "m.cert_member_id=Member.member_id","left")->where($wheres)->count();
             $this->assign('countmoney', $countmoney);
             $this->assign('order_lists', $order_lists);
             $this->assign('count', $count);
         #获取用户分组
        $member_group=MemberGroup::all();
        $this->assign('member_group', $member_group);
    
         $this->assign('r', $r);
         #渲染视图
        return view('admin/order/recomment');
     }
}