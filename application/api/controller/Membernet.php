<?php

namespace app\api\controller;

use think\Db;
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

/**
 * @version MemberNet controller / Api 代还入网
 * @author $bill$(755969423@qq.com)
 * @datetime    2017-12-08 10:13:05
 * @return
 */
class Membernet
{
    public $error;
    private $member; //会员信息
    private $membercert; //会员认证信息
    private $membercard; //会员结算卡信息
    private $passway; //通道信息
    // function __construct($memberId,$passwayId,$phone){
    //          try{
    //                #根据memberId获取会员信息和会员的实名认证信息还有会员银行卡信息
    //                $this->member=Member::get($memberId);
    //                if(! $this->member)
    //                     $this->error=314;
    //                if($this->member->member_cert!='1')
    //                     $this->error=356;
    //                $this->membercert=MemberCert::get(['cert_member_id'=>$memberId]);
    //                if(!$this->membercert)
    //                     $this->error=367;
    //                #获取用户结算卡信息
    //                $this->membercard=MemberCashcard::get(['card_member_id'=>$memberId]);
    //                if(!$this->membercard)
    //                     $this->error=459;
    //                #获取通道信息
    //                $this->passway=Passageway::get($passwayId);
    //                if(!$this->passway)
    //                     $this->error=454;
    //          }catch (\Exception $e) {
    //                $this->error=460; //TODO 更改错误码 入网失败错误码
    //          }
    //     }


    /**
     * @version bind_creditcard controller / Api 米刷绑定信用卡入网 废弃
     * @author $bill$(755969423@qq.com)
     * @datetime    2017-12-08 10:13:05
     * @return
     */
    public function mishuadaihuan($phone)
    {
        $params = array(
            'versionNo'    => '1',//接口版本号 必填  值固定为1
            'mchNo'        => $passageway->passageway_mech, //mchNo 商户号 必填  由米刷统一分配
            'mercUserNo'   => $member->member_id, //用户标识,下级机构对用户身份唯一标识。
            'userName'     => $member->member_info->cert_member_name,//姓名
            'userCertId'   => $member->member_info->cert_member_idcard,//身份证号  必填  注册后不可修改
            'userPhone'    => $phone,
            'feeRatio'     => $passageway->rate->item_also, //交易费率  必填  单位：千分位。如交易费率为0.005时,需传入5.0
            'feeAmt'       => '50',//单笔交易手续费  必填  单位：分。如机构无单笔手续费，可传入0
            'drawFeeRatio' => '0',//提现费率
            'drawFeeAmt'   => '0',//单笔提现易手续费
        );
        $url    = 'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/createMerchant';
        $income = repay_request($params, $passageway->passageway_mech, $url, $passageway->iv, $passageway->secretkey, $passageway->signkey);
        $arr    = array(
            'net_member_id'                => $member_info->cert_member_id,
            "{$passageway->passageway_no}" => $income['userNo']
        );
        return ['code' => 200, 'data' => $arr];
    }

    //删除待确认的代还计划
    public function delete_nouse_plan()
    {
        $plan_card = Generation::where(["generation_state" => 1])->select();
        foreach ($plan_card as $k => $card) {
            $res   = Generation::where(["generation_id" => $card['generation_id']])->delete();
            $order = GenerationOrder::where(["order_no" => $card['generation_id']])->delete();
        }
        echo 'success';
    }

    //执行计划
    public function action_repay_plan()
    {
        // $where['order_time']=array('lt',date('Y-m-d H:i:s',time()));
        ## 设定执行区间 避免状态修改失败 重复执行多次
        $time_start = date("Y-m-d H:i:s", time() - 60);
        $time_end   = date("Y-m-d H:i:s", time() + 60);
        $list       = GenerationOrder::where(['order_status' => 1])->whereTime('order_time', 'between', [$time_start, $time_end])->select();
        if ($list) {
            foreach ($list as $k => $v) {
                $value       = $v->toArray();
                $card_status = Generation::where(['generation_id' => $value['order_no']])->value('generation_state');
                if ($card_status == 2) {//如果是执行中的卡
                    //判断是哪个通道
                    $passageway      = Passageway::where(['passageway_id' => $value['order_passageway']])->find();
                    $passageway_mech = $passageway['passageway_mech'];
                    $action          = $passageway->Cashout->cashout_action;
                    $controller      = "app\api\controller\\" . $action;
                    //修改状态为已经执行
                     GenerationOrder::where(['order_id'=>$v['order_id']])->update(['order_status'=>5]);
                    if (!$action || $action == 'Membernet') {
                        if ($value['order_type'] == 1) { //消费
                            $this->payBindCard($value);
                        } else if ($value['order_type'] == 2) {//提现
                            $this->transferApply($value);
                        }
                    } else {
                        $action = new $controller();//实例化类
                        if ($value['order_type'] == 1) { //消费
                            $res = $action->pay($value, $passageway_mech);
                        } else if ($value['order_type'] == 2) {//提现
                            $res = $action->qfpay($value, $passageway_mech);
                        }
                    }
                }
            }
        }
    }

    /**
     * 重新执行 当天半小时以前失败次数小于3次的和未执行的 代还 订单
     */
    public function generation_order_check()
    {
        set_time_limit(600);
        $begin  = strtotime(date('Y-m-d'), time() - (90 * 60));
        $end    = time() - 60 * 60;
        $orders = db('generation_order')->alias('o')
            ->join('generation g', 'o.order_no=g.generation_id')
            ->where([
                'o.order_time'        => ['between time', [$begin, $end]],
                'o.order_status'      => ['in', '1,-1'],
                'o.order_retry_count' => ['<', 3],
                'g.generation_state'  => 2
            ])
            ->limit(100)
            ->column('order_id');
        trace('自动执行当日失败代还订单,数量' . count($orders));
        foreach ($orders as $value) {
            $this->action_single_plan($value, 1);
        }
        trace('执行完毕');
    }

