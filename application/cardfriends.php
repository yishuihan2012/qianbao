<?php
 namespace app\api\payment;
 use app\index\model\Order;
 use app\index\model\Member;
 use app\index\model\Passageway;
 use app\index\model\MemberCreditcard;
 class cardfriends(){
 		private $url;
 		public function __construct(){
 			$this->url='http://118.31.38.147:18888/open-gateway/';
 			$this->productId='0600',//同名快捷API-D0
 		}
 		// 支持的银行
 		// 	102	工商银行
		// 103	农业银行
		// 104	中国银行
		// 105	建设银行
		// 203	农业发展银行
		// 301	交通银行
		// 302	中信银行
		// 303	光大银行
		// 304	华夏银行
		// 305	民生银行
		// 306	广发银行
		// 307	平安银行
		// 308	招商银行
		// 309	兴业银行
		// 310	浦发银行
		// 313	北京银行
		// 315	恒丰银行
		// 325	上海银行
		// 403	邮储银行
 		/**
 		 * 1商户进件
 		 * @return [type] [description]
 		 */
 		public function merch_income(){
 			$data=array(
 				'third_mer_no'=>'10117569615504',//商户号	Str-max32	M	M	三方商户号，商户自行维护
				'type'=>2,//商户类型	int	M	M	进件商户类型1-企业 2-个人
				'full_name'=>'易水寒',//商户全称	Str-max18	M	M	进件商户全称
				'simple_name'=>'水寒',//商户简称	Str-max32	M	M	进件商户简称
				'address'=>'测试地址',//商户地址	Str-max32	C	M	进件商户的详细地址
				'bank_card'=>'6215590200003242971',//银行卡号	Str-max32	M	M	结算银行卡号
				'sub_bank_name'=>'济南支行',//支行名称	Str-max255	M	M	结算银行卡支行名称
				'sub_bank_code'=>'110',//支行联行号	Str-max255	M	M	结算银行卡支行联行号
				'bank_name'=>'工商银行',//银行名称	Str-max255	M	M	结算银行卡银行名称
				'idcard'=>'370983199109202832',//身份证	Str-max32	M	M	结算卡开户行证件号
				'mobile'=>'17569615504',//手机号码	Str-max255	M	M	结算卡开户行手机号
				'real_name'=>'许成成',//真实姓名	Str-max255	M	M	结算卡开户行真实姓名
				'set_type'=>'0',//结算类型	int	M	M	结算类型 0-D0  1-T1
				'rate_list'=>'',//支付费率	Str-max255	M	M	支持的支付产品费率集合，JSON数据格式，格式参考
				'mer_no'=>'',//平台商户号	mer_no		M	平台商户号
 			);
 			$sign=$this->sign($data);
 			$res=$this->send_request($this->url.'/merchant/invoke',json_encode($data),$sign);
 		}
 		/**
 		 * 2商户更新
 		 * @return [type] [description]
 		 */
 		public function  merch_update(){
 			$data=array(
 				'merno'=>'',//商户号	Str-max32	C	C	平台返回的商户号，商户号和三方商户号必传其一
				'third_mer_no'=>'',//三方商户号	Str-max32	C	C	三方商户号，商户自行维护，商户号和三方商户号必传其一
				'type'=>'',//商户类型	int	C	C	进件商户类型1-企业 2-个人
				'biz_type'=>'',//商户类型	int	C	C	改件类型 1-费率 2-结算卡 3-全部
				'bank_card'=>'',//银行卡号	Str-max32	C	C	结算银行卡号
				'sub_bank_name'=>'',//支行名称	Str-max255	C	C	结算银行卡支行名称
				'sub_bank_code'=>'',//支行联行号	Str-max255	C	C	结算银行卡支行联行号
				'bank_name'=>'',//银行名称	Str-max255	C	C	结算银行卡银行名称
				'idcard'=>'',//身份证	Str-max32	C	C	结算卡开户行证件号
				'mobile'=>'',//手机号码	Str-max255	C	C	结算卡开户行手机号
				'real_name'=>'',//真实姓名	Str-max255	C	C	结算卡开户行真实姓名
				'set_type'=>'',//结算类型	int	C	C	结算类型 0-D0  1-T1
				'rate_list'=>'',//支付费率	Str-max255	C	C	支持的支付产品费率集合，JSON数据格式，格式参考
 			);
 		}
 		/**
 		 * 3商户查询
 		 * @return [type] [description]
 		 */
 		public function merch_query(){
 			

 		}	
 		/**
 		 * 4绑卡申请
 		 * @return [type] [description]
 		 */
 		public function bind_card(){
 			$data=array(
 				'method'=>"api.bindcard.apply",//方法名	Str-max30	M	M	固定填写
				'merno'=>'',//商户号	Str-max32	M	M	平台进件返回商户号
				'bus_no'=>'',//业务编号	Str-max10	M	M	固定0303
				'cardno'=>'',//卡号	Str-max32	M	M	支付卡号
				'cardname'=>'',//银行名称	Str-max10	M	M	详见支持银行卡列表
				'idcard'=>'',//证件号码	Str-max32	M	M	支付卡对应开户人证件号码
				'name'=>'',//姓名	Str-max32	M	M	支付卡姓名
				'return_url'=>'',//前端通知地址	Str-max255	M	C	前端跳转回调地址
				'notify_url'=>'',//后台通知地址	Str-max255	M	C	后台通知回调地址
				'phone'=>'',//手机号	Str-max32	M	M	支付卡绑定手机号
				'card_type'=>'2',//银行卡类型	Int-1	M	M	绑卡类型固定填写2
				'bank_code'=>'',//银行卡编码	Str-max255	M	M	详见支持银行卡列表
				'cvv2'=>'',//信用卡cvv	Str-max4	C	C	当绑卡类型为2时，必填
				'validate'=>'',//信用卡有效期	Str-max4	C	C	当绑卡类型为2时，必填，格式为MMYY，例如：0721
				'meruserno'=>'',//商户自定义用户编号	Str-max32	C	C	商户自定义用户编号
				'needconfirm'=>'',//是否需要调确认绑卡接口	Int-1	C	C	是否需要调用绑卡确认接口1-需要0-不需要
				'smsseq'=>'',//验证码流水号	Str-max10 C	当needconfirm= 1时：代表需要调用确认绑卡接口，同时会向用户手机发送一条短信校验码
				'authcode'=>'',//授权码	Str-max20		C	当needconfirm= 1时：授权码，在调用确认卡巴接口时会用到
				'ishtml'=>'3',//是否返回的html	Str-int1		C	1-返回html 需要自行生成页面进行跳转2-验证码验证 - 需要调用确认绑卡接口3-返回url，直接跳转即可
				'url'=>'',//Html内容	Str-max255		C	当ishtml=1,则返回html代码 当ishtml=2,返回值为空 当ishtml=3,返回一个url地址，直接跳转
 			);
 		}
 		/**
 		 * 5确认绑卡
 		 * @return [type] [description]
 		 */
 		public function bind_sure(){
 			$data=array(
 				'method'=>'api.bindcard.confirm',//方法名	Str-max30	M	M	固定填写
				'merno'=>'',//商户号	Str-max32	M	M	平台进件返回商户号
				'bus_no'=>'',//业务编号	Str-max10	M	M	固定0303
				'cardno'=>'',//卡号	Str-max32	M	M	支付卡号
				'cardname'=>'',//银行名称	Str-max10	M	M	详见支持银行卡列表 
				'idcard'=>'',//证件号码	Str-max32	M	M	支付卡对应开户人证件号码
				'name'=>'',//姓名	Str-max32	M	M	支付卡姓名
				'phone'=>'',//手机号	Str-max32	M	M	支付卡绑定手机号
				'card_type'=>'2',//银行卡类型	Int-1	M	M	绑卡类型 固定填写2
				'bank_code'=>'',//银行卡编码	Str-max255	M	M	详见支持银行卡列表
				'cvv2'=>'',//信用卡cvv	Str-max4	C	C	当绑卡类型为2时，必填
				'validate'=>'',//信用卡有效期	Str-max4	C	C	当绑卡类型为2时，必填，格式为MMYY，例如：0721
				'smscode'=>'',//用户收到的短信验证码	Int-max6	M	M	调用绑卡，下发的手机短信验证码
				'meruserno'=>'',//商户自定义用户编号	Str-max32	C	C	商户自定义用户编号
 			);
 		}
 		/**
 		 * 6绑卡已异步通知
 		 * @return [type] [description]
 		 */
 		public function bind_callback(){
 			$data=array(
 				'orgid'=>'',//机构号	Str-max32	M	平台分配机构号
				'merno'=>'',//商户号	Str-max32	M	平台分配商户号
				'authcode'=>'',//交易金额	Str-max32	M	订单交易金额
				'meruserno'=>'',//商户自定义用户编号	Str-max32	C	商户自定义编号
				'bind_status'=>'',//订单交易时间	Str-max255	M	订单交易时间，格式：yyyy-MM-dd HH:mm:ss
				'sign_data'=>'',//数据签名	Str-max32	M	签名规则与请求的签名规则一致
				'timestamp'=>'',//请求时间戳	Str-max14	M	格式:yyyyMMddHHmmss
 			);
 		}
 		/**
 		 * 7订单预下单
 		 * @return [type] [description]
 		 */
 		public function order_preview(){
 			$data=array(
 				'method'=>'api.order.pre',//方法名	Str-max30	M	M	固定填写api.order.pre
				'merno'=>'',//商户号	Str-max32	M	M	平台进件返回商户号
				'bus_no'=>'',//业务编号	Str-max10	M	M	固定0303
				'amount'=>'',//交易金额	Str-max32	M	M	交易金额，单位（分）
				'authcode'=>'',//授权码	Str-max32	M	M	调用绑卡接口返回的授权码
				'goods_info'=>'',//商品信息	Str-max32	M	M	支付商品信息
				'order_id'=>'',//订单号	Str-max32	M	M	商户自定义订单号
				'api_type'=>'',//快捷类型	Int-2	M	M	11-有积分  12-无积分
				'plat_order_sn'=>'',//平台订单号	Str-max32		M	平台订单号
				'needconfirm'=>'',//是否需要调用确认支付1-需要0-不需要 
				'ishtml'=>'3',//是否返回的html	Str-int1		C	1 -返回html 需要自行生成页面进行跳转 3 -返回url，直接跳转即可
				'url'=>'3',//Html内容	Str-max255		C	当ishtml=1,则返回html代码 当ishtml=3,返回一个url地址，直接跳转
 			);
 		}
 		/**
 		 * 8订单支付
 		 * @return [type] [description]
 		 */
 		public function order_pay(){
 			$data=array(
 				'method'=>'api.order.pay',//方法名	Str-max30	M	M	固定填写
				'merno'=>'',//商户号	Str-max32	M	M	平台进件返回商户号
				'bus_no'=>'',//业务编号	Str-max10	M	M	固定0303
				'amount'=>'',//交易金额	Str-max32	M	M	交易金额，单位（分）
				'authcode'=>'',//授权码	Str-max32	M	M	调用绑卡接口返回的授权码
				'goods_info'=>'',//商品信息	Str-max32	M	M	支付商品信息
				'order_id'=>'',//订单号	Str-max32	M	M	商户自定义订单号
				'api_type'=>'',//快捷类型	Int-2	M	M	11-有积分  12-无积分
				'plat_order_sn'=>'',//平台订单号	Str-max32		M	平台订单号
				'smscode'=>'',//用户收到的短信验证码	Int-max6	M	M	调用下单，下发的手机短信验证码
 			);
 		}
 		/**
 		 * 9订单回调
 		 * @return [type] [description]
 		 */
 		public function order_callback(){

 		}
 		/**
 		 * 10订单查询
 		 * @return [type] [description]
 		 */
 		public function order_query(){
 			$data=array(
 				'order_id'=>'',//订单号	Str-max32	M	M	商户订单号
				'amount'=>'',//订单金额	Str-max11		M	订单金额
				'plat_ordre_sn'=>'',//平台订单号	Str-max32		M	支付平台订单号
				'payment_status'=>'',//支付状态	Int-1		M	0-待支付 1-已支付 2-已取消 3-支付失败 4-下单失败 5-处理中
				'requestId'=>'',//请求订单号	Str-max32	M	保证唯一
				'orgId'=>'',//机构号	Str-max16	M	平台分配机构号
				'timestamp'=>'',//请求时间戳	Str-max14	M	格式:yyyyMMddHHmmss
				'productId'=>'',//产品ID	Str-max10	M	产品ID对应关系详见产品ID对应表
				'businessData'=>'',//业务交互数据	JSON	M	对应产品交互业务数据
				'signData'=>"",//数据签名	Str-max32	M	签名规则详见签名算法
				'dataSignType'=>"",//业务数据加密方式 C 上送的业务参数是否加密，为空默认为明文传输0-不加密，明文传输1-DES加密，密文传输若为密文传输，需要将密文进行URLEncode处理
 			);
 		}
 		/**
 		 * 11签名
 		 * @return [type] [description]
 		 */
 		public function sign($data){
 			$key='123456';//****************
 			$data=SortByASCII($data);
 			$str=http_build_query($data);
 			$str=$str.'&key='.$key;
 			$sign=strtoupper(md5($str));
 			return $sign;
 		}
 		/**
 		 * 12des加密
 		 * @return [type] [description]
 		 */
 		public function des_encrypt($data){

 		}
 		/**
 		 * 13发送请求
 		 * @return [type] [description]
 		 */
 		public function send_request($url,$data,$sign){
 			$request_data=array(
 				'requestId'=>make_rand_code(),//请求订单号	Str-max32	M	保证唯一
				'orgId'=>'123',//机构号	Str-max16	M	平台分配机构号
				'timestamp'=>date('YmdHis',time()),//请求时间戳	Str-max14	M	格式:yyyyMMddHHmmss
				'productId'=>$this->productId,//产品ID	Str-max10	M	产品ID对应关系详见产品ID对应表 
				'businessData'=>$data,//业务交互数据	JSON	M	对应产品交互业务数据
				'signData'=>$sign,//数据签名	Str-max32	M	签名规则详见签名算法
				'dataSignType'=>'0',//业务数据加密方式	Int-max1	C	上送的业务参数是否加密，为空默认为明文传输0-不加密，明文传输 1-DES加密，密文传输若为密文传输，需要将密文进行URLEncode处理
 			);
 			$res=curl_post($url,'post',$request_data);
 			var_dump($res);die;

 		}
 } 