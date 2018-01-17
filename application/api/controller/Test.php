<?php
/**
* 
*/
namespace app\api\controller;
use think\Db;
class Test 
{
		public function renzheng()
		{
			    $name=urlencode('高志勇');
			    $host = "http://aliyuncardby4element.haoservice.com";
			    $path = "/creditop/BankCardQuery/QryBankCardBy4Element";
			    $method = "GET";
			    $appcode = "d04d00f17ddd430abc630269b4c30324";
			    $headers = array();
			    array_push($headers, "Authorization:APPCODE " . $appcode);
			    $querys = "accountNo=6217002340008894232&bankPreMobile=15098725525&idCardCode=370125199501237039&name=".$name;
			    $bodys = "";
			    $url = $host . $path . "?" . $querys;

			    $curl = curl_init();
			    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			    curl_setopt($curl, CURLOPT_URL, $url);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			    curl_setopt($curl, CURLOPT_FAILONERROR, false);
			    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($curl, CURLOPT_HEADER, 0);
			    if (1 == strpos("$".$host, "https://"))
			    {
			        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			    }
			dump(json_decode(curl_exec($curl)));    
		}
		public function test(){
			return 1;
		}
		public function message_text(){
			$a=send_sms('17560044406');
			print_r($a);
		}
}