    public function action_single_plan($id, $is_admin = null)
    {
        $value = GenerationOrder::where(['order_id' => $id])->find();
        if ($value['order_retry_count'] > 3) {
            return json_encode(['code' => 102, 'msg' => '最多有三次重试机会。']);
        } else {
            GenerationOrder::where(['order_id' => $id])->update(['order_retry_count' => $value['order_retry_count'] + 1]);
        }
        try {
            // print_r($value);die;
            $passageway      = Passageway::where(['passageway_id' => $value['order_passageway']])->find();
            $passageway_mech = $passageway['passageway_mech'];
            $action          = $passageway->Cashout->cashout_action;
            $controller      = "app\api\controller\\" . $action;
            if (!$action || $action == 'Membernet') {
                if ($value['order_type'] == 1) { //消费
                    $res = $this->payBindCard($value);
                } else if ($value['order_type'] == 2) {//提现
                    if (!empty($is_admin)) {
                        $res = $this->transferApply($value, null, 1);
                    } else {
                        $res = $this->transferApply($value);
                    }
                }
            } else {
                $action = new $controller();//实例化类
                if ($value['order_type'] == 1) { //消费
                    $res = $action->pay($value, $passageway_mech);
                    // var_dump($res);die;
                } else if ($value['order_type'] == 2) {//提现
                    $res = $action->qfpay($value, $passageway_mech);
                    // var_dump($res);die;
                }
            }
            return json_encode(['code' => 200, 'msg' => '执行成功。']);
        } catch (Exception $e) {
            return json_encode(['code' => 101, 'msg' => '执行失败。']);
        }
    }
    //7绑卡支付
    //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payBindCard
    public function payBindCard($pay)
    {
        $order_rate = 0;//0代表系统费率1代表订单上费率
        #1获取费率
        // print_r($pay);die;
        // 兼容老的数据没有费率的情况，新的订单都直接取订单里的费率
        if ($pay['user_rate'] > 0 || $pay['user_fix'] > 0) { //如果设置了费率
            $order_rate = 1;
            $also       = $pay['user_rate'] * 10;
            $daikou     = $pay['user_fix'] * 100;
        } else {
            $member_group_id = Member::where(['member_id' => $pay['order_member']])->value('member_group_id');
            $rate            = PassagewayItem::where(['item_passageway' => $pay['order_passageway'], 'item_group' => $member_group_id])->find();
            $also            = ($rate->item_also) * 10;
            $daikou          = ($rate->item_charges);
        }
        #2获取通道信息
        $merch = Passageway::where(['passageway_id' => $pay['order_passageway']])->find();
        // print_r($merch->passageway_mech);die;
        #3获取银行卡信息
        $card_info = MemberCreditcard::where(['card_bankno' => $pay['order_card']])->find();
        #4获取用户信息
        $member = MemberNets::where(['net_member_id' => $pay['order_member']])->find();
        // print_r($member);die;
        // print_r($pay);die;
        #5:获取用户基本信息
        $member_base = Member::where(['member_id' => $pay['order_member']])->find();
        #6获取渠道提供的费率，如果不一致，重新报备费率
        $passway_info = $this->accountQuery($pay['order_member'], $pay['order_passageway']);
        if (isset($passway_info['feeRatio']) && isset($passway_info['feeAmt'])) {
            if ($passway_info['feeRatio'] != $also || $passway_info['feeAmt'] != $daikou) {//不一致重新报备,修改商户信息
                $Membernetsedits = new \app\api\controller\Membernetsedit($pay['order_member'], $pay['order_passageway'], 'M03', '', $member_base['member_mobile']);
                if ($order_rate == 1) {
                    $update = $Membernetsedits->mishuadaihuan($also, $daikou);
                } else {
                    $update = $Membernetsedits->mishuadaihuan();
                }
            }
        }
        if (!$pay['order_platform_no'] || $pay['order_status'] != 1) {
            $update_order['order_platform_no'] = $pay['order_platform_no'] = get_plantform_pinyin() . $member_base->member_mobile . make_rand_code();
            $update_res                        = GenerationOrder::where(['order_id' => $pay['order_id']])->update($update_order);
        }
        $no     = $merch['passageway_no'];
        $userNo = $member->$no;
        $params = array(
            'mchNo'     => $merch->passageway_mech, //机构号 必填  由平台统一分配 16
            'userNo'    => $userNo,  //平台用户标识  必填  平台下发用户标识  32
            'payCardId' => $card_info->bindId, //支付卡签约ID 必填  支付签约ID，传入签约返回的平台签约ID  32
            'notifyUrl' => System::getName('system_url') . '/Api/Membernet/payCallback',  //异步通知地址  可填  异步回调地址，为空时不起推送  200
            'orderNo'   => $pay['order_platform_no'], //订单流水号 必填  机构订单流水号，需唯一 64
            'orderTime' => date('YmdHis', time() + 60),  //订单时间  必填  格式：yyyyMMddHHmmss 14
            'goodsName' => '虚拟商品',  //商品名称  必填    50
            'orderDesc' => '米刷信用卡还款', //订单描述  必填    50
            'clientIp'  => $_SERVER['REMOTE_ADDR'],  //终端IP  必填  格式：127.0.0.1  20
            'orderAmt'  => $pay['order_money'] * 100, //交易金额  必填  单位：分  整型(9,0)
            'feeRatio'  => $also,  //交易费率  必填  需与用户入网信息保持一致  数值(5,2)
            'feeAmt'    => $daikou, //交易单笔手续费   需与用户入网信息保持一致  整型(4,0)
        );
        // print_r($params);
        $income = repay_request($params, $merch->passageway_mech, 'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payBindCard', $merch->iv, $merch->secretkey, $merch->signkey);
        // print_r($income);die;
        //判断执行结果
        $is_commission = 0;
        // $arr['income_tradeNo']=$params['orderNo'];
        if ($income['code'] == '200') {
            $arr['back_tradeNo']    = $income['tradeNo'];
            $arr['back_statusDesc'] = $income['statusDesc'];
            $arr['back_status']     = $income['status'];
            if ($income['status'] == "SUCCESS") {
                $arr['order_status']            = '2';
                $generation['generation_state'] = 3;
                $arr['order_platform']          = $pay['order_pound'] - ($pay['order_money'] * $merch['passageway_rate'] / 100) - $merch['passageway_income'];
                $is_commission                  = 1;
                ##记录余额
                #0在此计划的还款卡余额中增加本次的金额 除去手续费
                db('reimbur')->where('reimbur_generation', $pay['order_no'])->setInc('reimbur_left', $pay['order_money'] - $pay['order_pound']);
            } else if ($income['status'] == "FAIL") {
                //失败推送消息
                $arr['order_status'] = '-1';
            } else {
                $arr['order_status'] = '4';
                //带查证或者支付中。。。
            }
        } else {
            $arr['back_statusDesc']         = $income['message'];
            $arr['back_status']             = 'FAIL';
            $arr['order_status']            = '-1';
            $generation['generation_state'] = -1;
            // $arr['order_buckle']=$rate['item_charges']/100;
        }
        //添加执行记录
        $res = GenerationOrder::where(['order_id' => $pay['order_id']])->update($arr);
        // 更新卡计划
        // Generation::where(['generation_id'=>$pay['order_no']])->update($generation);
        #更改完状态后续操作
        $action = $this->plan_notice($pay, $income, $member_base, $is_commission, $merch);
    }

