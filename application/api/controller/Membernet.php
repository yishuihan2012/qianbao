<?php
 namespace app\api\controller;
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
 use app\index\model\MemberNet as MemberNets;
 use app\index\model\MemberCreditcard;
 /**
 *  @version MemberNet controller / Api 代还入网
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-08 10:13:05
 *   @return 
 */
 class MemberNet
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
   *  @version bind_creditcard controller / Api 米刷绑定信用卡入网 废弃
   *  @author $bill$(755969423@qq.com)
   *   @datetime    2017-12-08 10:13:05
   *   @return 
   */
   public function mishuadaihuan($phone)
   {
    $params=array(
        'versionNo'=>'1',//接口版本号 必填  值固定为1 
        'mchNo'=>$passageway->passageway_mech, //mchNo 商户号 必填  由米刷统一分配 
        'mercUserNo'=>$member->member_id, //用户标识,下级机构对用户身份唯一标识。
        'userName'=>$member->member_info->cert_member_name,//姓名
        'userCertId'=>$member->member_info->cert_member_idcard,//身份证号  必填  注册后不可修改
        'userPhone'=>$phone,
        'feeRatio'=>$passageway->rate->item_also, //交易费率  必填  单位：千分位。如交易费率为0.005时,需传入5.0
        'feeAmt'=>'50',//单笔交易手续费  必填  单位：分。如机构无单笔手续费，可传入0
        'drawFeeRatio'=>'0',//提现费率
        'drawFeeAmt'=>'0',//单笔提现易手续费
      );
      $url='http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/createMerchant';
      $income=repay_request($params,$passageway->passageway_mech,$url,$passageway->iv,$passageway->secretkey,$passageway->signkey);
      $arr=array(
        'net_member_id'=>$member_info->cert_member_id,
        "{$passageway->passageway_no}"=>$income['userNo']
      );
      return ['code'=>200,'data'=>$arr];
   }
   //删除待确认的代还计划
   public function delete_nouse_plan(){
      $plan_card=Generation::where(["generation_state"=>1])->select();
      foreach ($plan_card as $k => $card) {
          $res=Generation::where(["generation_id"=>$card['generation_id']])->delete();
          $order=GenerationOrder::where(["order_no"=>$card['generation_id']])->delete();
      }
      echo 'success';
   }
    //执行计划
    public function action_repay_plan(){
      $where['order_status']='1';
      $where['order_time']=array('lt',date('Y-m-d H:i:s',time()));
      $list=GenerationOrder::where($where)->select();
       if($list){
            foreach ($list as $k => $v) {
                $value=$v->toArray();
                $card_status=Generation::where(['generation_id'=>$value['order_no']])->value('generation_state');
                if($card_status==2){//如果是执行中的卡
                     if($value['order_type']==1){ //消费
                          $this->payBindCard($value);
                      }else if($value['order_type']==2){//提现
                          $this->transferApply($value);
                      }
                }
             }
        }
    }
    public function action_single_plan($id){
        // $merch=Passageway::where(['passageway_no'=>'LkYQJ'])->find();
        // $datas = AESdecrypt('KRzQQIehaK51Vmym+4DzlR+7GKvblLEtXwMcMHeRje5ydeeaghK+iZro+PVz4vsS34LZsiz7TmQ//Vi7dnS4H13sVxqCOb50wHMB9OMOBGB7fKfWnKC4B3KTq8F5zF0V06a2zkgFD9+JfdJb4ycQ8xOp4vrRd1VRSTqG7ybDKoqWE8l5RiO3BffIaCGqm/ECOfojwZwLygWIGETVYf+GDwJRjCYwJmFPpmXBuxTKjXkgPsUSB4LuhgGBYqzDKKBMh3vOYlNhE+ce733EbEMkNybnE6oiTzLPtLQ5vgceKVBd5W4aIcICzASL645rxkZxZif7i3hTjzU7LdJhV8KUD5tCbN1yfHjzWfvjuJkFxb0\\u003d',$merch->secretkey,$merch->iv);
        // print_r($datas);die;

        $value=GenerationOrder::where(['order_id'=>$id])->find();
        // print_r($value);die;
        if($value['order_type']==1){ //消费
            $this->payBindCard($value);
        }else if($value['order_type']==2){//提现
            $this->transferApply($value);
        }
    }
     //7绑卡支付
      //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payBindCard
      public function payBindCard($pay){
        #1获取费率
        // print_r($pay);die;
        $member_group_id=Member::where(['member_id'=>$pay['order_member']])->value('member_group_id');
        $rate=PassagewayItem::where(['item_passageway'=>$pay['order_passageway'],'item_group'=>$member_group_id])->find();
        $also=($rate->item_also)*10;
        $daikou=($rate->item_charges);
        #2获取通道信息
        $merch=Passageway::where(['passageway_id'=>$pay['order_passageway']])->find();
        // print_r($merch->passageway_mech);die;
        #3获取银行卡信息
        $card_info=MemberCreditcard::where(['card_bankno'=>$pay['order_card']])->find();
        #4获取用户信息
        $member=MemberNets::where(['net_member_id'=>$pay['order_member']])->find();
        // print_r($member);die;
        // print_r($pay);die;
        #5:获取用户基本信息
        $member_base=Member::where(['member_id'=>$pay['order_member']])->find();
        $params=array(
          'mchNo'=>$merch->passageway_mech, //机构号 必填  由平台统一分配 16
          'userNo'=>$member->LkYQJ,  //平台用户标识  必填  平台下发用户标识  32
          'payCardId'=>$card_info->bindId, //支付卡签约ID 必填  支付签约ID，传入签约返回的平台签约ID  32
          'notifyUrl'=>System::getName('system_url').'/Api/Membernet/payCallback',  //异步通知地址  可填  异步回调地址，为空时不起推送  200
          'orderNo'=>uniqid(), //订单流水号 必填  机构订单流水号，需唯一 64
          'orderTime'=>date('YmdHis',time()+60),  //订单时间  必填  格式：yyyyMMddHHmmss 14
          'goodsName'=>'虚拟商品',  //商品名称  必填    50
          'orderDesc'=>'米刷信用卡还款', //订单描述  必填    50
          'clientIp'=>$_SERVER['REMOTE_ADDR'],  //终端IP  必填  格式：127.0.0.1  20
          'orderAmt'=>$pay['order_money']*100, //交易金额  必填  单位：分  整型(9,0)
          'feeRatio'=>$also,  //交易费率  必填  需与用户入网信息保持一致  数值(5,2)
          'feeAmt'=>$daikou, //交易单笔手续费   需与用户入网信息保持一致  整型(4,0)
        );  
        // print_r($params);
        $income=repay_request($params,$merch->passageway_mech,'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payBindCard',$merch->iv,$merch->secretkey,$merch->signkey);
        // print_r($income);
        //后四位银行卡尾号
        $card_num=substr($pay['order_card'],-4);
        if($income['code']=='200'){
             $arr['back_tradeNo']=$income['tradeNo'];
             $arr['back_statusDesc']=$income['statusDesc'];
             $arr['back_status']=$income['status'];
            if($income['status']=="SUCCESS"){
                $arr['order_status']='2';
                // $generation['generation_state']=3;
                //成功-分润
                 #先判断有没有分润
                 if($pay['is_commission']=='0'){
                    $fenrun= new \app\api\controller\Commission();
                    $fenrun_result=$fenrun->MemberFenRun($pay['order_member'],$pay['order_money'],$merch->passageway_id,2,'代还分润');
                 }
                //成功极光推送。
                jpush($pay['order_member'],'还款计划扣款成功通知',"您制定的尾号{$card_num}的还款计划成功扣款".$pay['order_money']."元，在APP内还款计划里即可查看详情。");
            }else if($income['status']=="FAIL"){
                //失败推送消息
                $arr['order_status']='-1';
                send_sms($member_base->member_mobile,"您制定的尾号{$card_num}的还款计划还款失败，在APP内还款计划里即可查看详情。");
            }else{
                $arr['order_status']='2';
                //带查证或者支付中。。。
            }
        }else{
          $arr['back_statusDesc']=$income['message'];
          $arr['back_status']='FAIL';
          $arr['order_status']='-1';
          $generation['generation_state']=-1;
          // 失败，短信通知
          send_sms($member_base->member_mobile,"您制定的尾号{$card_num}的还款计划执行失败，在APP内还款计划里即可查看详情。");
        }
        //添加执行记录
        GenerationOrder::where(['order_id'=>$pay['order_id']])->update($arr);
        //更新卡计划
        // Generation::where(['generation_id'=>$pay['order_no']])->update($generation);
        
      }
      //8:支付回调
      public function payCallback(){
        $data = file_get_contents("php://input");
        $result = json_decode($data, true);
           if ($result['code'] == 0) {
                $merch=Passageway::where(['passageway_no'=>'LkYQJ'])->find();
                $datas = AESdecrypt($result['payload'],$merch->secretkey,$merch->iv);
                $datas = trim($datas);
                $datas = substr($datas, 0, strpos($datas, '}') + 1);
                file_put_contents("payCallback.txt", $datas);
                $resul = json_decode($datas, true);
                $arr['back_status']=$resul['status'];
                $arr['back_statusDesc']=$resul['statusDesc'];
                if($resul['status']=="SUCCESS"){
                  $arr['order_status']='2';
                  $generation['generation_state']=3;
                  $pay=GenerationOrder::where(['back_tradeNo'=>$resul['tradeNo']])->find();
                   //成功-分润先判断有没有分润
                   if($pay['is_commission']=='0'){
                      $fenrun= new \app\api\controller\Commission();
                      $fenrun_result=$fenrun->MemberFenRun($pay['order_member'],$pay['order_money'],$merch->passageway_id,2,'代还分润');
                   }
                  // 极光推送
                  $card_num=substr($pay['order_card'],-4);
                  jpush($pay['order_member'],'还款计划扣款成功通知',"您制定的尾号{$card_num}的还款计划成功扣款".$pay['order_money']."元，在APP内还款计划里即可查看详情。");
                }
            }
            //更新计划表
            GenerationOrder::where(['back_tradeNo'=>$resul['tradeNo']])->update($arr);
            //更新卡计划
            // $id=GenerationOrder::where(['back_tradeNo'=>$resul['tradeNo']])->value('order_no');
            // Generation::where(['generation_id'=>$pay['order_no']])->update($generation);
            if($resul['status']=="SUCCESS"){
              echo "success";die;
            }
      }
      //9状态查询 unfinished
      //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payResultQuery
      public function payResultQuery(){
        $params=array(
          'mchNo'=>$this->mechid, //机构号 必填  由平台统一分配
          'userNo'=>'123',  //平台用户标识  必填  平台下发用户标识
          'orderNo'=>'',  //订单流水号 必填  机构订单流水号，需唯一
          'tradeNo'=>'',  //平台流水号 必填  绑卡支付返回的流水号
          'tradeDate'=>'',  //交易日期  可填  格式：yyyyMMdd为空时，仅查询仅3日内的交易数据；传入指定日期，可以查询更早前的数据
        );
        $income=$this->repay_request($params,'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/payResultQuery');
        var_dump($income);die;
      }
      //10.余额提现
      //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/transferApply
      public function transferApply($pay){
        #1获取费率
        $member_group_id=Member::where(['member_id'=>$pay['order_member']])->value('member_group_id');
        $rate=PassagewayItem::where(['item_passageway'=>$pay['order_passageway'],'item_group'=>$member_group_id])->find();
        $also=($rate->item_also)*10;
        $daikou=($rate->item_charges);
        #2获取通道信息
        $merch=Passageway::where(['passageway_id'=>$pay['order_passageway']])->find();
        // print_r($merch->passageway_mech);die;
        #3获取银行卡信息
        $card_info=MemberCreditcard::where(['card_bankno'=>$pay['order_card']])->find();
        #4获取用户信息
        $member=MemberNets::where(['net_member_id'=>$pay['order_member']])->find();
        $orderTime=date('YmdHis',time()+60);
        $params=array(
          'mchNo'=>$merch->passageway_mech, //机构号 必填  由平台统一分配 16
          'userNo'=>$member->LkYQJ,  //平台用户标识  必填  平台下发用户标识  32
          'settleBindId'=>$card_info->bindId,  //提现卡签约ID 必填  提现结算的卡，传入签约返回的平台签约ID  32
          'notifyUrl'=>System::getName('system_url').'/Api/Membernet/cashCallback',// 异步通知地址  可填  异步通知的目标地址,为空时平台不发起推送  200
          'orderNo'=>uniqid(), //提现流水号 必填  机构订单流水号，需唯一 64
          'orderTime'=>$orderTime,//  提现时间点 必填  格式：yyyyMMddHHmmss 14
          'depositAmt'=>$pay['order_money']*100,  //提现金额  必填  单位：分  整型(9,0)
          'feeRatio'=>0,  //提现费率  必填  需与用户入网信息保持一致  数值(5,2)
          'feeAmt'=>0,//提现单笔手续费   需与用户入网信息保持一致  整型(4,0)
        );
        $income=repay_request($params,$merch->passageway_mech,'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/transferApply',$merch->iv,$merch->secretkey,$merch->signkey);
        // print_r($income);
        $card_num=substr($pay['order_card'],-4);
        if($income['code']=='200'){
          $arr['back_tradeNo']=$income['orderNo'];
          $arr['back_status']=$income['status'];
          $arr['back_statusDesc']=$income['statusDesc'];
          if($income['status']=="SUCCESS"){
               $arr['order_status']='2';
               //成功极光推送。
              jpush($pay['order_member'],'还款成功通知',"您制定的尾号{$card_num}的还款计划成功还款".$pay['order_money']."元，在APP内还款计划里即可查看详情。");
          }elseif($income['status']=="FAIL"){
                //失败推送消息
                $arr['order_status']='-1';
                send_sms($member_base->member_mobile,"您制定的尾号{$card_num}的还款计划还款失败，在APP内还款计划里即可查看详情。");
            }else{
                $arr['order_status']='2';
            }
        }else{
          $arr['back_status']='FAIL';
          $arr['back_statusDesc']=$income['message'];
          $arr['order_status']='-1';
          // 失败，短信通知
          send_sms($member_base->member_mobile,"您制定的尾号{$card_num}的还款计划还款失败，在APP内还款计划里即可查看详情。");
        }
        //更新订单状态
        GenerationOrder::where(['order_id'=>$pay['order_id']])->update($arr);
        //更新卡计划
      }
      //提现回调
      public function cashCallback(){
            $data = file_get_contents("php://input");
            $result = json_decode($data, true);
            if ($result['code'] == 0) {
                $merch=Passageway::where(['passageway_no'=>'LkYQJ'])->find();
                $datas = AESdecrypt($result['payload'],$merch->secretkey,$merch->iv);
                $datas = trim($datas);
                $datas = substr($datas, 0, strpos($datas, '}') + 1);
                file_put_contents("cashCallback.txt", $datas);
                $resul = json_decode($datas, true);
                $arr['back_status']=$resul['status'];
                $arr['back_statusDesc']=$resul['statusDesc'];
                if($resul['status']=="SUCCESS"){
                  $arr['order_status']='2';
                  $pay=GenerationOrder::where(['back_tradeNo'=>$resul['tradeNo']])->find();
                  $card_num=substr($pay['order_card'],-4);
                  jpush($pay['order_member'],'还款计划扣款成功通知',"您制定的尾号{$card_num}的还款计划成功还款".$pay['order_money']."元，在APP内还款计划里即可查看详情。");
                   echo "success";die;
                }
                GenerationOrder::where(['back_tradeNo'=>$resul['tradeNo']])->update($arr);
            }
      }
      //提现状态查询 unfinished
      //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/transferQuery
      public function transferQuery(){
        $user_merch_info=M('repay_user_merch')->where(['rm_mercUserNo'=>$post['mercUserNo']])->find();
        $params=array(
          'mchNo'=>$this->mechid, //机构号 必填
          'userNo'=>$user_merch_info['rs_userno'],  //平台用户标识  必填
          'orderNo'=>$orderNo,  //订单流水号 必填
          'depositNo'=>$depositNo,  //平台流水号 必填
          'depositDate'=>$depositDate,  //交易日期  可填
        );
      }
      //3余额查询
      //http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/accountQuery
      public function accountQuery($uid){
        $passageway=Passageway::where(['passageway_id'=>8])->find();
        #4获取用户信息
        $member=MemberNets::where(['net_member_id'=>$uid])->find();
        // print_r($member);die;
        $orderTime=date('YmdHis',time()+60);
        $params=array(
          'mchNo'=>$passageway->passageway_mech, //机构号 必填  由平台统一分配 16
          'userNo'=>$member->LkYQJ,  //平台用户标识  必填  平台下发用户标识  32
        );
        // var_dump($params);die;
        $income=repay_request($params,$passageway->passageway_mech,'http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/accountQuery',$passageway->iv,$passageway->secretkey,$passageway->signkey);
        echo json_encode($income);
        // var_dump($income);die;
      }
      public function mishuaedit($uid=16,$passageway='8'){
         #1实名信息
         $member_info=MemberCert::where('cert_member_id='.$uid)->find();
         #2j基本信息
         $member=Member::where('member_id='.$uid)->find();
         #3通道信息
         $passageway=Passageway::where(['passageway_id'=>$passageway])->find();
         #4会员费率
         $rate=PassagewayItem::where('item_passageway='.$passageway.' and item_group='.$member['member_group_id'])->find();
         #5商户入网信息
         $member_net=MemberNet::where('net_member_id='.$uid)->find();
         mishuaedit($passageway, $rate, $member_info, $member['member_mobile'], $member_net[$passageway['passageway_no']]);
      }
 }