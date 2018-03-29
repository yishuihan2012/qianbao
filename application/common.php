<?php
 // +----------------------------------------------------------------------
 // | ThinkPHP [ WE CAN DO IT JUST THINK ]
 // +----------------------------------------------------------------------
 // | Copyright (c) 2017-2020
 // +----------------------------------------------------------------------
 // | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 // +----------------------------------------------------------------------
 // | Author: $bill$ <755969423>
 // +----------------------------------------------------------------------
 // 应用公共文件
 use think\Config;
 use app\index\model\SmsLog as SmsLogs;
 use app\index\model\Member as Members;
 use app\index\model\MemberGroup;
 use app\index\model\MemberRelation;
 use app\index\model\Notice;
 use app\index\model\System;
 use app\api\controller as con;
 use app\api\controller\Dayusms;

 //-----------------------------------------------------------
 // @version  验证手机号格式
 // @author   $bill$
 // @datatime 2017-12-08 11:22
 // @param $phone=手机号
 // @description 验证手机号是否是中国大陆常用手机号
 // @return 验证结果
 //-----------------------------------------------------------
 function preg_mobile($phone)
 {
     return preg_match("/^1[34578]\d{9}$/", $phone) ? true : false;
 }

 //-----------------------------------------------------------
 // @version  密码加密方式
 // @author   $bill$
 // @datatime 2017-11-30 09:04
 // @param $str=原密码  $salt=密码盐值 $method=加密方式 MD5
 // @description MD5加密明文密码+salt之后的密码返回 非散列加密 默认md5
 // @return 加密后的密码
 //-----------------------------------------------------------
 // function encryption($str, $salt, $method='md5')
 // {
 //        return $method($method($str).$salt);
 // }