    //计划执行完之后推送通知，分润
    public function plan_notice($pay, $income, $member_base, $is_commission = 0, $merch)
    {
        #1记录有效推荐人 #2 分润分佣 #3 短信通知 # 极光推送
        //后四位银行卡尾号
        $card_num = substr($pay['order_card'], -4);
        if ($income['code'] == '200') {
            if ($income['status'] != "FAIL") {
                if ($pay['order_type'] == 1) { //消费

                    #1分润
                    //先判断有没有分润
                    if ($pay['is_commission'] == '0' && $is_commission == 1) {
                        $has_fenrun = db('commission')->where('commission_from', $pay['order_id'])->find();
                        if (!$has_fenrun) {
                            $update_res    = GenerationOrder::where(['order_id' => $pay['order_id']])->update(['is_commission' => 1]);
                            $fenrun        = new \app\api\controller\Commission();
                            $fenrun_result = $fenrun->MemberFenRun($pay['order_member'], $pay['order_money'], $merch->passageway_id, 3, '代还分润', $pay['order_id']);
                        }
                    }
                    #2记录为 shangji 有效推荐人
                    $Plan_cation = new \app\api\controller\Planaction();
                    $Plan_cation = $Plan_cation->recommend_record($pay['order_member']);

                    #3极光推送
                    jpush($pay['order_member'], '还款计划扣款成功通知', "您制定的尾号{$card_num}的还款计划成功扣款" . $pay['order_money'] . "元，在APP内还款计划里即可查看详情。");
                } elseif ($pay['order_type'] == 2) { //还款
                    #1极光推送
                    jpush($pay['order_member'], '还款成功通知', "您制定的尾号{$card_num}的还款计划成功还款" . $pay['order_money'] . "元，在APP内还款计划里即可查看详情。");

                }
            } else if ($income['status'] == "FAIL") {
                //失败推送消息发短信
                if ($pay['order_type'] == 1) { //消费
                    //send_sms($member_base->member_mobile,"您制定的尾号{$card_num}的还款计划扣款失败，在APP内还款计划里即可查看详情。");
                    jpush($pay['order_member'], '扣款失败通知', "您制定的尾号{$card_num}的还款计划扣款" . $pay['order_money'] . "元失败，在APP内还款计划里即可查看详情。");
                } else {  //还款
                    //send_sms($member_base->member_mobile,"您制定的尾号{$card_num}的还款计划还款失败，在APP内还款计划里即可查看详情。");
                    jpush($pay['order_member'], '还款失败通知', "您制定的尾号{$card_num}的还款计划还款" . $pay['order_money'] . "元失败，在APP内还款计划里即可查看详情。");
                }
            } else {
                //带查证状态
            }
        } else if ($income['code'] == '-1') {
            //失败推送消息发短信
            if ($pay['order_type'] == 1) { //消费
                // send_sms($member_base->member_mobile,"您制定的尾号{$card_num}的还款计划扣款失败，在APP内还款计划里即可查看详情。");
                jpush($pay['order_member'], '扣款失败通知', "您制定的尾号{$card_num}的还款计划扣款" . $pay['order_money'] . "元失败，在APP内还款计划里即可查看详情。");
            } else {  //还款
                //send_sms($member_base->member_mobile,"您制定的尾号{$card_num}的还款计划还款失败，在APP内还款计划里即可查看详情。");
                jpush($pay['order_member'], '还款失败通知', "您制定的尾号{$card_num}的还款计划还款" . $pay['order_money'] . "元失败，在APP内还款计划里即可查看详情。");
            }
        }
    }

