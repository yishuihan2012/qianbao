<?php
/**
 * @version  CashOutCallBack 快捷支付回调 快捷支付
 * @authors John(1160608332@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */

namespace app\index\controller;

use think\Db;
use app\index\model\CashOrder;
use app\index\model\Passageway;
use app\index\model\PassagewayItem;
use app\index\model\Cashout;
use think\Request;
use app\api\controller\Commission;
use app\index\model\Commission as Commissions;
use app\index\model\Member;

class Cashoutcallback
{

    /**
     * @version  米刷快捷支付
     * @authors bill(755969423@qq.com)
     * @date    2017-12-23 16:25:05
     * @edit   xuchengcheng 2018-01-22
     * @version $Bill$
     */
    public function mishuaCallBack()
    {
        $data = file_get_contents("php://input");
        $data = trim($data);
        $data = json_decode($data, true);
        //回调详细信息 解密
        $localIV = "0102030405060708";
        if ($data['code'] == 0 && $data['mchNo']) {
            $passway = Passageway::get(['passageway_mech' => $data['mchNo']]);
        } else {
            die('找不到通道');
        }
        // 获取传过来参数
        #Open module
        $info  = AESdecrypt($data['payload'], $passway->passageway_pwd_key, $localIV);
        $datas = trim($info);
        $datas = substr($datas, 0, strpos($datas, '}') + 1);
        //返回结果
        $resul = json_decode($datas, true);
        // file_put_contents('datas3.txt',$resul);
        //订单详情
        $order = CashOrder::where(array('order_thead_no' => $resul['transNo']))->find();

        $member = Member::get($order->order_member);
        #通道费率
        $passwayitem = PassagewayItem::get(['item_group' => $member->member_group_id, 'item_passageway' => $passway->passageway_id]);
        // var_dump($passwayitem);die;
        //00代表成功
        if ($resul['status'] == '00' && $order) {
            $order->order_state = 2;
            //进行分润
            //判断之前有没有分润过
            $Commission_info = Commissions::where(['commission_from' => $order->order_id, 'commission_type' => 1])->find();
            if (!$Commission_info) {
                $fenrun        = new \app\api\controller\Commission();
                $fenrun_result = $fenrun->MemberFenRun($order->order_member, $order->order_money, $order->order_passway, 1, '快捷支付手续费分润', $order->order_id);
            } else {
                $fenrun_result['code'] = -1;
            }


            if ($fenrun_result['code'] == "200") {
                $order->order_fen      = $fenrun_result['leftmoney'];
                $order->order_buckle   = $passwayitem->item_charges / 100;
                $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
            } else {
                $order->order_fen      = -1;
                $order->order_buckle   = $passwayitem->item_charges / 100;
                $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
            }
            $res = $order->save();
            if ($resul['qfStatus'] == 'SUCCESS' || $resul['qfStatus'] == 'IN_PROCESS') {
                //订单更新成功的时候去执行分佣
                echo 'success';
                die;
            }
        }
    }

    /**
     * @version  CashOutCallBack 快捷支付回调 快捷支付0.23回调
     * @authors John(1160608332@qq.com)
     * @date    2017-09-29 16:03:05
     * @version $Bill$
     */
    public function quick023callback()
    {
        $data = file_get_contents("php://input");
        $data = trim($data);
        // file_put_contents('datas1.txt', $data);
        // file_put_contents('filecontent.txt',$data);
        $data = json_decode($data, true);
        // file_put_contents('success.txt',$data['state']);
        //回调详细信息 解密
        $localIV = "0102030405060708";
        $request = Request::instance();
        $action  = $request->controller();
        $str     = $action . "/mishuaCallBack";
        #去查找回调函数路径为这个的设置
        $passwayinfo = Cashout::where('cashout_callback', 'like', '%' . $str . '%')->find();
        if (!$passwayinfo)
            die('找不到回调地址');
        $passway = Passageway::get($passwayinfo['cashout_passageway_id']);
        if (!$passway)
            die('找不到通道');
        // 获取传过来参数
        #Open module
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $localIV);
        mcrypt_generic_init($module, $passway->passageway_pwd_key, $localIV);
        $encryptedData = base64_decode($data['payload']);
        $encryptedData = mdecrypt_generic($module, $encryptedData);
        $info          = $encryptedData;
        // file_put_contents('datas1.txt',$info);
        $datas = trim($info);
        $datas = substr($datas, 0, strpos($datas, '}') + 1);
        //file_put_contents('datas2.txt', $datas);
        //返回结果
        $resul = json_decode($datas, true);
        //file_put_contents('datas3.txt',$resul);
        //订单详情
        $order = CashOrder::where(array('order_thead_no' => $resul['transNo']))->find();

