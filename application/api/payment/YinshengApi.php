<?php
namespace app\api\payment;

use app\index\model;

/**
 * 银生宝 API
 */
class YinshengApi
{
    #api url 前缀
    public $url = 'http://180.166.114.151:28084/unspay-creditCardRepayment-business/';
    #接收通知 url 前缀
    public $notify;
    #正式
    // public $accountId = '2120180601135129001';
    // public $key = '123456abc';
    #测试
    public $accountId = '1120180523103326001';
    public $key       = '123456abc';

    /**
     * 入网 查询
     */
    public function queryInfo($members)
    {
        $arr = [
            //身份证号 + "_xj_" + 通道商户号后4位
            'memberId' => $members->memberCert->cert_member_name . strtoupper($members->memberCert->cert_member_idcard) . '_xj_' . substr($this->accountId, -4),
        ];
    }

    /**
     * 绑卡查询
     */
    public function card_bind_query()
    {

        $arr = [
            'memberId'   => $this->memberId,
            'merchantNo' => $this->merchantNo,
        ];
        return $this->curl('bind/queryCardInfo', $arr);
    }

    /**
     * 解绑信用卡
     * 必须指定 $creditcard 对象
     */
    public function unbind()
    {
        $passway    = model\Passageway::get(['passageway_method' => 'yinsheng']);
        $creditpass = model\MemberCreditPas::get(['member_credit_pas_creditid' => $this->creditcard->card_id, 'member_credit_pas_pasid' => $passway->passageway_id]);
        $arr        = [
            'memberId'   => $this->memberId,
            'merchantNo' => $this->merchantNo,
            'token'      => $creditpass->member_credit_pas_info,
        ];
        return $this->curl('bind/unbindCard', $arr);
    }

    /**
     * 消费订单查询
     */
    public function queryOrder($orderNo)
    {
        $arr = [
            'orderNo' => $orderNo,
        ];
        $res = $this->form('query/queryQuickPayOrderStatus', $arr);
        return $res;
    }

    /**
     * 代付订单查询
     */
    public function queryqf($orderNo)
    {
        $arr = [
            'orderNo' => $orderNo,
        ];
        $res = $this->form('query/queryDelegatePayOrderStatus', $arr);
        return $res;
    }

    /**
     * 批次(订单号)余额查询
     */
    public function queryBalance($orderNo)
    {
        $arr = [
            'memberId'   => $this->memberId,
            'merchantNo' => $this->merchantNo,
            'batchNo' => $orderNo,
        ];
        $res = $this->curl('batch/batchBalance', $arr);
        return $res;
    }



    /**
     * 接口封装
     */
    public function curl($method, $arr)
    {
        $arr = array_merge(['accountId' => $this->accountId], $arr);
        foreach ($arr as $k => $v) {
            if(is_array($v)){
                $v = json_encode($v);
            }
            $arr[$k] = (string) $v;
        }
        $signarr        = $arr;
        $signarr['key'] = $this->key;
        $query          = urldecode(http_build_query($signarr));
        // $query = http_build_query($signarr) ;
        // halt($query);
        $arr['mac'] = strtoupper(md5($query));
        // halt($arr);
        // echo json_encode($arr);die;
        // halt($this->url . $method);
        $res = curl_post($this->url . $method, 'post', json_encode($arr));
        return json_decode($res, 1);
    }
    /**
     * form 方式 封装
     */
    public function form($method, $arr)
    {
        $arr = array_merge(['accountId' => $this->accountId], $arr);
        foreach ($arr as $k => $v) {
            if(is_array($v)){
                $v = $this->toString($v);
            }
            $arr[$k] = (string) $v;
        }
        $signarr        = $arr;
        $signarr['key'] = $this->key;
        $query          = urldecode(http_build_query($signarr));
        // $query = http_build_query($signarr) ;
        // halt($query);
        $arr['mac'] = strtoupper(md5($query));
        // halt($arr);
        // trace(json_encode($arr));
        trace(http_build_query($arr));
        // halt($this->url . $method);
        $res = curl_post($this->url . $method, 'post', http_build_query($arr), 0);
        return $res;
    }
    /**
     * 转换string
     */
    public function toString($a){
        if(is_array($a)){
            foreach ($a as $k => $v) {
                $a[$k] = $this->toString($v);
            }
            $a = json_encode($a);
        }elseif(!is_string($a)){
            $a = (string)$a;
        }
        return $a;
    }

}