    //8:支付回调
    public function payCallback()
    {
        $data   = file_get_contents("php://input");
        $result = json_decode($data, true);
        if ($result['code'] == 0) {
            $merch = Passageway::where(['passageway_mech' => $result['mchNo']])->find();
            $datas = AESdecrypt($result['payload'], $merch->secretkey, $merch->iv);
            $datas = trim($datas);
            $datas = substr($datas, 0, strpos($datas, '}') + 1);
            file_put_contents("mishua_payCallback.txt", $datas);
            $resul = json_decode($datas, true);
            // $arr['back_tradeNo']=$resul['tradeNo'];
            $arr['back_status']     = $resul['status'];
            $arr['back_statusDesc'] = $resul['statusDesc'];
            if ($resul['status'] == "SUCCESS") {
                $arr['order_status']            = '2';
                $generation['generation_state'] = 3;

            } else if ($resul['status'] == "FAIL") {
                $arr['order_status'] = '-1';
            } else {
                $arr['order_status'] = '4';
                //带查证或者支付中。。。
            }
        }
        //更新计划表
        $update_res = GenerationOrder::where(['order_platform_no' => $resul['orderNo']])->update($arr);
        //更新卡计划
        // $id=GenerationOrder::where(['back_tradeNo'=>$resul['tradeNo']])->value('order_no');
        // Generation::where(['generation_id'=>$pay['order_no']])->update($generation);
        if ($resul['status'] == "SUCCESS") {
            $pay = GenerationOrder::where(['order_platform_no' => $resul['orderNo']])->find();
            //如果原来表里状态不是成功,添加余额。
            if ($pay['order_status'] != 2) {
                db('reimbur')->where('reimbur_generation', $pay['order_no'])->setInc('reimbur_left', $pay['order_money'] - $pay['order_pound']);
            }
            //判断有没有写入收益
            if ($pay['order_platform'] < 0.01) {
                $arr['order_platform'] = $pay['order_pound'] - ($pay['order_money'] * $merch['passageway_rate'] / 100) - $merch['passageway_income'];
                $update_res            = GenerationOrder::where(['order_platform_no' => $resul['orderNo']])->update($arr);
            }
            //成功-分润先判断有没有分润
            if ($pay['is_commission'] == '0') {
                $has_fenrun = db('commission')->where('commission_from', $pay['order_id'])->find();
                if (!$has_fenrun) {
                    $update_res    = GenerationOrder::where(['order_id' => $pay['order_id']])->update(['is_commission' => 1]);
                    $fenrun        = new \app\api\controller\Commission();
                    $fenrun_result = $fenrun->MemberFenRun($pay['order_member'], $pay['order_money'], $merch->passageway_id, 3, '代还分润', $pay['order_id']);
                }
            }
            // 极光推送
            $card_num = substr($pay['order_card'], -4);
            jpush($pay['order_member'], '还款计划扣款成功通知', "您制定的尾号{$card_num}的还款计划成功扣款" . $pay['order_money'] . "元，在APP内还款计划里即可查看详情。");
            echo "success";
            die;
        }
    }
    //9状态查询 unfinished
    //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payResultQuery
    //计划id
    public function payResultQuery($id, $is_print = '')
    {
        $generation_order = GenerationOrder::where(['order_id' => $id])->find();
        if (!$generation_order) {
            return false;
        }
        if (!$generation_order->back_tradeNo) { //无交易流水号，即失败的，不用处理
            return false;
        }
        // echo $generation_order;die;
        #2获取通道信息
        $merch = Passageway::where(['passageway_id' => $generation_order['order_passageway']])->find();
        if (!$merch) {
            return false;
        }
        #3
        $MemberNets = MemberNets::where(['net_member_id' => $generation_order['order_member']])->find();
        #5:获取用户基本信息
        // $member_base=Member::where(['member_id'=>$pay['order_member']])->find();
        $params = array(
            'mchNo'     => $merch->passageway_mech, //机构号 必填  由平台统一分配
            'userNo'    => $MemberNets->merch->passageway_no,  //平台用户标识  必填  平台下发用户标识
            'orderNo'   => $generation_order->order_platform_no,  //订单流水号 必填  机构订单流水号，需唯一
            'tradeNo'   => $generation_order->back_tradeNo,  //平台流水号 必填  绑卡支付返回的流水号
            // 'tradeDate'=>'',
            'tradeDate' => date('Ymd', strtotime($generation_order->order_time)),  //交易日期  可填  格式：yyyyMMdd为空时，仅查询仅3日内的交易数据；传入指定日期，可以查询更早前的数据
            // 'tradeDate'=>'',
        );
        // echo $generation_order->order_time;die;
        // echo json_encode($params);die;
        $income = repay_request($params, $merch->passageway_mech, 'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payResultQuery', $merch->iv, $merch->secretkey, $merch->signkey);
        if ($is_print) {
            echo json_encode($income);
        } else {
            return $income;
        }
    }
    //10.余额提现
    //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/transferApply
    public function transferApply($pay, $isCancel = null, $is_admin = '')
    {
        #1判断当天有没有失败的订单
        $today       = date('Y-m-d', strtotime($pay['order_time']));
        $fail_order  = GenerationOrder::where(['order_no' => $pay['order_no'], 'order_type' => 1])->where('order_status', 'neq', '2')->where('order_time', 'like', $today . '%')->find();
        $member_base = Member::where(['member_id' => $pay['order_member']])->find();
        $merch       = Passageway::where(['passageway_id' => $pay['order_passageway']])->find();
        // $remain_money=Reimbur::where(['reimbur_generation'=>$pay['order_no']])->find();
        // if($remain_money && $remain_money['reimbur_left']<$pay['order_money']){/
        if ($fail_order && empty($is_admin)) {//如果当天有失败订单
            $arr['back_status']     = 'FAIL';
            $arr['back_statusDesc'] = '当天有失败的订单无法进行还款，请先处理失败的订单。';
            $arr['order_status']    = '-1';
            $income['code']         = '200';
            $income['status']       = "FAIL";
        } else {
            // 兼容老的数据没有费率的情况，新的订单都直接取订单里的费率
            $order_rate = 0;//0代表系统费率1代表订单上费率
            if ($pay['user_rate'] > 0 || $pay['user_fix'] > 0) { //如果设置了费率
                $order_rate = 1;
                $also       = $pay['user_rate'] * 10;
                $daikou     = $pay['user_fix'] * 100;
            } else {
                $member_group_id = Member::where(['member_id' => $pay['order_member']])->value('member_group_id');
                $rate            = PassagewayItem::where(['item_passageway' => $pay['order_passageway'], 'item_group' => $member_group_id])->find();
                $also            = ($rate->item_qfalso) * 10;
                $daikou          = ($rate->item_qffix);
            }

            // print_r($merch->passageway_mech);die;
            #3获取银行卡信息
            $card_info = MemberCreditcard::where(['card_bankno' => $pay['order_card']])->find();
            #4获取用户信息
            $member = MemberNets::where(['net_member_id' => $pay['order_member']])->find();
            #5:获取用户基本信息
            $member_base = Member::where(['member_id' => $pay['order_member']])->find();
            #6获取渠道提供的费率，如果不一致，重新报备费率
            $passway_info = $this->accountQuery($pay['order_member'], $pay['order_passageway']);
            if (isset($passway_info['drawFeeRatio']) && isset($passway_info['drawFeeAmt'])) {
                if ($passway_info['drawFeeRatio'] != $also || $passway_info['drawFeeAmt'] != $daikou) {//不一致重新报备,修改商户信息
                    $Membernetsedits = new \app\api\controller\Membernetsedit($pay['order_member'], $pay['order_passageway'], 'M03', '', $member_base['member_mobile']);
                    if ($order_rate == 1) {
                        $update = $Membernetsedits->mishuadaihuan('', '', $also, $daikou);
                    } else {
                        $update = $Membernetsedits->mishuadaihuan();
                    }
                }
            }

            $orderTime = date('YmdHis', time() + 60);
            if (!$pay['order_platform_no'] || $pay['order_status'] != 1) {
                $update_order['order_platform_no'] = $pay['order_platform_no'] = get_plantform_pinyin() . $member_base->member_mobile . make_rand_code();
                $update_res                        = GenerationOrder::where(['order_id' => $pay['order_id']])->update($update_order);
            }

            $no     = $merch['passageway_no'];
            $userNo = $member->$no;

            $params = array(
                'mchNo'        => $merch->passageway_mech, //机构号 必填  由平台统一分配 16
                'userNo'       => $userNo,  //平台用户标识  必填  平台下发用户标识  32
                'settleBindId' => $card_info->bindId,  //提现卡签约ID 必填  提现结算的卡，传入签约返回的平台签约ID  32
                'notifyUrl'    => System::getName('system_url') . '/Api/Membernet/cashCallback',// 异步通知地址  可填  异步通知的目标地址
                'orderNo'      => $pay['order_platform_no'], //提现流水号 必填  机构订单流水号，需唯一 64
                'orderTime'    => $orderTime,//  提现时间点 必填  格式：yyyyMMddHHmmss 14
                'depositAmt'   => $pay['order_real_get'] * 100,  //提现金额  必填  单位：分  整型(9,0)
                'feeRatio'     => $also,  //提现费率  必填  需与用户入网信息保持一致  数值(5,2)
                'feeAmt'       => $daikou,//提现单笔手续费   需与用户入网信息保持一致  整型(4,0)
            );
            $income = repay_request($params, $merch->passageway_mech, 'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/transferApply', $merch->iv, $merch->secretkey, $merch->signkey);
            // print_r($income);
            //
            if ($income['code'] == '200') {
                $arr['back_tradeNo']    = $income['orderNo'];
                $arr['back_status']     = $income['status'];
                $arr['back_statusDesc'] = $income['statusDesc'];
                if ($income['status'] == "SUCCESS") {
                    $arr['order_status'] = '2';
                    #0在此计划的还款卡余额中减去本次的金额
                    db('reimbur')->where('reimbur_generation', $pay['order_no'])->setDec('reimbur_left', $pay['order_money']);
                } elseif ($income['status'] == "FAIL") {
                    //失败推送消息
                    $arr['order_status'] = '-1';
                } else {
                    $arr['order_status'] = '4';
                }
            } else {
                $arr['back_status']     = 'FAIL';
                $arr['back_statusDesc'] = $income['message'];
                $arr['order_status']    = '-1';
            }
        }
        //更新订单状态
        GenerationOrder::where(['order_id' => $pay['order_id']])->update($arr);
        //更新卡计划 判断是否是最后一次执行还款计划
        $GenerationOrder = GenerationOrder::where(['order_no' => $pay['order_no'], 'order_status' => 1])->find();
        if (!$GenerationOrder) {
            #根据传入的isCancel来判断是否是因为主动取消而结束的本次计划
            $generation_state = $isCancel ? 4 : 3;
            Generation::update(['generation_id' => $pay['order_no'], 'generation_state' => $generation_state]);
        }
        //执行完后操作
        $action = $this->plan_notice($pay, $income, $member_base, 0, $merch);
    }

