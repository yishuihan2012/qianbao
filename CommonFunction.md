##1 common.php 公共函数说明
###1.1 function encryption($str, $salt, $method='md5')
 //-----------------------------------------------------------
 // @version  密码加密方式
 // @author   $bill$
 // @datatime 2017-11-30 09:04
 // @param $str=原密码  $salt=密码盐值 $method=加密方式 MD5
 // @description MD5加密明文密码+salt之后的密码返回 非散列加密 默认md5
 // @return 加密后的密码
 //-----------------------------------------------------------

 ###1.2  function get_status_text($code,$type='status')
  //-----------------------------------------------------------
 // @version  状态转换 status
 // @author   $bill$
 // @datatime 2017-11-30 09:10
 // @param $code=状态码  $status=配置状态码库
 // @description 返回对应的状态码 (弃用?)
 // @return 状态码
 //-----------------------------------------------------------

 ###1.3 function make_rand_code()
  //-----------------------------------------------------------
 // @version  随机字符串生产
 // @author   $bill$
 // @datatime 2017-11-30 09:12
 // @param  
 // @description 返回一个随机字符串
 // @return  $d=字符串
 //-----------------------------------------------------------

 ###1.4  function format_money($num, $bool=false)
  //-----------------------------------------------------------
 // @version  金额格式化
 // @author   $bill$
 // @datatime 2017-11-30 09:21
 // @param  $num=需要格式化的金额 $bool=需不需要加金额符号 默认false不加
 // @description 将金额格式化为分
 // @return  $str=格式化后的金额
 //-----------------------------------------------------------

 ###1.5  format_money_get($num)
 //-----------------------------------------------------------
 // @version  金额格式化
 // @author   $bill$
 // @datatime 2017-11-30 09:21
 // @param  $num=需要格式化的金额
 // @description 将金额格式化为万元 分->万元
 // @return  $str=格式化后的金额
 //-----------------------------------------------------------

 ###1.6 function curl_post($url, $method = 'post', $data = '')
  //-----------------------------------------------------------
 // @version  Curl Post
 // @author   $bill$
 // @datatime 2017-11-30 09:28
 // @param  $url=请求地址 $method='post' 请求方式 $data='' 请求数据
 // @description Curl方式请求url 返回请求的数据
 // @return  $str=格式化后的金额
 //-----------------------------------------------------------

 ###1.7 function xml_to_array($xml)
 //-----------------------------------------------------------
 // @version  Xml 转数组
 // @author   $bill$
 // @datatime 2017-11-30 09:31
 // @param  $xml  
 // @description xml转为数组
 // @return  转换的数组
 //-----------------------------------------------------------

 ###1.8  function days_between_dates($date_one, $date_two)
  //-----------------------------------------------------------
 // @version  时间差计算
 // @author   $bill$
 // @datatime 2017-11-30 09:32
 // @param  $date_one=前一个时间  $date_two 后一个时间   
 // @description 返回两个时间相差多少天 两个参数为字符串时间
 // @return  相差的天数
 //-----------------------------------------------------------

 ###1.9 function send_sms($phone, $message)
  //-----------------------------------------------------------
 // @version  短信发送
 // @author   $bill$
 // @datatime 2017-11-30 09:35
 // @param  $phone=接收者手机号  $message 发送内容   
 // @description 给接收着发送短信内容
 // @return  返回短信发送状态
 //-----------------------------------------------------------

 ###2.0  function verify_code($leng = 6)
 //-----------------------------------------------------------
 // @version  生成随机验证码
 // @author   $bill$
 // @datatime 2017-11-30 09:38
 // @param  $leng=验证码位数  默认6位
 // @description 生成一个随机的验证码
 // @return  验证码
 //-----------------------------------------------------------

 ###2.1  function get_token()
  //-----------------------------------------------------------
 // @version  生成token
 // @author   $bill$
 // @datatime 2017-11-30 09:40
 // @param  
 // @description 生成一个随机的token
 // @return  token字符串
 //-----------------------------------------------------------

 ###2.2  function check_verification($phone, $code)
  //-----------------------------------------------------------
 // @version  校验短信验证码 并修改验证码使用状态
 // @author   $bill$
 // @datatime 2017-11-30 09:40
 // @param  $phone 手机号 $code 验证码
 // @description 校验该手机号的验证码 并且修改状态
 // @return  校验状态
 //-----------------------------------------------------------

 ###2.3  function state_preg($value, $success, $tips='')
 //-----------------------------------------------------------
 // @version  状态显示图标
 // @author   $bill$
 // @datatime 2017-11-30 10:15
 // @param  $value=信息值 $success=成功时的值 $tips=信息提示
 // @description 检验success值是否与出信息值相等 并返回相应图标
 // @return  图标 
 //-----------------------------------------------------------

 ###2.4 function get_age($idcard)
 //-----------------------------------------------------------
 // @version  根据身份证号码获取年龄
 // @author   $bill$
 // @datatime 2017-11-30 10:20
 // @param  $idcard 身份证号
 // @description 根据用户身份证号获取用户的年龄
 // @return  $age 年龄 
 //-----------------------------------------------------------

 ###2.5 function get_birthday($idcard)
 //-----------------------------------------------------------
 // @version  根据身份证号码获取生日
 // @author   $bill$
 // @datatime 2017-11-30 10:22
 // @param  $idcard 身份证号
 // @description 根据用户身份证号获取用户的生日
 // @return  用户生日
 //-----------------------------------------------------------

 ###2.6 function card_preg($card)
 //-----------------------------------------------------------
 // @version  处理身份证信息
 // @author   $bill$
 // @datatime 2017-11-30 10:25
 // @param  $card 身份证号
 // @description 处理身份证信息 保留首尾字符
 // @return  替换后的身份证信息
 //-----------------------------------------------------------

 ###2.7 function phone_preg($phone)
 //-----------------------------------------------------------
 // @version  处理手机号码 正则替换中间四位
 // @author   $bill$
 // @datatime 2017-11-30 10:34
 // @param  $phone=手机号
 // @description 处理手机号 替换中间部位为****  包括固定电话
 // @return  替换后的手机号
 //-----------------------------------------------------------

 ###2.8  function time_tran($the_time)
 //-----------------------------------------------------------
 // @version  计算时间与现在的时间差
 // @author   $bill$
 // @datatime 2017-11-30 10:37
 // @param  $the_time=传入的时间 datatime格式 
 // @description 计算传入的时间与当前的时间差 返回 秒前 分钟前 小时前 三天以内 实际时间
 // @return  返回时间差
 //-----------------------------------------------------------

 ###2.9 function certification_ID($ID, $name, $member_id)
  //-----------------------------------------------------------
 // @version  实名认证 
 // @author   $bill$
 // @datatime 2017-11-30 10:51
 // @param  $ID=身份证号  $name=名字 $member_id=会员id 
 // @description 阿里云市场实名认证接口
 // @return  返回实名状态
 //-----------------------------------------------------------