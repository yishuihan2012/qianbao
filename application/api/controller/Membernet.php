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
 	function __construct($memberId,$passwayId,$phone){
           try{
                 #根据memberId获取会员信息和会员的实名认证信息还有会员银行卡信息
                 $this->member=Member::get($memberId);
                 if(! $this->member)
                      $this->error=314;
                 if($this->member->member_cert!='1')
                      $this->error=356;
                 $this->membercert=MemberCert::get(['cert_member_id'=>$memberId]);
                 if(!$this->membercert)
                      $this->error=367;
                 #获取用户结算卡信息
                 $this->membercard=MemberCashcard::get(['card_member_id'=>$memberId]);
                 if(!$this->membercard)
                      $this->error=459;
                 #获取通道信息
                 $this->passway=Passageway::get($passwayId);
                 if(!$this->passway)
                      $this->error=454; 
           }catch (\Exception $e) {
                 $this->error=460; //TODO 更改错误码 入网失败错误码
           }
      }



	 /**
	 *  @version bind_creditcard controller / Api 米刷绑定信用卡入网
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


 }