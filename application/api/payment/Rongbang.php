<?php
namespace app\api\payment;

use app\index\model\Passageway;

class Rongbang
{
    /**
     * 订单查询
     * pay_status 1待支付 2成功 -1 失败 -2超时
     * qf_status -1代付失败  1代付中 2代付成功
     * resp_message 交易成功
     */
    public function order_query($order)
    {
        $ordernumber = $order->order_no;
        $p           = Passageway::get($order->order_passway);
        $userinfo    = db('member_net')->where('net_member_id', $order->order_member)->value($p->passageway_no);
        $userinfo    = explode(',', $userinfo);
        $arr         = [
            'ordernumber' => $ordernumber,
            'companyid'   => $userinfo[0],
        ];
        $data   = rongbang_curl($p, $arr, 'masget.pay.compay.router.paymentjournal.get');
        $result = [
            'pay_status'   => -1,
            'qf_status'    => -1,
            'resp_message' => 'xj:接口查询失败或返回参数不全',
        ];
        if (isset($data['ret']) && $data['ret'] == 0) {
            $code                = $data['data']['respcode'];
            $result['qf_status'] = -1;
            if ($code == 1 || $code == 5) {
                $result['pay_status'] = 1;
            } elseif ($code == 2) {
                $result['pay_status'] = 2;
                $result['qf_status']  = 2;
            } else {
                $result['pay_status'] = -1;
            }
            $result['resp_message'] = $data['data']['respmsg'];
        } elseif (isset($data['message'])) {
            $result['resp_message'] = $data['message'];
        }
        return $result;
    }
}
