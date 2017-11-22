<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
use think\Config;
use app\index\model\SmsLog as SmsLogs;
use app\index\model\Member as Members;

/**
 * 密码加密方法
 * string $str 愿密码
 * string $method 加密方式
 * return str  $result 加密后密码
 **/
function encryption($str, $salt, $method='md5')
{
    return $method($method($str).$salt);
}

/**
 * 状态 code-说明 转换
 * string $code 状态code
 * return str   状态说明
 *
 */
function get_status_text($code,$type='status')
{
    $status = Config::get($type);
    return $status[$code];
}
/**
 * 随机字符串生成
 * return str   随机字符串
 *
 */
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
/**
 * 金额格式化
 * string $num 金额/分
 * return str   格式化字符串
 *
 */
function format_money($num, $bool=true)
{
    if (!is_numeric($num)) {
        return false;
    }

    if ($bool) {
        $str=number_format($num/100, 2);
    } else {
        $str=number_format($num/100, 2) ;
    }
    return $str;
}

/**
 * 金额格式化
 * string $num 金额/分->万元
 * return str   格式化字符串
 *
 */
function format_money_get($num)
{
    if (!is_numeric($num)) {
        return false;
    }
    $str=number_format($num/100/10000, 2) ;
    return $str;
}

//curl post
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
/**
 * XML转数组
 * string $xml XML
 * return array 返回的数组
 *
 */
function xml_to_array($xml)
{
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
}
    /**
     * 计算时间差（天）
     * string $date_one 时间1
     * string $date_two 时间2
     * return str  时间差
     *
     */
function days_between_dates($date_one, $date_two)
{
    $date_one = strtotime($date_one);
    $date_two = strtotime($date_two);
    $days = ceil(abs($date_one - $date_two)/86400);
    return $days;
}
/**
 * 功能：短信发送函数
 * @param string $phone    接收者手机号码
 * @param string $message   短信内容
 * @return boolean
 */
function send_sms($phone, $message)
{
    $user_id        = Config::get('SMS.user_id');
    $user_name    = Config::get('SMS.user_name');
    $pwd                 = Config::get('SMS.password');
    $title            = Config::get('default_title');
    $content            = "【{$title}】$message";
    $url     = 'http://sms1.ronglaids.com/sms.aspx?action=send&userid=' . $user_id . '&account=' . $user_name . '&password=' . $pwd . '&mobile=' . $phone . '&content=' . $content . '&sendTime=&extno=';
    $res     = curl_post($url);
    $result  = xml_to_array($res);
    if ($result['message'] == 'ok') {
        return true;
    } else {
        return false;
    }
}
//生成随机验证码
function verify_code($leng = 6)
{
    return mt_rand(str_repeat(1, $leng), str_repeat(9, $leng));
}
//生成token
function get_token()
{
    return md5(md5(make_rand_code()).time());
}
//校检验证码
function check_verification($phone, $code)
{
    $return = SmsLogs::get([
                'sms_send'=>$phone,
                'sms_log_content'=>$code,
                'sms_log_add_time'=>['gt',date("Y-m-d H:i:s", time()-Config::get('valid_period'))],
                'sms_log_state'        =>1
    ]);
    //验证成功 修改状态
  if (!$return) {
      return false;
  }
    $return->sms_log_state=2;
    $return->save();
    return true;
}
//生成order
function make_order()
{
    $dates=date('y').date('z').str_pad((date('H')*60*60+date('i')*60 +date('s')), 5, 0, STR_PAD_LEFT);
    $next_sec = time() + 1;
    while (time() < $next_sec) {
        $mic_time = microtime(true);
        if (strpos($mic_time, '.') === false) {
            //暂停1毫秒
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
/**
 * 实名认证方法
 */
function certification_ID($ID, $name, $member_id)
{
    $member=Members::get($member_id);
    $total_count = Config::get("certification_count");
    $left_count = ($total_count-$member->member_ID_count);
    if (!preg_match("/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/", $ID)) {
        return ['code'=>349,'data'=>$left_count];
    }
    if ($member->member_ID_state>0) { //已经绑定
        return ['code'=>355,'data'=>$left_count];
    }
    if ($member->member_ID_count > $total_count) { //超次数
        return ['code'=>356,'data'=>$left_count];
    }
    $member->member_ID_count=$member->member_ID_count+1;
    $member->save();
    $host = "http://idcard.market.alicloudapi.com";
    $path = "/lianzhuo/idcard";
    $method = "GET";
    $appcode = "a8a8c4a969804842ae1b7463a9cac6a7";
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


/*
*计算时间与现在的时间 得到秒 时 天
*@param $the_time datetime格式
*@return 几秒前 几分钟前 几小时前 三天前 具体时间
*/
function time_tran($the_time)
{
    //$now_time = date("Y-m-d H:i:s",time()+8*60*60);
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
                    if ($dur < 259200) {//3天内
                            return floor($dur/86400).'天前';
                    } else {
                        return $the_time;
                    }
                }
            }
        }
    }
}

/*
*处理手机号码 正则替换中间四位
*@param $phone手机号
*@return 替换后的手机号
*/
function phone_preg($phone)
{
    $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone); //固定电话
    if ($IsWhat == 1) {
        return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i', '$1****$2', $phone);
    } else {
        return  preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
    }
}

/*
*处理用户姓名 保留首尾字符
*@param $name 用户member_nick
*@return 替换后的姓名
*/
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
 /*
*处理用户身份证信息 保留首尾字符
*@param $name 身份证号
*@return 替换后身份证信息
*/
function card_preg($card)
{
    $strlen     = mb_strlen($card);
    $firstStr   = mb_substr($card, 0, 1);
    $lastStr    = substr($card, -1);
    $return =   $firstStr . str_repeat('*', $strlen - 2).$lastStr;
    return $return;
}
/**
 *  根据身份证号码获取性别
 *  @param string $idcard    身份证号码
 *  @return int $sex 性别 1男 2女 0未知
 */
function get_sex($idcard)
{
    if (empty($idcard)) {
        return null;
    }
    $sexint = (int) substr($idcard, 16, 1);
    return $sexint % 2 === 0 ? '-7' : '7';
}
/**
 *  根据身份证号码获取生日
 *  @param string $idcard    身份证号码
 *  @return $birthday
 */
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
/**
 *  根据身份证号码计算年龄
 *  author:xiaochuan
 *  @param string $idcard    身份证号码
 *  @return int $age
 */
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

/*
*处理状态显示问题
*@param $value 接收的参数
*@param $success 正确的值
*@param $tips 提示信息
*@return 正确或者错误
*/
function state_preg($value, $success, $tips='')
{
    if($value==='' or $success==='')
        return '参数错误';

    $state = $value===$success ? 'check text-success' : 'times text-danger';

    if($tips!='')
        $tips.=$value===$success ? '成功' : '失败';

    echo "<i title='".$tips."' style='font-size:18px;' class='icon icon-".$state."'></i>";
}
