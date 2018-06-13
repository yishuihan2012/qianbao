<?php
/**
* 
*/
namespace app\api\controller;
use think\Db;
use app\index\model\System;
use think\Request;
use think\Config;
use app\index\model\MemberCert;
use app\index\model\MemberLogin;
use app\index\model\Member;
use app\index\model\Wallet;
use app\index\model\MemberRelation;
use app\index\model\MemberTeam;
use app\index\model\MemberNet;
use app\index\model\MemberCashcard;
use app\index\model\MemberCreditcard;
use app\index\model\CashOrder;
use app\index\model\PassagewayItem;
class Test 
{

		public function bankcert()
		{
			 $aa=new \app\api\payment\yibaoPay(17, 20);		
			 $bb=$aa->register();
			 dump($bb);	 
		}

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
			dump($data);
			echo "!23";
			$res=json_decode($data,true);
			if(is_array($res) && $res['code']==200){
				$data=$index->decryption_data($res['data']); 
			}else{
				$data=$res;
			}
			print_r($data);
			die;
		}
		public function change_bank_name(){
			$list=MemberCashcard::where('card_bankname like "%(%"')->select();
			foreach ($list as $key => $v) {
				 $bank_name=$v['card_bankname'];
                 $bank_name=substr($bank_name,0,strpos($bank_name,'('));
                 $update=MemberCashcard::where(['card_id'=>$v['card_id']])->update(['card_bankname'=>$bank_name]);
			}
		}
		#排查cash 与 generation 的status为2的 但是commission里没有的，进行重新分配
		public function fenrun_change(){
			set_time_limit(0);
			#分润与订单数据
			$cms=db("commission")
			    // ->where("commission_member_id","<>",-1)
			    // ->where("commission_type","in","1,3")->select();
			    ->select();
			// $cash_order=db('cash_order')->where('order_state',2)->column("*","order_id");
			$generation_order=db('generation_order')
			    ->where('order_status',2)
			    ->where('order_type',1)
			    ->where('back_status','SUCCESS')
			    // ->where('order_member','not in','469')
			    ->order("order_edit_time asc")
			    ->column('*','order_id');
			#遍历现有分润数据 拆分出快捷 与 代还 以commission_from 为键
			$cms_cash=[];
			$cms_generation=[];
			$i=0;
			$n=0;
			$from_null=0;
			$type=[];
			foreach($cms as $k=>$v){
				if(strpos($v['commission_desc'], '快捷')!==false){
				// if($v['commission_type']==1){
					if(isset($cms_cash[$v['commission_from']])){
						$cms_cash[$v['commission_from']][]=$v;
					}else{
						$cms_cash[$v['commission_from']]=[$v];
					}
					$i++;
				}else if(strpos($v['commission_desc'], '代还')!==false){
					if(isset($cms_generation[$v['commission_from']])){
						$cms_generation[$v['commission_from']][]=$v;
					}else{
						$cms_generation[$v['commission_from']]=[$v];
					}
					$n++;
				}
				if(isset($type[$v['commission_type']])){
					$type[$v['commission_type']]++;
				}else{
					$type[$v['commission_type']]=1;
				}
				if(!$v['commission_from'] && strpos($v['commission_desc'], '分佣')==false)
					$from_null++;
			}
			echo "分润：含快捷数据$i 条,含代还数据$n 条 , 其他".(count($cms)-$i-$n)."条</br>";
			echo "没有commission_from的分润有".$from_null."条</br>";
			echo "已有分润commission_from的订单 含快捷的为 ".count($cms_cash)."条，含代还的为".count($cms_generation)."条</br></br>";
			$i=0;
			$n=0;
			$n_nomoney=0;
			#重新分配 快捷
			// foreach ($cash_order as $k => $v) {
			// 	#如果现有的commision里没有这一条 则进行分配
			// 	if(!isset($cms_cash[$k])){
			// 		$i++;
			// 		$commission=new \app\api\controller\Commission();
			// 		$res=$commission->MemberFenRun($v['order_member'],$v['order_money'],$v['order_passway'],1,'快捷支付手续费分润',$k);
			// 		echo "对快捷：".$v['order_id']."分配</br>";
			// 	}
			// }
			#重新分配 代还
			foreach ($generation_order as $k => $v) {
				if(!isset($cms_generation[$k])){
					// echo $k."<br/>";
					// halt($v);
					$n++;
					$commission=new \app\api\controller\Commissions();
					$res=$commission->MemberFenRun($v['order_member'],$v['order_money'],$v['order_passageway'],3,'代还手续费分润',$k);
					echo "对a代还：".$v['order_id']."分配</br>";
				}else{
					#有commission 但总分润额为0的
					$sum=0;
					foreach ($cms_generation[$k] as $kk => $vv) {
						$sum+=$vv['commission_money'];
					}
					if($sum==0){
						$commission=new \app\api\controller\Commissions();
						$res=$commission->MemberFenRun($v['order_member'],$v['order_money'],$v['order_passageway'],3,'代还手续费分润',$k);
						$n_nomoney++;
						echo "对代还：".$v['order_id']."分配</br>";
						// echo $k."<br/>";
					}
				}
			}
			echo "代还表 成功的总计 ".count($generation_order)." 条</br>";
			echo "代还表 成功的 但是在分润表 没有from的 有$n 条</br>";
			echo "代还表 成功的 但是在分润表 有from 总分润额为0的 有$n_nomoney 条</br>";
			echo "代还表 成功的 但是在分润表 有from 的 有".(count($generation_order)-$n-$n_nomoney) ."条</br>";
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
		#重新生成wallet数据
		#先使用wallet函数删除重复分润，再使用本函数根据commission表进行重新分润
		public function wallet_reset(){
			set_time_limit(0);
			#将钱包归零
			db('wallet')->where("1=1")->update(['wallet_amount'=>0,'wallet_total_revenue'=>0,'wallet_fenrun'=>0]);
			// 删除分润钱包日志
			db('wallet_log')->where(['log_form'=>['like','%分润%']])->delete();
			$generation_order=db("generation_order")->select();
			#构建失败订单的id组
			$fail_order_ids=[];
			foreach ($generation_order as $k => $v) {
				if($v['order_status']!=2)
					$fail_order_ids[]=$v['order_id'];
			}
			#删除失败订单对应的commission
			$count=db("commission")->where("commission_type","in","1,3")->where("commission_from","in",$fail_order_ids)->delete();
			echo "delete commission ".$count."</br>";
			#取出commission
			$cms=db("commission")->where("commission_member_id","<>",-1)->where("commission_from","not null")->where("commission_type","in","1,3")->select();
			#会员数据
			$member=db('member')->alias('m')
			    ->join('wallet w','w.wallet_member=m.member_id')
			    ->column("*","member_id");
			#会员关系
			$relation=db('member_relation')->column("relation_member_id,relation_bench");
			#初始化balance
			foreach ($member as $k => $v) {
				$member[$k]['balance']=0;
			}
			#在无限极中3级外的统称下级
			$arr=['直接','间接','三级','下级'];
			foreach ($cms as $k => $v) {
				#实时余额累加
				$member[$v['commission_member_id']]['balance']+=$v['commission_money'];
				$type=$v['commission_type']==1 ? "快捷支付" : "代还";
				#分析层级 
				$cid=$v['commission_childen_member'];
				for($i=0;$i<4;$i++){
					#判断是否是本次上级
					if($relation[$cid]==$v['commission_member_id']){
						$this_relation=$arr[$i];
						break;
					}
					$cid=$relation[$cid];
					if($cid==0){
						echo "err! commission ".$v['commission_id']."对应的上下级关系出错,已跳过</br>";
						break;
					}
					#4级以上的
					if($i==3)
						$this_relation=$arr[3];
				}
				if($cid==0)
					continue;
				$desc=$type."分润-".$this_relation."分润:";
				if($v['commission_money']==0){
					$desc.="与下级会员级别相同或比下级级别低,不获得分润~";
				}else{
					$desc.="邀请的".$member[$v['commission_childen_member']]['member_nick'].$type."分润成功,获得收益".$v['commission_money']."元~";
				}
				#插入钱包日志
				db('wallet_log')->insert([
					'log_wallet_id'=>$member[$v['commission_member_id']]['wallet_id'],
					'log_wallet_amount'=>$v['commission_money'],
					'log_balance'=>$member[$v['commission_member_id']]['balance'],
					'log_wallet_type'=>1,
					'log_relation_id'=>$v['commission_id'],
					'log_relation_type'=>1,
					'log_form'=>$type."分润收益~",
					'log_desc'=>$desc,
					'log_add_time'=>$v['commission_creat_time'],
				]);
			}
			#更新钱包余额
			$i=0;
			foreach ($member as $k => $v) {
				if($v['balance']>0){
					db('wallet')->where('wallet_id',$v['wallet_id'])->update(['wallet_amount'=>$v['balance'],'wallet_total_revenue'=>$v['balance'],'wallet_fenrun'=>$v['balance']]);
					$i++;
				}
			}
			echo "finished,update ".$i;
		}
		public function tests(){
			echo "finished";
		}

	public function order_rate($page){
		$limit=($page-1)*3000;
		// var_dump($limit);die;
	 	$order=CashOrder::with('member,passageway')->where("order_state=2 and order_add_time>'2018-01-08 00:00:00'")->limit($limit,3000)->select();
	 	// var_dump($order);die;
	 	if(empty($order)){
	 		echo "123123";die;
	 	}
	 	// $passway_item=PassagewayItem::all();
			// $item=[];
			// foreach ($passway_item as $k => $v) {
			//     $key=$v['item_passageway'].'_'.$v['item_group'];
			//     $item[$key]=$v;
			// }
			// var_dump($item['8_4']);die;
	 	foreach ($order as $key => $value) {
	 		//用户费率
	 		// $rate=PassagewayItem::where('item_passageway='.$value['order_passway']. ' and item_group='.$value['member_group_id'])->field('item_rate,item_charges')->find();
	 		// $k=$value['order_passway'].'_'.$value['member_group_id'];
			 //    $rate=$item[$k]['item_rate'];
			 //    $charges=$item[$k]['item_charges'];
	 		$data=array(
	 			// 'user_rate'=>$rate,
	 			// 'user_fix'=>$charges/100,
	 			'passageway_rate'=>$value->passageway->passageway_rate,
	 			'passageway_fix' =>$value->passageway->passageway_income,
	 			'order_passway_profit'=>$value->order_money*$value->passageway->passageway_rate/100+$value->passageway->passageway_income
	 		);
	 		CashOrder::where('order_id='.$value['order_id'])->update($data);

	 	}
	 	echo 'success';die;
	 }
	 #手动调整钱包余额
	 public function change_wallet(){
	 	$arr=[
	 		'张奎同'=>9.65,
	 		'杨雪'=>5,
	 		'王玉强'=>7.5,
	 		'任爱芬'=>9.99,
	 	];
	 	foreach ($arr as $k => $v) {
	 		$member_id=db('member')->where('member_nick','like','%'.$k.'%')->value('member_id');
	 		$wallet=Wallet::get(['wallet_member'=>$member_id]);
	 		$wallet->wallet_amount-=$v;
	 		$wallet->wallet_total_revenue-=$v;
	 		$wallet_log=new Wallet_log([
              'log_wallet_id' =>$wallet->wallet_id,
              'log_wallet_amount'=>$v,
              'log_balance'=>$wallet->wallet_amount,
              'log_wallet_type'    =>2,
              'log_relation_id'     =>0,
              'log_relation_type' =>7,
              'log_form'              =>'手动调整',
              'log_desc'  =>'2018年1月30日之前余额校对',
	 		]);
	 		echo $wallet->wallet_amount.'</br>';
	 	}
	 }
	 /**
	  * 修正易生交易订单手续费
	  */
	 public function yisheng(){
        $passageway=db('passageway')->where('passageway_true_name','EspayBhjf')->find();
	 	$orders=db('cash_order')
            ->where('order_add_time','between time',['2018-04-01','2018-05-01'])
            ->where('order_passway',$passageway['passageway_id'])
            ->where('order_state',2)
            ->select();
        foreach ($orders as $k => $v) {
        	$order=CashOrder::get($v['order_id']);
        	$order->order_charge=$order->order_money*$order->user_rate/100;
        	$order->save();
        }
        halt($orders);
	 }
	 /**
	  * 修正易生支付分润
	  * @return [type] [description]
	  */
	 public function update_wallet(){
	 	$passageway=db('passageway')->where('passageway_true_name','EspayBhjf')->find();
	 	$orders=db('cash_order')
            ->where('order_add_time','between time',['2018-04-01','2018-05-01'])
            ->where('order_passway',$passageway['passageway_id'])
            ->where('order_state',2)
            ->select();
        $count=0;
        foreach ($orders as $k => $order) {
        	$commissions=db('commission')->where(['commission_from'=>$order['order_id'],'commission_type'=>1,'commission_childen_member'=>$order['order_member']])->select();
        	foreach ($commissions	 as $kk => $commission) {
        		 $true_commission=($order['user_rate']-$commission['commission_cash_rate'])/100*$order['order_money'];
        		 //如果金额大于0
        		 if($true_commission<0){
        		 	$true_commission=0;
        		 }
        		 if($true_commission!=$commission['commission_money'] && $commission['commission_money']>0){
        		 	    $cha_money=$true_commission-$commission['commission_money'];
        		 		//修改分润记录
        		 		 Db::startTrans();
        		 		$update=db('commission')->where(['commission_id'=>$commission['commission_id']])->update(['commission_money'=>$true_commission]);//

        		 		//修改钱包记录
        		 		$wallet_log=Wallet_log::where('log_relation_id',$commission['commission_id'])->find();
        		 		
        		 		$wallet_log->log_balance+=$cha_money;
        		 		$wallet_log->log_wallet_amount=$true_commission;
        		 		$wallet_log->log_desc=preg_replace('/收益(.+?)元/', '收益'.$true_commission.'元', $wallet_log->log_desc);
        		 		//修改钱包
        		 		$wallet=wallet::get(['wallet_member'=>$commission['commission_member_id']]);
        		 		$wallet->wallet_amount+=$cha_money;
        		 		$wallet->wallet_total_revenue+=$cha_money;
        		 		$wallet->wallet_fenrun+=$cha_money;
        		 		;
        		 		if($wallet_log->save() !==false && $wallet->save() !== false && $update ){
        		 			Db::commit();
        		 			echo "修改订单号：".$order['order_id']."分润id：".$commission['commission_id'].'成功'.$count++;
        		 			echo"<br/>";
        		 		}else{
        		 			Db::rollback();
        		 		}
        		 }
        	}
        }
	 }
	 #对每个项目执行sql
	 public function exesql(){
	 	$database=[
	 		'喜家'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/wallet#utf8',
	 		'鑫鑫'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/xxqg_wallet#utf8',
	 		'李掌柜'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/lizhanggui#utf8',
	 		'云众'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/yunzhong_wallet#utf8',
	 		'融易还呗'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/rongyihuanbai#utf8',
	 		'无忧'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/wuyou#utf8',
	 		'易享'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/yixiang_wallet#utf8',
	 		'乐还'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/lehuan#utf8',
	 		'金源乐享'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/jinyuan_wallet#utf8',
	 		'益信付'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/yixin_wallet#utf8',
	 		'众信'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/zhongxin_wallet#utf8',
	 		'富通'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/futong_wallet#utf8',
	 		'民麦'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/minmai_wallet#utf8',
	 		'E还宝'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/ehb_wallet#utf8',
	 		'如意付'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/ruyifu_wallet#utf8',
	 		'中京'=>'mysql://root:chfuck~>d5@47.104.4.73:3306/zhongjing_wallet#utf8',
	 		'惠钱包'=>'mysql://huiqianbao:huiqianbao@47.96.146.215:3306/huiqianbao#utf8',
	 		'叮当'=>'mysql://huiqianbao:huiqianbao@47.96.146.215:3306/dingdang_wallet#utf8',
	 	];
	 	// $sql="select system_val from wt_system where system_key='sitename'";
	 	$sql="select passageway_mech from wt_passageway WHERE passageway_true_name LIKE \"%mswjf%\"";
	 	// $sql="select system_val from wt_system where system_key='adminster_key' limit 1";
	 	$type=1;
	 	foreach ($database as $k => $v) {
	 		$db = Db::connect($v);
	 		echo $k.':__';
	 		if($type==1){
		 		$res=$db->query($sql);
		 		foreach ($res as $key => $value) {
		 			foreach ($value as $k => $v) {
		 				echo $v;
		 			}
		 		}
	 		}else{
		 		$res=$db->execute($sql);
		 		echo "影响行数:".$res;
	 		}
	 		echo "</br>";
	 	}
	 }
	 public function get_plantform_name($name="喜家钱包"){
	 	//转成带有声调的汉语拼音 TransformWithTone
		//转成带无声调的汉语拼音 TransformWithoutTone
		//转成汉语拼音首字母 TransformUcwords
	 	$name1=mb_substr($name,0,1,'utf-8');
	 	$name2=mb_substr($name, 1);
	 	$Chinatowords=new \app\api\controller\ChinesePinyin();
	 	$first=$Chinatowords->TransformWithoutTone($name1);
	 	$second=$Chinatowords->TransformUcwords($name2);
	 	$pinyin=$first.mb_strtolower($second);
	 	return $pinyin;
	 }
	 public function pay(){
	 	$YiJiFu=new \app\api\payment\YiJiFu();
		$res=$YiJiFu->passway_search('625976029153','16605383329');
		var_dump($res);die;
	 }
	 /**
	  * 导入喜家数据
	  * @return [type] [description]
	  */
	public function trans_data_xj(){
		set_time_limit(0);
		echo "[";
		#1查询喜家数据
		$connnect_xj=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/wallet#utf8');
		$new_connect=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/lianyunbao_wallet#utf8');
		$xj_sql="select * from wt_member left join wt_member_cashcard on wt_member_cashcard.card_member_id=wt_member.member_id left join wt_member_cert on wt_member_cert.cert_member_id=wt_member.member_id join wt_member_login on wt_member_login.login_member_id=wt_member.member_id";
		$xj_members=$connnect_xj->query($xj_sql);
		// var_dump($xj_members);die;
		#2循环导入新项目
		foreach ($xj_members as $k => $xj_member) {
			#1查询目标项目有没有当前用户
			$find_user=Member::where(['member_mobile'=>$xj_member['member_mobile']])->find();
				if(!$find_user){
					// var_dump($xj_member);die;
					Db::startTrans();
					try{
						// 对应会员id
						$member_group_id=1;
						if($xj_member['member_group_id']==2 || $xj_member['member_group_id']==3){
							$member_group_id=6;
						}
						if($xj_member['member_group_id']==4 && $xj_member['member_cert']==1){
							$member_group_id=7;
						}
						if($xj_member['member_group_id']==4 && $xj_member['member_cert']==0){
							$member_group_id=1;
						}
		            	 #新增会员基本信息
		            	 $member_info= new Member([
		            	 	 'member_nick'=>$xj_member['member_nick'],
		            	 	 'member_mobile'=>$xj_member['member_mobile'],
		            	 	 'member_group_id'=>$member_group_id,
			                 'member_image'=>$xj_member['member_image'],
			                 'member_root'=>0,
			                 'member_cert'=>$xj_member['member_cert'],
			                 'member_from'=>"喜家钱包"
		            	 ]);
		            	 if(!$member_info->save())
		            	 {
		            	 	 Db::rollback();
		            	 }
		                 $token = get_token();
		            	 $member_login= new MemberLogin([
		            	 	 'login_member_id'  => $member_info->member_id,
		            	 	 'login_account'	  => $xj_member['member_mobile'],
		            	 	 'login_pass'		    => $xj_member['login_pass'],
		            	 	 'login_pass_salt'  => '',
		                 	 'login_token'      => $token,
		            	 	 'login_attempts'	  => 0,
		            	 ]);
		            	 #初始化会员钱包信息
		            	 $member_wallet= new Wallet([
		            	 	 'wallet_member'=>$member_info->member_id,
		            	 	 'wallet_amount'=>0
		            	 ]);
		               #初始化会员团队信息
		               $member_team=new MemberTeam([
		                'team_name'=>$member_info->member_nick,
		                'team_member_id'=>$member_info->member_id,
		               ]);

		               #初始化会员入网信息
		               $MemberNet=new MemberNet([
		                'net_member_id'=>$member_info->member_id,
		               ]);
		               
		            	if( !$member_login->save()  || !$member_wallet->save() || !$member_team->save() || !$MemberNet->save()){
		            	 	 Db::rollback();
		            	}
		            	
		            	
		            	if($xj_member['member_cert']==1){
		            		#绑定储蓄卡
		            		$MemberCashcard= new MemberCashcard([
			            	 	 'card_member_id'   => $member_info->member_id,
			            	 	 'card_bankno'	    => $xj_member['card_bankno'],
			            	 	 'card_name'		=> $xj_member['card_name'],
			            	 	 'card_idcard'      => $xj_member['card_idcard'],
			                 	 'card_phone'       => $xj_member['card_phone'],
			            	 	 'card_bankname'	=> $xj_member['card_bankname'],
			            	 	'card_bank_province'=> $xj_member['card_bank_province'],
			            	 	 'card_bank_city'	=> $xj_member['card_bank_city'],
			            	 	 'card_bank_area'	=> $xj_member['card_bank_area'],
			            	 	 'card_bank_address'=> $xj_member['card_bank_address'],
			            	 	 'card_bank_lang'	=> $xj_member['card_bank_lang'],
			            	 	 // 'card_ident'	    => $xj_member['card_ident'],
			            	 	 // 'card_rname'		=> $xj_member['card_rname'],
			            	 	 'card_type'		=> $xj_member['card_type'],
			            	 	 // 'card_channel'		=> $xj_member['card_channel'],
			            	 	 'card_state'		=> $xj_member['card_state'],
			            	 	 'card_return'		=> $xj_member['card_return'],
			            	]);
			            	
		            		$MemberCert= new MemberCert([
			            	 	 'cert_member_id'   => $member_info->member_id,
			            	 	 'cert_card_id'	    => $MemberCashcard->card_id,
			            	 	 'cert_member_name'	=> $xj_member['cert_member_name'],
			            	 	 'cert_member_idcard' => $xj_member['cert_member_idcard'],
			                 	 'IdPositiveImgUrl'       => $xj_member['IdPositiveImgUrl'],
			            	 	 'IdNegativeImgUrl'	=> $xj_member['IdNegativeImgUrl'],
			            	 	 'IdPortraitImgUrl'=> $xj_member['IdPortraitImgUrl'],
			            	 	 'cert_add_time'	=> $xj_member['cert_add_time'],
			            	]);
			            	
			            	if( !$MemberCashcard->save() || !$MemberCert->save() ){
		            	 	   Db::rollback();
		            	    }
		            	
			            	#绑定信用卡
			            	$member_creditcards=$connnect_xj->query('select * from wt_member_creditcard where wt_member_creditcard.card_member_id='.$xj_member['member_id']);
			            	if($member_creditcards){
			            		foreach ($member_creditcards as $key => $member_creditcard) {
			            			$MemberCreditcard= new MemberCreditcard([
					            	 	 'card_member_id'   => $member_info->member_id,
					            	 	 'card_bankno'	    => $member_creditcard['card_bankno'],
					            	 	 'card_name'		=> $member_creditcard['card_name'],
					            	 	 'card_idcard'      => $member_creditcard['card_idcard'],
					                 	 'card_phone'       => $member_creditcard['card_phone'],
					            	 	 'card_bankname'	=> $member_creditcard['card_bankname'],
					            	 	 'card_Ident'		=> $member_creditcard['card_Ident'],
					            	 	 'card_expireDate'	=> $member_creditcard['card_expireDate'],
					            	 	 'card_billDate'	=> $member_creditcard['card_billDate'],
					            	 	 'card_deadline'    => $member_creditcard['card_deadline'],
					            	 	 'card_isRemind'	=> $member_creditcard['card_isRemind'],
					            	 	 'card_remindDate'  => $member_creditcard['card_remindDate'],
					            	 	 'card_state'		=> $member_creditcard['card_state'],
					            	 	 'card_return'		=> $member_creditcard['card_return'],
					            	 	 'card_bankicon'    => $member_creditcard['card_bankicon'],
				            		]);
				            		if( !$MemberCreditcard->save() ){
				            	 	   Db::rollback();
				            	    }
				            		
			            		}
			            	}
			            }
			            echo $xj_member['member_id'];
			            #拼装上下级关系
			            $parent=$connnect_xj->query('select * from wt_member_relation join wt_member on wt_member_relation.relation_parent_id= wt_member.member_id where wt_member_relation.relation_member_id='.$xj_member['member_id']);
			            if(!$parent || !isset($parent[0]['member_mobile'])){
			            	$parent[0]['member_mobile']=0;
			            }
		           		echo $k."=>[
		           				'member_id'=>{$member_info->member_id},'mobile'=>{$xj_member['member_mobile']},'parent_mobile'=>'{$parent[0]['member_mobile']}',
							],";
		            	Db::commit();
		            } catch (\Exception $e) {
		                Db::rollback();
		                echo $e->getMessage();die;
		            }
				}
		}
		echo "]";
	}
	public function reset_groups(){
		set_time_limit(0);
		$project="喜家钱包";
		$connnect_xj=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/wallet#utf8');
		$members=Member::where(['member_from'=>$project])->select();
		foreach ($members as $k => $member) {
			$member_group_id=1;
		// if($member['member_id']==438){
			#查询用户老平台id
			$xj_member=$connnect_xj->query('select * from wt_member where member_mobile='.$member['member_mobile']);
			if($xj_member){
				$xj_member=$xj_member[0];
			}
			#查询老平台父级的mobile
			if($xj_member['member_group_id']==2 || $xj_member['member_group_id']==3){
				$member_group_id=6;
			}
			if($xj_member['member_group_id']==4 && $xj_member['member_cert']==1){
				$member_group_id=7;
			}
			if($xj_member['member_group_id']==4 && $xj_member['member_cert']==0){
				$member_group_id=1;
			}
			// echo $member['member_group_id'];
			// echo $member_group_id;die;
			if($member['member_group_id']!=$member_group_id){
				$res=Member::where(['member_mobile'=>$member['member_mobile']])->update(['member_group_id'=>$member_group_id]);
				echo "手机号：".$member['member_mobile'].'变更成功';
				echo "<br/>";
			}
		// }
		}
	}
	public function  find_parent_people(){
		$yunyingshang_id=0;
		$project="喜家钱包";
		set_time_limit(0);
		$connnect_xj=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/wallet#utf8');
		#1查找新表里来源是喜家的所有用户
		$members=Member::where(['member_from'=>$project])->select();
		foreach ($members as $k => $member) {
			#查询用户老平台id
			$xj_member=$connnect_xj->query('select * from wt_member where member_mobile='.$member['member_mobile']);
			#查询老平台父级的mobile
			 $parent=$connnect_xj->query('select * from wt_member_relation left join wt_member on wt_member_relation.relation_parent_id= wt_member.member_id where wt_member_relation.relation_member_id='.$xj_member[0]['member_id']);
			 if($parent && $parent[0]['member_mobile']){
			 	     #如果存在父级，就获取新钱里父级id
				 	$parent_info=Member::where(['member_mobile'=>$parent[0]['member_mobile']])->find();
					if($parent_info){
						$parent_id=$parent_info['member_id'];
					}else{
						//没有父级默认为运营商id
						$parent_id=$yunyingshang_id;
					}
			 }else{
			 	//没有父级默认为运营商id
			 	$parent_id=$yunyingshang_id;
			 }
			$relation_type=1;
			$relation_info=MemberRelation::where(['relation_member_id'=>$member['member_id']])->find();
			if(!$relation_info){
				$MemberRelation= new MemberRelation([
        	 	 'relation_member_id'   => $member['member_id'],
        	 	 'relation_parent_id'	    =>$parent_id,
        	 	 'relation_type'		=> $relation_type,
	    		]);
	    		$MemberRelation->save(); 
	    		echo $k.'-';
			}else{
				if($relation_info['relation_parent_id']!=$parent_id){
					MemberRelation::where(['relation_member_id'=>$member['member_id']])->update(['relation_parent_id'=>$parent_id]);
					echo $k.'-';
				}
			}
		}
	}
	public function find_repeat_peopel(){
		$lists=Member::where('member_id <152')->select();
		$lists=$lists->toArray();
		$lists_mobile=array_column($lists, 'member_mobile');
		$str='(';
		foreach ($lists_mobile as $key => $v) {
			if($key==count($lists_mobile)-1){
				$str.=$v;
			}else{
				$str.=$v.',';
			}	
		}
		$str.=")";
		// echo $str;die;
		// 旧平台重读的数据
		$connnect_xj=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/wallet#utf8');
		$res= $connnect_xj->query("select *from wt_member where member_mobile in {$str}");
		// print_r($res);die;
		##获取mobile
		$common_mobile=array_column($res, 'member_mobile');
		// 新平台重复会员
		$new_members=Member::where(['member_mobile'=>['in',$common_mobile]])->select();
		$member_ids=array_column($new_members->toArray(), 'member_id');
		#删除member表
		
		#删除Login表	
		#删除实名
		#删除信用卡
		#
	}
	public function edit_pat_time(){
 		set_time_limit(0);
		$connnect_local=Db::connect('mysql://root:123456@127.0.0.1:3306/test#utf8');
		$dingdang_wallet=Db::connect('mysql://huiqianbao:huiqianbao@47.96.146.215:3306/dingdang_wallet#utf8');
		$xxqg_wallet=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/xxqg_wallet#utf8');
		$minmai_wallet=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/minmai_wallet#utf8');
		$zhongjing_wallet=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/zhongjing_wallet#utf8');
		$wallet=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/wallet#utf8');
		$wuyou=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/wuyou#utf8');
		$ruyifu_wallet=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/ruyifu_wallet#utf8');
		$huiqianbao=Db::connect('mysql://huiqianbao:huiqianbao@47.96.146.215:3306/huiqianbao#utf8');
		$futong_wallet=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/futong_wallet#utf8');
		$lists=$connnect_local->query("select * from data1 where plant ='喜家' or plant ='如意付' ");
		// print_r(count($lists));die;
		$total=0;
		foreach ($lists as $key => $list) {
				switch ($list['plant']) {
					case '叮当':
						$connnect=$dingdang_wallet;
						break;
					case '鑫鑫':
						$connnect=$xxqg_wallet;
						break;
					case '民麦':
						$connnect=$minmai_wallet;
						break;
					case '中京':
						$connnect=$zhongjing_wallet;
						break;
					case '喜家':
						$connnect=$wallet;
						break;
					case '无忧':
						$connnect=$wuyou;
						break;
					case '如意付':
						$connnect=$ruyifu_wallet;
						break;
					case '惠钱包':
						$connnect=$huiqianbao;
						break;
					case '富通':
						$connnect=$futong_wallet;
						break;
					default:
						# code...
						break;
				}
				$no =trim($list['order_no']);
				$detail=$connnect->query("select * from wt_generation_order where order_platform_no = '{$no}' ");
				if(isset($detail[0])){
					$rate=$list['rate']*100;
					$passway_time=date('Y-m-d',strtotime($list['pay_time']));
					$plantform_time=date('Y-m-d',strtotime($detail[0]['order_edit_time']));
					$edit_time=date('Y-m-d H:i:s',strtotime($list['pay_time']));
					// echo $detail[0]['user_rate'].'--'.$rate;
					// echo "<br/>";
					// if($detail[0]['user_rate']!=0 && (round((float)$detail[0]['user_rate'],2)>round($rate,2))){
					// 	$where=" set user_rate='{$rate}' ";
					// 	$where.=" ,order_edit_time='{$edit_time}' ";				
					// 	$update=$connnect->query("update wt_generation_order {$where}  where order_platform_no = '{$no}'");
					// }
					// ******************************更改通手续费********************************
					if($detail[0]['order_type']==1){

						// $chengben=round($detail[0]['order_money']*$detail[0]['passageway_rate']/100,2);

						// if($chengben!=$detail[0]['order_passageway_fee']){
						// 	$where=" set order_passageway_fee='{$chengben}' ";
						// 	$where.=" ,order_edit_time='{$edit_time}' ";				
						// 	$update=$connnect->query("update wt_generation_order {$where}  where order_platform_no = '{$no}'");
						// }
						// // 更改总手续费
						// $total_fee=ceil($detail[0]['order_money']*$detail[0]['user_rate'])/100; 
						// // echo $total_fee.'--'.$detail[0]['order_pound'];
						// // echo "<br/>";
						// // $total=$total+$total_fee;
						// if($total_fee!=$detail[0]['order_pound']){
						// 	$where=" set order_pound='{$total_fee}' ";
						// 	$where.=" ,order_edit_time='{$edit_time}' ";				
						// 	$update=$connnect->query("update wt_generation_order {$where}  where order_platform_no = '{$no}'");
						// }
						// if($detail[0]['user_fix']>0){

						// 	$where=" set user_fix= 0 ";
						// 	$where.=" ,order_edit_time='{$edit_time}' ";
						// 	$update=$connnect->query("update wt_generation_order {$where}  where order_platform_no = '{$no}'");
						// }
					}
					
					//  更改时间
					
				}
		}
		// echo $total;die;
	}

}