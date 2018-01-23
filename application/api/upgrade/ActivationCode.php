<?php
/*
 *  激活码升级
 * @Author: John（1160608332@qq.com） 
 * @Date: 2018-01-17 11:53:08 
 * @Last Modified by: John（1160608332@qq.com）
 * @Last Modified time: 2018-01-17 15:42:43
 */
namespace app\api\upgrade;

use think\Db;
use app\index\model\Member;
use app\index\model\Order;
use app\index\model\ActivationCode as ActivationCodeData;
 
 class ActivationCode
 {
    protected $member;
    protected $order;
    public function __construct(Member $member,Order $order){
        parent::__construct();
        $this->member   =   $member;
        $this->order   =   $order;
    }
    ## 会员升级 需要升级的会员,相关订单
    public function pay(){
        try{
            ## 查询激活码是否存在 状态是否正常
            $option = unserialize($this->order->upgrade_option);
            ## 校验验证码
            $activation = ActivationCodeData::get(['activation_code_key'=>$option['key'],'activation_code_pwd'=>$option['pwd']]);
            if(!$activation){
                throw new \think\Exception('激活码校验失败');
            }
            # 更改订单状态
            Db::startTrans();
            $this->order->upgrade_state=1;
            ## 更改会员组
            $this->member->member_group_id = $this->order->upgrade_group_id;
            if(false===$this->order->save() || false===$this->member->save()){
                Db::rollback();
                throw new \think\Exception('升级失败');
            }
            Db::commit();
            return true;
        }catch(\think\Exception $error){
            Db::rollback();
            throw new \think\Exception($error->getMessage());
        }
    }
 }