    //提现回调
    public function cashCallback()
    {
        $data   = file_get_contents("php://input");
        $result = json_decode($data, true);
        // print_r($result);die;
        if ($result['code'] == 0) {
            $merch = Passageway::where(['passageway_mech' => $result['mchNo']])->find();
            $datas = AESdecrypt($result['payload'], $merch->secretkey, $merch->iv);
            $datas = trim($datas);
            $datas = substr($datas, 0, strpos($datas, '}') + 1);
            file_put_contents("mishua_cashCallback.txt", $datas);
            $resul = json_decode($datas, true);
            // $arr['back_tradeNo']=$resul['tradeNo'];
            $arr['back_status']     = $resul['status'];
            $arr['back_statusDesc'] = $resul['statusDesc'];
            if ($resul['status'] == "SUCCESS") {
                $arr['order_status'] = '2';
            } else if ($resul['status'] == "FAIL") {
                $arr['order_status'] = '-1';

            } else {
                $arr['order_status'] = '4';
                //带查证或者支付中。。。mchNo
            }
            $res = GenerationOrder::where(['order_platform_no' => $resul['orderNo']])->update($arr);
            if ($resul['status'] == "SUCCESS") {
                $pay = GenerationOrder::where(['order_platform_no' => $resul['orderNo']])->find();

                if (isset($pay['order_status']) && $pay['order_status'] != 2) {
                    db('reimbur')->where('reimbur_generation', $pay['order_no'])->setDec('reimbur_left', $pay['order_money']);
                }
                $card_num = substr($pay['order_card'], -4);
                jpush($pay['order_member'], '还款计划扣款成功通知', "您制定的尾号{$card_num}的还款计划成功还款" . $pay['order_money'] . "元，在APP内还款计划里即可查看详情。");
                echo "success";
                die;
            }
        }
    }
    //提现状态查询 unfinished
    //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/transferQuery
    public function transferQuery()
    {
        $user_merch_info = M('repay_user_merch')->where(['rm_mercUserNo' => $post['mercUserNo']])->find();
        $params          = array(
            'mchNo'       => $this->mechid, //机构号 必填
            'userNo'      => $user_merch_info['rs_userno'],  //平台用户标识  必填
            'orderNo'     => $orderNo,  //订单流水号 必填
            'depositNo'   => $depositNo,  //平台流水号 必填
            'depositDate' => $depositDate,  //交易日期  可填
        );
    }
    //3余额查询
    //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/accountQuery
    public function accountQuery($uid, $passageway_id, $is_print = "")
    {
        $passageway = Passageway::where(['passageway_id' => $passageway_id])->find();
        #4获取用户信息
        $member = MemberNets::where(['net_member_id' => $uid])->find();
        // print_r($member);die;
        $orderTime = date('YmdHis', time() + 60);
        $params    = array(
            'mchNo'  => $passageway->passageway_mech, //机构号 必填  由平台统一分配 16
            'userNo' => $member->{$passageway->passageway_no},  //平台用户标识  必填  平台下发用户标识  32
        );
        // var_dump($params);die;
        $income = repay_request($params, $passageway->passageway_mech, 'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/accountQuery', $passageway->iv, $passageway->secretkey, $passageway->signkey);
        if ($is_print) {
            echo json_encode($income);
            die;
        } else {
            return $income;
        }
    }

