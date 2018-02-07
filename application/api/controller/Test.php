<?php
/**
* 
*/
namespace app\api\controller;
use think\Db;
use app\index\model\System;
use think\Request;
use think\Config;

class Test 
{
		//认证测试
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
		public function jpush_test($uid=42,$title='极光推送测试',$content="测试cehsi",$item='',$type=2){
			jpush($uid, $title=$item, $content=$content, $item=$item, $type=$type);
		}
		public function message_text(){
			 	$sms=new \app\index\controller\sms();
            	$a=$sms->check('17569615504','7041');
				print_r($a);
		}
		//curl请求
		public function curlPost($url, $method = 'post', $data = ''){
	        $ch = curl_init();
	        // curl_setopt($ch, CURLOPT_HTTPHEADER, 0);
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
		//发送请求
		public function send_request($action='',$method=''){
			$params=Request::instance()->param();
			if(!$action){
				$action=$params['action'];
			}
			if(!$method){
				$method=$params['method'];
			}
			// unset($params['action']);
			// unset($params['method']);
			$index=new Index;
			$data=array(
				'action'=>$action,
				'method'=>$method,
				'param'=>$params,
			);
			$data=$index->encryption_data(json_encode($data));
			$request['data']=$data;
			$host=System::getName('system_url');
			$host='wallet.dev.com/index.php';
			$data = $this->curlPost($host.'/api', 'post',$request);
			$res=json_decode($data,true);
			if(is_array($res) && $res['code']==200){
				
				$data=$index->decryption_data($res['data']); 
				
			}else{
				$data=$res;
			}
			print_r($data);die;
		}

}