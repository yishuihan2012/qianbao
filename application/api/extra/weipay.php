<?php
//微信支付相关参数
return [
    'app_id'       =>"wx422e2fdd35bacce2", //wx888888888  受理商户的APPID
    'mch_id'       =>"1365856702", //22222222  受理商户的商户号
    'key'          =>"XiJiaKeJi2016XiJiaKeJi2016123456",// key 47o3t53q6h91ildx18nyt9k47odry8ur
    'cert_pem_path'=>ROOT_PATH.'cert/weipay_apiclient_cert.pem',
    'key_pem_path' =>ROOT_PATH.'cert/weipay_apiclient_key.pem',
    'root_pem'     =>ROOT_PATH.'cert/weipay_rootca.pem',
    'sub_appid'   => "wx422e2fdd35bacce2", //子商户在微信开放平台上申请的APPID
    'sub_mch_id'  => "",//子商户的商户号
    'notify_url'  => "http://api.fangtoujr.com/api/Weipay/callback", //回调地址
    'check_name'  => "FORCE_CHECK",
    'transfers_url'      =>"https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers"
];
?>
