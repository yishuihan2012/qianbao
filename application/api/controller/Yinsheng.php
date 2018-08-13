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
 * 首次交易返回的批次号 batchNo 存在 MemberCreditPas 表 member_credit_pas_smsseq
 *
 * 注意事项
 * net 进件 接口 中 aduitCode 参数 为 1018 时为通过 (文档里写的是1)
 * bind_notify 绑卡回调 中 bindCode 为1028 为绑卡成功
 *
 * 后台地址
 * 测试
 * http://180.166.114.155/unspay/main.do
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
    #是否测试环境
    public $debug = 1;
    #自动还款延迟时间
    public $qf_time;
    #首次交易金额
    public $h5_pay_amount = 2;
    public function __construct()
    {
        parent::__construct();
        #设定商户信息
        $this->url       = $this->debug ? 'http://180.166.114.151:28084/unspay-creditCardRepayment-business/' : '';
        $this->accountId = $this->debug ? '1120180523103326001' : '';
        $this->key       = $this->debug ? '123456abc' : '';
        $this->qf_time   = $this->debug ? 20 : 3600;
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
            'responseUrl' => $this->notify . 'bind_notify/cardid/' . $this->creditcard->card_id . '?' . $_SERVER['QUERY_STRING'],
        ];

        $res = $this->form('bind/h5bind', $arr);
        $res = str_replace('/unspay-creditCardRepayment-business/bind/h5bindInfo', $this->url . 'bind/h5bindInfo', $res);
        return $res;
    }
    /**
     * 信用卡绑定回调
     * 前端回调
     */
    public function bind_notify()
    {
        trace('bind_notify');
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
            // $return['msg'] = "绑卡成功，请关闭当前页面重新提交";
            // 直接调用创建计划页面
            return redirect('Userurl/repayment_plan_create_detail?' . $_SERVER['QUERY_STRING']);

        } else {
            $return['msg'] = $param['result_msg'];
        }
        return redirect('Userurl/show_error', ['data' => $return['msg']]);
    }
    /**
     * 交易  H5
     */
    public function h5pay($param)
    {
        trace('yisheng_h5pay');
        $passageway = model\Passageway::get($param['passageway']);
        $card_info  = model\MemberCreditcard::get(['card_id' => $param['cardId']]);

        #生成一个独立order
        #
        $member = model\Member::get($param['uid']);
        $rate   = model\PassagewayItem::get(['item_passageway' => $param['passageway'], 'item_group' => $member->member_group_id]);
        #定义税率
        $also = ($rate->item_also) / 100;
        #定义代扣费
        $daikou = ($rate->item_charges) / 100;
        #获取代付费率
        $item_qfalso = ($rate->item_qfalso) / 100;
        #获取代付定额
        $item_qffix = ($rate->item_qffix) / 100;
        $pound             = $this->h5_pay_amount * $rate->item_also / 100 + $rate->item_qffix / 100;
        $Generation_result = new model\Generation([
            'generation_no'         => uniqidNumber(),
            'generation_count'      => 1,
            'generation_member'     => $param['uid'],
            'generation_card'       => $card_info['card_bankno'],
            'generation_total'      => $this->h5_pay_amount,
            'generation_left'       => 0,
            'generation_pound'      => $pound,
            'generation_start'      => date('Y-m-d'),
            'generation_end'        => date('Y-m-d'),
            'generation_passway_id' => $param['passageway'],
        ]);
        $Generation_result->save();

        $Userurl = new Userurl();
        // $pay = $Userurl->get_need_pay($also,$daikou,$this->h5_pay_amount);
        $real_each_get = $Userurl->get_real_money($also, $daikou, $this->h5_pay_amount, $passageway->passageway_rate, $passageway->passageway_income);
        $order         = new model\GenerationOrder([
            'order_no'             => $Generation_result->generation_id,
            'order_member'         => $param['uid'],
            'order_type'           => 2,
            'order_status'         => 5,
            'order_card'           => $card_info->card_bankno,
            'order_money'          => $this->h5_pay_amount,
            'order_pound'          => $real_each_get['fee'],
            'order_real_get'       => $real_each_get['money'],
            'order_platform_fee'   => $real_each_get['plantform_fee'],
            'order_passageway_fee' => $real_each_get['passageway_fee'],
            'passageway_rate'      => $passageway->passageway_rate,
            'passageway_fix'       => $passageway->passageway_income,
            'user_fix'             => $daikou,
            'user_rate'            => $also * 100,
            'order_desc'           => '银生宝首次验证消费~',
            'order_time'           => date('Y-m-d H:i:s'),
            'order_passageway'     => $param['passageway'],
            'order_passway_id'     => $param['passageway'],
            'order_platform_no'    => get_plantform_pinyin() . $member->member_mobile . make_rand_code(),
        ]);

        $order->save();

        // $order                                   = db('generation_order')->where(['order_id' => 1525])->find();
        $creditpass                              = model\MemberCreditPas::get(['member_credit_pas_creditid' => $card_info['card_id'], 'member_credit_pas_pasid' => $order['order_passageway']]);
        $passway                                 = model\Passageway::get(['passageway_id' => $order['order_passageway']]);
        $net                                     = model\MemberNet::get(['net_member_id' => $order['order_member']]);
        list($this->memberId, $this->merchantNo) = explode(',', $net->{$passway->passageway_no});
        $arr                                     = [
            'repayVersion'           => '2.0',
            'orderNo'                => $order['order_platform_no'],
            'amount'                 => round($order['order_money'], 2),
            'repayInfo'              => [
                'info' => [
                    [
                        'repayCycle'    => 'D0',
                        'repayAmount'   => round($order['order_money'], 2),
                        'repayOrderNo'  => 'qf' . $order['order_platform_no'],
                        'repayDateTime' => date('Y-m-d H:i', time() + $this->qf_time),
                    ],
                ],
            ],
            'memberId'               => $this->memberId,
            'merchantNo'             => $this->merchantNo,
            'deductCardToken'        => $creditpass->member_credit_pas_info,
            'repayCardToken'         => $creditpass->member_credit_pas_info,
            'purpose'                => '666',
            'quickPayResponseUrl'    => $this->notify . 'pay_notify/creditpassid/' . $creditpass->member_credit_pas_id,
            'delegatePayResponseUrl' => $this->notify . 'qf_notify',
            #返回提示页 因为 批次号 是异步通知的
            'pageResponseUrl'        => 'http://' . $_SERVER['HTTP_HOST'] . '/api/Userurl/show_error?data=验证成功 请关闭页面 重新创建还款计划',
        ];
        $this->assign('url', $this->url . 'quickPayWap/prePay');
        $this->assign('arr', $this->sign($arr));
        return view('Userurl/yinshengbao_h5pay');
        // $res = $this->form('quickPayWap/prePay', $arr);
        // trace($res);
        // return $res;
    }
    /**
     * 交易 消费还款
     * 同时会指定还款，每笔消费 都必须指定同额度的还款
     *
     *  首次交易 必须在H5页面执行 包含签约
     *
     */
    public function pay($order, $passageway_mech)
    // public function pay()
    {
        trace('yinsheng_pay_res');
        // $order = db('generation_order')->where(['order_id'=>1525])->find();

        $card_info                               = model\MemberCreditcard::where(['card_bankno' => $order['order_card']])->find();
        $creditpass                              = model\MemberCreditPas::get(['member_credit_pas_creditid' => $card_info['card_id'], 'member_credit_pas_pasid' => $order['order_passageway']]);
        $passway                                 = model\Passageway::get(['passageway_id' => $order['order_passageway']]);
        $net                                     = model\MemberNet::get(['net_member_id' => $order['order_member']]);
        list($this->memberId, $this->merchantNo) = explode(',', $net->{$passway->passageway_no});
        $arr                                     = [
            'repayVersion'           => '2.0',
            'orderNo'                => $order['order_platform_no'],
            'batchNo'                => $creditpass->member_credit_pas_smsseq,
            'amount'                 => round($order['order_money'], 2),
            'repayInfo'              => [
                [
                    'repayCycle'    => 'D0',
                    'repayAmount'   => round($order['order_money'], 2),
                    'repayOrderNo'  => 'qf' . $order['order_platform_no'],
                    'repayDateTime' => date('Y-m-d H:i', time() + $this->qf_time),
                ],
            ],
            'memberId'               => $this->memberId,
            'merchantNo'             => $this->merchantNo,
            'deductCardToken'        => $creditpass->member_credit_pas_info,
            'repayCardToken'         => $creditpass->member_credit_pas_info,
            'purpose'                => '666',
            'quickPayResponseUrl'    => $this->notify . 'pay_notify',
            'delegatePayResponseUrl' => $this->notify . 'qf_notify',
        ];
        // echo json_encode($arr['repayInfo']);die;
        // echo json_encode($arr);die;
        // trace($arr);
        // halt($arr);
        $res = $this->form('quickPayInterface/pay', $arr);
        return $res;
        trace($res);
    }
    /**
     * 消费 异步通知
     */
    public function pay_notify()
    {
        trace('yinsheng_pay_notify');
        $param = input();
        $order                  = model\GenerationOrder::get(['order_platform_no' => $param['orderNo']]);
        #首次交易
        if (input('creditpassid') && $param['result_code'] == '0000') {
            $creditpass                           = model\MemberCreditPas::get(input('creditpassid'));
            $creditpass->member_credit_pas_smsseq = $param['batchNo'];
            $creditpass->save();
            $order->order_status = 2;
            $order->save();
            return 'ok';
        }
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
        $pay         = model\GenerationOrder::get(['order_platform_no' => substr($param['orderId'], 2)]);
        $passway     = model\Passageway::get(['passageway_method' => 'yinsheng']);
        $member = model\Member::get($pay->order_member);
        $rate   = model\PassagewayItem::get(['item_passageway' => $passway->passageway_id, 'item_group' => $member->member_group_id]);
        $Userurl = new Userurl();
        #定义税率
        $also = ($rate->item_also) / 100;
        #定义代扣费
        $daikou = ($rate->item_charges) / 100;
        #获取代付费率
        $item_qfalso = ($rate->item_qfalso) / 100;
        #获取代付定额
        $item_qffix = ($rate->item_qffix) / 100;
        $real_qf_get=$Userurl->get_real_money($item_qfalso,$item_qffix,$pay->order_real_get,$passway->passageway_qf_rate,$passway->passageway_qf_fix);
        $order       = model\GenerationOrder::get(['order_platform_no' => $param['orderId']]);
        if (!$order) {
            #为该笔代付 创建订单
            $order                       = new model\GenerationOrder();
            $order->order_no             = $pay->order_no;
            $order->order_member         = $pay->order_member;
            $order->order_type           = 2;
            $order->order_card           = $pay->order_card;
            $order->order_money          = $pay->order_real_get;
            $order->order_pound          = $real_qf_get['fee'];
            $order->order_real_get       = $real_qf_get['money'];
            $order->order_platform_fee   = $real_qf_get['plantform_fee'];
            $order->order_passageway_fee = $real_qf_get['passageway_fee'];
            $order->passageway_rate      = $passway->passageway_qf_rate;
            $order->passageway_fix       = $passway->passageway_qf_fix;
            $order->user_fix             = $item_qffix;
            $order->user_rate            = $item_qfalso*100;
            $order->order_desc           = '银生宝首次验证还款';
            $order->order_time           = date('Y-m-d H:i:s');
            $order->order_passageway     = $passway->passageway_id;
            $order->order_passway_id     = $passway->passageway_id;
            $order->order_platform_no    = $param['orderId'];
        }
        if ($param['result_code'] == '0000') {
            $order->order_status = 2;
        } else {
            $order->order_status = -1;
        }
        $order->save();
        return 'ok';
    }
}
