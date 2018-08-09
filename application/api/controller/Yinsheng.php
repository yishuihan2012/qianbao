<?php
namespace app\api\controller;

use app\index\model;

/**
 * 银生宝
 *
 * 注意事项
 * 返回结果 中 aduitCode 参数 为 1018 时为通过 (文档里写的是1)
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
    public $memberId;
    #银生宝 电商助手系统分配的用户编号 存在member_net 第2个
    public $merchantNo;
    #费率
    public $rate;
    #固定代扣费
    public $fix;
    public function __construct()
    {
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
     * 这里用的web 方式
     */
    public function bind()
    {
        $arr = [
            'memberId'    => $this->memberId,
            'merchantNo'  => $this->merchantNo,
            'responseUrl' => 'http://'.$_SERVER['HTTP_HOST'].'/api/yinsheng/bind_notify/cardid/'.$this->creditcard->card_bankno,
        ];
        return $this->curl('bind/h5bind', $arr);
    }
    /**
     * 信用卡绑定回调
     */
    public function bind_notify(){
        $arr = input();

    }
    /**
     * 绑卡查询
     */
    public function card_bind_query(){
        $arr = [
            'memberId'   => $this->memberId,
            'merchantNo' => $this->merchantNo,
        ];
        return $this->curl('bind/queryCardInfo', $arr);
    }
}
