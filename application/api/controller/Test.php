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
		public function jpush_test($uid=42,$title='极光推送测试',$content="测试cehsi",$item='test',$type=2){
			// phpinfo();
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
			$host="localhost";
			// $host='wallet.dev.com/index.php';
			$data = $this->curlPost($host.'/api', 'post',$request);
			$res=json_decode($data,true);
			if(is_array($res) && $res['code']==200){
				
				$data=$index->decryption_data($res['data']); 
				
			}else{
				$data=$res;
			}
			print_r($data);die;
		}
		public function yibao(){
		 	 $membernetObject=new \app\api\payment\yibaoPay(12, 99);
		 	 return json_encode($membernetObject->queryFee("10019100228",3));
		 	 // return json_encode($membernetObject->fee("10019100228"));
		}
		#修正平台收益 修正之前未计算固定附加费除100的数据
		public function cashorder(){
			$order=db('cash_order')->where("order_platform > order_charge")->select();
			$passway=db('passageway')->column("*","passageway_id");
			$passwayitem=db('passageway_item')->select();
			$members=db('member')->alias('m')
					->join('member_group g','m.member_group_id=g.group_id')
					->column("*","member_id");
			foreach ($order as $k => $v) {
				if($v['order_platform']>$v['order_charge']){
					$passway_data=$passway[$v['order_passway']];
					$user=$members[$v['order_member']];
					foreach ($passwayitem as $key => $value) {
						if($value['item_passageway']==$v['order_passway'] && $value['item_group']==$user['member_group_id']){
							$passagewayitem_data=$value;
							break;
						}
					}
                    $platform=$v['order_charge']-($v['order_money']*$passway_data['passageway_rate']/100)+$passagewayitem_data['item_charges']/100-$passway_data['passageway_income'];

                    db('cash_order')->where("order_id",$v['order_id'])->update(["order_platform"=>$platform]);
                    echo sprintf("ID%d由%s修正为%s\n",$v['order_id'],$v['order_platform'],$platform);
				}
			}
		}
		#修正分润类型 部分代还的类型在分润表中记录为快捷支付，将此部分找出并修正
		public function commission(){
			$commission=db('commission')->where(['commission_type'=>1,'commission_desc'=>['like','代还%']])->select();
			foreach ($commission as $k => $v) {
				db('commission')->where('commission_id',$v['commission_id'])->update(['commission_type'=>3]);
			}
			echo sprintf("finished,num:%d",count($commission));
		}
		#修正重复分润的历史
		#删除多余分润记录 删除对应的wallet_log记录 对多分配的wallet余额进行减去
		public function wallet(){
			// $cms=db('commission')->group("commission_from,commission_member_id")->having("count(commission_id)>1")->where("commission_member_id","<>",-1)->where("commission_from","not null")->select();
			#预处理commission数据 以订单号为键位
			$cms=db("commission")->where("commission_member_id","<>",-1)->where("commission_from","not null")->select();
			$cms_orderkey=[];
			foreach($cms as $k=>$v){
				if(isset($cms_orderkey[$v['commission_from']])){
					$cms_orderkey[$v['commission_from']][]=$v;
				}else{
					$cms_orderkey[$v['commission_from']]=[$v];
				}
			}
			#代还订单
			$generation_order=db("generation_order")->select();
			try{
	            Db::startTrans();
				foreach ($generation_order as $k => $v) {
					#本次分润
					// $commission=db("commission")->where('commission_from',$v['order_id'])->column("*","commission_id");
					if(!isset($cms_orderkey[$v['order_id']])){
						// echo "no commission".$v['order_id']."</br>";
						continue;
					}
					$commission=$cms_orderkey[$v['order_id']];
					#代还不成功的情况
					if($v['order_status']!=2){
						foreach ($commission as $kk => $vv) {
							$this->wallet_change($vv);
						}
					#代还成功的情况 判断是否重复
					}else{
						#从数组中构建重复数据
						$repeat=[];
						$re=false;
						foreach ($commission as $kk => $vv) {
							if(isset($repeat[$vv['commission_member_id']]))
								$re=true;
							$repeat[$vv['commission_member_id']]=$vv;
						}
						#从数据库构建重复数据
						// $repeat=db("commission")->group("commission_member_id")->having("count(commission_id)>1")->where('commission_from',$v['order_id'])->field('*,count(commission_id) as count')->select();
						if($repeat && $re){
							foreach ($repeat as $kk => $vv) {
								#取出该订单对应每个分润用户的重复数据
								#从数组中取出每个用户的重复数据
								$repeat_data=[];
								foreach ($commission as $kkk => $vvv) {
									if($vv['commission_member_id']==$vvv['commission_member_id']){
										$repeat_data[]=$vvv;
									}
								}
								// $repeat_data=db("commission")->where("commission_member_id",$vv['commission_member_id'])->where('commission_from',$v['order_id'])->select();
								foreach ($repeat_data as $kkk => $vvv) {
									#保留第一条数据
									if($kkk){
										$this->wallet_change($commission[$kkk]);
									}
								}
							}
						}
					}	
				}
				Db::commit();
			}catch (Exception $e) {
	            echo  $e->getMessage.$e->getLine().$e->getFile();
	            Db::rollback();
	        }
			echo "finished";
		}
		#修正重复分润 子函数
		private function wallet_change($c){
			#删除分润记录
			db("commission")->delete($c['commission_id']);
			#删除钱包日志
			$log_id=db("wallet_log")->alias('l')
				->join('wallet w','l.log_wallet_id=w.wallet_id')
				->where(['l.log_relation_id'=>$c['commission_id'],'l.log_relation_type'=>1,'w.wallet_member'=>$c['commission_member_id']])
				->value('log_id');
			if($log_id){
				db("wallet_log")->delete($log_id);
			}else{
				#没有wallet_log 不进行金额操作
				$c['commission_money']=0;
			}
			#对变动金额大于0的进行操作 减去钱包余额
			if($c['commission_money']>0){
				$m=$c['commission_money'];
				db("wallet")->where('wallet_member',$c['commission_member_id'])->update(['wallet_amount'=>["exp","wallet_amount-".$m],'wallet_total_revenue'=>["exp","wallet_total_revenue-".$m],"wallet_fenrun"=>["exp","wallet_fenrun-".$m]]);
			}
			echo sprintf("user %d reduce %s,order_id %d,log_id %s;</br>",$c['commission_member_id'],$c['commission_money'],$c['commission_from'],$log_id);
		}
		public function tests(){
			echo "finished";
		}
}