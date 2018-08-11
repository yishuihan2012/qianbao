<?php
/**
 * Created by PhpStorm.
 * User: zlu
 * Date: 2018/8/6 0006
 * Time: 10:05
 */

namespace app\api\payment;

use app\index\model\Passageway;
use app\index\model\Member;
use app\index\model\System;
use app\index\model\MemberCashcard;
use app\index\model\PassagewayItem;
use app\index\model\MemberCreditcard;

class Tonglian
{

    protected $configPassway;                //用于存放该通道的配置信息
    protected $configMember;                //用于存放操作会员的基本信息
    protected $config;                              //存放基础信息

    function __construct($passwayId, $memberId)
    {
        //固定参数基本配置
        $this->config        = array(
//            'tonglianUrl' => 'https://test.allinpaygd.com/ipayapiweb/', //测试
            'tonglianUrl' => 'https://ipay.allinpay.com/apiweb/', //正式
        );
        $this->configPassway = Passageway::find($passwayId);
        if (!$this->configPassway)
            return ['code' => -404, 'msg' => '找不到此通道~'];
        $this->configMember = Member::find($memberId);
        if (!$this->configMember)
            return ['code' => -404, 'msg' => '找不到会员信息~'];
        #获取用户结算卡信息
        $this->membercard = MemberCashcard::get(['card_member_id' => $memberId]);
        if (!$this->membercard)
            return ['code' => -404, 'msg' => '找不到会员结算卡~'];
        #测试环境
//        $this->orgid  = '200000000001';//平台分配的机构号
//        $this->appid  = '0000001';//平台分配的机构APPID
//        $this->appkey = '111111';//key
        #正式环境
        $this->orgid  = '201000077740';//平台分配的机构号
        $this->appid  = '0000125';//平台分配的机构APPID
        $this->appkey = '10399a98777db00248c317c1d0f13cc4';//key

        $this->randomstr = generate_password(16);//商户自行生成的随机字符串
        $this->version   = '11';//接口版本号
        $this->reqip     = $_SERVER['REMOTE_ADDR'];//请求IP 可空
        $this->reqtime   = time();//请求时间戳

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

    //入网 商户进件
    public function addcus()
    {
        $memberAlso   = PassagewayItem::where(['item_group' => $this->configMember->member_group_id, 'item_passageway' => $this->configPassway->passageway_id])->find();
        $url          = 'org/addcus';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
//            'belongorgid'  => $this->orgid,//拓展代理商号
//            'outcusid'     => '123456789123456789',//商户外部唯一标记 //身份证号
//            'cusname'      => '大王',//商户名称
//            'cusshortname' => '大王',//商户简称
//            'merprovice'   => '530000',//所在省 山东
//            'areacode'     => '530900',//所在市 济南
//            'legal'        => '大王',//法人姓名
//            'idno'         => '370105199901015321',//法人代表证件号
//            'phone'        => '15192495297',//法人手机号码
//            'address'      => '山东省济南市天桥区齐鲁云商大厦20楼喜居集团',//注册地址
//            'acctid'       => '6216911601375970',//账户号
//            'acctname'     => '大王',//账户名
//            'accttp'       => '00',//卡折类型:00-借记卡;01-存折;
//            'expanduser'   => '大王',//拓展人
//            'prodlist'     => "[{'trxcode':'QUICKPAY_OF_HP','feerate':'0.50'},{'trxcode':'QUICKPAY_OF_NP','feerate':'0.50'},{'trxcode':'QUICKPAY_OL_HP','feerate':'0.50'}]",//支付产品信息列表
//            'settfee'      => '1.00',//提现手续费:2块/笔,该字 段填2.00，为空时，取所属代理商费率
            'belongorgid'  => $this->orgid,//拓展代理商号
            'outcusid'     => strtoupper($this->membercard->card_idcard),//商户外部唯一标记 //身份证号
            'cusname'      => $this->configMember->member_nick,//商户名称
            'cusshortname' => $this->configMember->member_nick,//商户简称
            'merprovice'   => '530000',//所在省 山东
            'areacode'     => '530900',//所在市 济南
            'legal'        => $this->membercard->card_name,//法人姓名
            'idno'         => $this->membercard->card_idcard,//法人代表证件号
            'phone'        => $this->membercard->card_phone,//法人手机号码
            'address'      => '山东省济南市天桥区齐鲁云商大厦20楼喜居集团',//注册地址
            'acctid'       => $this->membercard->card_bankno,//账户号
            'acctname'     => $this->membercard->card_name,//账户名
            'accttp'       => '00',//卡折类型:00-借记卡;01-存折;
            'expanduser'   => $this->membercard->card_name,//拓展人
            'prodlist'     => "[{'trxcode':'QUICKPAY_OF_HP','feerate':" . $memberAlso->item_rate . "},{'trxcode':'QUICKPAY_OF_NP','feerate':" . $memberAlso->item_rate . "},{'trxcode':'QUICKPAY_OL_HP','feerate':" . $memberAlso->item_rate . "},{'trxcode':'QUICKPAY_NOSMS','feerate':" . $memberAlso->item_rate . "}]",//支付产品信息列表
            'settfee'      => number_format($memberAlso->item_charges / 100, 2),//提现手续费:2块/笔,该字 段填2.00，为空时，取所属代理商费率
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //商户进件状态查询
    public function cusquery()
    {
        $url          = 'org/cusquery';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
//            'outcusid' => '123456789123456789',//商户外部唯一标记
            'outcusid' => strtoupper($this->membercard->card_idcard),//商户外部唯一标记
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //商户结算、费率信息修改
    public function updatesettinfo($cusid)
    {
        $memberAlso   = PassagewayItem::where(['item_group' => $this->configMember->member_group_id, 'item_passageway' => $this->configPassway->passageway_id])->find();
        $url          = 'org/updatesettinfo';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
//            'cusid'    => $cusid,//商户号
//            'acctid'   => '6216911601375970',//账户号
//            'accttp'   => '00',//卡折类型:00-借记卡;01-存折;
//            'prodlist' => "[{'trxcode':'QUICKPAY_OF_HP','feerate':'0.50'},{'trxcode':'QUICKPAY_OF_NP','feerate':'0.50'},{'trxcode':'QUICKPAY_OL_HP','feerate':'0.50'}]",//支付产品信息列表
//            'settfee'  => '2.00',//提现手续费:2块/笔,该字 段填2.00，为空时，取所属代理商费率
            'cusid'    => $cusid,//商户号
            'acctid'   => $this->membercard->card_bankno,//账户号
            'accttp'   => '00',//卡折类型:00-借记卡;01-存折;
            'prodlist' => "[{'trxcode':'QUICKPAY_OF_HP','feerate':" . $memberAlso->item_rate . "},{'trxcode':'QUICKPAY_OF_NP','feerate':" . $memberAlso->item_rate . "},{'trxcode':'QUICKPAY_OL_HP','feerate':" . $memberAlso->item_rate . "},{'trxcode':'QUICKPAY_NOSMS','feerate':" . $memberAlso->item_rate . "}]",//支付产品信息列表
            'settfee'  => number_format($memberAlso->item_charges / 100, 2),//提现手续费:2块/笔,该字 段填2.00，为空时，取所属代理商费率
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //绑定银行卡
    public function bindcard($cusid, $cardId)
    {
        $creditCard   = MemberCreditcard::get($cardId);
        $url          = 'org/bindcard';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
//            'cusid'     => $cusid,//商户号
//            'meruserid' => '123456789123456789',//商户外部唯一标记
//            'cardno'    => '6259588730937256',//信用卡号
//            'acctname'  => '大王',//账户名
//            'accttype'  => '02',//卡折类型:00-借记卡;02-信用卡;
//            'validdate' => '1122',//信用卡有效期
//            'cvv2'      => '123',
//            'idno'      => '370105199901015321',
//            'tel'       => '15192495297'
            'cusid'     => $cusid,//商户号
            'meruserid' => strtoupper($this->membercard->card_idcard),//商户外部唯一标记
            'cardno'    => $creditCard['card_bankno'],//信用卡号
            'acctname'  => $creditCard['card_name'],//账户名
            'accttype'  => '02',//卡折类型:00-借记卡;02-信用卡;
            'validdate' => $creditCard['card_expireDate'],//信用卡有效期
            'cvv2'      => $creditCard['card_Ident'],
            'idno'      => $creditCard['card_idcard'],
            'tel'       => $creditCard['card_phone']
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //解除绑定
    public function unbindcard($cusid, $cardId)
    {
        $creditCard   = MemberCreditcard::get($cardId);
        $url          = 'org/unbindcard';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
//            'cusid'  => $cusid,//商户号
//            'cardno' => '6259588730937256',//信用卡号
            'cusid'  => $cusid,//商户号
            'cardno' => $creditCard['card_bankno'],//信用卡号
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //重新获取验证码
    public function resendbindsms($cusid, $cardId, $thpinfo)
    {
        $creditCard = MemberCreditcard::get($cardId);
        $url        = 'org/resendbindsms';
        $dataP      = $this->paramsPublic();
        $dataS      = array(
//            'cusid'     => $cusid,//商户号
//            'meruserid' => '123456789123456789',//商户外部唯一标记
//            'cardno'    => '6259588730937256',//信用卡号
//            'acctname'  => '大王',//账户名
//            'accttype'  => '02',//卡折类型:00-借记卡;02-信用卡;
//            'validdate' => '1122',//信用卡有效期
//            'cvv2'      => '123',
//            'idno'      => '370105199901015321',
//            'tel'       => '15192495297'
            'cusid'     => $cusid,//商户号
            'meruserid' => strtoupper($this->membercard->card_idcard),//商户外部唯一标记
            'cardno'    => $creditCard['card_bankno'],//信用卡号
            'acctname'  => $creditCard['card_name'],//账户名
            'accttype'  => '02',//卡折类型:00-借记卡;02-信用卡;
            'validdate' => $creditCard['card_expireDate'],//信用卡有效期
            'cvv2'      => $creditCard['card_Ident'],
            'idno'      => $creditCard['card_idcard'],
            'tel'       => $creditCard['card_phone'],
        );
        if (!empty($thpinfo)) {
            $dataS['thpinfo'] = $thpinfo;
        }
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //绑定确认
    public function bindcardconfirm($cusid, $cardId, $smscode, $thpinfo)
    {
        $creditCard = MemberCreditcard::get($cardId);
        $url        = 'org/bindcardconfirm';
        $dataP      = $this->paramsPublic();
        $dataS      = array(
//            'cusid'     => $cusid,//商户号
//            'meruserid' => '123456789123456789',//商户外部唯一标记
//            'cardno'    => '6259588730937256',//信用卡号
//            'acctname'  => '大王',//账户名
//            'accttype'  => '02',//卡折类型:00-借记卡;02-信用卡;
//            'validdate' => '1122',//信用卡有效期
//            'cvv2'      => '123',
//            'idno'      => '370105199901015321',
//            'tel'       => '15192495297',
//            'thpinfo'   => $thpinfo,
//            'smscode'   => $smscode
            'cusid'     => $cusid,//商户号
            'meruserid' => strtoupper($this->membercard->card_idcard),//商户外部唯一标记
            'cardno'    => $creditCard['card_bankno'],//信用卡号
            'acctname'  => $creditCard['card_name'],//账户名
            'accttype'  => '02',//卡折类型:00-借记卡;02-信用卡;
            'validdate' => $creditCard['card_expireDate'],//信用卡有效期
            'cvv2'      => $creditCard['card_Ident'],
            'idno'      => $creditCard['card_idcard'],
            'tel'       => $creditCard['card_phone'],
            'smscode'   => $smscode
        );
        if (!empty($thpinfo)) {
            $dataS['thpinfo'] = $thpinfo;
        }
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);

        $result = $this->getData($url, $data);
        return $result;

    }

    //快捷交易支付申请
    public function applypay($cusid, $tradeNo, $agreeId, $price, $trxcode)
    {
        $url          = 'qpay/applypay';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
            'cusid'      => $cusid,//商户号
            'orderid'    => $tradeNo,//商户订单号
            'agreeid'    => $agreeId,//协议编号 签约返回
            'trxcode'    => $trxcode,//交易类型
            'amount'     => $price * 100,//订单金额 单位分
            'currency'   => 'CNY',//币种
            'subject'    => '订单' . $tradeNo . '的支付申请',//订单内容 订单的展示标题
            'validtime'  => '',//订单有效时间
            'trxreserve' => '备注',//交易备注 用于用户订单个性化信息 交易完成通知会带上本字段
            'notifyurl'  => System::getName('system_url') . '/index/Cashoutcallback/tongliancallback', //异步通知URL,  //后台异步通知地址'//交易结果通知地址 接收交易结果通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //交易订单确认
    public function confirmpay($cusid, $trxid, $agreeid, $smscode, $thpinfo)
    {
        $url          = 'qpay/confirmpay';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
            'cusid'   => $cusid,//商户号
            'trxid'   => $trxid,//平台交易流水号
            'agreeid' => $agreeid,//协议编号 签约返回
            'smscode' => $smscode,//短信验证码
            'thpinfo' => $thpinfo,//交易透传信息
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //快捷支付短信重新获取
    public function paysms($cusid, $trxid, $agreeid, $thpinfo)
    {
        $url          = 'qpay/paysms';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
            'cusid'   => $cusid,//商户号
            'trxid'   => $trxid,//平台交易流水号
            'agreeid' => $agreeid,//协议编号 签约返回
            'thpinfo' => $thpinfo,//交易透传信息
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //快捷交易查询
    public function query($cusid, $trxid, $orderid, $date)
    {
        $url          = 'qpay/query';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
            'cusid'   => $cusid,//商户号
            'trxid'   => $trxid,//平台交易流水号
            'orderid' => $orderid,//商户订单号
            'date'    => $date,//交易日期
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //快捷支付提现
    public function withdraw($cusid, $orderid)
    {
        $url          = 'acct/withdraw';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
            'cusid'      => $cusid,//商户号
            'orderid'    => $orderid,//商户订单号
            'isall'      => 1,//交易日期
            'trxreserve' => '订单' . $orderid . '的提现申请',//订单内容 订单的展示标题
            'notifyurl'  => System::getName('system_url') . '/index/Cashoutcallback/tongliancallback'
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
    }

    //代还
    public function quickpass($cusid, $orderid, $agreeid, $amount)
    {
        $url          = 'qpay/quickpass';
        $dataP        = $this->paramsPublic();
        $dataS        = array(
            'cusid'     => $cusid,//商户号
            'orderid'   => $orderid,//商户订单号
            'agreeid'   => $agreeid,//协议编号
            'amount'    => $amount,//订单金额
            'currency'  => 'CNY',
            'subject'   => '订单' . $orderid . '的支付申请',//订单内容 订单的展示标题
            'notifyurl' => ''
        );
        $data         = array_merge($dataP, $dataS);
        $data['sign'] = $this->getSign($data);
        $result       = $this->getData($url, $data);
        return $result;
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