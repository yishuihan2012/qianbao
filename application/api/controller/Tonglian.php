<?php
/**
 * Created by PhpStorm.
 * User: zlu
 * Date: 2018/8/10 0010
 * Time: 11:38
 */

namespace app\api\controller;

use think\Db;
use think\Session;
use think\Config;
use app\index\model\Member;
use app\index\model\System;
use app\index\model\Wallet;
use app\index\model\WalletLog;
use app\index\model\MemberGroup;
use app\index\model\PassagewayItem;
use app\index\model\MemberRelation;
use app\index\model\MemberCert;
use app\index\model\MemberCashcard;
use app\index\model\Passageway;
use app\index\model\Generation;
use app\index\model\GenerationOrder;
use app\index\model\Reimbur;
use app\index\model\MemberNet as MemberNets;
use app\index\model\MemberCreditcard;
use app\index\model\MemberCreditPas;

class Tonglian
{

    protected $config;                              //存放基础信息

    function __construct()
    {
        //固定参数基本配置
        $this->config = array(
            'tonglianUrl' => 'https://test.allinpaygd.com/ipayapiweb/', //测试
//            'tonglianUrl' => 'https://ipay.allinpay.com/apiweb/', //正式
        );
        #测试环境
        $this->orgid  = '200000000001';//平台分配的机构号
        $this->appid  = '0000001';//平台分配的机构APPID
        $this->appkey = '111111';//key
        #正式环境
//        $this->orgid  = '201000077740';//平台分配的机构号
//        $this->appid  = '0000125';//平台分配的机构APPID
//        $this->appkey = '10399a98777db00248c317c1d0f13cc4';//key

        $this->randomstr = '123456';//商户自行生成的随机字符串
        $this->version   = '11';//接口版本号
        $this->reqip     = $_SERVER['REMOTE_ADDR'];//请求IP 可空
        $this->reqtime   = time();//请求时间戳

    }

    //代还
    public function pay($order, $passageway_mech)
    {
        //获取通道信息
        $passageway = Passageway::where(['passageway_id' => $order['order_passageway']])->find();
        //获取入网信息
        $memberNet         = MemberNets::where(['net_member_id' => $order['order_member']])->find();
        $memberNet_value   = $memberNet[$passageway['passageway_no']];
        $memberNet_explode = explode(',', $memberNet_value);
        $url               = 'qpay/quickpass';
        $dataP             = $this->paramsPublic();
        $dataS             = array(
            'cusid'     => $memberNet_explode[0],//商户号
            'orderid'   => $order['order_platform_no'],//商户订单号
            'agreeid'   => $memberNet_explode[1],//协议编号
            'amount'    => $order['order_money'] * 100,//订单金额 单位分
            'currency'  => 'CNY',
            'subject'   => '订单' . $order['order_platform_no'] . '的代还申请',//订单内容 订单的展示标题
            'notifyurl' => System::getName('system_url') . "/api/Tonglian/card_quickpass_notifyUrl",
        );
        $data              = array_merge($dataP, $dataS);
        $data['sign']      = $this->getSign($data);
        $result            = $this->getData($url, $data);

        $income['code']            = -1;
        $income['msg']             = $income['msg'] = 'FAIL';
        $update['back_statusDesc'] = isset($result['errmsg']) ? $result['errmsg'] : $result['trxstatus'];
        $is_commission             = 0;

        if ($result['trxstatus'] == '0000') {
            $urlPay          = 'acct/pay';
            $dataPayS        = array(
                'cusid'      => $memberNet_explode[0],//商户号
                'orderid'    => $order['order_platform_no'],//商户订单号
                'amount'     => $order['order_money'] * 100,//订单金额 单位分
                'agreeid'    => $memberNet_explode[1],//协议号
                'trxreserve' => '订单' . $order['order_platform_no'] . '的付款订单',
                'notifyurl'  => System::getName('system_url') . "/api/Tonglian/card_pay_notifyUrl",
            );
            $dataPay         = array_merge($dataP, $dataPayS);
            $dataPay['sign'] = $this->getSign($dataPay);
            $resultPay       = $this->getData($urlPay, $dataPay);
            if ($resultPay['trxstatus'] == '0000') {
                $update['back_tradeNo'] = $result['orderid'];
                $income['code']         = 200;
                $income['back_status']  = $income['msg'] = 'success';
                $update['order_status'] = '2';
                $is_commission          = 1;
            } else if ($result['trxstatus'] == '2000') {//处理中
                $update['order_status'] = '4';
            } else {//失败
                $update['order_status'] = '-1';
            }
        } else if ($result['trxstatus'] == '2000') {//处理中
            $update['order_status'] = '4';
        } else {//失败
            $update['order_status'] = '-1';
        }
        $member_base = Member::where(['member_id' => $order['order_member']])->find();
        //添加执行记录
        $res = GenerationOrder::where(['order_id' => $order['order_id']])->update($update);
        #更改完状态后续操作
        $notice = new \app\api\controller\Membernet();
        $action = $notice->plan_notice($order, $income, $member_base, $is_commission, $passageway);
    }

