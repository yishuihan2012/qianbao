<?php

namespace app\api\controller;

use think\Db;
use app\index\model\Member;
use app\index\model\System;
use app\index\model\Passageway;
use app\index\model\PassagewayItem;
use app\index\model\MemberCreditcard;
use app\index\model\MemberNet as MemberNets;
use app\index\model\MemberCreditPas;
use app\index\model\Generation;
use app\index\model\GenerationOrder;
class Misdhnew{
	protected $mech='100464';
	protected $secretkey='H54H745496Y4569H';
	protected $signkey='G54Y4R';
	protected $iv="0102030405060708";
	protected $url="http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans";
	public function __construct(){

	}
	/**
	 * 商户入网
	 */
	public function income($passageway_id,$uid){

		$passageway = Passageway::where(['passageway_id' => $passageway_id])->find();
		$member_info = Member::where('member_id=' . $uid)->find();
        $rate        = PassagewayItem::where('item_passageway=' . $passageway_id . ' and item_group=' . $member_info->member_group_id)->find();
        if(!$member_info || !$member_info || !$rate){
        	return ['code'=>'-1','data'=>'获取参数失败'];
        }
        $params = array(
	        'versionNo'    => '1',//接口版本号 必填  值固定为1
	        'mchNo'        => $this->mech, //mchNo 商户号 必填  由米刷统一分配
	        'mercUserNo'   => $this->mech . $member_info->MemberCashcard->card_phone, //用户标识,下级机构对用户身份唯一标识。
	        'userName'     => $member_info->MemberCashcard->card_name,//姓名
	        'userCertId'   => $member_info->MemberCashcard->card_idcard,//身份证号  必填  注册后不可修改
	        'userPhone'    => $member_info->MemberCashcard->card_phone,
	        'cardNo'       => $member_info->MemberCashcard->card_bankno,//姓名
	        'feeRatio'     => $rate['item_also'] * 10, //交易费率  必填  单位：千分位。如交易费率为0.005时,需传入5.0
	        'feeAmt'       => '0',//单笔交易手续费  必填  单位：分。如机构无单笔手续费，可传入0
	        'drawFeeRatio' => '0',//提现费率
	        'drawFeeAmt'   => $rate['item_qffix'],//单笔提现易手续费
	    );
	    $url    = $this->url.'/createMerchant';
	    $income = repay_request($params, $this->mech, $url, $this->iv, $this->secretkey, $this->signkey);
	    return $income;	
	}
	public function mech_update($userNo,$userName,$userCertId,$userPhone,$feeRatio,$drawFeeAmt){
		$params=array(
			'versionNo'=>'1',//	接口版本号	必填	值固定为1	2
			'mchNo'=>$this->mech,//	机构号	必填	由平台统一分配	16
			'userNo'=>$userNo,//	平台用户标识	必填	平台下发用户标识	32
			'userName'=>$userName,//	姓名	必填		50
			'userCertId'=>$userCertId,//	身份证号	必填		64
			'userPhone'=>$userPhone,//	用户联系电话	必填		20
			'feeRatio'=>$feeRatio,//	交易费率	必填	单位：千分位。如交易费率为0.005时,需传入5.0	数值(5,2)
			'feeAmt'=>'0',//	单笔交易手续费	必填	单位：分。(通常为0，如需变更请跟运营人员沟通)	整型(4,0)
			'drawFeeRatio'=>"0",//	提现费率	必填	单位：千分位。(通常为0，如需变更请跟运营人员沟通)	数值(5,2)
			'drawFeeAmt'=>$drawFeeAmt,//	单笔提现易手续费	必填	单位：整型-分。如机构无单笔手续费，可传入0	整型(4,0)
		);
		$url    = $this->url.'/updateMerchant';
	    $income = repay_request($params, $this->mech, $url, $this->iv, $this->secretkey, $this->signkey);
	    var_dump($income);die;
	    return $income;	
	}
	/**
	 * 信用卡签约查询
	 */
	public function sign_search($userNo,$card_bankno){
		$res=0;
		$params=array(
			'mchNo'=>$this->mech,
			'userNo'=>$userNo,
		);
		// echo json_encode($params);die;
		$url    = $this->url.'/listBindCards';
	    $result = repay_request($params, $this->mech, $url, $this->iv, $this->secretkey, $this->signkey);
	    // var_dump($result);die;
	    if($result['code']==200 && isset($result['cards'])){
	    	foreach ($result['cards'] as $key => $card) {
	    		if($card['cardNo']==$card_bankno && $card['bindStatus']=='01'){
	    			$res=1;
	    		}
	    	}
	    }else{
	    	$res=0;
	    }
	    return $res;
	}
	/**
	 * 卡签约
	 */
	public function sign_card($userNo,$phone,$cardNo,$expiredDate,$cvv){
		$params= array(
            'mchNo'       => $this->mech, //mchNo 商户号 必填  由米刷统一分配
            'userNo'      => $userNo,
            'phone'       => $phone,
            'cardNo'      => $cardNo,
            'expiredDate' => $expiredDate,
            'cvv'         => $cvv
        );
        // echo json_encode($params);die;
        $url    =  $this->url.'/bindCardSms';
        $res = repay_request($params, $this->mech, $url, $this->iv, $this->secretkey, $this->signkey);
        // var_dump($res);die;
        return $res;
	}
	/**
	 * 订单状态查询
	 */
	public function order_status($uid, $passageway_id, $is_print = "")
    {
        if(!$uid){
            return false;
        }
        $passageway = Passageway::where(['passageway_id' => $passageway_id])->find();
        if(!$passageway){
            return false;
        }
        #4获取用户信息
        $member = MemberNets::where(['net_member_id' => $uid])->find();
        if(!$member){
            return false;
        }
        if(!isset($member->{$passageway->passageway_no}) || !$member->{$passageway->passageway_no}){
            return false;
        }
        // print_r($member);die;
        $orderTime = date('YmdHis', time() + 60);
        $params    = array(
            'mchNo'  => $this->mech, //机构号 必填  由平台统一分配 16
            'userNo' => $member->{$passageway->passageway_no},  //平台用户标识  必填  平台下发用户标识  32
        );
        // var_dump($params);die;
        $income = repay_request($params, $this->mech, $url, $this->iv, $this->secretkey, $this->signkey);
        if ($is_print) {
            echo json_encode($income);
            die;
        } else {
            return $income;
        }
    }
	/**
	 * 支付
	 */
	public function pay($pay){
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
        #2获取通道信息
        $merch = Passageway::where(['passageway_id' => $pay['order_passageway']])->find();
        // print_r($merch->passageway_mech);die;
        $member_base = Member::where(['member_id' => $pay['order_member']])->find();
        #3获取银行卡信息
        $card_info = MemberCreditcard::where(['card_bankno' => $pay['order_card']])->find();
        #4获取用户信息
        $member = MemberNets::where(['net_member_id' => $pay['order_member']])->find();
        #6获取渠道提供的费率，如果不一致，重新报备费率
        $passway_info = $this->accountQuery($pay['order_member'], $pay['order_passageway']);
        if (isset($passway_info['drawFeeRatio']) && isset($passway_info['drawFeeAmt'])) {
            if ($passway_info['drawFeeRatio'] != $also || $passway_info['drawFeeAmt'] != $daikou) {//不一致重新报备,修改商户信息
                $Membernetsedits =$this->mech_update($member->{$merch['passageway_no']},$card_info['card_name'],$card_info['card_idcard'],$member_base['member_mobile'],$also,$daikou);
            }
        }

        $orderTime = date('YmdHis', time() + 60);
        if (!$pay['order_platform_no'] || $pay['order_status'] != 1) {
            $update_order['order_platform_no'] = $pay['order_platform_no'] = get_plantform_pinyin() . $member_base->member_mobile . make_rand_code();
            $update_res = GenerationOrder::where(['order_id' => $pay['order_id']])->update($update_order);
        }
        $MemberCreditPas = MemberCreditPas::where(['member_credit_pas_creditid' => $card_info['card_id'], 'member_credit_pas_pasid' => $pay['order_passageway']])->find();
        $params = array(
            'mchNo'        => $this->mech, //机构号 必填  由平台统一分配 16
            'userNo'       => $member->{$merch['passageway_no']},  //平台用户标识  必填  平台下发用户标识  32
            'payCardId'    => $MemberCreditPas->member_credit_pas_info,  //提现卡签约ID 必填  提现结算的卡，传入签约返回的平台签约ID  32
            'notifyUrl'    => System::getName('system_url') . '/Api/Misdhnew/cashCallback',// 异步通知地址  可填  异步通知的目标地址
            'orderNo'      => $pay['order_platform_no'], //提现流水号 必填  机构订单流水号，需唯一 64
            'orderTime'    => $orderTime,//  提现时间点 必填  格式：yyyyMMddHHmmss 14
            'goodsName'    =>"线上消费",//商品名称
            'orderDesc'    =>"米刷代还",
            'clientIp'     =>$_SERVER['REMOTE_ADDR'],
            'orderAmt'     => $pay['order_money'] * 100,  //提现金额  必填  单位：分  整型(9,0)
            'feeRatio'     => $also,  //提现费率  必填  需与用户入网信息保持一致  数值(5,2)
            'feeAmt'       => $daikou,//提现单笔手续费   需与用户入网信息保持一致  整型(4,0)
            // 'payFeeMode'   =>'',//默认验证入网费率信息。传入01，标识不效验入网信息，feeRatio和feeAmt仅针对本次交易有效
        );
        $url    =  $this->url.'/payBindCard';
  	    $income = repay_request($params, $this->mech, $url, $this->iv, $this->secretkey, $this->signkey);
        // print_r($income);
        $is_commission = 0;
        if ($income['code'] == '200') {
            $arr['back_tradeNo']    = $income['tradeNo'];
            $arr['back_statusDesc'] = $income['statusDesc'];
            $arr['back_status']     = $income['status'];
            if ($income['status'] == "SUCCESS") {
                $arr['order_status']            = '2';
                $generation['generation_state'] = 3;
                $arr['order_platform']          = $pay['order_pound'] - ($pay['order_money'] * $merch['passageway_rate'] / 100) - $merch['passageway_income'];
                $is_commission                  = 1;
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
        file_put_contents('new_mishua_pay.txt',json_encode($income));
        // 更新卡计划
        // Generation::where(['generation_id'=>$pay['order_no']])->update($generation);
        #更改完状态后续操作
        $notice=new \app\api\controller\Membernet();
        $action = $notice->plan_notice($pay, $income, $member_base, $is_commission, $merch);
	}
	/**
	 * 支付回掉
	 */
	public function cashCallback(){
		$data   = file_get_contents("php://input");
		file_put_contents('new_mishua_paycallback.txt', $data);
        $result = json_decode($data, true);
        // print_r($result);die;
        if ($result['code'] == 0) {
            $merch = Passageway::where(['passageway_mech' => $result['mchNo']])->find();
            $datas = AESdecrypt($result['payload'], $this->secretkey, $this->iv);
            $datas = trim($datas);
            $datas = substr($datas, 0, strpos($datas, '}') + 1);
            // file_put_contents("mishua_cashCallback.txt", $datas);
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
	/**
	 * 代付
	 */
	public function qfpay($pay, $isCancel = null, $is_admin = '')
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
            #6获取渠道提供的费率，如果不一致，重新报备费率
            $passway_info = $this->accountQuery($pay['order_member'], $pay['order_passageway']);
            if (isset($passway_info['drawFeeRatio']) && isset($passway_info['drawFeeAmt'])) {
                if ($passway_info['drawFeeRatio'] != $also || $passway_info['drawFeeAmt'] != $daikou) {//不一致重新报备,修改商户信息
                    $Membernetsedits =$this->mech_update($member->{$merch['passageway_no']},$card_info['card_name'],$card_info['card_idcard'],$member_base['member_mobile'],$also,$daikou);
                }
            }

            $orderTime = date('YmdHis', time() + 60);
            if (!$pay['order_platform_no'] || $pay['order_status'] != 1) {
                $update_order['order_platform_no'] = $pay['order_platform_no'] = get_plantform_pinyin() . $member_base->member_mobile . make_rand_code();
                $update_res                        = GenerationOrder::where(['order_id' => $pay['order_id']])->update($update_order);
            }
            $MemberCreditPas = MemberCreditPas::where(['member_credit_pas_creditid' => $card_info['card_id'], 'member_credit_pas_pasid' => $pay['order_passageway']])->find();
            $params = array(
                'mchNo'        => $this->mech, //机构号 必填  由平台统一分配 16
                'userNo'       => $member->{$merch['passageway_no']},  //平台用户标识  必填  平台下发用户标识  32
                'settleBindId' => $MemberCreditPas->member_credit_pas_info,//提现卡签约ID 必填  提现结算的卡，传入签约返回的平台签约ID 
                'notifyUrl'    => System::getName('system_url') . '/Api/Misdhnew/qfcallback',// 异步通知地址  可填  异步通知的目标地址
                'orderNo'      => $pay['order_platform_no'], //提现流水号 必填  机构订单流水号，需唯一 64
                'orderTime'    => $orderTime,//  提现时间点 必填  格式：yyyyMMddHHmmss 14
                'depositAmt'   => $pay['order_real_get'] * 100,  //提现金额  必填  单位：分  整型(9,0)
                'feeRatio'     => $also,  //提现费率  必填  需与用户入网信息保持一致  数值(5,2)
                'feeAmt'       => $daikou,//提现单笔手续费   需与用户入网信息保持一致  整型(4,0)
            );
            $url=$this->url.'/transferApply';
            $income = repay_request($params, $this->mech, $url, $this->iv, $this->secretkey, $this->signkey);
            file_put_contents('new_mishua_qf.txt',json_encode($income));
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
            $generation_state =  3;
            Generation::update(['generation_id' => $pay['order_no'], 'generation_state' => $generation_state]);
        }
        //执行完后操作
        $notice=new \app\api\controller\Membernet();
        $action = $notice->plan_notice($pay, $income, $member_base, 0, $merch);
    }
    /**
     * 代付回调
     */
    public function qfcallback(){
    	$data   = file_get_contents("php://input");
    	file_put_contents('new_mishua_qfcallback', $data);
        $result = json_decode($data, true);
        // print_r($result);die;
        if ($result['code'] == 0) {
            $merch = Passageway::where(['passageway_mech' => $result['mchNo']])->find();
            $datas = AESdecrypt($result['payload'], $this->secretkey, $this->iv);
            $datas = trim($datas);
            $datas = substr($datas, 0, strpos($datas, '}') + 1);
            // file_put_contents("mishua_cashCallback.txt", $datas);
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
    //3余额查询
    public function accountQuery($uid, $passageway_id, $is_print = "")
    {
        if(!$uid){
            return false;
        }
        $passageway = Passageway::where(['passageway_id' => $passageway_id])->find();
        if(!$passageway){
            return false;
        }
        #4获取用户信息
        $member = MemberNets::where(['net_member_id' => $uid])->find();
        if(!$member){
            return false;
        }
        if(!isset($member->{$passageway->passageway_no}) || !$member->{$passageway->passageway_no}){
            return false;
        }
        // print_r($member);die;
        $orderTime = date('YmdHis', time() + 60);
        $params    = array(
            'mchNo'  => $passageway->passageway_mech, //机构号 必填  由平台统一分配 16
            'userNo' => $member->{$passageway->passageway_no},  //平台用户标识  必填  平台下发用户标识  32
        );
        // var_dump($params);die;
        $url=$this->url.'/accountQuery';
        $income = repay_request($params, $this->mech, $url, $this->iv, $this->secretkey, $this->signkey);
        if ($is_print) {
            echo json_encode($income);
            die;
        } else {
            return $income;
        }
    }
} 