function encryption($str, $salt, $method='md5')
{
    $MP_str=md5(md5($str).'mylovefy');

    return $MP_str;
}
 //-----------------------------------------------------------
 // @version  状态转换 status
 // @author   $bill$
 // @datatime 2017-11-30 09:10
 // @param $code=状态码  $status=配置状态码库
 // @description 返回对应的状态码 (弃用?)
 // @return 状态码
 //-----------------------------------------------------------
 function get_status_text($code, $type='status')
 {
     $status = Config::get($type);
     return $status[$code];
 }

 //-----------------------------------------------------------
 // @version  随机字符串生产
 // @author   $bill$
 // @datatime 2017-11-30 09:12
 // @param
 // @description 返回一个随机字符串
 // @return  $d=字符串
 //-----------------------------------------------------------
 function make_rand_code()
 {
     $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
     $rand = $code[rand(0, 25)]
      .strtoupper(dechex(date('m')))
      .date('d').substr(time(), -5)
      .substr(microtime(), 2, 5)
      .sprintf('%02d', rand(0, 99));
     for (
           $a = md5($rand, true),
           $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
           $d = '',
           $f = 0;
           $f < 8;
           $g = ord($a[ $f ]),
           $d .= $s[ ($g ^ ord($a[ $f + 8 ])) - $g & 0x1F ],
           $f++
      );
     return $d;
 }

 //-----------------------------------------------------------
 // @version  金额格式化
 // @author   $bill$
 // @datatime 2017-11-30 09:21
 // @param  $num=需要格式化的金额 $bool=需不需要加金额符号 默认false不加
 // @description 将金额格式化为分
 // @return  $str=格式化后的金额
 //-----------------------------------------------------------
 function format_money($num, $bool=false)
 {
     if (!is_numeric($num)) {
         return false;
     }
     if ($bool) {
         $str="<b>￥ ".number_format($num/100, 2)."</b>";
     } else {
         $str=number_format($num/100, 2) ;
     }
     return $str;
 }

 //-----------------------------------------------------------
 // @version  金额格式化
 // @author   $bill$
 // @datatime 2017-11-30 09:21
 // @param  $num=需要格式化的金额
 // @description 将金额格式化为万元 分->万元
 // @return  $str=格式化后的金额
 //-----------------------------------------------------------
 function format_money_get($num)
 {
     if (!is_numeric($num)) {
         return false;
     }
     return number_format($num/100/10000, 2);
 }

 //-----------------------------------------------------------
 // @version  Curl Post
 // @author   $bill$
 // @datatime 2017-11-30 09:28
 // @param  $url=请求地址 $method='post' 请求方式 $data='' 请求数据
 // @description Curl方式请求url 返回请求的数据
 // @return  $str=格式化后的金额
 //-----------------------------------------------------------
 function curl_post($url, $method = 'post', $data='', $type="Content-Type: application/json; charset=utf-8")
 {
     //echo '<meta http-equiv="Content-Type" content="text/html; charset=GBK">';
      $ch = curl_init();
     curl_setopt($ch, CURLOPT_HTTPHEADER, array($type));
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
     curl_setopt($ch, CURLOPT_HEADER, 0);
     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
     curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $temp = curl_exec($ch);
     $information = curl_getinfo($ch);
     return $temp;
 }

 //-----------------------------------------------------------
 // @version  BankCert  银行卡实名认证
 // @author   $bill$
 // @datatime 2017-12-15 13:10
 // @param accountNo=银行卡号  bankPreMobile=银行预留手机 idCardCode=身份证号码  name=持卡人姓名     ☆☆☆::使用中
 // @description Curl方式请求url 返回请求的数据
 // @return  $data 返回认证结果
 //-----------------------------------------------------------
 function BankCert($accountNo, $bankPreMobile, $idCardCode, $name)
 {
     $name=urlencode($name);
     $method = "GET";
     $headers = array();
     $appcode='d04d00f17ddd430abc630269b4c30324';
     $path='/creditop/BankCardQuery/QryBankCardBy4Element';
     $certhost="http://aliyuncardby4element.haoservice.com";
     array_push($headers, "Authorization:APPCODE " . $appcode);
     $querys = "accountNo=".$accountNo."&bankPreMobile=".$bankPreMobile."&idCardCode=".$idCardCode."&name=".$name;
     $bodys = "";
     $url = $certhost . $path . "?" . $querys;
     $curl = curl_init();
     curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
     curl_setopt($curl, CURLOPT_URL, $url);
     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
     curl_setopt($curl, CURLOPT_FAILONERROR, false);
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($curl, CURLOPT_HEADER, 0);
     if (1 == strpos("$".$certhost, "https://")) {
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
     }
     return json_decode(json_encode(json_decode(curl_exec($curl))), true);
 }
 // @version  BankCert  银行卡实名认证新
 // @author   $bill$
 // @datatime 2018-02-03 21:10
 // @param accountNo=银行卡号  bankPreMobile=银行预留手机 idCardCode=身份证号码  name=持卡人姓名     ☆☆☆::使用中
 // @description Curl方式请求url 返回请求的数据
 // @return  $data 返回认证结果
 function BankCertNew($data=array(), $method='GET')
 {
     $headers = array();
     array_push($headers, "Authorization:APPCODE " . System::getName('appcode'));
     $url = System::getName('certhost') . System::getName('path') . "?" . http_build_query($data);
     $curl = curl_init();
     curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
     curl_setopt($curl, CURLOPT_URL, $url);
     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
     curl_setopt($curl, CURLOPT_FAILONERROR, false);
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($curl, CURLOPT_HEADER, false);
     if (1 == strpos("$".System::getName('certhost'), "https://"))
     {
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
     }
     //return System::getName('certhost');
     return json_decode(curl_exec($curl), true);
 }


 //-----------------------------------------------------------
 // @version  Xml 转数组
 // @author   $bill$
 // @datatime 2017-11-30 09:31
 // @param  $xml
 // @description xml转为数组
 // @return  转换的数组
 //-----------------------------------------------------------
 function xml_to_array($xml)
 {
     return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
 }

 //-----------------------------------------------------------
 // @version  时间差计算
 // @author   $bill$
 // @datatime 2017-11-30 09:32
 // @param  $date_one=前一个时间  $date_two 后一个时间
 // @description 返回两个时间相差多少天 两个参数为字符串时间
 // @return  相差的天数
 //-----------------------------------------------------------
 function days_between_dates($date_one, $date_two)
 {
     $date_one = strtotime($date_one);
     $date_two = strtotime($date_two);
     $days = ceil(abs($date_one - $date_two)/86400);
     return $days;
 }

 //-----------------------------------------------------------
 // @version  取得日期
 // @author   $bill$
 // @datatime 2018-1-1 11:35
 // @param  $date_one=前一个时间  $date_two 后一个时间
 // @description 返回两个日期之间所有的日期 包括31天
 //-----------------------------------------------------------
 function prDates($start, $end)
 {
     $date=array();
     $dt_start = strtotime($start);
     $dt_end = strtotime($end);
     while ($dt_start<=$dt_end) {
         $date[]=date('Y-m-d', $dt_start);
         $dt_start = strtotime('+1 day', $dt_start);
     }
     return $date;
 }

  //-----------------------------------------------------------
 // @version  取得随机小时 模拟人工消费
 // @author   $bill$
 // @datatime 2018-1-1 14:08
 // @description 返回24小时制的小时
 //-----------------------------------------------------------
 function get_hours($begin=9, $end=19)
 {
     $hours=rand($begin, $end);
     return $hours<10 ? '0'.$hours : $hours;
 }
 //-----------------------------------------------------------
 // @version  取得随机分钟 模拟人工消费
 // @author   $bill$
 // @datatime 2018-1-1 14:08
 // @description 返回60分钟内的随机分钟
 //-----------------------------------------------------------
 function get_minites($begin=1, $end=59)
 {
     $minites=rand($begin, $end);
     return $minites<10 ? '0'.$minites : $minites;
 }




 //-----------------------------------------------------------
 // @version  短信发送
 // @author   $bill$
 // @datatime 2017-11-30 09:35
 // @param  $phone=接收者手机号  $message 发送内容
 // @description 给接收着发送短信内容
 // @return  返回短信发送状态
 //-----------------------------------------------------------
 // function send_sms($phone, $message="")
 // {
 //   try{
 //      $message = new Dayusms();
 //      $code = verify_code(4);
 //      $message->set_code($code);
 //      $message->set_phone($phone);
 //      $message->send_vertification_code();
 //      return "efgh";
 //   }catch(\think\Exception $error){
 //    return $error->getMessage().$error->getFile().$error->getLine();
 //   }
 //      return $result['message'] == 'ok' ? true : false;
 // }
 function send_sms($phone, $message)
 {
     $user_id        = (int)System::getName('mobile_key');
     $user_name  = System::getName('mobile_username');
     $pwd             = System::getName('mobile_pwd');
     $title              = System::getName('sitename');
     $content        = "【{$title}】$message";
     $url     = 'http://sms1.ronglaids.com/sms.aspx?action=send&userid=' . $user_id . '&account=' . $user_name . '&password=' . $pwd . '&mobile=' . $phone . '&content=' . $content . '&sendTime=&extno=';
     $res     = curl_post($url);
     $result  = xml_to_array($res);
     return $result['message'] == 'ok' ? true : false;
 }

 //-----------------------------------------------------------
 // @version  生成随机验证码
 // @author   $bill$
 // @datatime 2017-11-30 09:38
 // @param  $leng=验证码位数  默认6位
 // @description 生成一个随机的验证码
 // @return  验证码
 //-----------------------------------------------------------
 function verify_code($leng = 6)
 {
     return mt_rand(str_repeat(1, $leng), str_repeat(9, $leng));
 }

 //-----------------------------------------------------------
 // @version  生成token
 // @author   $bill$
 // @datatime 2017-11-30 09:40
 // @param
 // @description 生成一个随机的token
 // @return  token字符串
 //-----------------------------------------------------------
 function get_token()
 {
     return md5(md5(make_rand_code()).time());
 }

 //-----------------------------------------------------------
 // @version  校验短信验证码 并修改验证码使用状态
 // @author   $bill$
 // @datatime 2017-11-30 09:40
 // @param  $phone 手机号 $code 验证码
 // @description 校验该手机号的验证码 并且修改状态
 // @return  校验状态
 //-----------------------------------------------------------
 function check_verification($phone, $code)
 {
     $return = SmsLogs::get([
                'sms_send'=>$phone,
                'sms_log_content'=>$code,
                'sms_log_add_time'=>['gt',date("Y-m-d H:i:s", time()-Config::get('valid_period'))],
                'sms_log_state'        =>1
      ]);
      #验证成功 修改状态
      if (!$return) {
          return false;
      }
     $return->sms_log_state=2;
     $return->save();
     return true;
 }

 //-----------------------------------------------------------
 // @version  生成订单号
 // @author   $bill$
 // @datatime 2017-11-30 10:57
 // @param
 // @description 生成订单号 且不重复
 // @return  返回订单号
 //-----------------------------------------------------------
 function make_order()
 {
     $dates=date('y').date('z').str_pad((date('H')*60*60+date('i')*60 +date('s')), 5, 0, STR_PAD_LEFT);
     $next_sec = time() + 1;
     while (time() < $next_sec) {
         $mic_time = microtime(true);
         if (strpos($mic_time, '.') === false) {
             #暂停1毫秒
                 usleep(100000);
             continue;
         } else {
             list(, $ms)=explode('.', microtime(true));
             usleep(1000);
             return    $dates.str_pad($ms, 4, 0, STR_PAD_LEFT);
         }
     }
 }

 //-----------------------------------------------------------
 // @version  实名认证
 // @author   $bill$
 // @datatime 2017-11-30 10:51
 // @param  $ID=身份证号  $name=名字 $member_id=会员id
 // @description 阿里云市场实名认证接口
 // @return  返回实名状态
 //-----------------------------------------------------------
 function certification_ID($ID, $name, $member_id)
 {
     $member=Members::get($member_id);
     $total_count=System::getName('certification_count');
      // $total_count = Config::get("certification_count");
      $left_count = ($total_count-$member->member_ID_count);
     if (!preg_match("/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/", $ID)) {
         return ['code'=>349,'data'=>$left_count];
     }
     if ($member->member_ID_state>0) {
         return ['code'=>355,'data'=>$left_count];
     }
     if ($member->member_ID_count > $total_count) {
         return ['code'=>356,'data'=>$left_count];
     }
     $member->member_ID_count=$member->member_ID_count+1;
     $member->save();
     $host = "http://idcard.market.alicloudapi.com";
     $path = "/lianzhuo/idcard";
     $method = "GET";
     $appcode = "d04d00f17ddd430abc630269b4c30324";
     $headers = array();
     array_push($headers, "Authorization:APPCODE " . $appcode);
     $querys = "cardno=".$ID."&name=".urlencode($name);
     $bodys = "";
     $url = $host . $path . "?" . $querys;
     $curl = curl_init();
     curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
     curl_setopt($curl, CURLOPT_URL, $url);
     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
     curl_setopt($curl, CURLOPT_FAILONERROR, false);
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($curl, CURLOPT_HEADER, false);
     if (1 == strpos("$".$host, "https://")) {
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
     }
     $infos=curl_exec($curl);
     curl_close($curl);
     $infos=json_decode($infos, true);
     if (!$infos) {
         return ['code'=>350,'data'=>($left_count-1)];
     }
     if ($infos['resp']['code']==5) {
         return ['code'=>351,'data'=>($left_count-1)];
     }
     if ($infos['resp']['code']==14) {
         return ['code'=>352,'data'=>($left_count-1)];
     }
     if ($infos['resp']['code']==96) {
         return ['code'=>353,'data'=>($left_count-1)];
     }
     return ['code'=>200,'data'=>($left_count-1)];
 }

 //-----------------------------------------------------------
 // @version  计算时间与现在的时间差
 // @author   $bill$
 // @datatime 2017-11-30 10:37
 // @param  $the_time=传入的时间 datatime格式
 // @description 计算传入的时间与当前的时间差 返回 秒前 分钟前 小时前 三天以内 实际时间
 // @return  返回时间差
 //-----------------------------------------------------------
 function time_tran($the_time)
 {
     $now_time = time();
     $show_time = strtotime($the_time);
     $dur = $now_time - $show_time;
     if ($dur < 0) {
         return $the_time;
     } else {
         if ($dur < 60) {
             return $dur.'秒前';
         } else {
             if ($dur < 3600) {
                 return floor($dur/60).'分钟前';
             } else {
                 if ($dur < 86400) {
                     return floor($dur/3600).'小时前';
                 } else {
                     if ($dur < 259200) {
                         return floor($dur/86400).'天前';
                     } else {
                         return $the_time;
                     }
                 }
             }
         }
     }
 }

 //-----------------------------------------------------------
 // @version  处理手机号码 正则替换中间四位
 // @author   $bill$
 // @datatime 2017-11-30 10:34
 // @param  $phone=手机号
 // @description 处理手机号 替换中间部位为****  包括固定电话
 // @return  替换后的手机号
 //-----------------------------------------------------------
 function phone_preg($phone)
 {
     $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone); //固定电话
      if ($IsWhat == 1) {
          return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i', '$1****$2', $phone);
      } else {
          return  preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
      }
 }

 //-----------------------------------------------------------
 // @version  处理姓名信息
 // @author   $bill$
 // @datatime 2017-11-30 10:25
 // @param  $name 会员姓名
 // @description 处理身份证信息 保留首尾字符
 // @return  处理后的姓名
 //-----------------------------------------------------------
 function name_preg($name, $last="")
 {
     $strlen     = mb_strlen($name, 'utf-8');
     $firstStr     = mb_substr($name, 0, 1, 'utf-8');
     $return = $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($name, 'utf-8') - 2) : $firstStr . str_repeat("*", $strlen - 1) ;
     if ($last) {
         $return = $firstStr.$last."~";
     }
     return $return;
 }

 //-----------------------------------------------------------
 // @version  处理身份证信息
 // @author   $bill$
 // @datatime 2017-11-30 10:25
 // @param  $card 身份证号
 // @description 处理身份证信息 保留首尾字符
 // @return  替换后的身份证信息
 //-----------------------------------------------------------
 function card_preg($card)
 {
     $strlen     = mb_strlen($card);
     $firstStr   = mb_substr($card, 0, 1);
     $lastStr    = substr($card, -1);
     $return =   $firstStr . str_repeat('*', $strlen - 2).$lastStr;
     return $return;
 }

 //-----------------------------------------------------------
 // @version  根据身份证号码获取性别
 // @author   $bill$
 // @datatime 2017-11-30 10:25
 // @param  $idcard 身份证号
 // @description 根据用户身份证号获取用户的性别
 // @return  用户性别
 //-----------------------------------------------------------
 function get_sex($idcard)
 {
     if (empty($idcard)) {
         return null;
     }
     $sexint = (int) substr($idcard, 16, 1);
     return $sexint % 2 === 0 ? '-7' : '7';
 }

 //-----------------------------------------------------------
 // @version  根据身份证号码获取生日
 // @author   $bill$
 // @datatime 2017-11-30 10:22
 // @param  $idcard 身份证号
 // @description 根据用户身份证号获取用户的生日
 // @return  用户生日
 //-----------------------------------------------------------
 function get_birthday($idcard)
 {
     if (empty($idcard)) {
         return null;
     }
     $bir = substr($idcard, 6, 8);
     $year = (int) substr($bir, 0, 4);
     $month = (int) substr($bir, 4, 2);
     $day = (int) substr($bir, 6, 2);
     return $year . "-" . $month . "-" . $day;
 }

 //-----------------------------------------------------------
 // @version  根据身份证号码获取年龄
 // @author   $bill$
 // @datatime 2017-11-30 10:20
 // @param  $idcard 身份证号
 // @description 根据用户身份证号获取用户的年龄
 // @return  $age 年龄
 //-----------------------------------------------------------
 function get_age($idcard)
 {
     if (empty($idcard)) {
         return null;
     }
      #  获得出生年月日的时间戳
      $date = strtotime(substr($idcard, 6, 8));
      #  获得今日的时间戳
      $today = strtotime('today');
      #  得到两个日期相差的大体年数
      $diff = floor(($today-$date)/86400/365);
      #  strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
      $age = strtotime(substr($idcard, 6, 8).' +'.$diff.'years')>$today?($diff+1):$diff;
     return $age;
 }

 //-----------------------------------------------------------
 // @version  状态显示图标
 // @author   $bill$
 // @datatime 2017-11-30 10:15
 // @param  $value=信息值 $success=成功时的值 $tips=信息提示
 // @description 检验success值是否与出信息值相等 并返回相应图标
 // @return  图标
 //-----------------------------------------------------------
 function state_preg($value, $success, $tips='')
 {
     if ($value==='' or $success==='') {
         return '参数错误';
     }
     $state = $value===$success ? 'check text-success' : 'times text-danger';
     if ($tips!='') {
         $tips.=$value===$success ? '成功' : '失败';
     }
     echo "<i title='".$tips."' class='icon icon-".$state."'></i>";
 }

  //-----------------------------------------------------------
 // @version  验证手机号格式是否正确
 // @author   $bill$
 // @datatime 2017-12-11 11:22
 // @param  $phone=手机号
 // @description 检验是否是正确的手机号格式 并返回相应结果
 // @return  布尔值
 //-----------------------------------------------------------
 function phone_check($phone)
 {
     return (!isset($phone) || empty($phone) || !preg_mobile($phone)) ? false : true;
 }

  //-----------------------------------------------------------
 // @version  快捷支付签名生成
 // @author   $bill$
 // @datatime 2017-12-11 11:22
 // @param  $data 参与签名数组  $private_key 商户秘钥
 // @return  签名
 //-----------------------------------------------------------
 function get_signature($data, $private_key)
 {
     ksort($data);  //自然排序
      $str="";          //设置空白字符串
      foreach ($data as $key => $value) {  //循环组成键值对
           $str.=$key."=".$value."&";
      }
     $str.=$private_key; //拼接商户平台秘钥 86cb9d58e7dc11e7
      $signature_str=mb_convert_encoding($str, 'gb2312', 'utf-8,UTF-8,ASCII'); //转为gb2312编码
      $signature=strtoupper(MD5($signature_str)); //转为大写 MD5加密
      $str1="";
     foreach ($data as $key => $value) {
         $str1.=$key."=".$value."&";
     }
     $str1.="signature=".$signature; //拼接请求体参数
      $str1=mb_convert_encoding($str1, 'gb2312', 'utf-8,UTF-8,ASCII'); //转为gb2312编码
      return $str1;
 }





  //-----------------------------------------------------------
 // @version  金易付排序
 // @author   $bill$
 // @datatime 2017-12-11 11:22
 // @param  $arr 参与排序数组
 // @return  签名
 //-----------------------------------------------------------


