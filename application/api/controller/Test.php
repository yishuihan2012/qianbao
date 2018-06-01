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
			$find_user=$new_connect->query('select * from wt_member where member_mobile='.$xj_member['member_mobile']);
				if(!$find_user){
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
		            		#导入实名信息
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
			            	$MemberCashcard->save(); 
		            		
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
			            	$MemberCert->save(); 

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
				            		$MemberCreditcard->save(); 
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
	public function  find_parent_people(){
		set_time_limit(0);

		$arr=[43=>[ 'member_id'=>152,'mobile'=>15318828885,'parent_mobile'=>'0', ],65=>[ 'member_id'=>153,'mobile'=>18953115553,'parent_mobile'=>'0', ],86=>[ 'member_id'=>154,'mobile'=>15069909799,'parent_mobile'=>'0', ],128=>[ 'member_id'=>155,'mobile'=>13969187582,'parent_mobile'=>'0', ],139=>[ 'member_id'=>156,'mobile'=>15634098838,'parent_mobile'=>'18764092012', ],1410=>[ 'member_id'=>157,'mobile'=>15265559461,'parent_mobile'=>'15634098838', ],1711=>[ 'member_id'=>158,'mobile'=>18888298492,'parent_mobile'=>'18764092012', ],1912=>[ 'member_id'=>159,'mobile'=>18562060599,'parent_mobile'=>'0', ],2213=>[ 'member_id'=>160,'mobile'=>17862263870,'parent_mobile'=>'0', ],2514=>[ 'member_id'=>161,'mobile'=>15809008221,'parent_mobile'=>'15069909799', ],2815=>[ 'member_id'=>162,'mobile'=>15552751788,'parent_mobile'=>'18562060599', ],2916=>[ 'member_id'=>163,'mobile'=>13020602566,'parent_mobile'=>'15552751788', ],3117=>[ 'member_id'=>164,'mobile'=>13287701331,'parent_mobile'=>'0', ],3420=>[ 'member_id'=>165,'mobile'=>18815310198,'parent_mobile'=>'0', ],3521=>[ 'member_id'=>166,'mobile'=>15376222956,'parent_mobile'=>'18764092012', ],4122=>[ 'member_id'=>167,'mobile'=>15806635920,'parent_mobile'=>'18764092012', ],4223=>[ 'member_id'=>168,'mobile'=>15866683365,'parent_mobile'=>'18764092012', ],4324=>[ 'member_id'=>169,'mobile'=>13365463038,'parent_mobile'=>'0', ],4525=>[ 'member_id'=>170,'mobile'=>15253115251,'parent_mobile'=>'0', ],4927=>[ 'member_id'=>171,'mobile'=>13355487399,'parent_mobile'=>'18764092012', ],5128=>[ 'member_id'=>172,'mobile'=>15666446111,'parent_mobile'=>'0', ],5229=>[ 'member_id'=>173,'mobile'=>15853185521,'parent_mobile'=>'0', ],5330=>[ 'member_id'=>174,'mobile'=>13287787881,'parent_mobile'=>'0', ],6031=>[ 'member_id'=>175,'mobile'=>13475980909,'parent_mobile'=>'13365463038', ],6132=>[ 'member_id'=>176,'mobile'=>17705493690,'parent_mobile'=>'13969187582', ],6533=>[ 'member_id'=>177,'mobile'=>15562660718,'parent_mobile'=>'0', ],6734=>[ 'member_id'=>178,'mobile'=>17705318886,'parent_mobile'=>'15562660718', ],6835=>[ 'member_id'=>179,'mobile'=>15966339528,'parent_mobile'=>'13287701331', ],7336=>[ 'member_id'=>180,'mobile'=>18205316115,'parent_mobile'=>'0', ],7837=>[ 'member_id'=>181,'mobile'=>15275159628,'parent_mobile'=>'0', ],8038=>[ 'member_id'=>182,'mobile'=>15069145726,'parent_mobile'=>'13287701331', ],8440=>[ 'member_id'=>183,'mobile'=>13181826199,'parent_mobile'=>'15069145726', ],8641=>[ 'member_id'=>184,'mobile'=>18766411300,'parent_mobile'=>'0', ],8742=>[ 'member_id'=>185,'mobile'=>18805311995,'parent_mobile'=>'0', ],8943=>[ 'member_id'=>186,'mobile'=>17862975212,'parent_mobile'=>'0', ],9144=>[ 'member_id'=>187,'mobile'=>18953142015,'parent_mobile'=>'0', ],9245=>[ 'member_id'=>188,'mobile'=>15064076557,'parent_mobile'=>'0', ],9646=>[ 'member_id'=>189,'mobile'=>18615280813,'parent_mobile'=>'13287701331', ],10047=>[ 'member_id'=>190,'mobile'=>13285488929,'parent_mobile'=>'15000633450', ],10249=>[ 'member_id'=>191,'mobile'=>13396411610,'parent_mobile'=>'0', ],10551=>[ 'member_id'=>192,'mobile'=>15550998608,'parent_mobile'=>'13562905145', ],10952=>[ 'member_id'=>193,'mobile'=>15064105615,'parent_mobile'=>'13475980909', ],11553=>[ 'member_id'=>194,'mobile'=>18095220875,'parent_mobile'=>'15000633450', ],11654=>[ 'member_id'=>195,'mobile'=>18254145311,'parent_mobile'=>'15000633450', ],11956=>[ 'member_id'=>196,'mobile'=>15205415207,'parent_mobile'=>'13156141887', ],12057=>[ 'member_id'=>197,'mobile'=>15269100928,'parent_mobile'=>'18663726136', ],12358=>[ 'member_id'=>198,'mobile'=>17664394735,'parent_mobile'=>'0', ],12459=>[ 'member_id'=>199,'mobile'=>13184603877,'parent_mobile'=>'13475980909', ],12661=>[ 'member_id'=>200,'mobile'=>15254837889,'parent_mobile'=>'13287701331', ],12762=>[ 'member_id'=>201,'mobile'=>15853441397,'parent_mobile'=>'15069145726', ],13763=>[ 'member_id'=>202,'mobile'=>15509588676,'parent_mobile'=>'15650447290', ],14164=>[ 'member_id'=>203,'mobile'=>13395314767,'parent_mobile'=>'13969187582', ],14965=>[ 'member_id'=>204,'mobile'=>15726108129,'parent_mobile'=>'15000633450', ],15166=>[ 'member_id'=>205,'mobile'=>13954871239,'parent_mobile'=>'13287701331', ],15367=>[ 'member_id'=>206,'mobile'=>13665386160,'parent_mobile'=>'15275159628', ],15768=>[ 'member_id'=>207,'mobile'=>13335272365,'parent_mobile'=>'0', ],15969=>[ 'member_id'=>208,'mobile'=>15689690152,'parent_mobile'=>'13184603877', ],16070=>[ 'member_id'=>209,'mobile'=>13834059232,'parent_mobile'=>'15064105615', ],16271=>[ 'member_id'=>210,'mobile'=>13944941903,'parent_mobile'=>'13184603877', ],16372=>[ 'member_id'=>211,'mobile'=>17620132039,'parent_mobile'=>'13184603877', ],16473=>[ 'member_id'=>212,'mobile'=>13231942956,'parent_mobile'=>'13184603877', ],16574=>[ 'member_id'=>213,'mobile'=>13944061903,'parent_mobile'=>'13184603877', ],16875=>[ 'member_id'=>214,'mobile'=>13102117561,'parent_mobile'=>'13184603877', ],16976=>[ 'member_id'=>215,'mobile'=>13969027378,'parent_mobile'=>'13184603877', ],17077=>[ 'member_id'=>216,'mobile'=>17093188983,'parent_mobile'=>'13184603877', ],17178=>[ 'member_id'=>217,'mobile'=>15315568279,'parent_mobile'=>'13843148191', ],17279=>[ 'member_id'=>218,'mobile'=>15734599303,'parent_mobile'=>'13843148191', ],17380=>[ 'member_id'=>219,'mobile'=>13634309717,'parent_mobile'=>'13184603877', ],17481=>[ 'member_id'=>220,'mobile'=>13630589217,'parent_mobile'=>'13184603877', ],17682=>[ 'member_id'=>221,'mobile'=>15615622982,'parent_mobile'=>'15318845382', ],17783=>[ 'member_id'=>222,'mobile'=>15863189709,'parent_mobile'=>'13184603877', ],17884=>[ 'member_id'=>223,'mobile'=>17181710236,'parent_mobile'=>'13184603877', ],17985=>[ 'member_id'=>224,'mobile'=>18513501540,'parent_mobile'=>'13634309717', ],18086=>[ 'member_id'=>225,'mobile'=>18603365409,'parent_mobile'=>'13634309717', ],18187=>[ 'member_id'=>226,'mobile'=>13664317103,'parent_mobile'=>'13184603877', ],18388=>[ 'member_id'=>227,'mobile'=>13844078201,'parent_mobile'=>'13184603877', ],18489=>[ 'member_id'=>228,'mobile'=>15098746982,'parent_mobile'=>'13184603877', ],18590=>[ 'member_id'=>229,'mobile'=>13944820845,'parent_mobile'=>'13634309717', ],18691=>[ 'member_id'=>230,'mobile'=>13604438751,'parent_mobile'=>'13634309717', ],18892=>[ 'member_id'=>231,'mobile'=>17093077274,'parent_mobile'=>'13184603877', ],19093=>[ 'member_id'=>232,'mobile'=>17098749978,'parent_mobile'=>'13634309717', ],19194=>[ 'member_id'=>233,'mobile'=>18866859380,'parent_mobile'=>'13634309717', ],19395=>[ 'member_id'=>234,'mobile'=>18334778336,'parent_mobile'=>'13184603877', ],19696=>[ 'member_id'=>235,'mobile'=>17193710828,'parent_mobile'=>'13944941903', ],19897=>[ 'member_id'=>236,'mobile'=>15269121072,'parent_mobile'=>'13634309717', ],20098=>[ 'member_id'=>237,'mobile'=>13943011715,'parent_mobile'=>'13944941903', ],20399=>[ 'member_id'=>238,'mobile'=>13230927372,'parent_mobile'=>'13634309717', ],204100=>[ 'member_id'=>239,'mobile'=>17506429132,'parent_mobile'=>'13287701331', ],206101=>[ 'member_id'=>240,'mobile'=>17189387995,'parent_mobile'=>'13944941903', ],212103=>[ 'member_id'=>241,'mobile'=>15233606013,'parent_mobile'=>'13184603877', ],213104=>[ 'member_id'=>242,'mobile'=>15769583695,'parent_mobile'=>'13944941903', ],214105=>[ 'member_id'=>243,'mobile'=>17335796643,'parent_mobile'=>'13184603877', ],216106=>[ 'member_id'=>244,'mobile'=>15143109438,'parent_mobile'=>'13184603877', ],217107=>[ 'member_id'=>245,'mobile'=>15253131065,'parent_mobile'=>'13184603877', ],220108=>[ 'member_id'=>246,'mobile'=>13604431423,'parent_mobile'=>'13944941903', ],223109=>[ 'member_id'=>247,'mobile'=>15948705861,'parent_mobile'=>'13184603877', ],225110=>[ 'member_id'=>248,'mobile'=>13774908161,'parent_mobile'=>'0', ],227111=>[ 'member_id'=>249,'mobile'=>18514822039,'parent_mobile'=>'13634309717', ],228112=>[ 'member_id'=>250,'mobile'=>15176079212,'parent_mobile'=>'13287701331', ],233113=>[ 'member_id'=>251,'mobile'=>15969935328,'parent_mobile'=>'13969187582', ],234114=>[ 'member_id'=>252,'mobile'=>17090057453,'parent_mobile'=>'13184603877', ],235115=>[ 'member_id'=>253,'mobile'=>15101253047,'parent_mobile'=>'13184603877', ],236116=>[ 'member_id'=>254,'mobile'=>18339297185,'parent_mobile'=>'13944820845', ],237117=>[ 'member_id'=>255,'mobile'=>18661684011,'parent_mobile'=>'13396411610', ],240119=>[ 'member_id'=>256,'mobile'=>15122732419,'parent_mobile'=>'13944941903', ],243120=>[ 'member_id'=>257,'mobile'=>13012270917,'parent_mobile'=>'13634309717', ],244121=>[ 'member_id'=>258,'mobile'=>13944143531,'parent_mobile'=>'13184603877', ],246122=>[ 'member_id'=>259,'mobile'=>15948705629,'parent_mobile'=>'13634309717', ],247123=>[ 'member_id'=>260,'mobile'=>15943042961,'parent_mobile'=>'13634309717', ],249124=>[ 'member_id'=>261,'mobile'=>18203319806,'parent_mobile'=>'13184603877', ],250125=>[ 'member_id'=>262,'mobile'=>15948023561,'parent_mobile'=>'13634309717', ],251126=>[ 'member_id'=>263,'mobile'=>17093718992,'parent_mobile'=>'13634309717', ],252127=>[ 'member_id'=>264,'mobile'=>18630572030,'parent_mobile'=>'13184603877', ],255128=>[ 'member_id'=>265,'mobile'=>18792485338,'parent_mobile'=>'13634309717', ],257129=>[ 'member_id'=>266,'mobile'=>18806403007,'parent_mobile'=>'13944820845', ],259130=>[ 'member_id'=>267,'mobile'=>18419953254,'parent_mobile'=>'13944941903', ],263131=>[ 'member_id'=>268,'mobile'=>13694308073,'parent_mobile'=>'13184603877', ],264132=>[ 'member_id'=>269,'mobile'=>17073082796,'parent_mobile'=>'13944941903', ],266133=>[ 'member_id'=>270,'mobile'=>13894854645,'parent_mobile'=>'13634309717', ],268134=>[ 'member_id'=>271,'mobile'=>13821527507,'parent_mobile'=>'13634309717', ],270135=>[ 'member_id'=>272,'mobile'=>17090057468,'parent_mobile'=>'13184603877', ],272136=>[ 'member_id'=>273,'mobile'=>17615833909,'parent_mobile'=>'13184603877', ],278137=>[ 'member_id'=>274,'mobile'=>13405499885,'parent_mobile'=>'17862263870', ],283138=>[ 'member_id'=>275,'mobile'=>15169197317,'parent_mobile'=>'15064105615', ],287139=>[ 'member_id'=>276,'mobile'=>17090056401,'parent_mobile'=>'13184603877', ],288140=>[ 'member_id'=>277,'mobile'=>15143162697,'parent_mobile'=>'13843148191', ],289141=>[ 'member_id'=>278,'mobile'=>18210258620,'parent_mobile'=>'13944941903', ],293142=>[ 'member_id'=>279,'mobile'=>13058097528,'parent_mobile'=>'13944941903', ],296143=>[ 'member_id'=>280,'mobile'=>17729319376,'parent_mobile'=>'13634309717', ],298144=>[ 'member_id'=>281,'mobile'=>13793928063,'parent_mobile'=>'13405499885', ],305145=>[ 'member_id'=>282,'mobile'=>13500774951,'parent_mobile'=>'18513501540', ],312146=>[ 'member_id'=>283,'mobile'=>18678851318,'parent_mobile'=>'0', ],314147=>[ 'member_id'=>284,'mobile'=>15700005876,'parent_mobile'=>'13634309717', ],318148=>[ 'member_id'=>285,'mobile'=>18954180531,'parent_mobile'=>'13475980909', ],319149=>[ 'member_id'=>286,'mobile'=>15092875567,'parent_mobile'=>'15069909799', ],323150=>[ 'member_id'=>287,'mobile'=>13625395186,'parent_mobile'=>'17862263870', ],324151=>[ 'member_id'=>288,'mobile'=>18865775552,'parent_mobile'=>'13156141887', ],327152=>[ 'member_id'=>289,'mobile'=>15054903535,'parent_mobile'=>'17862263870', ],329153=>[ 'member_id'=>290,'mobile'=>13604305309,'parent_mobile'=>'13634309717', ],335154=>[ 'member_id'=>291,'mobile'=>18853427832,'parent_mobile'=>'15275159628', ],338155=>[ 'member_id'=>292,'mobile'=>18766126715,'parent_mobile'=>'15253131065', ],341156=>[ 'member_id'=>293,'mobile'=>15000222725,'parent_mobile'=>'18396850121', ],344157=>[ 'member_id'=>294,'mobile'=>13386449781,'parent_mobile'=>'15634098838', ],346158=>[ 'member_id'=>295,'mobile'=>18315681575,'parent_mobile'=>'17862263870', ],347159=>[ 'member_id'=>296,'mobile'=>15854122223,'parent_mobile'=>'18805311995', ],348160=>[ 'member_id'=>297,'mobile'=>15705487123,'parent_mobile'=>'13287701331', ],353161=>[ 'member_id'=>298,'mobile'=>18637341257,'parent_mobile'=>'15853185521', ],357162=>[ 'member_id'=>299,'mobile'=>18105309448,'parent_mobile'=>'15000633450', ],360163=>[ 'member_id'=>300,'mobile'=>15339955333,'parent_mobile'=>'15853185521', ],362164=>[ 'member_id'=>301,'mobile'=>18663777795,'parent_mobile'=>'15339955333', ],370165=>[ 'member_id'=>302,'mobile'=>15254825577,'parent_mobile'=>'15339955333', ],388167=>[ 'member_id'=>303,'mobile'=>18599062995,'parent_mobile'=>'13184603877', ],395168=>[ 'member_id'=>304,'mobile'=>13605372255,'parent_mobile'=>'13184603877', ],396169=>[ 'member_id'=>305,'mobile'=>15733437339,'parent_mobile'=>'13184603877', ],418170=>[ 'member_id'=>306,'mobile'=>13386476959,'parent_mobile'=>'18562060599', ],424171=>[ 'member_id'=>307,'mobile'=>15863997529,'parent_mobile'=>'18375491507', ],429172=>[ 'member_id'=>308,'mobile'=>13209550597,'parent_mobile'=>'13389553330', ],431173=>[ 'member_id'=>309,'mobile'=>18053181983,'parent_mobile'=>'15854122223', ],435174=>[ 'member_id'=>310,'mobile'=>13335199366,'parent_mobile'=>'18954180531', ],437175=>[ 'member_id'=>311,'mobile'=>17662533413,'parent_mobile'=>'13365463038', ],443176=>[ 'member_id'=>312,'mobile'=>15865281321,'parent_mobile'=>'13386449781', ],444177=>[ 'member_id'=>313,'mobile'=>13937017057,'parent_mobile'=>'18369772838', ],445178=>[ 'member_id'=>314,'mobile'=>15094815593,'parent_mobile'=>'17662533413', ],448179=>[ 'member_id'=>315,'mobile'=>15093562979,'parent_mobile'=>'15615622982', ],451180=>[ 'member_id'=>316,'mobile'=>18660132310,'parent_mobile'=>'18954180531', ],452181=>[ 'member_id'=>317,'mobile'=>13953186663,'parent_mobile'=>'15275159628', ],454182=>[ 'member_id'=>318,'mobile'=>15199115225,'parent_mobile'=>'18599062995', ],457183=>[ 'member_id'=>319,'mobile'=>18205003629,'parent_mobile'=>'15615622982', ],463184=>[ 'member_id'=>320,'mobile'=>13012977881,'parent_mobile'=>'18363231678', ],464185=>[ 'member_id'=>321,'mobile'=>18027655426,'parent_mobile'=>'15615622982', ],465186=>[ 'member_id'=>322,'mobile'=>17362180250,'parent_mobile'=>'0', ],470187=>[ 'member_id'=>323,'mobile'=>13581139393,'parent_mobile'=>'17662533413', ],471188=>[ 'member_id'=>324,'mobile'=>15376240678,'parent_mobile'=>'17862263870', ],475189=>[ 'member_id'=>325,'mobile'=>15153822125,'parent_mobile'=>'15562660718', ],476190=>[ 'member_id'=>326,'mobile'=>15966022882,'parent_mobile'=>'15562660718', ],479191=>[ 'member_id'=>327,'mobile'=>15205415885,'parent_mobile'=>'13287701331', ],480192=>[ 'member_id'=>328,'mobile'=>18763845511,'parent_mobile'=>'15866601345', ],486194=>[ 'member_id'=>329,'mobile'=>13650201396,'parent_mobile'=>'13435456791', ],487195=>[ 'member_id'=>330,'mobile'=>15909558540,'parent_mobile'=>'13209550597', ],490196=>[ 'member_id'=>331,'mobile'=>15660446288,'parent_mobile'=>'15318845382', ],491197=>[ 'member_id'=>332,'mobile'=>15066669499,'parent_mobile'=>'15205415885', ],500199=>[ 'member_id'=>333,'mobile'=>15562573153,'parent_mobile'=>'18764092012', ],543200=>[ 'member_id'=>334,'mobile'=>18615598001,'parent_mobile'=>'18764092012', ],554201=>[ 'member_id'=>335,'mobile'=>15662795576,'parent_mobile'=>'0', ],555202=>[ 'member_id'=>336,'mobile'=>15098995870,'parent_mobile'=>'18865767066', ],568204=>[ 'member_id'=>337,'mobile'=>18765322430,'parent_mobile'=>'0', ],616205=>[ 'member_id'=>338,'mobile'=>13843148191,'parent_mobile'=>'13184603877', ],666206=>[ 'member_id'=>339,'mobile'=>17094595688,'parent_mobile'=>'13184603877', ],701207=>[ 'member_id'=>340,'mobile'=>18663771925,'parent_mobile'=>'13287701331', ],703208=>[ 'member_id'=>341,'mobile'=>15364818699,'parent_mobile'=>'17615833909', ],734209=>[ 'member_id'=>342,'mobile'=>18554230826,'parent_mobile'=>'13335272365', ],735210=>[ 'member_id'=>343,'mobile'=>15275485757,'parent_mobile'=>'13355487399', ],737211=>[ 'member_id'=>344,'mobile'=>13012995024,'parent_mobile'=>'13287701331', ],738212=>[ 'member_id'=>345,'mobile'=>15866923499,'parent_mobile'=>'17862263870', ],859213=>[ 'member_id'=>346,'mobile'=>13954655741,'parent_mobile'=>'18562060599', ],923214=>[ 'member_id'=>347,'mobile'=>13583163993,'parent_mobile'=>'15854122223', ],925215=>[ 'member_id'=>348,'mobile'=>17686260777,'parent_mobile'=>'18853854569', ],935216=>[ 'member_id'=>349,'mobile'=>13395387767,'parent_mobile'=>'15269699695', ],936217=>[ 'member_id'=>350,'mobile'=>18660894145,'parent_mobile'=>'13581139393', ],939218=>[ 'member_id'=>351,'mobile'=>15192995788,'parent_mobile'=>'0', ],943219=>[ 'member_id'=>352,'mobile'=>18007112113,'parent_mobile'=>'0', ],949220=>[ 'member_id'=>353,'mobile'=>15006836667,'parent_mobile'=>'15318845382', ],951221=>[ 'member_id'=>354,'mobile'=>15806615880,'parent_mobile'=>'13475980909', ],952222=>[ 'member_id'=>355,'mobile'=>13685485830,'parent_mobile'=>'13355487399', ],953223=>[ 'member_id'=>356,'mobile'=>17664512630,'parent_mobile'=>'17664394735', ],955224=>[ 'member_id'=>357,'mobile'=>13539064225,'parent_mobile'=>'13435456791', ],957226=>[ 'member_id'=>358,'mobile'=>15550011190,'parent_mobile'=>'0', ],958227=>[ 'member_id'=>359,'mobile'=>15165011853,'parent_mobile'=>'0', ],962228=>[ 'member_id'=>360,'mobile'=>13953843784,'parent_mobile'=>'18853854569', ],963229=>[ 'member_id'=>361,'mobile'=>13761992021,'parent_mobile'=>'15253131065', ],964230=>[ 'member_id'=>362,'mobile'=>17806175760,'parent_mobile'=>'0', ],966231=>[ 'member_id'=>363,'mobile'=>13584900140,'parent_mobile'=>'13761992021', ],973232=>[ 'member_id'=>364,'mobile'=>15016881716,'parent_mobile'=>'13435456791', ],974233=>[ 'member_id'=>365,'mobile'=>13583885160,'parent_mobile'=>'0', ],977234=>[ 'member_id'=>366,'mobile'=>15264107802,'parent_mobile'=>'0', ],978235=>[ 'member_id'=>367,'mobile'=>18615543099,'parent_mobile'=>'13287701331', ],979236=>[ 'member_id'=>368,'mobile'=>17093197921,'parent_mobile'=>'13944941903', ],980237=>[ 'member_id'=>369,'mobile'=>15069037280,'parent_mobile'=>'18764092012', ],981238=>[ 'member_id'=>370,'mobile'=>13256752222,'parent_mobile'=>'15205415885', ],982239=>[ 'member_id'=>371,'mobile'=>18532030963,'parent_mobile'=>'13184603877', ],983240=>[ 'member_id'=>372,'mobile'=>13562820199,'parent_mobile'=>'15275159628', ],985241=>[ 'member_id'=>373,'mobile'=>13562883288,'parent_mobile'=>'15275485757', ],986242=>[ 'member_id'=>374,'mobile'=>18853748636,'parent_mobile'=>'13365463038', ],988243=>[ 'member_id'=>375,'mobile'=>15806262583,'parent_mobile'=>'13584900140', ],989244=>[ 'member_id'=>376,'mobile'=>15666081141,'parent_mobile'=>'17662533413', ],990245=>[ 'member_id'=>377,'mobile'=>17083240285,'parent_mobile'=>'13184603877', ],991246=>[ 'member_id'=>378,'mobile'=>13027629915,'parent_mobile'=>'18599062995', ],992247=>[ 'member_id'=>379,'mobile'=>15154128637,'parent_mobile'=>'15853185521', ],996248=>[ 'member_id'=>380,'mobile'=>18513889628,'parent_mobile'=>'13287701331', ],997249=>[ 'member_id'=>381,'mobile'=>15963881985,'parent_mobile'=>'13386476959', ],1000250=>[ 'member_id'=>382,'mobile'=>13562840517,'parent_mobile'=>'13287701331', ],1004251=>[ 'member_id'=>383,'mobile'=>13722481217,'parent_mobile'=>'15733437339', ],1009252=>[ 'member_id'=>384,'mobile'=>13515487681,'parent_mobile'=>'15275485757', ],1011253=>[ 'member_id'=>385,'mobile'=>13538632546,'parent_mobile'=>'13435456791', ],1013254=>[ 'member_id'=>386,'mobile'=>15168981665,'parent_mobile'=>'0', ],1016255=>[ 'member_id'=>387,'mobile'=>13475388987,'parent_mobile'=>'15376222956', ],1018256=>[ 'member_id'=>388,'mobile'=>18796435515,'parent_mobile'=>'18663726136', ],1025257=>[ 'member_id'=>389,'mobile'=>15726153305,'parent_mobile'=>'15562660718', ],1026258=>[ 'member_id'=>390,'mobile'=>18615578805,'parent_mobile'=>'15253115251', ],1027259=>[ 'member_id'=>391,'mobile'=>15563352665,'parent_mobile'=>'15066663318', ],1029260=>[ 'member_id'=>392,'mobile'=>13271368787,'parent_mobile'=>'18738718890', ],1031261=>[ 'member_id'=>393,'mobile'=>13552763685,'parent_mobile'=>'18764092012', ],1032262=>[ 'member_id'=>394,'mobile'=>18953899013,'parent_mobile'=>'18764092012', ],1033263=>[ 'member_id'=>395,'mobile'=>18610912524,'parent_mobile'=>'18764092012', ],1035264=>[ 'member_id'=>396,'mobile'=>18853359210,'parent_mobile'=>'15165011853', ],1036265=>[ 'member_id'=>397,'mobile'=>18235561676,'parent_mobile'=>'15866683365', ],1038266=>[ 'member_id'=>398,'mobile'=>13453194290,'parent_mobile'=>'17615833909', ],1042267=>[ 'member_id'=>399,'mobile'=>15901824848,'parent_mobile'=>'13584900140', ],1043268=>[ 'member_id'=>400,'mobile'=>15963937501,'parent_mobile'=>'15069909799', ],1047269=>[ 'member_id'=>401,'mobile'=>15866723333,'parent_mobile'=>'15866601345', ],1048270=>[ 'member_id'=>402,'mobile'=>15964891256,'parent_mobile'=>'15963937501', ],1052271=>[ 'member_id'=>403,'mobile'=>13580769699,'parent_mobile'=>'13435456791', ],1054272=>[ 'member_id'=>404,'mobile'=>13518685754,'parent_mobile'=>'15966022882', ],1055273=>[ 'member_id'=>405,'mobile'=>13583881863,'parent_mobile'=>'15966022882', ],1059274=>[ 'member_id'=>406,'mobile'=>15318066279,'parent_mobile'=>'15866601345', ],1060275=>[ 'member_id'=>407,'mobile'=>18615405201,'parent_mobile'=>'15098725525', ],1061276=>[ 'member_id'=>408,'mobile'=>15151632033,'parent_mobile'=>'13584900140', ],1062277=>[ 'member_id'=>409,'mobile'=>18913245177,'parent_mobile'=>'13584900140', ],1065278=>[ 'member_id'=>410,'mobile'=>13356265055,'parent_mobile'=>'15069145726', ],1069279=>[ 'member_id'=>411,'mobile'=>18888277985,'parent_mobile'=>'0', ],1070280=>[ 'member_id'=>412,'mobile'=>15966025397,'parent_mobile'=>'15275485757', ],1071281=>[ 'member_id'=>413,'mobile'=>18914045116,'parent_mobile'=>'13584900140', ],1072282=>[ 'member_id'=>414,'mobile'=>18710781144,'parent_mobile'=>'15253131065', ],1076283=>[ 'member_id'=>415,'mobile'=>15063371622,'parent_mobile'=>'18764092012', ],1077284=>[ 'member_id'=>416,'mobile'=>13953973228,'parent_mobile'=>'13562905145', ],1078285=>[ 'member_id'=>417,'mobile'=>17710099280,'parent_mobile'=>'18805311995', ],1079286=>[ 'member_id'=>418,'mobile'=>15275183787,'parent_mobile'=>'18805311995', ],1080287=>[ 'member_id'=>419,'mobile'=>15169092388,'parent_mobile'=>'18805311995', ],1081288=>[ 'member_id'=>420,'mobile'=>18253259610,'parent_mobile'=>'18805311995', ],1086289=>[ 'member_id'=>421,'mobile'=>15763733992,'parent_mobile'=>'18853748636', ],1090290=>[ 'member_id'=>422,'mobile'=>18766114268,'parent_mobile'=>'13505415606', ],1091291=>[ 'member_id'=>423,'mobile'=>17826086662,'parent_mobile'=>'18660894145', ],1096292=>[ 'member_id'=>424,'mobile'=>13962651680,'parent_mobile'=>'13584900140', ],1101293=>[ 'member_id'=>425,'mobile'=>13751397699,'parent_mobile'=>'13435456791', ],1103294=>[ 'member_id'=>426,'mobile'=>15318343134,'parent_mobile'=>'18562060599', ],1104295=>[ 'member_id'=>427,'mobile'=>15662654400,'parent_mobile'=>'15318828885', ],1106297=>[ 'member_id'=>428,'mobile'=>15858517368,'parent_mobile'=>'15205415885', ],1107298=>[ 'member_id'=>429,'mobile'=>15050258956,'parent_mobile'=>'15253131065', ],1110299=>[ 'member_id'=>430,'mobile'=>15806652957,'parent_mobile'=>'13287701331', ],1113300=>[ 'member_id'=>431,'mobile'=>13969039400,'parent_mobile'=>'15066663318', ],1115301=>[ 'member_id'=>432,'mobile'=>15153884156,'parent_mobile'=>'15662003222', ],1116302=>[ 'member_id'=>433,'mobile'=>13465388382,'parent_mobile'=>'15662003222', ],1118303=>[ 'member_id'=>434,'mobile'=>13733676600,'parent_mobile'=>'15901824848', ],1119304=>[ 'member_id'=>435,'mobile'=>13838466753,'parent_mobile'=>'15901824848', ],1120305=>[ 'member_id'=>436,'mobile'=>13872811625,'parent_mobile'=>'15901824848', ],1126306=>[ 'member_id'=>437,'mobile'=>13280077128,'parent_mobile'=>'18853748636', ],1127307=>[ 'member_id'=>438,'mobile'=>13854717011,'parent_mobile'=>'18764092012', ],1128308=>[ 'member_id'=>439,'mobile'=>15098736816,'parent_mobile'=>'0', ],1129309=>[ 'member_id'=>440,'mobile'=>13581800768,'parent_mobile'=>'0', ],1133310=>[ 'member_id'=>441,'mobile'=>15066628002,'parent_mobile'=>'0', ],1142312=>[ 'member_id'=>442,'mobile'=>18654517922,'parent_mobile'=>'18653158357', ],1144313=>[ 'member_id'=>443,'mobile'=>13589015287,'parent_mobile'=>'15318828885', ],1145314=>[ 'member_id'=>444,'mobile'=>15564266999,'parent_mobile'=>'13287701331', ],1146315=>[ 'member_id'=>445,'mobile'=>13156117534,'parent_mobile'=>'0', ],1151316=>[ 'member_id'=>446,'mobile'=>18575659698,'parent_mobile'=>'13435456791', ],1157319=>[ 'member_id'=>447,'mobile'=>15169041618,'parent_mobile'=>'15098736816', ],1158320=>[ 'member_id'=>448,'mobile'=>13515496425,'parent_mobile'=>'13562905145', ],1160321=>[ 'member_id'=>449,'mobile'=>18335610861,'parent_mobile'=>'18637341257', ],1164322=>[ 'member_id'=>450,'mobile'=>15269118108,'parent_mobile'=>'13156117534', ],1165323=>[ 'member_id'=>451,'mobile'=>15069067813,'parent_mobile'=>'13156117534', ],1167324=>[ 'member_id'=>452,'mobile'=>13608925317,'parent_mobile'=>'18653158357', ],1169325=>[ 'member_id'=>453,'mobile'=>18823106198,'parent_mobile'=>'13435456791', ],1171326=>[ 'member_id'=>454,'mobile'=>15185005315,'parent_mobile'=>'15901824848', ],1175327=>[ 'member_id'=>455,'mobile'=>15378993218,'parent_mobile'=>'15726108129', ],1187329=>[ 'member_id'=>456,'mobile'=>13225486888,'parent_mobile'=>'18888277985', ],1188330=>[ 'member_id'=>457,'mobile'=>13256177960,'parent_mobile'=>'18766411300', ],1189331=>[ 'member_id'=>458,'mobile'=>15688881344,'parent_mobile'=>'13156117534', ],1191332=>[ 'member_id'=>459,'mobile'=>18538952660,'parent_mobile'=>'15253131065', ],1193333=>[ 'member_id'=>460,'mobile'=>17616533563,'parent_mobile'=>'15066663318', ],1194334=>[ 'member_id'=>461,'mobile'=>13605316281,'parent_mobile'=>'13396411610', ],1195335=>[ 'member_id'=>462,'mobile'=>15033871898,'parent_mobile'=>'15733437339', ],1196336=>[ 'member_id'=>463,'mobile'=>15303206148,'parent_mobile'=>'15733437339', ],1198337=>[ 'member_id'=>464,'mobile'=>15882038886,'parent_mobile'=>'15033871898', ],1205338=>[ 'member_id'=>465,'mobile'=>18274356707,'parent_mobile'=>'15901824848', ],1207339=>[ 'member_id'=>466,'mobile'=>15138939772,'parent_mobile'=>'13027629915', ],1210340=>[ 'member_id'=>467,'mobile'=>18530899082,'parent_mobile'=>'13027629915', ]];
		foreach ($arr as $k => $v) {
			$relation_type=1;
			if($v['parent_mobile']){
				$connnect_xj=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/wallet#utf8');

				$parent_info=$connnect_xj->query('select * from wt_member where member_mobile='.$v['parent_mobile']);
				// var_dump($parent_info);die;
				if($parent_info){
					$parent_id=$parent_info[0]['member_id'];
					// $relation_type=$parent_info->relation_type;
				}else{
					$parent_id=0;
				}
			}else{
				$parent_id=0;
			}
			$MemberRelation= new MemberRelation([
        	 	 'relation_member_id'   => $v['member_id'],
        	 	 'relation_parent_id'	    =>$parent_id,
        	 	 'relation_type'		=> $relation_type,
    		]);

    		$MemberRelation->save(); 
		}
	}
}