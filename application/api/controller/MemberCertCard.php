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

           #获取用户信息
           $member_info=MemberCerts::where('cert_member_id='.$this->param['uid'])->find();
           if(empty($member_info))
              $this->error=356;

            #获取后台费率
            $group=Member::where('member_id='.$this->param['uid'])->find();


           #判断需要入网的通道
           $passageway=Passageway::where('passageway_status=1 and passageway_also=2')->find();


            $member_net=MemberNet::where('net_member_id='.$this->param['uid'])->find();//168f4e4c10024dbf8d85b53d607a3b13

            if(empty($member_net[$passageway['passageway_no']])){

                $rate=PassagewayItem::where('item_passageway='.$passageway['passageway_id'].' and item_group='.$group['member_group_id'])->find();

                $income=mishua($passageway, $rate, $member_info, $this->param['phone']);
                if($income['code']==200){
                    $arr=array(
                       'net_member_id'=>$member_info['cert_member_id'],
                       "{$passageway['passageway_no']}"=>$income['userNo']
                  );
                }else{
                    return['code'=>104,'msg'=>$income['message'],'data'=>[]];
                }

                $add_net=MemberNet::where('net_member_id='.$this->param['uid'])->update($arr);
           }

            $member_net=MemberNet::where('net_member_id='.$this->param['uid'])->find();
            
            #绑定信用卡签约
            $params=array(
                  'mchNo'=>$passageway['passageway_mech'], //mchNo 商户号 必填  由米刷统一分配 
                  'userNo'=>$member_net[$passageway['passageway_no']],
                  'phone'=>$this->param['phone'],
                  'cardNo'=>$this->param['creditCardNo'],
                  'expiredDate'=>$this->param['expireDate'],
                  'cvv'=>$this->param['cvv']
            );


            $url='http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/bindCardSms';
            $income=repay_request($params,$passageway['passageway_mech'],$url,$passageway['iv'],$passageway['secretkey'],$passageway['signkey']);
            
            if($income['code']=='200'){
              if($income['bindStatus']=='01'){
                return ['code'=>463];//此卡已签约
              }
                 $card=MemberCreditcard::where(["card_bankno"=>$this->param['creditCardNo']])->find();
                if(empty($card)){
                  $ident_code=substr($this->param['creditCardNo'],0,6);
                  $ident_icon=BankIdent::where(['ident_code'=>$ident_code])->value('ident_icon');
                   #写入信用卡表
                   $member_cashcard=new MemberCreditcard([
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
                        'bindId' => $income['bindId'],
                        'bindStatus' => $income['bindStatus'],
                        'mchno' => $passageway['passageway_mech'],
                        'card_bankicon' => $ident_icon,
                        'card_state'  => 0,
                        // 'card_return' =>json_encode($card_validate),
                   ]);
                   if($member_cashcard->save()===false)
                         return ['code'=>436];
                  }
                 return ['code'=>200, 'msg'=>'短信发送成功~', 'data'=>['bindId'=>$income['bindId']]];
              }else{
                  return ['code'=>400, 'msg'=> $income['message']];
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
      public function addition_card_code(){
           #判断参数是否存在
           if(!isset($this->param['bindId']) || empty($this->param['bindId']))
                 return ['code'=>441];
           #查询信用卡信息
           $creditcard=MemberCreditcard::where("bindId='{$this->param['bindId']}' and card_member_id={$this->param['uid']}")->find();
           // return ['code'=>441,'msg'=>'13','data'=>$creditcard];
           if(empty($creditcard))
              return ['code'=>353];
            if($creditcard['bindStatus']=='01')
              return ['code'=>463];

             #查询当前卡有没有绑定过
              if($creditcard['card_state']=='1')
                  return ['code'=>437];
          
               #信用卡有效状态校验
               $card_validate=BankCert($creditcard['card_bankno'],$creditcard['card_phone'],$creditcard['card_idcard'],$creditcard['card_name']);
               if($card_validate['error_code']!=0){
                  return ['code'=>351,'msg'=>$card_validate['reason']];
               }
               $state=$card_validate['result']['result']=='T' ? '1' : '0';
               if($card_validate['result']['result']=='P')
                     return ['code'=>440];

               if($card_validate['result']['result']=='F')
                    return ['code'=>439];
               if($card_validate['result']['result']=='N')
                    return ['code'=>353];


            $passageway=Passageway::where('passageway_status=1 and passageway_also=2')->find();


            $member_net=MemberNet::where('net_member_id='.$this->param['uid'])->find();
            $params=array(
              'mchNo'=>$creditcard['mchno'], //机构号 必填  由平台统一分配 16
              'userNo'=>$member_net[$passageway['passageway_no']],  //平台用户标识  必填  默认为平台下发用户标识 32
              'bindId'=>$creditcard['bindId'],  //平台签约ID  必填  签约短信下发  32
              'smsCode'=>$this->param['smsCode'],  //短信验证码 必填    10
            );
            $url='http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/bindCardConfirm';
            $income=repay_request($params,$passageway['passageway_mech'],$url,$passageway['iv'],$passageway['secretkey'],$passageway['signkey']);
            // var_dump($income);die;
            if($income['code']=='200'){
              //修改签约状态
              $bindStatus=array(
                'bindStatus'=>$income['bindStatus'],
                'card_return' =>json_encode($card_validate),
                'card_state'  => $state
              );
              $edit=MemberCreditcard::where("bindId='{$this->param['bindId']}' and mchno='{$creditcard['mchno']}'")->update($bindStatus);
              
              if($edit){
                return ['code'=>200, 'msg'=>'签约成功~', 'data'=>''];
              }else{
                return ['code'=>464];
              }
              
            }else{
              return ['code' => 102, 'msg' => $income['message'], 'data' => ''];
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

            $passageway=Passageway::where('passageway_status=1 and passageway_also=2')->find();
            $member_net=MemberNet::where('net_member_id='.$this->param['uid'])->find();
            $url='http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/unbindCard';
            $params=array(
              'mchNo'=> $cert_card['mchno'],
              'userNo'=> $member_net[$passageway['passageway_no']],
              'bindId'=>$cert_card['bindId']
            );
            $income=repay_request($params,$passageway['passageway_mech'],$url,$passageway['iv'],$passageway['secretkey'],$passageway['signkey']);
            if($income['bindStatus']!='02')
                return ['code'=>444];
           if($cert_card->delete()===false)
                 return ['code'=>444];
            MemberNet::where(['net_member_id'=>$this->param['uid']])->update([$passageway['passageway_no']=>'']);
               
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
       $data=MemberCreditcard::with("repayment")->where('card_member_id='.$this->param['uid'].' and bindStatus=01')->select();
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