        #通道费率
        $passwayitem = PassagewayItem::get(['item_group' => $order->member, 'item_passageway' => $passway->passageway_id]);
        //00代表成功
        if ($resul['status'] == '2' && $order) {
            $order->order_state = 2;

            $Commission_info = Commissions::where(['commission_from' => $order->order_id, 'commission_type' => 1])->find();
            //进行分润
            if (!$Commission_info) {
                $fenrun        = new \app\api\controller\Commission();
                $fenrun_result = $fenrun->MemberFenRun($order->order_member, $order->order_money, $order->order_passway, 1, '快捷支付手续费分润', $order->order_id);
            } else {
                $fenrun_result['code'] = -1;
            }


            if ($fenrun_result['code'] == "200") {
                $order->order_fen      = $fenrun_result['leftmoney'];
                $order->order_buckle   = $passwayitem->item_charges / 100;
                $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges - $passway->passageway_income;
            } else {
                $order->order_fen      = -1;
                $order->order_buckle   = $passwayitem->item_charges / 100;
                $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges - $passway->passageway_income;
            }
            $res = $order->save();
            if ($resul['qfStatus'] == 'SUCCESS' || $resul['qfStatus'] == 'IN_PROCESS') {
                //订单更新成功的时候去执行分佣
                echo 'success';
                die;
            }
        }
    }


    /**
     * @version  jinyifuCallBack 快捷支付回调 金易付回调
     * @authors John(1160608332@qq.com)
     * @date    2017-09-29 16:03:05
     * @version $Bill$
     */
    public function jinyifucallback()
    {
        $data = file_get_contents("php://input");
        $data = trim($data);
        // file_put_contents('datas1.txt', $data);
        // file_put_contents('filecontent.txt',$data);
        $data = json_decode($data, true);
        // file_put_contents('success.txt',$data['state']);
        //回调详细信息 解密
        $localIV = "0102030405060708";
        $request = Request::instance();
        $action  = $request->controller();
        $str     = $action . "/mishuaCallBack";
        #去查找回调函数路径为这个的设置
        $passwayinfo = Cashout::where('cashout_callback', 'like', '%' . $str . '%')->find();
        if (!$passwayinfo)
            die('找不到回调地址');
        $passway = Passageway::get($passwayinfo['cashout_passageway_id']);
        if (!$passway)
            die('找不到通道');
        // 获取传过来参数
        #Open module
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $localIV);
        mcrypt_generic_init($module, $passway->passageway_pwd_key, $localIV);
        $encryptedData = base64_decode($data['payload']);
        $encryptedData = mdecrypt_generic($module, $encryptedData);
        $info          = $encryptedData;
        // file_put_contents('datas1.txt',$info);
        $datas = trim($info);
        $datas = substr($datas, 0, strpos($datas, '}') + 1);
        //file_put_contents('datas2.txt', $datas);
        //返回结果
        $resul = json_decode($datas, true);
        //file_put_contents('datas3.txt',$resul);
        //订单详情
        $order = CashOrder::where(array('order_thead_no' => $resul['transNo']))->find();

        #通道费率
        $passwayitem = PassagewayItem::get(['item_group' => $order->member, 'item_passageway' => $passway->passageway_id]);
        //00代表成功
        if ($resul['status'] == '2' && $order) {
            $order->order_state = 2;
            //进行分润
            $Commission_info = Commissions::where(['commission_from' => $order->order_id, 'commission_type' => 1])->find();
            //进行分润
            if (!$Commission_info) {
                $fenrun        = new \app\api\controller\Commission();
                $fenrun_result = $fenrun->MemberFenRun($order->order_member, $order->order_money, $order->order_passway, 1, '快捷支付手续费分润', $order->order_id);
            } else {
                $fenrun_result['code'] = -1;
            }

            if ($fenrun_result['code'] == "200") {
                $order->order_fen      = $fenrun_result['leftmoney'];
                $order->order_buckle   = $passwayitem->item_charges / 100;
                $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges - $passway->passageway_income;
            } else {
                $order->order_fen      = -1;
                $order->order_buckle   = $passwayitem->item_charges / 100;
                $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges - $passway->passageway_income;
            }
            $res = $order->save();
            if ($resul['qfStatus'] == 'SUCCESS' || $resul['qfStatus'] == 'IN_PROCESS') {
                //订单更新成功的时候去执行分佣
                echo 'success';
                die;
            }
        }
    }

    /**
     * @version 易宝支付回调     @method yibaoCallBack
     * @author   Bill 755969423@qq.com    @datetime  2018-01-31 13:09
     **/
    public function yibaoCallBack()
    {
        $data = file_get_contents("php://input");
        parse_str($data, $result);
        $order = CashOrder::where(['order_thead_no' => $result['requestId']])->find();         #查询到当前订单
        if ($order['order_state'] == 2)
            return 'SUCCESS';
        if ($result['status'] != 'SUCCESS')
            return 'Fail';
        $order->order_charge = $result['fee'];
        $order->order_state  = 2;
        $order->order_desc   = $order['order_desc'] . "消费成功,收款宝订单号:" . $result['externalld'];
        $order->save();
        $member                        = Member::get($order['order_member']);
        $passwayinfo                   = Passageway::get($order['order_passway']);
        $order_commis                  = CashOrder::where(['order_thead_no' => $result['requestId']])->find();
        $amount                        = $order['order_money'] - $result['fee'] - $order['order_buckle'];
        $membernetObject               = new \app\api\payment\yibaoPay($order_commis['order_passway'], $order_commis['order_member']);
        $also_result                   = $membernetObject->Draw($member->membernet[$passwayinfo['passageway_no']], $amount, "出款" . make_order());
        $order_commis->order_outmoney  = $amount;
        $order_commis->order_outstatus = $also_result['code'] != 0000 ? -1 : 2;
        $order_commis->order_outbak    = $also_result['message'] . ",流水号:" . $also_result['serialNo'];
        if ($also_result['code'] != 0000) {
            $order_commis->order_state = 1;
            $order_commis->save();
            return 'Fail';
        } else {
            $Commission_info = Commissionz::where(['commission_from' => $order_commis->order_id, 'commission_type' => 1])->find();
            if ($Commission_info) {
                $order_commis->save();
                return 'SUCCESS';
            }
            $fenrun                       = new \app\api\controller\Commissions();
            $fenrun_result                = $fenrun->MemberFenRun($order_commis->order_member, $order_commis->order_money, $order_commis->order_passway, 1, '快捷支付手续费分润', $order_commis->order_id);
            $order_commis->order_fen      = $fenrun_result['code'] == 200 ? (string)$fenrun_result['leftmoney'] : -1;
            $order_commis->order_platform = $order_commis->order_charge - ($order_commis->order_money * $passwayinfo->passageway_rate / 100) + $passwayinfo->passageway_income;
            $res                          = $order_commis->save();
            return 'SUCCESS';
        }
    }

    /**
     *  易生支付回调
     * @return [type] [description]
     */
    public function elife_notify()
    {
        $params = Request::instance()->param();
        // file_put_contents('elife.txt', json_encode($params));
        $elifepay = new \app\api\payment\Elifepay;
        // 校验签名
        $sign = $params['sign'];
        unset($params['sign']);
        $verifyResult = $elifepay->verifySignature($params, $sign);
        if (!$verifyResult) {
            // file_put_contents('elife_error.txt', 'check sign fail!');
            return null;
        }
        // 解密数据
        $randomKey  = $elifepay->merchantPrivateDecrypt($params['random_key']);
        $bizContent = $elifepay->opensslDecrypt(hex2bin(strtolower($params['biz_content'])), '16-Bytes--String', $randomKey);
        if (!$bizContent) {
            // file_put_contents('elife_error.txt', 'Decrypt fail！');
            return null;
        }
        // 去掉特殊字符
        $bizContent = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', trim($bizContent));
        // 处理业务
        $result = json_decode($bizContent, true);
        // file_put_contents('elife_result.txt', json_encode($result));


        $order = CashOrder::where(['order_no' => $result['out_trade_no']])->find();  #查询到当前订单
        if (!$order) {
            // file_put_contents('elife_error.txt', 'get order fail！');
            echo 'FAIL';
            die;
        }
        $member = Member::get($order->order_member);

        $passway = Passageway::get(['passageway_id' => $order->order_passway]);

        #通道费率
        $passwayitem = PassagewayItem::get(['item_group' => $member->member_group_id, 'item_passageway' => $passway->passageway_id]);

        $order->order_thead_no = $result['trade_no'];
        if ($result['trade_status'] == "TRADE_FINISHED") {//成功
            $order->order_state = 2;
            $returnData         = 'SUCCESS';
        }
        if ($result['trade_status'] == "TRADE_CLOSED_BY_SYS") {//超时
            $order->order_state = -2;
            $returnData         = 'FAIL';
        }
        if ($result['trade_status'] == "TRADE_CLOSED") {//关闭
            $order->order_state = -1;
            $returnData         = 'FAIL';
        }
        Db::startTrans();
        if ($result['trade_status'] == "TRADE_FINISHED") {//成功
            //进行分润
            //判断之前有没有分润过
            $Commission_info = Commissions::where(['commission_from' => $order->order_id, 'commission_type' => 1])->find();
            if (!$Commission_info) {
                $fenrun        = new \app\api\controller\Commission();
                $fenrun_result = $fenrun->MemberFenRun($order->order_member, $order->order_money, $order->order_passway, 1, '快捷支付手续费分润', $order->order_id);
            } else {
                $fenrun_result['code'] = -1;
            }
        } else {
            $fenrun_result['code'] = -1;
        }

        if ($fenrun_result['code'] == "200") {
            $order->order_fen      = $fenrun_result['leftmoney'];
            $order->order_buckle   = $passwayitem->item_charges / 100;
            $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
        } else {
            $order->order_fen      = -1;
            $order->order_buckle   = $passwayitem->item_charges / 100;
            $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
        }
        $res = $order->save();
        if ($res) {
            Db::commit();
        } else {
            Db::rollback();
            $returnData = 'FAIL';
        }
        echo $returnData;
        die;
    }

    /**
     * 易联快捷支付回调
     * @return [type] [description]
     */
    public function yilian_notify()
    {
        $params = Request::instance()->param();
        // file_put_contents('yilian.txt', json_encode($params));

        $order = CashOrder::where(['order_no' => $params['ord_no']])->find();  #查询到当前订单
        if (!$order) {
            file_put_contents('yilian_error.txt', 'get order fail！');
            echo 'FAIL';
            die;
        }
        $member = Member::get($order->order_member);

        $passway = Passageway::get(['passageway_id' => $order->order_passway]);

        #通道费率
        $passwayitem = PassagewayItem::get(['item_group' => $member->member_group_id, 'item_passageway' => $passway->passageway_id]);

        $order->order_thead_no = $params['ord_no'];
        if ($params['paysts'] == "S") {//成功
            $order->order_state = 2;
            $returnData         = 'success';
        }
        if ($params['paysts'] == "F") {//关闭
            $order->order_state = -1;
            $returnData         = 'FAIL';
        }
        Db::startTrans();
        if ($params['paysts'] == "S") {//成功
            //进行分润
            //判断之前有没有分润过
            $Commission_info = Commissions::where(['commission_from' => $order->order_id, 'commission_type' => 1])->find();
            if (!$Commission_info) {
                $fenrun        = new \app\api\controller\Commission();
                $fenrun_result = $fenrun->MemberFenRun($order->order_member, $order->order_money, $order->order_passway, 1, '快捷支付手续费分润', $order->order_id);
            } else {
                $fenrun_result['code'] = -1;
            }
        } else {
            $fenrun_result['code'] = -1;
        }

        if ($fenrun_result['code'] == "200") {
            $order->order_fen      = $fenrun_result['leftmoney'];
            $order->order_buckle   = $passwayitem->item_charges / 100;
            $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
        } else {
            $order->order_fen      = -1;
            $order->order_buckle   = $passwayitem->item_charges / 100;
            $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
        }
        $res = $order->save();
        if ($res) {
            Db::commit();
        } else {
            Db::rollback();
            $returnData = 'FAIL';
        }
        echo $returnData;
        die;
    }

    /**
     * 极易付支付回调
     * @return [type] [description]
     */
    public function jiyifucallback()
    {
        $returnData='';
        $params = Request::instance()->param();
        // print_r($params);die;
        file_put_contents('jiyifucallback.txt', json_encode($params));

        \think\Log::init(['type' => 'File', 'path' => '../runtime/api_log/']);
        $log['data'] = $params;
        \think\Log::write(json_encode($log), '接口请求日志');

        if ($params['action'] == 'WITHDRAW') {
            $order = CashOrder::where(['order_no' => $params['partnerOrderNo']])->find();  #查询到当前订单
            if (!$order) {
                // file_put_contents('yilian_error.txt', 'get order fail！');
                echo 'FAIL';
                die;
            }
            #验签
            $sign = $params['sign'];

            $member = Member::get($order->order_member);

            $passway = Passageway::get(['passageway_id' => $order->order_passway]);

            #通道费率
            $passwayitem = PassagewayItem::get(['item_group' => $member->member_group_id, 'item_passageway' => $passway->passageway_id]);

            $order->order_thead_no = $params['orderNo'];
            if ($params['status'] == "SUCCESS") {//成功
                $order->order_state = 2;
                $returnData         = 'success';
            }
            if ($params['status'] == "FAIL") {//关闭
                $order->order_state = -1;
                $returnData         = 'FAIL';
            }
            Db::startTrans();
            if ($params['status'] == "SUCCESS") {//成功
                //进行分润
                //判断之前有没有分润过
                $Commission_info = Commissions::where(['commission_from' => $order->order_id, 'commission_type' => 1])->find();
                if (!$Commission_info) {
                    $fenrun        = new \app\api\controller\Commission();
                    $fenrun_result = $fenrun->MemberFenRun($order->order_member, $order->order_money, $order->order_passway, 1, '快捷支付手续费分润', $order->order_id);
                } else {
                    $fenrun_result['code'] = -1;
                }
            } else {
                $fenrun_result['code'] = -1;
            }

            if ($fenrun_result['code'] == "200") {
                $order->order_fen      = $fenrun_result['leftmoney'];
                $order->order_buckle   = $passwayitem->item_charges / 100;
                $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
            } else {
                $order->order_fen      = -1;
                $order->order_buckle   = $passwayitem->item_charges / 100;
                $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
            }
            $res = $order->save();
            if ($res) {
                Db::commit();
            } else {
                Db::rollback();
                $returnData = 'FAIL';
            }
            echo $returnData;
            die;
        }
    }

    /**
     * 极易付支付回调
     * @return [type] [description]
     */
    public function Jinchengxinda_callback($params)
    {
        $order = CashOrder::where(['order_no' => $params['orderNo']])->find();  #查询到当前订单
        if (!$order) {
            return fail;
        }
        $member = Member::get($order->order_member);

        $passway = Passageway::get(['passageway_id' => $order->order_passway]);

        #通道费率
        $passwayitem = PassagewayItem::get(['item_group' => $member->member_group_id, 'item_passageway' => $passway->passageway_id]);

        $order->order_thead_no = $params['mchno'];
        if ($params['code'] == "200") {//成功
            $order->order_state = 2;
            $returnData         = 'success';
        } else {//关闭
            $order->order_state = -1;
            $returnData         = 'FAIL';
        }
        Db::startTrans();
        if ($params['code'] == "200") {//成功
            //进行分润
            //判断之前有没有分润过
            $Commission_info = Commissions::where(['commission_from' => $order->order_id, 'commission_type' => 1])->find();
            if (!$Commission_info) {
                $fenrun        = new \app\api\controller\Commission();
                $fenrun_result = $fenrun->MemberFenRun($order->order_member, $order->order_money, $order->order_passway, 1, '快捷支付手续费分润', $order->order_id);
            } else {
                $fenrun_result['code'] = -1;
            }
        } else {
            $fenrun_result['code'] = -1;
        }

        if ($fenrun_result['code'] == "200") {
            $order->order_fen      = $fenrun_result['leftmoney'];
            $order->order_buckle   = $passwayitem->item_charges / 100;
            $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
        } else {
            $order->order_fen      = -1;
            $order->order_buckle   = $passwayitem->item_charges / 100;
            $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
        }
        $res = $order->save();
        if ($res) {
            Db::commit();
        } else {
            Db::rollback();
            $returnData = 'FAIL';
        }
    }

    /**
     * 通联收银宝 支付回调
     */
    public function tongliancallback()
    {
        $params = Request::instance()->param();
        // file_put_contents('tonglian.txt', json_encode($params));

        $order = CashOrder::where(['order_no' => $params['outtrxid']])->find();  #查询到当前订单
        if (!$order) {
            // file_put_contents('tonglian_error.txt', 'get order fail！');
            echo 'FAIL';
            die;
        }
        $member = Member::get($order->order_member);

        $passway = Passageway::get(['passageway_id' => $order->order_passway]);

        #通道费率
        $passwayitem = PassagewayItem::get(['item_group' => $member->member_group_id, 'item_passageway' => $passway->passageway_id]);

        $order->order_thead_no = $params['trxid'];
        if ($params['trxstatus'] == '0000') {//成功
            $order->order_state = -2;
            $returnData         = 'success';
        } else {
            $returnData = 'FAIL';
        }

        Db::startTrans();
        if ($params['trxstatus'] == '0000') {//成功
            //进行分润
            //判断之前有没有分润过
            $Commission_info = Commissions::where(['commission_from' => $order->order_id, 'commission_type' => 1])->find();
            if (!$Commission_info) {
                $fenrun        = new \app\api\controller\Commission();
                $fenrun_result = $fenrun->MemberFenRun($order->order_member, $order->order_money, $order->order_passway, 1, '快捷支付手续费分润', $order->order_id);
            } else {
                $fenrun_result['code'] = -1;
            }
        } else {
            $fenrun_result['code'] = -1;
        }

        if ($fenrun_result['code'] == "200") {
            $order->order_fen      = $fenrun_result['leftmoney'];
            $order->order_buckle   = $passwayitem->item_charges / 100;
            $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
        } else {
            $order->order_fen      = -1;
            $order->order_buckle   = $passwayitem->item_charges / 100;
            $order->order_platform = $order->order_charge - ($order->order_money * $passway->passageway_rate / 100) + $passwayitem->item_charges / 100 - $passway->passageway_income;
        }
        $res = $order->save();
        if ($res) {
            Db::commit();
        } else {
            Db::rollback();
            $returnData = 'FAIL';
        }
        echo $returnData;
        die;
    }

    /**
     * 通联收银宝 提现回调
     */
    public function tonglianWithdrawcallback()
    {
        $params = Request::instance()->param();
        file_put_contents('tonglian_callback.txt', json_encode($params));

        $order = CashOrder::where(['order_no' => $params['outtrxid']])->find();  #查询到当前订单
        if (!$order) {
            // file_put_contents('tonglian_error.txt', 'get order fail！');
            echo 'FAIL';
            die;
        }
        $member = Member::get($order->order_member);

        $passway = Passageway::get(['passageway_id' => $order->order_passway]);

        #通道费率
        $passwayitem = PassagewayItem::get(['item_group' => $member->member_group_id, 'item_passageway' => $passway->passageway_id]);

        $order->order_thead_no = $params['trxid'];
        if ($params['trxstatus'] == '0000') {//成功
            $order->order_state = 2;
            $returnData         = 'success';
        } else {
            $order->order_state = -1;
            $returnData = 'FAIL';
        }
        $res = $order->save();
        echo $returnData;
        die;
    }
}