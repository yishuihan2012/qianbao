<?php
/**
 * @authors John(1160608332@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use think\Loader;
use think\Config;
use app\index\model\Order as Orders;
use app\index\model\System;
use app\index\model\Upgrade as Upgrades;
use app\index\model\Wallet as Wallets;
use app\index\model\Withdraw as Withdraws;
use app\index\model\CallbackLog as CallbackLogs;

 class Alipay
 {
      protected $aop;
      protected $order;
      public function __construct()
      {
           Loader::import('alipay.AopSdk');
           $this->aop = new \AopClient();
           // $this->aop->appId                        = System::getName('Alipay_appid');
           // // $this->aop->format                       = Config::get('alipay.format');
           // $this->aop->format                       = System::getName('Alipay_format');
           // $this->aop->signType                     = Config::get('alipay.sign_type');
           // // $this->aop->signType                     = System::getName('Alipay_sign_type');
           // $this->aop->gatewayUrl                   = Config::get('alipay.gateway_url');
           // // $this->aop->gatewayUrl                   = System::getName('alipay_gateway_url');
           // $this->aop->apiVersion                   = Config::get('alipay.app_version');
           // // $this->aop->apiVersion                   = System::getName('alipay_app_version');
           // $this->aop->postCharset                  = Config::get('alipay.post_charset');
           // // $this->aop->postCharset                  = System::getName('alipay_post_charset');
           // $this->aop->rsaPrivateKey                = System::getName('Alipay_secretkey');
           // $this->aop->alipayrsaPublicKey           = System::getName('Alipay_key');
           // $this->aop->alipaycallback               = System::getName('system_url').System::getName('Alipay_callback');
           // // print_r($this->aop);die;


           $this->aop->appId                        = Config::get('alipay.app_id');
           $this->aop->format                       = Config::get('alipay.format');
           $this->aop->signType                     = Config::get('alipay.sign_type');
           $this->aop->gatewayUrl                   = Config::get('alipay.gateway_url');
           $this->aop->apiVersion                   = Config::get('alipay.app_version');
           $this->aop->postCharset                  = Config::get('alipay.post_charset');
           $this->aop->rsaPrivateKey                = Config::get('alipay.rsa_private_key');
           $this->aop->alipayrsaPublicKey           = Config::get('alipay.rsa_public_key');
           $this->aop->alipaycallback               = System::getName('system_url').System::getName('Alipay_callback');
      }

      /**
       * 支付
       * @param  Orders $order [description]
       * @return [type]        [description]
       */
      public function pay($order)
      {
           try {
                 $request = new \AlipayTradeAppPayRequest();
                 $bizcontent = "{\"body\":\"".$order['upgrade_bak']."\","
                      . "\"subject\": \"".$order['upgrade_bak']."\","
                      . "\"out_trade_no\": \"".$order['upgrade_no']."\","
                      . "\"timeout_express\": \"".Config::get('alipay.timeout_express')."\","
                      . "\"total_amount\": \"".($order['upgrade_money'])."\"," //支付宝支付 单位为 元
                      . "\"product_code\":\"".Config::get('alipay.product_code')."\""
                      . "}";
                 $request->setNotifyUrl($this->aop->alipaycallback);
                 $request->setBizContent($bizcontent);
                 $response = $this->aop->sdkExecute($request);
                 return $response;
           } catch (\Exception $e) {
                  return false;
                 // return ['code'=>344,"msg"=>$e->getMessage()];
           }
      }

      /**
       * 回调
       * @param  [type]   $post [description]
       * @return function       [description]
       */
      public function callback($post)
      {
            try {
              // var_dump($post);die;
              // var_dump($this->aop->rsaCheckV1($post, null,"RSA2"));die;
                 if (!$this->aop->rsaCheckV1($post, null,"RSA2")){
                    echo "FAIL33";
                 }
                 if ($post['trade_status']=='WAIT_BUYER_PAY'){
                    echo "FAIL";die;
                 }
                 if ($post['trade_status']=='TRADE_CLOSED'){
                    echo "FAIL";die;
                 }
                 if ($post['trade_status']=='TRADE_FINISHED'){
                    echo "FAIL";die;
                 }

                 if ($post['trade_status']=='TRADE_SUCCESS') {
                      $order=Upgrades::get(['upgrade_no'=>$post['out_trade_no']]); //订单信息
                      if ($order->upgrade_state>0)//判断当前订单状态已支付 结束
                           return "FAIL";
                      if ($post['total_amount']!=($order->upgrade_money)) //回调金额（元） 与订单金额不符（分） 结束
                           return "FAIL";
                      $order->upgrade_state=1; //更改状态为已支付
                      $order->upgrade_type="Alipay";
                      $order->upgrade_alipay_no=$post['trade_no'];
                      $order->upgrade_update_time=date("Y-m-d H:i:s",time());
                      if (false===$order->save()) //修改失败 结束
                           return "FAIL";
                      return "SUCCESS";
                 }
           } catch (\Exception $e) {
                 return "FAIL".$e->getMessage();
           }
      }

      /**
       * 支付订单查询接口
       * @param  [string] $out_trade_no [平台订单号]
       * @param  [string] $alipay_order [支付宝订单号]
       * @return [array]               [返回详细支付订单内容]
       */
      public function query_order($out_trade_no)
      {
            $request = new \AlipayTradeQueryRequest ();
            $request->setBizContent("{" .
            "\"out_trade_no\":\"$out_trade_no\"" .
            "}");
            $result = $this->aop->execute ($request); 
            $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
            // $resultCode = $result->$responseNode->code;
            return $result->$responseNode;
      }

      /**
       * 转账
       * @param  Obj $withdraws [description]
       * @return [type]               [description]
       */
      public function transfer(Withdraws $withdraws)
      {
           $system_name=System::getName('sitename').$withdraws->withdraw_name.'提现';
           $request = new \AlipayFundTransToaccountTransferRequest();
           // 判断账户信息
           $payee_type ="ALIPAY_USERID"; //默认转账方式
           if(preg_match("/^1[34578]\d{9}$/", $withdraws->withdraw_account) || filter_var($withdraws->withdraw_account,FILTER_VALIDATE_EMAIL))
                 $payee_type ="ALIPAY_LOGONID";
           $request->setBizContent("{" .
                 "\"out_biz_no\":\"".$withdraws->withdraw_no."\"," .
                 "\"payee_type\":\"".$payee_type."\"," .
                 "\"payee_account\":\"".$withdraws->withdraw_account."\"," .
                 "\"amount\":\"".($withdraws->withdraw_amount)."\"," . //转账金额 单位 元
                 "\"payer_show_name\":\"{$system_name}\"," .
                 "\"payee_real_name\":\"".$withdraws->withdraw_name."\"," .
                 "\"remark\":\"{$system_name}\"" .
            "  }");
           $result = $this->aop->execute($request);
           $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
           if ($result->$responseNode->code != "10000")
                 return ['code'=>'356','msg'=>"支付失败~".$result->$responseNode->sub_msg];
           return ['code'=>'200'];
      }

      /**
       * 签名
       * @return [type] [description]
      */
      public function get_sign()
      {
           try {
                 $sign_data = [
                      'apiname'       => 'com.alipay.account.auth',
                      'method'        => 'alipay.open.auth.sdk.code.get',
                      'app_name'      => 'mc',
                      'biz_type'      => 'openservice',
                      'pid'           => System::getName('alipay_pid'),
                      // 'pid'           =>Config::get('alipay.pid'),
                      'product_id'    => 'APP_FAST_LOGIN',
                      'scope'         => 'kuaijie',
                      'target_id'     => get_token(),
                      'auth_type'     => 'AUTHACCOUNT',
                      // 'app_id'        =>Config::get('alipay.app_id'),
                      'app_id'        =>System::getName('Alipay_appid'),
                 ];
                 $sign =   $this->aop->generateSign($sign_data);
                 // var_dump($sign);die;
                 $sign_data['sign_type']  = "RSA2";
                 $sign_data['sign']  = $sign;
                 $sign_content = $this->aop->getSignContentUrlencode($sign_data);
                 if (!$sign_content || !$sign)
                      return ['code'=>364];
                 return ['code'=>200,'data'=>['sign'=>$sign_content]];
           } catch (\Exception $e) {
                 return ['code'=>364];
           }
      }

      
    /**
     * 获取支付宝信息 TODO 授权获取信息
     * @return [type] [description]
     */
    public function get_info($auth_code)
    {
        try {
            $request = new \AlipayOpenAuthTokenAppRequest();
            $request->setBizContent("{" .
           "\"grant_type\":\"authorization_code\"," .
           "\"code\":\"".$auth_code."\"," .
           "  }");
            $result = $this->aop->execute($request);
            $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
            $resultCode = $result->$responseNode->code;
            if (!empty($resultCode) &&  $resultCode == 10000) {
                $request_info = new \AlipayUserInfoShareRequest();
                $result_info = $this->aop->execute($request_info, $result->$responseNode->app_auth_token);
                $response_node_info = str_replace(".", "_", $request_info->getApiMethodName()) . "_response";
                $result_code = $result_info->$response_node_info->code;
                if (!empty($result_code) && $result_code == 10000) {
                    return ['code'=>200,'data'=>['user_id'=>$result_info->$response_node_info->user_id,'is_certified'=>$result_info->$response_node_info->is_certified]];
                } else {
                    return ['code'=>365,"msg"=>$result_info->$response_node_info->sub_msg];
                }
            } else {
                return ['code'=>365,'msg'=>$result->$responseNode->sub_msg];
            }
        } catch (\Exception $e) {
            return ['code'=>365];
        }
    }
}