    public function ger_remain()
    {

        for ($i = 4; $i < 193; $i++) {
            $url = "http://lehuan.xijiakei.com/api/Membernet/accountQuery/uid/{$i}/is_print/11";
            @$res = file_get_contents($url);
            if ($res) {
                $res = json_decode($res, true);
                if (isset($res['code']) && $res['code'] == 200) {
                    $money = $res['lastAmt'] + $res['availableAmt'] + $res['refundAmt'] - $res['usedAmt'];
                    if ($money > 0) {
                        $data[$i]['money'] = $money;
                        $data[$i]['uid']   = $i;
                    }
                }
            }

        }
        print_r($data);
        die;

    }

    //米刷信息变更
    public function mishuaedit($uid = 16, $passageway = '8')
    {
        #1实名信息
        $member_info = MemberCert::where('cert_member_id=' . $uid)->find();
        #2j基本信息
        $member = Member::where('member_id=' . $uid)->find();
        #3通道信息
        $passageway = Passageway::where(['passageway_id' => $passageway])->find();
        #4会员费率
        $rate = PassagewayItem::where('item_passageway=' . $passageway . ' and item_group=' . $member['member_group_id'])->find();
        #5商户入网信息
        $member_net = MemberNets::where('net_member_id=' . $uid)->find();
        mishuaedit($passageway, $rate, $member_info, $member['member_mobile'], $member_net[$passageway['passageway_no']]);
    }
    #取消还款计划【整体】

