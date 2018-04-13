<?php
 /**
 *  @version Login controller / Api 会员登录
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-08 10:13:05
 *   @return 
 */

 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use think\Loader;
 use app\index\model\MemberLogin;
 use app\index\model\System;
 use app\index\model\Alert;
 use app\index\model\Member;
 use app\index\model\SmsCode as SmsCodes;
 use app\index\model\MemberCreditcard;
 use app\index\model\MemberAccount;
 use app\index\model\MemberRelation;
 use app\index\model\MemberCashcard;

 class Login 
 {
      protected $param;
      public $error;
      public function __construct($param)
      {
        	 $this->param=$param;
      }

      /**
 	 *  @version Login method / Api 登录方法
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-08 11:19:05
 	 *  @param phone=手机号  pwd=密码     ☆☆☆::使用中
      **/
      public function login()
      {	

      	 #验证参数是否存在
      	 if(!phone_check($this->param['phone']))
      	 	 return ['code'=>401];
      	 #验证密码
      	 if(!isset($this->param['pwd']) || empty($this->param['pwd']))
      	 	 return ['code'=>402];
      	 #查找账号
      	 $memberLogin=MemberLogin::phone_exit($this->param['phone']);
      	 #能否查找到手机号码
      	 if(!$memberLogin)
      	 	 return ['code'=>403,'msg'=>'该手机号尚未注册'];
      	 #验证最大尝试次数 TODO: 验证多久时间内最大的登录次数
           if($memberLogin['login_attempts']>=System::getName('is_locking'))
           {
                 #判断当前登录时间是否距离上一次锁定时间超出系统设定时间
                 $timeout=floor(abs(time()-strtotime($memberLogin['login_update_time']))/60);
                 if( $timeout < System::getName('is_locking_time'))
                      return ['code'=>312];
                 $memberSetInc=MemberLogin::where(['login_account'=>$this->param['phone']])->setField('login_attempts',0);
           }
      	 #如果手机号存在的话 对比密码信息 TODO 密码加密算法 非对称加密
           if ($memberLogin['login_pass']!=encryption($this->param['pwd'], $memberLogin['login_pass_salt'])) {
           	 $memberSetInc=MemberLogin::where(['login_account'=>$this->param['phone']])->setInc('login_attempts');
                 $memberTime=MemberLogin::where(['login_account'=>$this->param['phone']])->setField('login_update_time',date('Y-m-d H:i:s',time()));
                 return ['code'=>302];
           }
           #验证账号异常
           if($memberLogin['login_state']!='1')
                 return ['code'=>318];

           $member=Member::get($memberLogin['member']['member_id']);

           $data=array();
           $data['authState']=$member['member_cert'];
           $data['name']=$member['member_cert']==1 ? $member['member_nick'] : '';
           // var_dump($member->memberCert);die;
           $data['membercard']=$member['member_cert']==1 ? $member->memberCert->cert_member_idcard : '';#card_preg()
           $data['phone']=$member['member_mobile'];
           $data['portrait']=$member['member_image'];
           
           $data['wallet_total_revenue']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_total_revenue), 0, -1));
           $data['wallet_fenrun']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_fenrun), 0, -1));
           $data['wallet_commission']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_commission), 0, -1));
           $data['wallet_amount']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_amount), 0, -1));

           $data['memberLevelId']=$member['member_group_id'];
           $data['memberLevelName']=$member->memberGroup->group_name;
          
           #查询信用卡绑定数量
           $data['numberOfCreditCard']=MemberCreditcard::where(['card_member_id'=>$memberLogin['member']['member_id'],'card_state'=>'1'])->count();
           $alipay=MemberAccount::where(['account_user'=>$memberLogin['member']['member_id'],'account_type'=>'Alipay'])->find();
           $wei=MemberAccount::where(['account_user'=>$memberLogin['member']['member_id'],'account_type'=>'Weipay'])->find(); 

           $data['alipayBindState']=$alipay? 1 : 0;
           $data['wechatBindState']= $wei ? 1 : 0;
           if($data['alipayBindState']==1){
              $data['payPlatformId']=$alipay['account_id'];
           }else{
             $data['alipayPlatformId']=0;
           }
           if($data['wechatBindState']==1){
              $data['wechatpayPlatformId']=$wei['account_id'];
           }else{
            $data['wechatpayPlatformId']=0;
           }

           #查询会员下级数量 TODO: 现在是直接下级数量 是否改成三级
           $data['subordinateNumber']=$this->get_lower_total($memberLogin['member']['member_id']);
           $parent_id=MemberRelation::where(['relation_member_id'=>$memberLogin['member']['member_id']])->value('relation_parent_id');
           $parent=Member::where('member_id',$parent_id)->value('member_nick');
           $parent=is_numeric($parent) ? ' ' : $parent;
           $data['parent']=$parent_id=='0' ? '' : $parent;
           $data['parent_phone']=$parent_id=='0' ? '' : Member::where('member_id',$parent_id)->value('member_mobile');
           #查询用户储蓄卡
           $CashCard=MemberCashcard::where(['card_member_id'=>$memberLogin['member']['member_id'],'card_state'=>1])->find();
           if(empty($CashCard)){
              $data['cashcardinfo']='';
           }else{
              $data['cashcardinfo']=$CashCard['card_bankname'].' 尾号'.substr($CashCard['card_bankno'], -4); 
           }
           $newToken=get_token();
           MemberLogin::update(['login_id'=>$memberLogin['login_id'],'login_token'=>$newToken,'login_attempts'=>0]);
           //是否有未读消息
          $hasmsg=db('notice')->where(['notice_recieve'=>$memberLogin['member']['member_id'],'notice_status'=>0])->find();
           $data['hasmessage']=$hasmsg ? 1 : 0; 

           $data['token']=$newToken;
           $data['uid']=$memberLogin['member']['member_id'];

           return ['code'=>200,'msg'=>'登录成功~', 'data'=>$data];
      }
       //获取三级下级总数
      public function get_lower_total($uid){
          $count=0;
          $MemberRelation_1rd=MemberRelation::haswhere("members",'member_id!=""')->where(["relation_parent_id"=>$uid])->select();
          $count+=count($MemberRelation_1rd);
          foreach ($MemberRelation_1rd as $k => $val) {
                $member_2rd=MemberRelation::haswhere("members",'member_id!=""')->where(['relation_parent_id'=>$val['relation_member_id']])->select();
                $count+=count($member_2rd);
                foreach ($member_2rd as $k1 => $val1) {
                  $group3 = MemberRelation::haswhere("members",'member_id!=""')->where(['relation_parent_id'=>$val1['relation_member_id']])->select();
                    $count+=count($group3);
                }

             }
             return $count;
      }
      /**
      *  @version find_pwd method / Api 找回密码
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-13 09:25:05
      *  @param phone=手机号  smscode=验证码  new_pwd=新密码     ☆☆☆::使用中
      **/
      public function find_pwd()
      {
          //return ['code'=>505, 'data'=>$this->param['smsCode'].$this->param['phone']];
           #验证参数是否存在
           if(!phone_check($this->param['phone']))
               return ['code'=>401];
           #手机验证码参数
           if(!isset($this->param['smsCode']) || empty($this->param['smsCode']))
                 return ['code'=>404];
           #验证码验证规则 读取本手机号最后一条没有使用的验证码 并且在系统设置的有效时间内
           $code_info=SmsCodes::where(['sms_send'=>$this->param['phone'],'sms_log_state'=>1])->whereTime('sms_log_add_time', "-".System::getName('code_timeout').' minutes')->order('sms_log_id','desc')->find();
           //return ['code'=>600, 'data'=>SmsCodes::getLastSql()];
           if(!$code_info || ($code_info['sms_log_content']!=$this->param['smsCode']))
                 return ['code'=>404];
           #改变验证码使用状态
           $code_info->sms_log_state=2;
           #验证是否成功
           if($code_info->save()===false)
                 return ['code'=>429];
           #检查用户名(是否存在)
           $member=MemberLogin::phone_exit($this->param['phone']);
           if(!$member)
                 return ['code'=>304];
           if(encryption($this->param['new_pwd'], $member['login_pass_salt'])==$member['login_pass'])
                 return ['code'=>319];
           Db::startTrans();
           try {
                 #随机密码salt
                 $rand_salt=make_rand_code();
                 #加密密码
                 $pwd=encryption($this->param['new_pwd'], $rand_salt);
                 #修改会员登录信息和token
                 $token = get_token();
                 $member_login= new MemberLogin();
                 $result=$member_login->save([
                    'login_pass'  =>$pwd,
                    'login_pass_salt' => $rand_salt,
                    'login_token'   => $token
                  ],['login_account' =>$this->param['phone']]);
                 if(!$result){
                      Db::rollback();
                      return ['code'=>313];
                 }
                 Db::commit();
                 $data=Member::member_info($token);
                 return ['code'=>200, 'msg'=>'密码更改成功~', 'data'=>$data];
           } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>308,'msg'=>$e->getMessage()];
           }
      }

      // #获取与登录相同的信息
      public function get_info(){
           $memberLogin=MemberLogin::where("login_member_id={$this->param['uid']} and login_token='{$this->param['token']}'")->find();
           if(empty($memberLogin)){
              return ['code'=>317];
           }
           $member=Member::get($memberLogin['login_member_id']);
           $data=array();
           $data['authState']=$member['member_cert'];
           $data['name']=$member['member_cert']==1 ? $member['member_nick'] : '';

           $data['membercard']=$member['member_cert']==1 ? $member->memberCert->cert_member_idcard : '';#card_preg()
           $data['phone']=$member['member_mobile'];
           $data['portrait']=$member['member_image'];
           
           $data['wallet_total_revenue']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_total_revenue), 0, -1));
           $data['wallet_fenrun']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_fenrun), 0, -1));
           $data['wallet_commission']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_commission), 0, -1));
           $data['wallet_amount']=sprintf("%.2f",substr(sprintf("%.3f", $member->memberWallet->wallet_amount), 0, -1));

           $data['memberLevelId']=$member['member_group_id'];
           $data['memberLevelName']=$member->memberGroup->group_name;
           #查询信用卡绑定数量
           $data['numberOfCreditCard']=MemberCreditcard::where(['card_member_id'=>$memberLogin['member']['member_id'],'card_state'=>'1'])->count();
           $alipay=MemberAccount::where(['account_user'=>$memberLogin['member']['member_id'],'account_type'=>'Alipay'])->find();
           $wei=MemberAccount::where(['account_user'=>$memberLogin['member']['member_id'],'account_type'=>'Weipay'])->find();
           $data['alipayBindState']=$alipay? 1 : 0;
           $data['wechatBindState']= $wei ? 1 : 0;
           if($data['alipayBindState']==1){
              $data['payPlatformId']=$alipay['account_id'];
           }else{
             $data['alipayPlatformId']=0;
           }
           if($data['wechatBindState']==1){
              $data['wechatpayPlatformId']=$wei['account_id'];
           }else{
            $data['wechatpayPlatformId']=0;
           }
           #查询会员下级数量 TODO: 现在是直接下级数量 是否改成三级
           $data['subordinateNumber']=$this->get_lower_total($memberLogin['member']['member_id']);
           $parent_id=MemberRelation::where(['relation_member_id'=>$memberLogin['member']['member_id']])->value('relation_parent_id');
           $parent=Member::where('member_id',$parent_id)->value('member_nick');
           $parent=is_numeric($parent) ? ' ' : $parent;
           $data['parent']=$parent_id=='0' ? '' : $parent;
           $data['parent_phone']=$parent_id=='0' ? '' : Member::where('member_id',$parent_id)->value('member_mobile');
           #查询用户储蓄卡
           $CashCard=MemberCashcard::where(['card_member_id'=>$memberLogin['member']['member_id'],'card_state'=>1])->find();
           if(empty($CashCard)){
              $data['cashcardinfo']='';
           }else{
              $data['cashcardinfo']=$CashCard['card_bankname'].' 尾号'.substr($CashCard['card_bankno'], -4); 
           }
            
           $data['hasmessage']=1; 

           $data['token']=$memberLogin['login_token'];
           $data['uid']=$memberLogin['member']['member_id'];
           return ['code'=>200,'msg'=>'获取成功~', 'data'=>$data];
      }
      /**
       * 弹窗广告
       */
      public function alert(){
        $data=Alert::where('alert_status',1)->order('alert_id desc')->find();
        return json_encode(['code'=>$data ? 200 : 404,'msg'=>'获取成功~', 'data'=>$data]);
        return ['code'=>$data ? 200 : 404,'msg'=>'获取成功~', 'data'=>$data];
      }
 }
