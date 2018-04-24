<?php
/**
 *  @version MemberCertCard controller / Api 会员信用卡
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-16 13:50:05
 *   @return 
 */

/*
                   _ooOoo_
                  o8888888o
                  88" . "88
                  (| -_- |)
                  O\  =  /O
               ____/`---'\____
             .'  \\|     |//  `.
            /  \\|||  :  |||//  \
           /  _||||| -:- |||||-  \
           |   | \\\  -  /// |   |
           | \_|  ''\---/''  |   |
           \  .-\__  `-`  ___/-. /
         ___`. .'  /--.--\  `. . __
      ."" '<  `.___\_<|>_/___.'  >'"".
     | | :  `- \`.;`\ _ /`;.`/ - ` : | |
     \  \ `-.   \_ __\ /__ _/   .-` /  /
======`-.____`-.___\_____/___.-`____.-'======
                   `=---='
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         佛祖保佑       永无BUG
*/

 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use think\Loader;
 use app\index\model\MemberLogin;
 use app\index\model\System;
 use app\index\model\Member;
 use app\index\model\MemberGroup;
 use app\index\model\MemberNet;
 use app\index\model\MemberCert as MemberCerts;
 use app\index\model\MemberCreditcard;
 use app\index\model\MemberCashcard;
 use app\index\model\Passageway;
 use app\index\model\PassagewayItem;
 use app\index\model\PassagewayBind;
 use app\index\model\BankIdent;
 use app\index\model\SmsCode as SmsCodes;
 use app\index\model\GenerationOrder;
 use app\index\model\Generation;

 class MemberCertCard 
 {
      protected $param;
      public $error;
      public $name;
      public $idcard;
      protected $mechid;
      public function __construct($param)
      {
        	 $this->param=$param;
            try{
                 if(!isset($this->param['uid']) || empty($this->param['uid']) || !isset($this->param['token']) ||empty($this->param['token']))
                       $this->error=314;
                 #查找到当前用户
                 $member=Member::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
                 if($member['member_cert']!=1)
                      $this->error=356;
                 if(empty($member))
                       $this->error=314;
                 #查找实名认证信息
                 $member_cert=MemberCerts::get(['cert_member_id'=>$member['member_id']]);
                 if(empty($member_cert) && !$this->error )
                      $this->error=356;
                 $this->name=$member_cert['cert_member_name'];
                 $this->idcard=$member_cert['cert_member_idcard'];
            }catch (\Exception $e) {
                 $this->error=317;
           }
      }


    

      /**
      *  @version addition_card method / Api 绑定信用卡1(获取验证码)
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 09:22:05
      *  @param uid=会员ID token=登录令牌 
          'creditCardNo:信用卡卡号',  'cvv:信用卡背面的3位cvv数字',  'expireDate:有效期',
          'billDate:账单日，两位数字，如1号->01',  'deadline:最后还款日',  'isRemind:是否提醒，0表示不提醒，1表示提醒，\ 当关闭提醒时，下方的日期选择将隐藏',
          'remindDate:提醒日，数据格式和账单日相同'
      **/ 
      public function addition_card()
      {
           //  $add=new \app\api\controller\MemberNet($this->param['uid'],$this->param['passageway_id'],$this->param['phone']);
           // return ['code'=>436, 'msg'=>'qwewq','data'=>$add];
           #验证器验证 验证参数合法性
           $validate = Loader::validate('Memberadditioncard');
           #如果验证不通过 返回错误代码 及提示信息
           if(!$validate->check($this->param))
                 return ['code'=>436, 'msg'=>$validate->getError()];
           $card=MemberCreditcard::where(["card_bankno"=>$this->param['creditCardNo']])->find();
           if($card){
              if($card['card_member_id']!=$this->param['uid']){
                   return ['code'=>437,'msg'=>'该卡已被其他人绑定'];
              }
              if($card['card_state']==1 && $card['bindStatus']=='01'){
                   return ['code'=>437,'msg'=>'该卡已经绑定过了'];
              }
           }
           #获取用户信息
           $member_info=MemberCerts::where('cert_member_id='.$this->param['uid'])->find();
           if(empty($member_info))
              $this->error=356;
            $member_net=MemberNet::where('net_member_id='.$this->param['uid'])->find();
            #信用卡有效状态校验
        if (System::getName('certhost') == 'http://yhsys.market.alicloudapi.com') {
           $card_validate=BankCertNew(['bankCardNo'=>$this->param['creditCardNo'], 'identityNo'=>$this->idcard, 'mobileNo'=>$this->param['phone'], 'name'=>$this->name]);
           if($card_validate['code']!=0000) return ['code'=>351, 'msg'=>'实名认证失败'];
           if($card_validate['data']['resultCode']!='R001')  return ['code'=>351, 'msg'=>'认证失败:'.$card_validate['data']['remark']];
           if(isset($card_validate['data']['bankCardBin']) && $card_validate['data']['bankCardBin']['cardTy']!='C')  return ['code'=>351, 'msg'=>'认证失败:只能绑定信用卡'];
        }else{
           $card_validate=BankCert_Java($this->param['card_bankno'],$this->param['card_idcard'],$this->param['card_name'],$this->param['card_phone']);
          if(isset($card_validate['data']['identType']) && $card_validate['data']['identType']!='信用卡')
            return ['code'=>351,'msg'=>'认证失败:只能验证信用卡'];
          if($card_validate['code']!=200)
              return ['code'=>351,'msg'=>$card_validate['info']];
        }
            $ident_code=substr($this->param['creditCardNo'],0,6);
            $ident_icon=BankIdent::where(['ident_code'=>$ident_code])->value('ident_icon');
            $passageway=Passageway::where('passageway_also=2 and passageway_state=1')->find();
            #写入信用卡表
            $bindId=uniqid();
            $arr=[
                'card_member_id'=>$this->param['uid'],
                'card_bankno'=>$this->param['creditCardNo'],
                'card_name'  =>$this->name,
                'card_idcard' =>$this->idcard,
                'card_phone' =>$this->param['phone'],
                'card_bankname' => $this->param['bank_name'],
                'card_Ident'  => $this->param['cvv'],
                'card_expireDate' => $this->param['expireDate'],
                'card_billDate'   => $this->param['billDate'],
                'card_deadline' => $this->param['deadline'],
                'card_bankicon' => $ident_icon,
                'card_state'  => 0,
                'mchno' =>$passageway->passageway_mech,
                'bindId'    =>$bindId
                // 'card_return' =>json_encode($card_validate),
            ];
             if($card){
                $member_cashcard=MemberCreditcard::where(["card_bankno"=>$this->param['creditCardNo']])->update($arr);
             }else{
                $res=new MemberCreditcard($arr);
                $res=$res->save();
             }
            //发送短信验证码
            $sms=new \app\index\controller\Sms();
            $res=$sms->send_sms($this->param['phone']);
            if($res['code']==200){
                return ['code'=>'200','msg'=>'短信发送成功','data'=>['bindId'=>$bindId]];
            }else{
               return ['code'=>'101','msg'=>'短信发送失败'];
            }
      }
      //绑定信用卡
      public function addition_card_code(){
          if(!isset($this->param['bindId']) || empty($this->param['bindId']))
                 return ['code'=>441];
           #查询信用卡信息
          $creditcard=MemberCreditcard::where("bindId='{$this->param['bindId']}' and card_member_id={$this->param['uid']}")->find();
           // return ['code'=>441,'msg'=>'13','data'=>$creditcard];
          if(empty($creditcard)){
               return ['code'=>353];
          }
           
          if($creditcard['bindStatus']=='01'){
              return ['code'=>463];
          }
          #查询当前卡有没有绑定过
          if($creditcard['card_state']=='1'){
              return ['code'=>437];
          }

          //校验验证码
          $sms=new \app\index\controller\Sms();
           $res=$sms->check($creditcard['card_phone'],$this->param['smsCode']);
          if($res['code']!=200){
              return $res;
          }
          $bindStatus=array(
            'bindStatus'=>'00',
            'card_state'  => 1,
          );
          $edit=MemberCreditcard::where("bindId='{$this->param['bindId']}' and mchno='{$creditcard['mchno']}'")->update($bindStatus);
          if($edit){
            return ['code'=>200, 'msg'=>'绑定成功', 'data'=>''];
          }else{
            return ['code'=>464];
          }
      }

       /**
      *  @version addition_card_code method / Api 绑定信用卡2(签约)
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 09:22:05
      *  @param uid=会员ID token=登录令牌 
          'creditCardNo:信用卡卡号',  'cvv:信用卡背面的3位cvv数字',  'expireDate:有效期',
          'billDate:账单日，两位数字，如1号->01',  'deadline:最后还款日',  'isRemind:是否提醒，0表示不提醒，1表示提醒，\ 当关闭提醒时，下方的日期选择将隐藏',
          'remindDate:提醒日，数据格式和账单日相同'
      **/ 
      public function addition_card_codes(){
           #判断参数是否存在
           if(!isset($this->param['bindId']) || empty($this->param['bindId']))
                 return ['code'=>441];
           #查询信用卡信息
           $creditcard=MemberCreditcard::where("bindId='{$this->param['bindId']}' and card_member_id={$this->param['uid']}")->find();
           // return ['code'=>441,'msg'=>'13','data'=>$creditcard];
           if(empty($creditcard))
              return ['code'=>353,'msg'=>'获取信用卡信息失败'];
             #查询当前卡有没有绑定过
            $passageway=Passageway::where('passageway_status=1 and passageway_also=2')->find();
            $passageway_id=input('passageway_id');
            // $passageway=Passageway::where('passageway_id',$passageway_id)->find();

            $member_net=MemberNet::where('net_member_id='.$this->param['uid'])->find();
            $params=array(
              'mchNo'=>$creditcard['mchno'], //机构号 必填  由平台统一分配 16
              'userNo'=>$member_net[$passageway['passageway_no']],  //平台用户标识  必填  默认为平台下发用户标识 32
              'bindId'=>$creditcard['bindId'],  //平台签约ID  必填  签约短信下发  32
              'smsCode'=>$this->param['smsCode'],  //短信验证码 必填    10
            );
            $url='http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/bindCardConfirm';
            $income=repay_request($params,$passageway['passageway_mech'],$url,$passageway['iv'],$passageway['secretkey'],$passageway['signkey']);
            // print_r($income);die;
            if($income['code']=='200'){
              #记录签约日志
              PassagewayBind::create([
                'bind_passway_id'=>$passageway->passageway_id,
                'bind_member_id'=>$this->param['uid'],
                'bind_card'=>$creditcard->card_bankno,
                'bind_money'=>$passageway->passageway_bind_money
              ]);

              //修改签约状态
              $bindStatus=array(
                'bindStatus'=>$income['bindStatus'],
              );
              $edit=MemberCreditcard::where("bindId='{$this->param['bindId']}' and mchno='{$creditcard['mchno']}'")->update($bindStatus);
              
              if($edit){
                return ['code'=>200, 'msg'=>'签约成功~', 'data'=>''];
              }else{
                return ['code'=>464];
              }
              
            }else{
              return ['code' => 102, 'msg' => $income['message']];
            }
            
      }
      /**
      *  @version ubind_card method / Api 解绑信用卡信用卡
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 09:22:05
      *  @param uid=会员ID token=登录令牌  creditCardId='信用卡ID'
      **/ 
      public function ubind_card()
      {
           #判断参数是否存在
           if(!isset($this->param['creditCardId']) || empty($this->param['creditCardId']))
                 return ['code'=>441];
           #查找到当前信用卡
           $cert_card=MemberCreditcard::get($this->param['creditCardId']);
           if(empty($cert_card))
                 return ['code'=>442];
            #查询信用卡是否在还款计划中
           $generation=Generation::where(['generation_card'=>$cert_card['card_bankno'],'generation_state'=>2])->select();
           if($generation){
              foreach ($generation as $key => $value) {
                $generation_order=GenerationOrder::where(['order_card'=>$cert_card['card_bankno'],'order_status'=>1,'order_no'=>$value['generation_id']])->select();
                 if($generation_order){
                      return ['code'=>469];
                 }
              }
           }
           #查找出会员的实名信息
           $member_cert=MemberCerts::get(['cert_member_id'=>$this->param['uid']]);
           #进行和当前会员信息比对
           if($cert_card['card_name']!=$member_cert['cert_member_name'] ||  $cert_card['card_idcard']!=$member_cert['cert_member_idcard'])
                 return ['code'=>443];
              if($cert_card['bindStatus']=="01" && strlen($cert_card['bindId'])>20){
                  $passageway=Passageway::where('passageway_status=1 and passageway_also=2')->find();
                  $member_net=MemberNet::where('net_member_id='.$this->param['uid'])->find();
                  $url='http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/unbindCard';
                  $params=array(
                    'mchNo'=> $cert_card['mchno'],
                    'userNo'=> $member_net[$passageway['passageway_no']],
                    'bindId'=>$cert_card['bindId']
                  );
                  $income=repay_request($params,$passageway['passageway_mech'],$url,$passageway['iv'],$passageway['secretkey'],$passageway['signkey']);

                  if($income['code']!=200){
                    return ['code'=>444];
                  }
                  if($income['bindStatus']!='02')
                      return ['code'=>444];
              }
           if($cert_card->delete()===false)
                 return ['code'=>444];
            // $card=MemberCreditcard::where(['card_member_id'=>$this->param['uid']])->find();
            // if(empty($card)){
            //   MemberNet::where(['net_member_id'=>$this->param['uid']])->update([$passageway['passageway_no']=>null]);
            // }               
           return ['code'=>200, 'msg'=>'解绑成功~', 'data'=>''];
      }

      /**
      *  @version get_card_list method / Api 获取信用卡列表
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-15 09:22:05
      *  @param uid=会员ID token=登录令牌
      **/ 
      public function get_card_list()
      {
       #判断参数是否存在
       if(!isset($this->param['uid']) || empty($this->param['uid']) || !isset($this->param['token']) ||empty($this->param['token']))
            $this->error=314;
       #查找到当前用户信用卡列表
       $data=MemberCreditcard::with("repayment")->where('card_member_id='.$this->param['uid'].' and card_state=1')->select();
       foreach ($data as $key => $value) {
         $data[$key]['card_bankicon']=System::getName('system_url').$value['card_bankicon'];
         $data[$key]['card_banktitle']=$value['card_bankname'].'(尾号'.substr($value['card_bankno'],-4).')';
         //查找当前执行计划表中状态为等待执行的数据
         // $tmp=GenerationOrder::where(['order_card'=>$value['card_bankno'],'order_status'=>1])->find();
         $tmp=db('generation')->alias('g')
              ->join('generation_order o','g.generation_id=o.order_no')
              ->where(['o.order_card'=>$value['card_bankno'],'o.order_status'=>1,'g.generation_state'=>2])
              ->find();
         $data[$key]['isInRepaySchedule']=empty($tmp) ? 0 : 1 ;
         $data[$key]['order_no']=empty($tmp) ? 0 : $tmp['order_no'] ;
        }
       return ['code'=>200, 'msg'=>'获取信用卡列表成功~', 'data'=>$data];
      }
 }
