<?php
 namespace app\api\payment;
 use app\index\model\{Passageway, Member, System, MemberNet, RegionArea, PassagewayItem};
 /**
 * @version  易宝支付通道综合类  
 * @author $Bill$(755969423@qq.com)   @date   2018-01-29 11:00
 * @param passwayId  通道id/易宝支付在通道表中的唯一ID
 * @param memberId   会员id/操作易宝支付的会员唯一ID
 * @method  getData(获取易宝支付返回数据)
 * @method  register(子商户入网)
 * @method queryFee (费率查询)
 * @method fee (设置子商户费率)
 * @method editBank (更改用户银行卡信息)
 * @method info (子商户信息查询)
 * @method  usreAudit  (用户审核)
 * @method trade (收款)
 * @method Draw (出款接口)
 * @method  buld_pram(参数拼接处理)
 * @method  HmacMd5(HmacMd5计算加密)
 * @method buld_sign (签名生成)
 * @method  hexToStr(16进制转字符串)
 * @method  strToHex(字符串转16进制)
 * @method decrypt  (解密密文)
 * @method exec  (Curl请求)
 **/
 class yibaoPay
 {
      protected $configPassway;				//用于存放该通道的配置信息
      protected $configMember;				//用于存放操作会员的基本信息
      protected $config;                              //存放基础信息
    	 function __construct($passwayId, $memberId)
    	 {
           //固定参数基本配置
           $this->config=array(
                'yeepayUrl'=>'https://skb.yeepay.com/skb-app/',                            //易宝服务端站点
                'drawNoticeUrl'=>"http://www.sundayltd.com/skb/drawnotice.php",                   //出款回调页面地址
           );
           $this->configPassway=Passageway::find($passwayId);
           if(!$this->configPassway)
                return ['code'=>-404, 'msg'=>'找不到此通道~'];
           $this->configMember=Member::find($memberId);
           if(!$this->configMember)
                return ['code'=>-404, 'msg'=>'找不到会员信息~'];
    	 }

    	 /**
     	 * @version getData 获取易宝数据    @method getData 子商户注册
     	 * @param string $url     @author Bill 755969423@qq.com   @datetime 2018-01-29 11:31
     	 * @param array $data
      */
      private function getData($url="",$data=array())
      {
           /*echo "签名字符串：";
           dump($this->buld_pram($data));
           echo "签名Key:";
           dump($this->configPassway['passageway_pwd_key']);*/
        	 if(!isset($data['hmac']))$data['hmac']= $this->buld_sign($this->buld_pram($data));
           /*echo "签名：";
           dump($data['hmac']);die();*/
           $url=$this->config['yeepayUrl'].$url;
        	 $result=$this->exec($data, $url);
        	 $result=json_decode($result, true);
        	 return $result ? $result : array('code'=>'9997','message'=>'服务端数据请求错误');
   	 }

      /**
      * @version 子商户注册    @method register 子商户注册
      * @param array $data  @author Bill 755969423@qq.com   @datetime 2018-01-29 11:31
      */
    	 public function register()
    	 {
        	 $url="register.action";
           //查询出地区码
           $code=RegionArea::where(['pro_id'=>$this->configMember->memberCashcard['card_bank_province'], 'city_id'=>$this->configMember->memberCashcard['card_bank_city']])->find();
        	 $data=array(
           	 'mainCustomerNumber'=> $this->configPassway['passageway_mech'],     				//大商户ID
            	 //'loginPassword'=>$this->encrypt("abc123456"),						                          //登陆密码
            	 //'tradePassword'=>$this->encrypt("abc123"), 						                          //交易密码
            	 'requestId'=>"zhuce".make_order(),      								                          //注册流水号
            	 'customerType'=>'PERSON', 											                          //个人|PERSON , ENTERPRISE|企业 , INDIVIDUAL|个体工商户
                'businessLicence'=>'',                                                                                            //营业执照号  个人时传空参与签名
            	 'bindMobile'=>$this->configMember['member_mobile'], 	                           		     //绑定的手机号
            	 'signedName'=>$this->configMember['member_nick'],  									//商户签约名: 个人用户时,签约名和开户名一样; 企业、个体工商户 时,签约名传企业名称全称
            	 'linkMan'=>$this->configMember['member_nick'], 										//推荐人姓名
            	 'idCard'=>$this->configMember->membercert['cert_member_idcard'], 					//商户法人身份证号,同一个身份证号，只能在一个大商户下注册一个账号
            	 'legalPerson'=>$this->configMember['member_nick'], 									//商户的法人姓名
            	 'minSettleAmount'=>'1', 													                          //起结金额
            	 'riskReserveDay'=>'0', 													                          //0|T0出款,N|TN出款
            	 'bankAccountNumber'=>$this->configMember->memberCashcard['card_bankno'],	//银行卡号
            	 'bankName'=>$this->configMember->memberCashcard['card_bankname'],	          //工商、农业、招商、建设、交通、中信、光大、北京、深圳发展、中国、兴业、民生
            	 'accountName'=>$this->configMember['member_nick'],  									//开户名
            	 //'areaCode'=>$code['yibao_code'],												          //商户所在地区,请根据【银联 32 域码 表 0317.xls-来自易宝】,填写编码
            	 //'certFee'=>'0',															                          //认证费用
            	 'manualSettle'=>'Y', 														                          //N否是自助结算 N - 隔天自动打 款;Y - 不会自动打款
            	 'BankCardPhoto'=>new \CURLFile(ROOT_PATH.'\public\static\bank\default.png','image/jpeg','default.png'),//new CURLFile('uploads/bank.png','image/jpeg','bank.png'), 		//银行卡正面照
            	 'idCardPhoto'=>new \CURLFile(ROOT_PATH.'\public\static\bank\default.png','image/jpeg','default.png'),//new CURLFile('uploads/bank.png','image/jpeg','bank.png'), 			//身份证正面照
            	 'idCardBackPhoto'=>new \CURLFile(ROOT_PATH.'\public\static\bank\default.png','image/jpeg','default.png'),//new CURLFile('uploads/bank.png','image/jpeg','bank.png'), 		//身份证背面照
            	 'PersonPhoto'=>new \CURLFile(ROOT_PATH.'\public\static\bank\default.png','image/jpeg','default.png'),//new CURLFile('uploads/bank.png','image/jpeg','bank.png'), 			//银行卡与身份证及本人上半身合照
           );
           $result=$this->getData($url,$data);
           if($result['code']!=0000)
           {
                return ['code'=>$result['code'], 'msg'=>$result['message'], 'data'=>''];
           }else{
                $nets=MemberNet::where(['net_member_id'=>$this->configMember['member_id']])->setField('sTQZm',$result['customerNumber']);
                //入网成功后执行子商户审核审核
                if(!$nets){
                      return ['code'=>-101, 'msg'=>'入网信息更改失败~', 'data'=>''];
                }else{
                     $audit=$this->usreAudit($result['customerNumber']);
                     if($audit['code']!=0000){
                           return ['code'=>-102, 'msg'=>$audit['message'], 'data'=>''];
                     }else{
                           $also=PassagewayItem::where(['item_passageway'=>$this->configPassway['passageway_id'], 'item_group'=>$this->configMember['member_group_id']])->value('item_rate');
                           $also=$also ?? 0.7 ;
                           $free=$this->fee($result['customerNumber'], 1, $also/100);//去设置费率
                           $YourSister=true;
                           for ($i=2; $i<=5 ; $i++)
                           { 
                                $MyReayThinkCaoYourMather=$membernetObject->fee($this->member_infos->membernet[$this->passway_info->passageway_no], $i, 0);
                                if($MyReayThinkCaoYourMather['code']!=0000)
                                {
                                     $YourSister=false;
                                     break;
                                }
                           }
                           if($free['code']!=0000 or $YourSister!=true)
                                return ['code'=>-103, 'msg'=>'费率设置失败~', 'data'=>''];
                           else
                                return ['code'=>200, 'msg'=>'入网成功~', 'data'=>''];
                     }
                }
           }
      }

      /**
      * @version queryFee 费率查询   @method queryFee 费率查询
      * @param string $customerNumber 子商户编号    @author Bill 755969423@qq.com
      * @param number $type
      */
      public function queryFee($customerNumber='',$type=1)
      {
           $url="queryFeeSetApi.action";
           $data=array(
                'customerNumber'=>$customerNumber,                                                           //子商户编号
                'mainCustomerNumber'=>$this->configPassway['passageway_mech'],               //总商户ID
                'productType'=>(string)$type,                                                                          //整数类型, 1.交易 2.提现 3.日结通基 本 4.日结通额外 5.日结通非工作日 6.微信
           );
           return $this->getData($url,$data);
      }

      /**
      * @version fee 费率设置    @method fee 设置子商户费率  @datatime 2018-01-29 13:27
      * @param string $customerNumber   @author Bill 755969423@qq.com 
      * @param number $ype
      * @param number $rate
      */
      public function fee($customerNumber='',$type=1,$rate=1)
      {
           $url="feeSetApi.action";
           $data=array(
                'customerNumber'=>$customerNumber,                                                                    //子商户编号
                'mainCustomerNumber'=>$this->configPassway['passageway_mech'],                        //平台商户ID
                'productType'=>(string)$type,                                                                                   //整数类型, 1.交易 2.提现 3.日结通基 本 4.日结通额外 5.日结通非工作日 6.微信
                'rate'=>(string)$rate,                                                                                                 //费率
           );
           return $this->getData($url,$data);
      }

      /**
      * @version editBank 更改用户银行卡信息   @method editBank 更改用户银行卡信息   @datatime 2018-01-29 13:34
      * @param string $customerNumber 子商户编号     @author Bill 755969423@qq.com
      * @param string $bankCardNumber 银行卡卡号
      * @param string $bankName 开户行 【工商、农业、招商、建设、交通、中信、光大、北京、深圳发展、中国、兴业、民生】
      */
      public function editBank($customerNumber="",$bankCardNumber="",$bankName="")
      {
           $url="customerInforUpdate.action";
           $data=array(
                'mainCustomerNumber'=>$this->configPassway['passageway_mech'],                   //平台商户编号
                'customerNumber'=>$customerNumber,                                                               //子商户编号
                'bankCardNumber'=>(string)$bankCardNumber,                                                   //银行卡号
                'bankName'=>(string)$bankName,                                                                        //银行名称
           );
           $data['hmac']= $this->buld_sign($this->buld_pram($data));
           $data['modifyType']='2';
           return $this->getData($url,$data);
      }

      /**
      * @version info 子商户信息查询     @method info 子商户信息查询   @datatime 2018-01-29 13:39
      * @param string $mobilePhone 子商户手机号码   @author Bill 755969423@qq.com
      */
      public function info($mobilePhone="")
      {
           $url="customerInforQuery.action";
           $data=array(
                'mainCustomerNumber'=>$this->configPassway['passageway_mech'], 
                'mobilePhone'=>$mobilePhone
           );
           return $this->getData($url,$data);
      }

      /**
      * @version usreAudit 审核用户         @method  usreAudit  审核用户  @datatime 2018 01-29 13:44
      * @param string $customerNumber 子商户编号    @author Bill 755969423@qq.com
      * @param string $status 审核状态 SUCCESS|通过,FAILED|拒绝
      * @param string $reason 如果审核状态为拒绝，此处为拒绝原因
      */
      public function usreAudit($customerNumber='',$status="SUCCESS",$reason='')
      {
           $url="auditMerchant.action";
           $data=array(
                'customerNumber'=> $customerNumber,
                'mainCustomerNumber'=>$this->configPassway['passageway_mech'], 
                'status'=>$status,
                'reason'=>$reason,
           );
           if($reason=='') unset($data['reason']);
           return $this->getData($url,$data);
      }

      /**
      * @version method收款（仅店主收款）  @method trade (收款)     @datatime 2018-01-31 11:43
      * @param string $customerNumber    @author Bill 755969423@qq.com
      * @param number $amount        @param string $tradeNo
      * @param string $payBankNo     @param string $mcc
      */
      public function trade($customerNumber='',$amount=0,$tradeNo='',$payBankNo='',$mcc="5311")
      {
           $test = array('5311','4511','4733');
           $i = rand(0,2);
           $mcc=$test[$i];
           $url="receiveApi.action";
           $data=array(
                'source'=>'B',                                                                                                                                            #支付方式 D - 卡号收款B - 店主收款S - 短信收款T - 二维码收款 W - 微信支付
                'mainCustomerNumber'=>$this->configPassway['passageway_mech'],                                                                #平台商户ID
                'customerNumber'=>$customerNumber,                                                                                                            #子商户ID
                'amount'=>(string)$amount,                                                                                                                               #金额
                'mcc'=>$mcc,                                                                                                                                                  #这是个啥                                                                   
                'requestId'=>$tradeNo,                                                                                                                                     #应该是订单号
                'callBackUrl'=>System::getName('system_url').$this->configPassway->cashout->cashout_callback,                       #异步回调地址
                'webCallBackUrl'=>System::getName('system_url').'/api/Userurl/calllback_success',                                               #页面跳转地址
           );
           if($payBankNo!='') $data['payerBankAccountNo']= $payBankNo;                                                                              #支付银行卡号
           $result= $this->getData($url,$data);
           if($result['code']!=0000)
                return ['code'=>206, 'msg'=>$result['message'], 'data'=>''];
           $return=['code'=>200, 'msg'=>'请求成功~', 'data'=>['url'=>$this->decrypt($result['url']), 'data'=>$result]];
           return $return;
      }

      /**
      * @version 出款接口     @method Draw 出款接口    @datetime  2018-01-31 14:30
      * @param string $customerNumber 子商户编号   @author Bill 755969423@qq.com
      * @param number $amount 出款金额    @param string $externalNo 出款流水号  
      * @param string $transferWay 出款方式:1|日结通,2|委托结算   @return array
      */
      public function Draw($customerNumber='',$amount=0,$externalNo="",$transferWay='1')
      {
           $url="withDrawApi.action";
           $data=array(
                'amount' => (string)$amount,
                'customerNumber'=>(string)$customerNumber,
                'externalNo'=>$externalNo,
                'mainCustomerNumber'=>$this->configPassway['passageway_mech'], 
                'transferWay'=>(string)$transferWay,
                'callBackUrl'=>$this->config['drawNoticeUrl'],
           );
           $return=array('code'=> '0000',  'message'=>'');
           $result=$this->getData($url,$data);
           return $result;
      }

      /**
      * @version 参数排序等处理    @method buld_pram 对数据进行处理排序 拼接
      * @param array $data  @author Bill 755969423@qq.com   @datetime 2018-01-29 11:31
      */
      private function buld_pram($data=array(),$method="")
      {
        	 $str='';
        	 if(count($data)<1) return $str;
        	 foreach($data as $key=>$val)
        	 {
            	 if(gettype($val)!='string') continue;
            	 if($method!='')
            	 {
                 	 if($str!='') $str.="&";
                 	 $str.="$key=$val";
            	 }else{
                	 $str.=$val;
            	 }
        	 }
        	 return $str;
      }

      /**
      *	@version  HmacMd5计算     @author Bill 755969423@qq.com     @method HmacMd5  HmacMd5计算 返回不可逆128加密字符串
      * 	@param string $data            @datetime 2018-01-29 11:31
      * 	@param string $key
      */
      private  function HmacMd5($data="",$key="")
      {
        	 $b = 64; // byte length for md5
        	 if (strlen($key) > $b)
        		 $key = pack("H*",md5($key));
        	 $key = str_pad($key, $b, chr(0x00));
        	 $ipad = str_pad('', $b, chr(0x36));
        	 $opad = str_pad('', $b, chr(0x5c));
        	 $k_ipad = $key ^ $ipad ;
        	 $k_opad = $key ^ $opad;
        	 return md5($k_opad . pack("H*",md5($k_ipad . $data)));
      }

      /**
      * @version buld_sign 生成签名   @data 2018-11-29 13:20  @method buld_sign 签名生成
      * @param string $str   @author Bill 755969423@qq.com
      **/
      private function buld_sign($str='')
      {
           return $this->HmacMd5($str,$this->configPassway['passageway_pwd_key']);
      }      

      /**
      *	@version  hexToStr 十六进制转字符串     @author Bill 755969423@qq.com     @method hexToStr 十六进制转字符串
      * 	@param string $hex            @datetime 2018-01-29 11:31
      */
      public function hexToStr($hex)
      {
       	 $string="";
        	 for ($i=0;$i<strlen($hex)-1;$i+=2)
            	 $string.=chr(hexdec($hex[$i].$hex[$i+1]));
        	 return  $string;
    	 }

      /**
      *	@version  strToHex 字符串转十六进制     @author Bill 755969423@qq.com     @method strToHex 字符串转十六进制
      * 	@param string $string            @datetime 2018-01-29 11:31
      */
    	 public function strToHex($string)//
    	 {
        	 $hex="";
        	 $tmp="";
        	 for ($i=0;$i<strlen($string);$i++)
        	 {
            	 $tmp = dechex(ord($string[$i]));
            	 $hex.= strlen($tmp) == 1 ? "0".$tmp : $tmp;
        	 }
        	 $hex=strtoupper($hex);
        	 return $hex;
    	 }

      /**
      * @version  decrypt 解密密文     @author Bill 755969423@qq.com     @method decrypt  解密密文
      * @param string $sStr            @datetime 2018-01-29 11:31
      */
      private  function decrypt($sStr="") 
      {
           $sKey=$this->configPassway['passageway_pwd_key'];
           if(strlen($sKey)>16) $sKey=substr($sKey,0,16);
           $decrypted= mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $sKey, $this->hexToStr($sStr), MCRYPT_MODE_ECB);
           $dec_s = strlen($decrypted);
           $padding = ord($decrypted[$dec_s-1]);
           $decrypted = substr($decrypted, 0, -$padding);
           //die(urldecode($decrypted));
           return urldecode($decrypted);
      }

      /**
      * @version  exec Curl请求     @author Bill 755969423@qq.com     @method exec  Curl请求
      * @param string $data  $url            @datetime 2018-01-29 11:31
      */
      function exec($data, $url) 
      {
           $ch = curl_init();
           //echo $url;
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_HEADER, 0);
           curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
           curl_setopt($ch, CURLOPT_TIMEOUT, 100);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
           curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);
           curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
           if( ! $result = curl_exec($ch))
                trigger_error(curl_error($ch));
           curl_close($ch);
           return $result;
      }

 }