    /**
     * @param  [type]
     * @return [type]
     */
    public function cancle_plan($generation_id)
    {
        Db::startTrans();
        $generation = Generation::get($generation_id);
        if (!$generation) {
            return ['code' => 482, 'msg' => '获取计划失败'];
        }
        if ($generation['generation_state'] == 4 || $generation['generation_state'] == 1 || $generation['generation_state'] == 3) {
            return ['code' => 483, 'msg' => '计划不在执行过程中，无法取消'];
        }
        #1如果当天没还款且有消费成功的不能取消
        $where1['order_status'] = 1;
        $where1['order_type']   = 2;
        $where1['order_member'] = $generation['generation_member'];
        $where1['order_no']     = $generation_id;

        $where2['order_status'] = 2;
        $where2['order_type']   = 1;
        $where2['order_member'] = $generation['generation_member'];
        $where2['order_no']     = $generation_id;

        $order_back = GenerationOrder::where($where1)->whereTime('order_time', 'today')->find();
        $order_cash = GenerationOrder::where($where2)->whereTime('order_time', 'today')->find();
        if ($order_back && $order_cash) {
            return ['code' => 484, 'msg' => '您当天有笔还款还未执行，暂时不能取消'];//如果当天没还款且有消费成功的不能取消
        }
        #执行取消计划操作
        $Generation       = Generation::where(['generation_id' => $generation_id])->update(['generation_state' => 4]);
        $generation_order = GenerationOrder::where(['order_no' => $generation_id, 'order_status' => 1])->update(['order_status' => 3]);
        if ($Generation && $generation_order) {
            Db::commit();
            return ['code' => 200];
        } else {
            Db::rollback();
            return ['code' => 481, 'msg' => '取消计划失败，如有疑问请联系客服。'];
        }
    }
    #取消还款计划【整体】 old
    # generation_id
    public function cancle_plans($generation_id)
    {
        Db::startTrans();
        try {
            $generation = Generation::get($generation_id);
            // $generation->generation_state=4;
            $generation_order = GenerationOrder::where(['order_no' => $generation_id, 'order_status' => 1]);
            $generation_order->update(['order_status' => 3]);
            if ($generation->generation_state == 2) {
                #执行中的，将本次计划还款卡中余额返回信用卡
                $money = db('reimbur')->where('reimbur_generation', $generation_id)->value('reimbur_left');
                if ($money > 0) {
                    $userinfo = $this->accountQuery($generation['generation_member'], $generation['generation_passway_id']);
                    // print_r($userinfo);die;
                    $realMoney = $userinfo['lastAmt'] + $userinfo['availableAmt'] - $userinfo['usedAmt'];
                    //判断本次计划还款总金额 是否不大于 商户平台中该用户的余额
                    if ($money <= $realMoney) {
                        //写入本次取消返还的还款订单
                        $reback_order = GenerationOrder::create([
                            'order_no'         => $generation_id,
                            'order_passageway' => $generation->generation_passway_id,
                            'order_member'     => $generation->generation_member,
                            'order_type'       => 2,
                            'order_card'       => $generation->generation_card,
                            'order_money'      => $money,
                            'order_pound'      => 0,
                            'order_desc'       => '取消还款计划，自动返还本次计划剩余款项',
                            'order_time'       => date('Y-m-d H:i:s'),
                        ]);
                        $this->transferApply($reback_order->toArray(), true);
                    } else {
                        Db::rollback();
                        return ['code' => 481, 'msg' => '取消计划失败，账户余额异常，如有疑问请联系客服。'];
                    }
                }
            }
            Generation::update(['generation_id' => $generation_id, 'generation_state' => 4]);
            Db::commit();
            return ['code' => 200];
        } catch (\Exception $e) {
            Db::rollback();
            echo $e->getMessage();
            die;
            return ['code' => 308, 'msg' => $e->getMessage(), 'data' => []];
        }
    }

    /**
     * 米刷入网
     * @return [type] [description]
     * 许成成 20180206
     */
    public function mishua_income()
    {
        $params     = input('');
        $passageway = Passageway::where(['passageway_id' => $params['passageway_id']])->find();
        $member_net = MemberNets::where('net_member_id=' . $params['uid'])->find();
        // 判断有没有入网：
        if (!$member_net[$passageway['passageway_no']]) {//没有入网
            $member_info = MemberCert::where('cert_member_id=' . $params['uid'])->find();
            $Members     = Member::where(['member_id' => $params['uid']])->find();
            $rate        = PassagewayItem::where('item_passageway=' . $params['passageway_id'] . ' and item_group=' . $Members['member_group_id'])->find();
            $mishua_res  = mishua($passageway, $rate, $member_info, $params['phone']);
            if ($mishua_res['code'] != 200) {
                return ['code' => '101', 'msg' => $mishua_res['message']];
            }
            $arr                                      = array(
                'net_member_id'                  => $member_info['cert_member_id'],
                "{$passageway['passageway_no']}" => $mishua_res['userNo']
            );
            $add_net                                  = MemberNets::where('net_member_id=' . $params['uid'])->update($arr);
            $member_net[$passageway['passageway_no']] = $mishua_res['userNo'];
        }
        // $passageway=Passageway::where(['passageway_id'=>$params['passageway_id']])->find();
        #绑定信用卡签约
        $data   = array(
            'mchNo'       => $passageway['passageway_mech'], //mchNo 商户号 必填  由米刷统一分配
            'userNo'      => $member_net[$passageway['passageway_no']],
            'phone'       => $params['phone'],
            'cardNo'      => $params['creditCardNo'],
            'expiredDate' => $params['expireDate'],
            'cvv'         => $params['cvv']
        );
        $url    = 'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/bindCardSms';
        $income = repay_request($data, $passageway['passageway_mech'], $url, $passageway['iv'], $passageway['secretkey'], $passageway['signkey']);

        if ($income['code'] == '200') {
            if ($income['bindStatus'] == '01') {
                $card = MemberCreditcard::where(["card_bankno" => $params['creditCardNo']])->update(['bindId' => $income['bindId'], 'bindStatus' => $income['bindStatus'], 'mchno' => $passageway['passageway_mech']]);
                return ['code' => 463, 'msg' => '签约成功'];//此卡已签约
            }
            #更新信用卡表
            $card = MemberCreditcard::where(["card_bankno" => $params['creditCardNo']])->update(['bindId' => $income['bindId'], 'bindStatus' => $income['bindStatus'], 'mchno' => $passageway['passageway_mech']]);
            return ['code' => 200, 'msg' => '短信发送成功~', 'data' => ['bindId' => $income['bindId']]];
        } else {
            return ['code' => 400, 'msg' => $income['message']];
        }
    }