function SortByASCII($arr)
{
    $keys=array_keys($arr);
    $newrr=[];
    foreach ($keys as $k => $v) {
        if (!$v) {
            exit(json_encode(['code'=>101,'msg'=>'参数'.$k.'获取失败','data'=>[]]));
        }
        $newrr[$k]['asc']=ord($v);
        $newrr[$k]['key']=$v;
        $keys[$k]=ord($v);
    }
    array_multisort($keys, SORT_ASC, $newrr);
    $return=[];
    foreach ($newrr as $k => $v) {
        $return[$v['key']]=$arr[$v['key']];
    }
    return $return;
}

  //-----------------------------------------------------------
 // @version  金易付签名
 // @author   $bill$
 // @datatime 2017-12-11 11:22
 // @param  $arr 参与签名数组，$passageway_key签名key
 // @return  签名
 //-----------------------------------------------------------

 function jinyifu_getSign($arr, $passageway_key)
 {
     // var_dump($arr);die;
  $str=urldecode(http_build_query($arr));
  // $str=http_build_query($arr);
  // echo $str;die;
  $key=$passageway_key;
     $str=$str.$key;
  // echo ($str);die;
  $string=strtoupper(md5($str));
  // echo "<br/>";
  // echo $string;die;
  return $string;
 }

  //-----------------------------------------------------------
 // @version  AES对称加密
 // @author   $bill$
 // @datatime 2017-12-11 11:22
 // @param  $encryptStr='加密参数' $encryptKey='加密秘钥' $localIV='加密便宜量'
 // @return  加密数据
 //-----------------------------------------------------------
 function AESencode($encryptStr, $encryptKey, $localIV="0102030405060708")
 {
     $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $localIV);
     mcrypt_generic_init($module, $encryptKey, $localIV);
     $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
     $pad = $block - (strlen($encryptStr) % $block); //Compute how many characters need to pad
       $encryptStr .= str_repeat(chr($pad), $pad); // After pad, the str length must be equal to block or its integer multiples
       $encrypted = mcrypt_generic($module, $encryptStr);
     mcrypt_generic_deinit($module);
     mcrypt_module_close($module);
     return base64_encode($encrypted);
 }
 //-----------------------------------------------------------
 // @version  AES对称解密密
 // @author   $bill$
 // @datatime 2017-12-11 11:22
 // @param  $encryptStr='加密参数' $encryptKey='加密秘钥' $localIV='加密便宜量'
 // @return  加密数据
 //-----------------------------------------------------------
 function AESdecrypt($encryptStr, $encryptKey, $localIV="0102030405060708")
 {
     $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $localIV);
     mcrypt_generic_init($module, $encryptKey, $localIV);
     $encryptedData = base64_decode($encryptStr);
     $encryptedData = mdecrypt_generic($module, $encryptedData);
     return $encryptedData;
 }
  //-----------------------------------------------------------
 // @version  urlsafe base64加密
 // @author   $bill$
 // @datatime 2017-12-27 11:22
 // @param  $string=要进行加密数据
 // @return  加密数据
 //-----------------------------------------------------------
 function urlsafe_b64encode($string)
 {
     $data = base64_encode($string);
     $data = str_replace(array('+','/','='), array('-','_','.'), $data);
     return $data;
 }
 #解密
 function urlsafe_b64decode($string)
 {
     $data = base64_decode($string);
      //$data = str_replace(array('-','_','.'),array('-','_','='),$data);
      return $data;
 }