    //支付回调
    public function card_quickpass_notifyUrl()
    {
        $params = input();
        file_put_contents('card_pay_notifyUrl.txt', json_encode($params));
        $pay = GenerationOrder::get(['order_platform_no' => $params['outtrxid']]);
        if ($params['trxcode'] == '0000') {//成功
            $income['code']        = 200;
            $income['back_status'] = $arr['back_status'] = 'success';
            $arr['order_status']   = '2';
            $is_commission         = 1;
        } else if ($params['trxcode'] == '2000') {//处理中
            $arr['order_status'] = '4';
        } else {//失败
            $arr['order_status'] = '-1';
            $arr['back_status']  = 'FAIL';
        }
        $arr['back_statusDesc'] = $params['trxreserved'];
        $arr['back_status']     = $params['trxcode'];
        $arr['back_tradeNo']    = $params['trxid'];
        //添加执行记录
        $pay->save($arr);
        if ($params['trxcode'] == '0000') {//成功
            // 极光推送
            if ($pay['is_commission'] == '0') {
                $has_fenrun = db('commission')->where('commission_from', $pay['order_id'])->find();
                if (!$has_fenrun) {
                    $update_res    = GenerationOrder::where(['order_id' => $pay['order_id']])->update(['is_commission' => 1]);
                    $fenrun        = new \app\api\controller\Commission();
                    $fenrun_result = $fenrun->MemberFenRun($pay['order_member'], $pay['order_money'], $pay['order_passageway'], 3, '代还分润', $pay['order_id']);
                }
            }
            $card_num = substr($pay['order_card'], -4);
            jpush($pay['order_member'], '还款计划扣款成功通知', "您制定的尾号{$card_num}的还款计划成功扣款" . $pay['order_money'] . "元，在APP内还款计划里即可查看详情。");
            echo "success";
            die;
        }
    }

    //付款回调
    public function card_pay_notifyUrl()
    {
        $params = input();
        file_put_contents('card_pay_notifyUrl.txt', json_encode($params));
        $pay = GenerationOrder::get(['order_platform_no' => $params['outtrxid']]);
        if ($params['trxcode'] == '0000') {//成功
            $income['code']        = 200;
            $income['back_status'] = $arr['back_status'] = 'success';
            $arr['order_status']   = '2';
            $is_commission         = 1;
        } else if ($params['trxcode'] == '2000') {//处理中
            $arr['order_status'] = '4';
        } else {//失败
            $arr['order_status'] = '-1';
            $arr['back_status']  = 'FAIL';
        }
        $arr['back_statusDesc'] = $params['trxreserved'];
        $arr['back_status']     = $params['trxcode'];
        $arr['back_tradeNo']    = $params['trxid'];
        //添加执行记录
        $pay->save($arr);
        if ($params['trxcode'] == '0000') {//成功
            // 极光推送
            $card_num = substr($pay['order_card'], -4);
            jpush($pay['order_member'], '还款计划到款成功通知', "您制定的尾号{$card_num}的还款计划成功到款" . $pay['order_money'] . "元，在APP内还款计划里即可查看详情。");
            echo "success";
            die;
        }
    }

    private function paramsPublic()
    {
        $data = array(
            'orgid'     => $this->orgid,
            'appid'     => $this->appid,
            'randomstr' => $this->randomstr,
//            'version'   => $this->version,
            'reqip'     => $this->reqip,
            'reqtime'   => $this->reqtime,
        );
        return $data;
    }

//拼sign
    public function getSign($data)
    {
        unset($data['sign']);
        $data['key'] = $this->appkey;
        ksort($data);
        $sign = '';
        foreach ($data as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $sign .= $key . '=' . $value . '&';
        }
        $sign = substr($sign, 0, -1);
        return strtoupper(md5($sign));
    }

//请求数据
    public function getData($url, $data = array())
    {
        $url    = $this->config['tonglianUrl'] . $url;
        $result = $this->exec($data, $url);
        $result = json_decode($result, true);
        return $result ? $result : array('code' => '9997', 'message' => '服务端数据请求错误');

    }

//exec_curl请求
    function exec($data, $url)
    {
        $ch = curl_init();
        //echo $url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if (!$result = curl_exec($ch))
            trigger_error(curl_error($ch));
        curl_close($ch);
        return $result;
    }

}