    //处理没有结果的订单
    public function no_result_order()
    {
        $time_start = date('Y-m-d H:i:s', time() - 3600);
        $time_end   = date('Y-m-d H:i:s', time() - 180);
        $arr        = [];
        //查询半小时前状态为带查证的订单
        $list = GenerationOrder::where('order_status=4 or order_status=-1')->whereTime('order_time', 'between', [$time_start, $time_end])->select();
        foreach ($list as $key => $order) {
            $generation = Generation::where(['generation_id' => $order['order_no']])->find();
            //如果计划是执行中的
            if ($generation['generation_state'] == 2) {
                //判断哪个通道
                $passageway      = Passageway::where(['passageway_id' => $order['order_passageway']])->find();
                $passageway_mech = $passageway['passageway_mech'];
                $action          = $passageway->Cashout->cashout_action;

                if (!$action || $action == 'Membernet') {
                    // 米刷通道
                    $result = $this->payResultQuery($order['order_id']);
                    if ($result && $result['code'] == 200 && $result['status']) {
                        $arr['back_statusDesc'] = $result['statusDesc'];
                        if ($result['status'] == "SUCCESS") {
                            $arr['order_status'] = '2';
                        } else if ($result['status'] == "FAIL") {
                            $arr['order_status'] = '-1';

                        } else {
                            $arr['order_status'] = '4';
                            //带查证或者支付中。。。mchNo
                        }
                        $update = GenerationOrder::where(['order_id' => $order['order_id']])->update($arr);
                    }
                } else {
                    $controller = "app\api\controller\\" . $action;
                    $action     = new $controller();//实例化类
                    $res        = $action->order_status($order['order_id']);
                    if (isset($res['respCode']) && $res['respCode'] == '10000') {
                        $arr['order_status'] = 2;
                    } else if (isset($res['respCode']) && $res['respCode'] == '10001') {
                        $arr['order_status'] = -1;
                    }
                    if ($order['order_id'] == 1444) {
                    }
                    $arr['back_statusDesc'] = isset($res['respMessage']) ? $res['respMessage'] : $res['message'];
                    $update                 = GenerationOrder::where(['order_id' => $order['order_id']])->update($arr);
                }
            }
        }

    }

    /**
     * 修改还款金额
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function update_bak_money($id)
    {
        $order_info = GenerationOrder::where(['order_id' => $id])->find();
        $time       = date('Y-m-d', strtotime($order_info['order_time']));
        $list       = GenerationOrder::where(['order_no' => $order_info['order_no'], 'order_type' => 1])->where('order_time', 'like', $time . '%')->select();
        $back_money = 0;
        foreach ($list as $k => $v) {
            if ($v['order_status'] == 2) {
                if ($v['order_real_get'] > 0) {
                    $back_money = $v['order_real_get'];
                } else {
                    $back_money += ($v['order_money'] - $v['order_pound']);
                }
            }
            if ($v['order_status'] != 2) {
                $update = GenerationOrder::where(['order_id' => $v['order_id']])->update(['order_status' => 5]);
            }
        }
        $order_real_get = $back_money - ($back_money * $order_info['user_rate'] / 100 + $order_info['user_fix']);
        if ($order_real_get == $order_info['order_real_get']) {
            return json_encode(['code' => '101', 'msg' => '当前计划还款额不需要变更。']);
            die;
        }
        $res = GenerationOrder::where(['order_id' => $id])->update(['order_real_get' => $order_real_get, 'order_money' => $back_money]);
        if ($res) {
            return json_encode(['code' => '200', 'msg' => '变更成功，当前金额为' . $order_real_get]);
            die;
        } else {
            return json_encode(['code' => '101', 'msg' => '变更失败']);
            die;
        }

    }

    public function update_back_money($id)
    {
        $params                = input('');
        $order_info            = GenerationOrder::where(['order_id' => $id])->find();
        $arr['order_real_get'] = $params['money'];
        if ($order_info['user_rate'] == 0) {
            $arr['order_pound'] = $order_info['user_fix'];
        } else {
            $arr['order_pound'] = round(($order_info['user_fix'] + $params['money']) / (1 - $order_info['user_rate'] / 100), 2);
        }
        $arr['order_money']          = $arr['order_real_get'] + $arr['order_pound'];
        $arr['order_passageway_fee'] = $arr['order_money'] * $order_info['passageway_rate'] / 100 + $order_info['passageway_fix'];
        $arr['order_platform_fee']   = $arr['order_pound'] - $arr['order_passageway_fee'];
        $update                      = GenerationOrder::where(['order_id' => $order_info['order_id']])->update($arr);
        if ($update) {
            return json_encode(['code' => '200', 'msg' => '变更成功，当前金额为' . $arr['order_real_get']]);
            die;
        } else {
            return json_encode(['code' => '101', 'msg' => '变更失败']);
            die;
        }
    }

    /**
     * 修改重试次数
     * @param  string $id [description]
     * @return [type]     [description]
     */
    public function edit_pay_count($id = '')
    {
        $order = GenerationOrder::where(['order_id' => request()->param('id')])->find();
        if (!$order) {
            return json_encode(['code' => '-1', 'msg' => '找不到该订单']);
        }
        $update = GenerationOrder::where(['order_id' => request()->param('id')])->update(['order_retry_count' => 0]);
        if ($update) {
            return json_encode(['code' => '200', 'msg' => '修改成功']);
        } else {
            return json_encode(['code' => '-1', 'msg' => '未做修改']);
        }
    }
}