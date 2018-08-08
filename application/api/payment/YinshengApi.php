<?php
namespace app\api\payment;
/**
* 银生宝 API
*/
class YinshengApi
{
    public $url = 'http://180.166.114.151:28084/unspay-creditCardRepayment-business/';
    #正式
    // public $accountId = '2120180601135129001';
    // public $key = '123456abc';
    #测试
    public $accountId = '1120180523103326001';
    public $key = '123456abc';

    /**
     * 入网 查询
     */
    public function queryInfo($members){
        $arr = [
            //身份证号 + "_xj_" + 通道商户号后4位
            'memberId'=>$members->memberCert->cert_member_name . strtoupper($members->memberCert->cert_member_idcard) . '_xj_'. substr($this->accountId,-4),
        ];
    }

    /**
     * 订单查询
     */
    public function queryOrder(){
        $arr = [
            'orderNo'=>'sdfsdfsdf23432432',
        ];
        $res = $this->curl('query/queryQuickPayOrderStatus',$arr);
        halt($res);
    }


    /**
     * 接口封装
     */
    public function curl($method,$arr){
        $arr = array_merge(['accountId'=>$this->accountId],$arr);
        foreach ($arr as $k => $v) {
            $arr[$k] = (string)$v;
        }
        $signarr = $arr;
        $signarr['key'] = $this->key;
        $query = urldecode(http_build_query($signarr)) ;
        // $query = http_build_query($signarr) ;
        // halt($query);
        $arr['mac'] = strtoupper(md5($query));
        // halt($arr);
        // echo json_encode($arr);die;
        // halt($this->url . $method);
        $res = curl_post($this->url . $method,'post',json_encode($arr));
        return json_decode($res,1);
    }
    
}