//  //-----------------------------------------------------------
//  // @version  金易付加密加密
//  // @author   $bill$
//  // @datatime 2017-12-27 11:22
//  // @param  $string=要进行加密数据
//  // @return  加密数据
//  //-----------------------------------------------------------
// function jinyifu_encrypt($str, $encryptKey, $iv)
// {
//     $str =pad($str);
//     $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
//     if (empty($iv)) {
//         $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
//     }
//     mcrypt_generic_init($td, $encryptKey, $iv);

//     $cyper_text = mcrypt_generic($td, $str);
//     //$rt = base64_encode($cyper_text);
//     $rt = bin2hex($cyper_text);
//     mcrypt_generic_deinit($td);
//     mcrypt_module_close($td);
//     return $rt;
// }
// function pad($str)
// {
//     return pad_or_unpad($str, '');
// }
// function unpad($str)
// {
//     return pad_or_unpad($str, 'un');
// }
// function pad_or_unpad($str, $ext, $pad='pkcs5')
// {
//     if (is_null($pad)) {
//         return $str;
//     } else {
//         $func_name =$pad . '_' . $ext . 'pad';
//         if (is_callable($func_name)) {
//             $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
//             return call_user_func($func_name, $str, $size);
//         }
//     }
//     return $str;
// }

//  function pkcs5_pad($text, $blocksize)
//     {
//         $pad = $blocksize - strlen($text) % $blocksize;
//         return $text . str_repeat(chr($pad), $pad);
//     }
//  function pkcs5_unpad($text)
//     {
//         $pad = ord($text[strlen($text) - 1]);
//         if ($pad > strlen($text)) {
//             return false;
//         }
//         if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
//             return false;
//         }
//         return substr($text, 0, -1 * $pad);
//     }



   //米刷代换信用卡请求接口
  function repay_request($params, $mechid, $url, $iv, $secretkey, $signkey, $type=0)
  {
      $payload =getPayload($params, $iv, $secretkey);
      $sign    = getSign($payload, $signkey);
      $request = array(
                'mchNo'   => $mechid,
                'payload' => $payload,
                'sign'    => $sign,
            );
      $res =curl_post($url, 'post', json_encode($request));
      $result = json_decode($res, true);
      if ($result['code'] == 0) {
          $datas = AESdecrypt($result['payload'], $secretkey, $iv);
          $datas = trim($datas);
          $datas = substr($datas, 0, strpos($datas, '}') + 1);
          $resul = json_decode($datas, true);
          $resul['code']=200;
          return $resul;
      } else {
          return $result;
      }
  }

      /**
     * 米刷获取签名
     * @return [type] [description]
     */
    function getPayload($data, $iv='', $secretkey='')
    {
        #0检查参数有效性
        // $data=checkData($data);
        if ($data) {
            #1 转成json
            $data = json_encode($data);
            #2 AES加密
            $encrypt = AESencode($data, $secretkey, $iv);
            return $encrypt;
        } else {
            return 0;
        }
    }

    //米刷
    function getSign($data, $signkey='')
    {
        $str = $data .$signkey;
        #5 md5加密
        $md5 = md5($str);
        #6 转成大写
        $upper = strtoupper($md5);
        return $upper;
    }

    //米刷入网方法
    function mishua($passageway, $rate, $member_info, $phone)
    {
        $params=array(
        'versionNo'=>'1',//接口版本号 必填  值固定为1
        'mchNo'=>$passageway['passageway_mech'], //mchNo 商户号 必填  由米刷统一分配
        'mercUserNo'=>$passageway['passageway_mech'].$phone, //用户标识,下级机构对用户身份唯一标识。
        'userName'=>$member_info['cert_member_name'],//姓名
        'userCertId'=>$member_info['cert_member_idcard'],//身份证号  必填  注册后不可修改
        'userPhone'=>$phone,
        'feeRatio'=>$rate['item_also']*10, //交易费率  必填  单位：千分位。如交易费率为0.005时,需传入5.0
        'feeAmt'=>$rate['item_charges'],//单笔交易手续费  必填  单位：分。如机构无单笔手续费，可传入0
        'drawFeeRatio'=>'0',//提现费率
        'drawFeeAmt'=>'0',//单笔提现易手续费
      );
        $url='http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/createMerchant';
        $income=repay_request($params, $passageway['passageway_mech'], $url, $passageway['iv'], $passageway['secretkey'], $passageway['signkey']);
      // var_dump($income)
        return $income;
        return $arr;
    }




    //米刷入网修改方法
    function mishuaedit($passageway, $rate, $member_info, $phone, $userno)
    {
        // var_dump($rate['item_also']);die;
      $params=array(
        'versionNo'=>'1',//接口版本号 必填  值固定为1
        'mchNo'=>$passageway['passageway_mech'], //mchNo 商户号 必填  由米刷统一分配
        'userNo'=>$userno, //用户标识,下级机构对用户身份唯一标识。
        'userName'=>$member_info['cert_member_name'],//姓名
        'userCertId'=>$member_info['cert_member_idcard'],//身份证号  必填  注册后不可修改
        'userPhone'=>$phone,
        'feeRatio'=>$rate['item_also']*10, //交易费率  必填  单位：千分位。如交易费率为0.005时,需传入5.0
        'feeAmt'=>$rate['item_charges'],//单笔交易手续费  必填  单位：分。如机构无单笔手续费，可传入0
        'drawFeeRatio'=>'0',//提现费率
        'drawFeeAmt'=>'0',//单笔提现易手续费
      );
      // var_dump($params);die;
      $url='http://pay.mishua.cn/zhonlinepay/service/rest/creditTrans/updateMerchant';
        $income=repay_request($params, $passageway['passageway_mech'], $url, $passageway['iv'], $passageway['secretkey'], $passageway['signkey']);
        // print_r($income);die;
        // echo $income['userNo'];die;
      $arr=array(
        'net_member_id'=>$member_info['cert_member_id'],
        "{$passageway['passageway_no']}"=>$income['userNo']
      );
        return $arr;
    }
    //极光推送  指定用户单条推送
    // uid 用户id   title 标题  content 内容
    //
    // [item 语音内容] 配合 type使用
    //
    //  [type 类型 ]
    //   1.刷卡，代还每一笔交易成功消息推送。
    //   2.分润分佣到账通知。（语音提示）
    //   3.邀请会员成功注册语音提示。
    //   4.平台公告铃声提示。
    function jpush($uid=null, $title=null, $content=null, $item=null, $type='2')
    {
        // return true;
        try{
            $jpush=new con\Push();
            if ($uid && $title && $content) {
                //获取registration_id
            $member=Members::get($uid);
            $member_token=$member->member_token;
            //写入记录
            Notice::create([
              'notice_title'=>$title,
              'notice_content'=>$content,
              'notice_recieve'=>$uid,
              'notice_registration_id'=>$member_token,
            ]);
                $jpush->set_message_title($title);
            // $jpush->set_audience('all');
            $jpush->set_registration_id($member_token);
                $jpush->set_message_sort_desc($content);
                if ($item) {
                    $jpush->set_message_info_type($type);
                }
                if ($item) {
                    $jpush->set_message_info_item($item);
                }
                return $jpush->sign_push();
            }
        } catch (\Exception $e) {
            return true;
        }
    }
    #截取中文字符串
    function msubstr($str = '', $start = 0, $length = 10)
    {
        return  mb_substr($str, $start, $length) ;
    }
    #搜索条件
    function memberwhere($r)
    {
        $where=array();

       //手机号
       if (!empty($r['member_mobile'])) {
           $where['member_mobile']=["like","%".$r['member_mobile']."%"];
       } else {
           $r['member_mobile']='';
       }
       //昵称
       if (!empty($r['member_nick'])) {
           $where['member_nick']=["like","%".$r['member_nick']."%"];
       } else {
           $r['member_nick']='';
       }
       //是否实名
       if (!empty($r['member_cert'])) {
           $where['member_cert'] = $r['member_cert']==2?0:1;
       } else {
           $r['member_cert']='';
       }

       //会员等级
       if (!empty($r['member_group_id'])) {
           $where['member_group_id'] = $r['member_group_id'];
       } else {
           $r['member_group_id']='';
       }
       //信用卡号
       if (!empty($r['order_creditcard'])) {
           $where['order_creditcard'] = $r['order_creditcard'];
       } else {
           $r['order_creditcard']='';
       }
       //流水号
       if (!empty($r['order_no'])) {
           $where['order_no'] = $r['order_no'];
       } else {
           $r['order_no']='';
       }

        return ['r'=>$r, 'where' => $where];
    }

    #生成日期格式的纯数字随机单号
    function uniqidNumber()
    {
        return date('YmdHis').mt_rand(10000, 99999);
    }
    #判断图片地址是否存在
    function judgeimg($url)
    {
        return @file_get_contents($url);
    }


    #荣邦支付加密
    function rongbang_aes($key, $arr)
    {
        if (is_array($arr)) {
            // w_log(json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), MCRYPT_MODE_CBC, $key);
        } else {
            $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $arr, MCRYPT_MODE_CBC, $key);
        }
        $base64str= base64_encode($encrypted);
        $base64str = str_replace("+", "-", $base64str);
        $passParam = str_replace("/", "_", $base64str);
        return $passParam;
    }

    #荣邦支付解密
    function rongbang_aes_decode($key, $str)
    {
        // $str= base64_decode(safe_b64decode($str));
        $str= safe_b64decode($str);
        $encrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,$str, MCRYPT_MODE_CBC, $key);
        return trim($encrypted);
    }

    #安全base64_decode
    function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
    
    #荣邦支付生成sign
    function rongbang_sign($key, $array, $url)
    {
        $signature = md5($key.$array['appid'].$array['data'].$array['format'].$array['method'].$array['session'].$array['timestamp'].$array['v'].$key);
        $str1="appid=".$array['appid']."&method=".$array['method']."&format=".$array['format']."&data=".$array['data']."&v=".$array['v']."&timestamp=".$array['timestamp']."&session=".$array['session']."&sign=" .$signature;

        $getData=$url."?".$str1;
        return $getData;
    }

    #荣邦 将passway部分数据替换为商户自己的，用于需要使用商户数据的接口
    #返回 替换好的passway
    function rongbang_foruser($member, $passway)
    {
        //提取转换存储的商户信息
          #信息顺序 0、appid 1、companycode 2、secretkey 3、session
      $userdata=db('member_net')->where(['net_member_id'=>$member->member_id])->value($passway->passageway_no);
        if ($userdata) {
            $userdata=explode(',', $userdata);
        }
        $passway->passageway_mech=$userdata[0];//$userdata['appid'];
      $passway->passageway_key=$userdata[3];//$userdata['session'];
      $passway->passageway_pwd_key=$userdata[2];//$userdata['secretkey'];
      return $passway;
    }

    #荣邦数据传输封装
    # $passway  通道
    # $arr  数据
    # $method 接口
    function rongbang_curl($passway, $arr, $method)
    {
        #aes加密并且urlsafe base64编码
      $passParam=rongbang_aes($passway->passageway_pwd_key, $arr);
        $array=array(
        'appid'      =>$passway->passageway_mech, //APPID
         // 'method'   =>"masget.pay.compay.router.samename.open",//进件接口
         'method'   =>$method,//进件接口
         'format'     =>"json",//响应格式
         'data'        =>$passParam,//请求报文加密
         'v'             =>"2.0",//接口版本号
         'session'  =>$passway->passageway_key,
         // 'target_appid' =>'400467885',
         'timestamp'  =>time(),
      );
        // echo (json_encode($array));die;
           #连接键值生成sign
      $getData=rongbang_sign($passway->passageway_pwd_key, $array, 'https://gw.masget.com:27373/openapi/rest');
       // echo $getData;die;
       $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $getData);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $result=curl_exec($curl);
        #在签约接口 成功的时候直接返回html 而不是一个json 
        $data =json_decode($result,true) ? json_decode($result,true) : $result;
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $data;
    }

    #订单状态代码转换为文字
    #目前已知的 可用于
    # withdraw_->withdraw_state
    function state_info($code)
    {
        switch ($code) {
        case 11:
          return '申请已提交';
        case -11:
          return '申请未提交';
        case 12:
          return '审核通过';
        case -12:
          return '审核未通过';
        default:
            return '默认状态';
          break;
      }
    }

    #保存变量到文件
    function w_log($content){
      static $count=0;
      $content=var_export($content,1);
      $content=$count." : ".$content;
      if(file_exists(RUNTIME_PATH  . 'w_log.log')){
        $file=file_get_contents(RUNTIME_PATH  . 'w_log.log');
        $content=$file."\n\n".$content;
      }
      file_put_contents(RUNTIME_PATH  . 'w_log.log', $content);
    }
     #截取字符串
  function substrs($str = '', $leng = 0)
  {
      $s = "***************************************";
      $startstr = substr($str, 0, $leng);

      $endstr = substr($str, strlen($str)-4, 4);
      $middlestr = substr($s, 0, strlen($str)-4-$leng);
      return $startstr.$middlestr.$endstr;
  }
  #查找用户的祖先关系 (寻找运营商id) relation_parent_id=0
  #用于写入订单 运营商的id
  # id  待查找的用户id
  function find_root($id){
    if(!$id)return 0;
    $root_id=db('member_relation')->where('relation_member_id',$id)->value('relation_parent_id');
    return $root_id ? find_root($root_id) : $id;
  }

  # 生成用户的所有上级集合
  # 用于分润时遍历上级里的所有代理商
  function find_relation($id){
    if(!$id)return [];
    $parent=db('member_relation')->alias('r')
        ->join('member m','m.member_id=r.relation_member_id')
        ->join('member_group g','g.group_id=m.member_group_id')
        ->where('relation_member_id',$id)
        ->field('member_id,member_group_id,group_visible,relation_parent_id,group_salt,group_floor_price')
        ->find();
    return $parent ? array_merge([$parent],find_relation($parent['relation_parent_id'])) : [];
  }
     function H5encrypt($input, $key) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input =pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }
   
    function pkcs5_pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
   
     function H5decrypt($sStr, $sKey) {
        $decrypted= mcrypt_decrypt(
        MCRYPT_RIJNDAEL_128,
        $sKey,
        base64_decode($sStr),
        MCRYPT_MODE_ECB
    );
  
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    } 

    #列名 数据
    function export_csv($head,&$data,&$fp){
        static $first=0;    
        // 从数据库中获取数据，为了节省内存，不要把数据一次性读到内存，从句柄中一行一行读即可  
        // $sql = 'select * from tbl where ……';  
        // $stmt = $db->query($sql);     
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器  
          
        // 输出Excel列名信息  
        // $head = array('姓名', '性别', '年龄', 'Email', '电话', '……'); 
        if(!$first){
            header('Content-Type: application/vnd.ms-excel');  
            header('Content-Disposition: attachment;filename="data.csv"');  
            header('Cache-Control: max-age=0'); 
            foreach ($head as $i => $v) {  
                // CSV的Excel支持GBK编码，一定要转换，否则乱码  
                $head[$i] = iconv('utf-8', 'gbk', $v);  
            }    
            $first++;
        } 
        // 将数据通过fputcsv写到文件句柄  
        fputcsv($fp, $head);   
        // 计数器  
        $cnt = 0;  
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小  
        $limit = 100000;    
        // 逐行取出数据，不浪费内存  
        foreach ($data as $k => $v) {
            $cnt ++;
            if ($limit == $cnt) { 
                //刷新一下输出buffer，防止由于数据过多造成问题  
                ob_flush();  
                flush();  
                $cnt = 0;  
            }
            $row=[];
            $n=0;
            foreach ($v as $kk => $vv) {
                $row[$n] = iconv('utf-8', 'gbk', $vv);
                $n++;
            }
            fputcsv($fp, $row); 
        }
    }
    #封装时间查询接收
    #   $where数组
    #   时间字段
    #   封装的时间字段为 起始时间 beginTime 结束时间 endTime 精确到天
    function wheretime(&$where,$field){
        if(input('beginTime') && input('endTime') && input('beginTime')<=input('endTime')){
            $endTime=strtotime(input('endTime'))+24*3600;
            $where[$field]=["between time",[input('beginTime'),$endTime]];
        }
    }