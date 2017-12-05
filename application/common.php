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

 //-----------------------------------------------------------
 // @version  密码加密方式
 // @author   $bill$
 // @datatime 2017-11-30 09:04
 // @param $str=原密码  $salt=密码盐值 $method=加密方式 MD5
 // @description MD5加密明文密码+salt之后的密码返回 非散列加密 默认md5
 // @return 加密后的密码
 //-----------------------------------------------------------
 function encryption($str, $salt, $method='md5')
 {
        return $method($method($str).$salt);
 }

 //-----------------------------------------------------------
 // @version  状态转换 status
 // @author   $bill$
 // @datatime 2017-11-30 09:10
 // @param $code=状态码  $status=配置状态码库
 // @description 返回对应的状态码 (弃用?)
 // @return 状态码
 //-----------------------------------------------------------
 function get_status_text($code,$type='status')
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
      if (!is_numeric($num)) return false;
      if ($bool) $str="<b>￥ ".number_format($num/100, 2)."</b>";
      else $str=number_format($num/100, 2) ;
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
      if (!is_numeric($num)) return false;
      return number_format($num/100/10000, 2) ;
 }

 //-----------------------------------------------------------
 // @version  Curl Post
 // @author   $bill$
 // @datatime 2017-11-30 09:28
 // @param  $url=请求地址 $method='post' 请求方式 $data='' 请求数据
 // @description Curl方式请求url 返回请求的数据
 // @return  $str=格式化后的金额
 //-----------------------------------------------------------
 function curl_post($url, $method = 'post', $data = '')
 {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $temp = curl_exec($ch);
      return $temp;
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
 // @version  短信发送
 // @author   $bill$
 // @datatime 2017-11-30 09:35
 // @param  $phone=接收者手机号  $message 发送内容   
 // @description 给接收着发送短信内容
 // @return  返回短信发送状态
 //-----------------------------------------------------------
 function send_sms($phone, $message)
 {
      $user_id        = Config::get('SMS.user_id');
      $user_name  = Config::get('SMS.user_name');
      $pwd             = Config::get('SMS.password');
      $title              = Config::get('default_title');
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
      if (!$return)  return false;
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
      return getOrderId();
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
      $total_count = Config::get("certification_count");
      $left_count = ($total_count-$member->member_ID_count);
      if (!preg_match("/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/", $ID)) return ['code'=>349,'data'=>$left_count];
      if ($member->member_ID_state>0)  return ['code'=>355,'data'=>$left_count];
      if ($member->member_ID_count > $total_count)  return ['code'=>356,'data'=>$left_count];
      $member->member_ID_count=$member->member_ID_count+1;
      $member->save();
      $host = "http://idcard.market.alicloudapi.com";
      $path = "/lianzhuo/idcard";
      $method = "GET";
      $appcode = "  ";
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
      if (1 == strpos("$".$host, "https://")) 
      {
           curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      }
      $infos=curl_exec($curl);
      curl_close($curl);
      $infos=json_decode($infos, true);
      if (!$infos) return ['code'=>350,'data'=>($left_count-1)];
      if ($infos['resp']['code']==5) return ['code'=>351,'data'=>($left_count-1)];
      if ($infos['resp']['code']==14)  return ['code'=>352,'data'=>($left_count-1)];
      if ($infos['resp']['code']==96)  return ['code'=>353,'data'=>($left_count-1)];
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
      if ($dur < 0) return $the_time;
      else {
           if ($dur < 60)  return $dur.'秒前';
           else {
                if ($dur < 3600) return floor($dur/60).'分钟前';
                else {
                     if ($dur < 86400) return floor($dur/3600).'小时前';
                     else {
                         if ($dur < 259200) return floor($dur/86400).'天前';
                         else return $the_time;
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
      if ($IsWhat == 1) return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i', '$1****$2', $phone);
      else return  preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
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
      if ($last)  $return = $firstStr.$last."~";
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
      if (empty($idcard)) return null;
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
      if (empty($idcard)) return null;
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
      if (empty($idcard))  return null;
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
      if($value==='' or $success==='') return '参数错误';
      $state = $value===$success ? 'check text-success' : 'times text-danger';
      if($tips!='') $tips.=$value===$success ? '成功' : '失败';
      echo "<i title='".$tips."' class='icon icon-".$state."'></i>";
 }
