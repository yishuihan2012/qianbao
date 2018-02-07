<?php
/*
 * 会员升级
 * @Author: John（1160608332@qq.com） 
 * @Date: 2018-01-17 09:55:44 
 * @Last Modified by: John（1160608332@qq.com）
 * @Last Modified time: 2018-01-17 16:42:50
 **/
 namespace app\api\controller;
 use app\index\model\Member;
 use app\index\model\Upgrade;
 use app\index\model\MemberGroup;

 class MemberLebvelup
 {
    protected $member;
    public function __construct(Member $member){
        parent::__construct();
        $this->member   =   $member;
    }
    ## 生成订单 升级凭据 wt_upgrade
    public function make_order($upgrade){
        try{
            ## 根据当前 以及升级后 组别 确认升级金额
            $money_current = $this->member->membergroup->group_level_money; ##当前升级金额
            $group_next_info = MemberGroup::get($upgrade['group_id']);
            $money_next = $group_next_info->group_level_money;
            if(!$money_next || $money_next<=0 || $money_next<=$money_current  || ($money_next-$money_current)<0){
                ## 判断升级合法
                throw new \think\Exception('升级失败，请确认升级配置正确');
            }
            $upgrade = new Upgrade;
            $upgrade->upgrade_member_id = $this->member->id;
            $upgrade->upgrade_before_group  = $this->member->member_group_id;
            $upgrade->upgrade_group_id = $upgrade['group_id'];     //升级组
            $upgrade->upgrade_type = '';   ## 支付时填充
            $upgrade->upgrade_no = make_order();
            $upgrade->upgrade_money =  $money_next; ## 支付完成填充 计算所需金额
            $upgrade->upgrade_commission = ''; ## 支付完成后填充
            $upgrade->upgrade_state = 0; 
            $upgrade->upgrade_creat_time = date('Y-m-d H:i:s');
            $upgrade->save();
            return $upgrade->upgrade_id;
        }catch(\think\Exception $error){
            throw new \think\Exception($error->getMessage());
        }
    }
    ## 支付订单
    public function pay_order(Order $order){
        try{
            $method_class = "\app\index\upgrade\\".$order->upgrade_type;
            $method = new $method_class($this->member,$order);
            $method->pay(); 
            return true;
        }catch(\think\Exception $error){
            throw new \think\Exception('升级失败');
        }        
    }
    ## 失效订单
    public function del_order(){
        
    }
    ## 查询订单
    public function query_order(){
        
    }
 }