<?php
namespace app\api\controller;

use app\index\model;

/**
 * 银生宝
 *
 * 参数约定
 *
 * 进件信息存储 memberId,merchantNo 进件获取的这个两个字段 存在net表
 * 绑卡信息存储 token  存在 MemberCreditPas 表 member_credit_pas_info 字段
 *
 * 注意事项
 * net 进件 接口 中 aduitCode 参数 为 1018 时为通过 (文档里写的是1)
 * bind_notify 绑卡回调 中 bindCode 为1028 为绑卡成功
 *
 */
class Yinsheng extends \app\api\payment\YinshengApi
{
    #通道 对象
    public $passway;
    #用户 对象
    public $members;
    #信用卡 对象
    public $creditcard;
    #银生宝 平台会员号 存在member_net 第一个
    #370983199109202832_xj_6001,2110000000000000001342
    public $memberId = '370983199109202832_xj_6001';
    #银生宝 电商助手系统分配的用户编号 存在member_net 第2个
    public $merchantNo = '2110000000000000001342';
    #费率
    public $rate;
    #固定代扣费
    public $fix;
    public function __construct()
    {
        // config('default_return_type','json');
        $this->notify = 'http://' . $_SERVER['HTTP_HOST'] . '/api/yinsheng/';
    }
    /**
     * 入网 报件
     * web h5 接口
     * 这里只用接口方式
     */
    public function net()
    {
        //获取通道费率
        $rate =
        $arr  = [
            //身份证号 + "_xj_" + 通道商户号后4位
            'memberId'   => strtoupper($this->members->memberCert->cert_member_idcard) . '_xj_' . substr($this->accountId, -4),
            // 'memberId'=>400000654321,
            'name'       => $this->members->memberCert->cert_member_name,
            // 'name'=>'李 林',
            'certType'   => '1',
            'certNo'     => strtoupper($this->members->memberCert->cert_member_idcard),
            // 'certNo'=>'411081199112225658',
            // 'D0FeeRate'=>$this->rate,
            'D0FeeRate'  => 1,
            'D0FixedFee' => $this->fix / 100,
            // 'D0FixedFee'=>3,
            'T1FeeRate'  => 1,
            // 'T1FeeRate'=>$this->rate,
            // 'T1FixedFee'=>2,
            'T1FixedFee' => $this->fix / 100,
        ];
        // halt($this->curl('report/register',$arr));
        return $this->curl('report/register', $arr);
    }
    /**
     * 修改费率
     */
    public function rate()
    {
        $arr = [
            'memberId'   => $this->memberId,
            'merchantNo' => $this->merchantNo,
            'D0FeeRate'  => 1,
            'D0FixedFee' => $this->fix / 100,
            'T1FeeRate'  => 1,
            'T1FixedFee' => $this->fix / 100,
        ];
        return $this->curl('report/update', $arr);
    }
    /**
     * 信用卡绑定
     * web h5
     * 这里用的 h5 方式
     */
    public function bind()
    {
        // $this->creditcard = model\MemberCreditcard::get(63);
        $arr = [
            'memberId'    => $this->memberId,
            'merchantNo'  => $this->merchantNo,
            'responseUrl' => $this->notify . 'bind_notify/cardid/' . $this->creditcard->card_id,
        ];
        return $this->form('bind/h5bind', $arr);
    }
    /**
     * 信用卡绑定回调 
     * 前端回调
     */
    public function bind_notify()
    {
        $param = input();
        if ($param['result_code'] == '0000' && $param['bindCode'] == '1028') {
            $passway    = model\Passageway::get(['passageway_method' => 'yinsheng']);
            $creditpass = model\MemberCreditPas::get(['member_credit_pas_creditid' => $param['cardid'], 'member_credit_pas_pasid' => $passway->passageway_id]);
            if (!$creditpass) {
                $creditpass                             = new model\MemberCreditPas();
                $creditpass->member_credit_pas_creditid = $param['cardid'];
                $creditpass->member_credit_pas_pasid    = $passway->passageway_id;
            }
            $creditpass->member_credit_pas_info   = $param['token'];
            $creditpass->member_credit_pas_status = 1;
            $creditpass->save();
            $return['msg']="绑卡成功，请关闭当前页面重新提交";
        }else{
            $return['msg']=$param['result_msg'];
        }
        return redirect('Userurl/show_error', ['data' =>$return['msg']]);
    }
    /**
     * 交易 消费还款
     * 同时会指定还款，每笔消费 都必须指定同额度的还款
     */
    public function pay($order, $passageway_mech)
    {
        trace(111);
        $card_info=model\MemberCreditcard::where(['card_bankno'=>$order['order_card']])->find();
        $creditpass = model\MemberCreditPas::get(['member_credit_pas_creditid' => $card_info['card_id'], 'member_credit_pas_pasid' => $order['order_passageway']]);
        $arr        = [
            'repayVersion'           => '2.0',
            'orderNo'                => $order['order_platform_no'],
            'orderNo'                => $order['order_platform_no'],
            'amount'                 => round($order['order_money'], 2),
            'repayInfo'              => [
                'repayCycle'    => 'D0',
                'repayAmount'   => round($order['order_money'], 2),
                'repayOrderNo'  => 'qf_' . $order['order_platform_no'],
                'repayDateTime' => date('Y-m-d H:i', time() + 3600 * 2 /10),
            ],
            'memberId'               => $this->memberId,
            'merchantNo'             => $this->merchantNo,
            'deductCardToken'        => $creditpass->member_credit_pas_info,
            'repayCardToken'         => $creditpass->member_credit_pas_info,
            'purpose'                => '666',
            'quickPayRes'            => $this->notify . 'pay_notify',
            'delegatePayResponseUrl' => $this->notify . 'qf_notify',
        ];
        $res = $this->form('quickPayInterface/pay', $arr);
        trace('yinsheng_pay_res');
        trace($res);
    }
    /**
     * 消费 异步通知
     */
    public function pay_notify()
    {
        $param                  = input();
        $order                  = model\GenerationOrder::get(['order_platform_no' => $param['orderNo']]);
        $order->back_statusDesc = $param['result_msg'];
        if ($param['result_code'] == '0000') {
            $order->order_status = 2;
            $has_fenrun          = db('commission')
                ->where('commission_from', $order->order_id)
                ->where('commission_type', 3)
                ->find();
            if (!$has_fenrun) {
                $order->is_commission = 1;
                $fenrun               = new \app\api\controller\Commission();
                $fenrun_result        = $fenrun->MemberFenRun($order->order_member, $order->order_money, $order->order_passageway, 3, '代还分润', $order->order_id);
            }
        } else {
            $order->order_status = -1;
        }
        $order->save();
        trace('yinsheng_pay_notify');
        trace($param);
        return 'ok';
    }
    /**
     * 代付 异步通知
     */
    public function qf_notify()
    {
        $param = input();
        #取出对应消费订单
        $pay         = model\GenerationOrder::get(['order_platform_no' => substr($param['orderId'], 3)]);
        $passway     = model\Passageway::get(['passageway_method' => 'yinsheng']);
        $fee         = ceil($pay->order_money * 100 * $rate + $fix * 100) / 100;
        $passway_fee = ceil($pay->order_money * 100 * $passway->passageway_qf_rate / 100 + $passway->passageway_qf_fix * 100) / 100;
        $order = model\GenerationOrder::get(['order_platform_no' => $param['orderId']]);
        if(!$order){
            #为该笔代付 创建订单
            $order                       = new model\GenerationOrder();
            $order->order_no             = $pay->order_no;
            $order->order_member         = $pay->order_member;
            $order->order_type           = 2;
            $order->order_card           = $pay->order_card;
            $order->order_money          = $pay->order_money;
            $order->order_pound          = $passway->passageway_qf_fix;
            $order->order_real_get       = $pay->order_money - $passway->passageway_qf_fix;
            $order->order_platform_fee   = $fee - $passway_fee;
            $order->order_passageway_fee = $passway_fee;
            $order->passageway_rate      = $passway->passageway_qf_rate;
            $order->passageway_fix       = $passway->passageway_qf_fix;
            $order->user_fix             = $passway->passageway_qf_fix;
            $order->user_rate            = 0;
            $order->order_desc           = '自动代还还款';
            $order->order_time           = date('Y-m-d H:i:s');
            $order->order_passageway     = $passway->passageway_id;
            $order->order_passway_id     = $passway->passageway_id;
            $order->order_platform_no    = $param['orderId'];
        }
        if($param['result_code']=='0000'){
            $order->order_status = 2;
        }else{
            $order->order_status = -1;
        }
        $order->save();
        return 'ok';
    }
}
