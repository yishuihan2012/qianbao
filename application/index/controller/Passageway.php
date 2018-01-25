<?php
/**
 *  @version Passageway controller / 通道控制器
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-11-24 10:22:05
 *   @return 
 */
namespace app\index\controller;
use app\index\model\Passageway as Passageways;
use app\index\model\PassagewayItem;
use app\index\model\MemberGroup;
use app\index\model\Cashout;
use app\index\model\CreditCard;
use app\index\model\Member;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;
use think\Loader;
use think\Db;

class Passageway extends Common{
	protected	$order_state=['1'=>'待支付','2'=>'成功','-1'=>'失败','-2'=>'超时'];
	 #通道列表
	 public function index()
	 {
		 if($this->admin['adminster_group_id']==5){
	 	 #查询通道列表分页
		 	#代理商不显示禁用的通道
		 	$passageway=Passageways::order('passageway_id','desc')->where('passageway_state',1)->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		 }else{
		 	 $passageway=Passageways::order('passageway_id','desc')->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		 }
 		 $this->assign('button', ['text'=>'新增通道', 'link'=>url('/index/passageway/creat'), 'modal'=>'modal']);
 		 $this->assign('passageway_list', $passageway);
		 #渲染视图
		 return view('admin/passageway/index');
	 }
	 #通道对会员组税率调整
	 public function rate()
	 {
	  	 #获取通道
	  	 if(!Request::instance()->param('id'))
	  	 	 return '参数错误';
	 	 if(Request::instance()->isPost())
	 	 {
	 	 	 #获取提交的数据
	 	 	 $post=Request::instance()->post();
	 	 	 $result=true;
 	 	 	//取出通道信息
 	 	 	$passageway=Passageways::get(Request::instance()->param('id'));
 	 	 	$data=[];
 	 	 	$content=false;//错误提示
 	 	 	//遍历提交数据
 	 	 	foreach ($post as $k => $v) {
	 	 		#拆分Key键
	 	 	 	$group_id=strrev(strstr(strrev($k),strrev('_'),true));
	 	 	 	$key_fix=strtok($k,'_');
	 	 	 	//该通道为套现 则丢弃 代还数据 否则 丢弃 套现数据
	 	 	 	#杨注释掉的
	 	 	 	if($passageway->passageway_also==1){
	 	 	 		if($key_fix=='also')continue;
	 	 	 	}else{
	 	 	 		if($key_fix=='rate')continue;
	 	 	 	}
	 	 	 	//以用户组为键 转储到data
	 	 	 	$data[$group_id]['item_'.$key_fix]=$v;
 	 	 	} 	
 	 	 	// echo "<pre>";
 	 	 	// print_r($data);die;
 	 	 	// halt($passageway->passageway_mech);
 	 	 	 #查询库中是否存在数据
 	 	 	 $passage=PassagewayItem::where(['item_passageway'=>Request::instance()->param('id')])->select();
 	 	 	//  echo "<pre>";
 	 	 	// print_r($passage);die;
 	 	 	 if($passage){
 	 	 	 	//针对每条数据执行 (每条对应一个用户组)
 	 	 	 	foreach ($passage as $k => $v) {
 	 	 	 		//若该条对应的用户组 在post中存在 (正常情况会存在)
 	 	 	 		if(isset($data[$v['item_group']])){
 	 	 	 			 $haschange=false;
 	 	 	 			 //遍历该用户组post数据
 	 	 	 			 foreach ($data[$v['item_group']] as $key => $value) {
 	 	 	 				 //若与数据库中的数据不一致，则需要更新
 	 	 	 				 if($value!=$v[$key])
 	 	 	 					 $haschange=true;
 	 	 	 			 }
 	 	 	 			//开始更新
 	 	 	 			if($haschange){
				           Db::startTrans();
				           try{
	 	 	 	 				$PassagewayItem = new PassagewayItem();
	 	 	 	 				$wheres['item_id'] = $v['item_id'];
	 	 	 	 				$PassagewayItem->where($wheres)->update($data[$v['item_group']]);
	 	 	 	 				//取出该用户组下所有会员
	 	 	 	 				// $members=db('member')->where('member_group_id',$v['item_group'])->select();
	 	 	 	 				$members=Member::haswhere('membernet',$passageway->passageway_no." != ''")->where(['member_group_id'=>$v['item_group']])->select();
	 	 	 	 				 //遍历进行 第三方资料变更 
	 	 	 	 				foreach($members as $member){
	 	 	 	 					// $membernet=db('member_net')->where('net_member_id',$member['member_id'])->find();
	 	 	 	 					// if(empty($membernet[$passageway->passageway_no])){
	 	 	 	 					// 	continue;
	 	 	 	 					// }
	 	 	 	 					//修改费率 代还通道  米刷代还(1是米刷套现，不需要更改费率)
									if($passageway->passageway_id!=1){

							 			 $Membernetsedit=new \app\api\controller\Membernetsedit($member['member_id'],$passageway->passageway_id,'M03','',$member['member_mobile']);
							 			 $method=$passageway->passageway_method;
							 			 $success=$Membernetsedit->$method();
						 	 	 		//通过是否存在返回更新
						 	 	 		if($success!==true){
						 	 	 			$content=['type'=>'warning','msg'=>$success];
						 	 	 			break;
						 	 	 		}
						 	 	 	}
	 	 	 	 				}
	 	 	 	 				if($content){
	 	 	 	 					Db::rollback();
	 	 	 	 					break;
	 	 	 	 				}else{
					           		Db::commit();
	 	 	 	 				}
				           } catch (\Exception $e) {
				                Db::rollback();
			 	 	 			$content=['type'=>'warning','msg'=>$e->getMessage()];
			 	 	 			break;
				           }
 	 	 	 			}
 	 	 	 			#剔除已使用的数据
 	 	 	 			unset($data[$v['item_group']]);
 	 	 	 		}
 	 	 	 	}
 	 	 	 	
 	 	 	 }
 	 	 	 //还有数据的时候 就新增数据
 	 	 	 if(count($data)>0){
 	 	 	 	$newData=[];
 	 	 	 	foreach ($data as $k => $v) {
 	 	 	 		//组合单条数据
 	 	 	 		$v['item_group']=$k;
 	 	 	 		$v['item_passageway']=$passageway->passageway_id;
 	 	 	 		$newData[]=$v;
 	 	 	 	}
 	 	 	 	$PassagewayItem=new PassagewayItem();
 	 	 	 	$result=$PassagewayItem->allowField(true)->saveAll($newData);
 	 	 	 	if(!$result)
 	 	 	 		$content=['type'=>'warning','msg'=>'税率新增失败'];
 	 	 	 }
		 	 $content = $content ? $content : ['type'=>'success','msg'=>'税率调整成功'];
		 	  // ['type'=>'warning','msg'=>'税率调整失败'];
		 	 Session::set('jump_msg', $content);
		 	 $this->redirect($this->history['0']);
	 	 }
	  	 #查询出当前通道对会员组的原始税率
	  	 $list=PassagewayItem::where('item_passageway',Request::instance()->param('id'))->select();
	  	 $this->assign('list', $list);
	  	 #查询出所有的用户组
	  	 $group=MemberGroup::all();
	  	 $this->assign('group', $group);
	  	 $this->assign('id', Request::instance()->param('id'));
	  	 return view('admin/passageway/rate');
	 }

	 #新增通道
	 public function creat()
	 {
	 	 if(Request::instance()->isPost())
	 	 {
			 #验证器验证 触发Add事件验证
			 #$validate = Loader::validate('VillageValidate');
			 #如果验证不通过
			 #if(!$validate->check($_POST)){
			      #绑定表单值
				 #$this->assign('category', Request::instance()->Post());
				 #传回错误信息
				 #$this->assign('errormsg', $validate->getError());
				 #加载提交视图
				 #return view('admin/category/creat');
				 #exit;
			 #}
			 #验证器验证成功

	 	 			$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符
					$strlen = 52;
					while(5 > $strlen){
						$str .= $str;
						$strlen -= 52; 
					}
					$str = str_shuffle($str);
					$alert=substr($str,0,5);

					$_POST['passageway_no']=$alert;


	 	 		$sql="ALTER TABLE `wt_member_net` ADD `".$_POST['passageway_no']."` varchar(255) COMMENT '".$_POST['passageway_name']."'";
	 	 		Db::query($sql);



			 $passageway = new Passageways($_POST);
			 $result = $passageway->allowField(true)->save();
			 #数据是否提交成功
			 $content = $result ? ['type'=>'success','msg'=>'通道添加成功'] : ['type'=>'warning','msg'=>'通道添加失败'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('Passageway/index');
	 	 }
	 	 return view('admin/passageway/creat');
	 }


	 #修改通道
	 public function edit(Request $request)
	 {
	 	#获取到详细信息
		 $passageways = Passageways::get(Request::instance()->param('id'));
		 #提交更改信息
		 if(Request::instance()->isPost())
		 {
			 #验证器验证 触发Add事件验证
			 #$validate = Loader::validate('VillageValidate');
			 #如果验证不通过
			 #if(!$validate->check($_POST)){
				 #传递当前信息源去视图
				 #$this->assign('category', $categoryArray);
	 	 		 #$this->assign('category_list', $category_list);
		 		 #$this->assign('secend_category', $secend_category);
			      #传回错误信息
			      #$this->assign('errormsg', $validate->getError());
			      #加载提交视图
			      #return view('admin/category/edit');
			    	 #exit;
			 #}
			 $Passageways =Passageways::get(Request::instance()->param('id'));
			 $result= $Passageways->allowField(true)->save($_POST);
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/Passageway/index');
		 }
		 #传递当前信息源去视图
		 $this->assign('passageways', $passageways);
		 return view('admin/passageway/edit');
	 }


	 #删除文章
	 public function remove()
	 {
	 	 $article = Articles::get(Request::instance()->param('id'));
		 $result = $article->delete();
		 #数据是否提交成功
		 $content = ($result===false) ? ['type'=>'error','msg'=>'文章删除失败'] : ['type'=>'success','msg'=>'文章删除成功'];
		 Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect($this->history['1']);
	 }

	 #获取一级分类下的二级分类
	 public function getCategory()
	 {
	 	$category_list=ArticleCategorys::where('category_parent', Request::instance()->param('id'))->select();
	 	echo json_encode($category_list);
	 }

	 #提现设置
	 public function cashout(){

	 	$data = Cashout::with('Passageway')->where('cashout_passageway_id='.Request::instance()->param('id'))->find();

	 	if(empty($data)){
	 		$data=array(
	 			'cashout_passageway_id'=>Request::instance()->param('id'),
	 			'cashout_add_time'=>date("Y-m-d H:i:s",time())
	 		);
	 		$result= Cashout::insert($data);
	 	}elseif(Request::instance()->isPost()){
	 		if(Request::instance()->param('cashout_open')!=1){
	 			$_POST['cashout_open']=0;
	 		}
	 		$cashout =Cashout::get(Request::instance()->param('cashout_id'));

			 $result= $cashout->allowField(true)->save($_POST);
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/passageway/index');die;
	 	}

	 	$data = Cashout::with('passageway')->where('cashout_passageway_id='.Request::instance()->param('id'))->find();

	 	$this->assign('data',$data);
	 	return view('admin/passageway/cashout');
	 }
	 #添加信用卡
	 public function add_credit_card(){
	 	 if(Request::instance()->isPost()){
	 	    $CreditCard = new CreditCard($_POST);
			 $result = $CreditCard->allowField(true)->save();
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'添加失败'] : ['type'=>'success','msg'=>'添加成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/passageway/index');die;
		}
	 	$this->assign("passageway_id",Request::instance()->param('id'));
	 	return view("admin/passageway/add_credit_card");
	 }
	 #查看信用卡列表
	 public function  list_credit_card(){
	 	$CreditCard = new CreditCard();
	 	$where['bank_passageway_id'] = Request::instance()->param('id');
	 	
	 	$list = $CreditCard->where($where)->select();

	 	$this->assign("list",$list);
	 	return view("admin/passageway/list_credit_card");
	 }
	 #删除信用卡号
	public function remove_credit_card(){
		 $CreditCard = new CreditCard();
	 	$where['card_id'] = Request::instance()->param('id');
		 $result = $CreditCard->where($where)->delete();
		 #数据是否提交成功
		 $content = ($result===false) ? ['type'=>'error','msg'=>'文章删除失败'] : ['type'=>'success','msg'=>'文章删除成功'];
		 Session::set('jump_msg', $content);
		 #重定向控制器 跳转到列表页
		 $this->redirect($this->history['1']);
	}
	#获取银行名称
	public function getbank(){
		$where['card_bank'] = ['like',"%".Request::instance()->param('bankname')."%"];
		$list = Db::table("wt_bank_card")->distinct(true)->field("card_bank")->where($where)->select();
		echo json_encode($list);
	}
	#代还设置
	public function also(){
		// $data = Cashout::with('passageway')->where('cashout_passageway_id='.Request::instance()->param('id'))->find();

	 	// if(empty($data)){
	 	// 	$data=array(
	 	// 		'cashout_passageway_id'=>Request::instance()->param('id'),
	 	// 		'cashout_add_time'=>date("Y-m-d H:i:s",time())
	 	// 	);
	 	// 	$result= Cashout::insert($data);
	 	// }else
	 	if(Request::instance()->isPost()){
	 		$cashout = new PassagewayItem();
			 $result = $cashout->allowField(true)->save($_POST);
			 #数据是否提交成功
			 $content = ($result===false) ? ['type'=>'error','msg'=>'修改失败'] : ['type'=>'success','msg'=>'修改成功'];
			 Session::set('jump_msg', $content);
			 #重定向控制器 跳转到列表页
			 $this->redirect('/index/passageway/index');die;
	 	}
	 	$this->assign("item_passageway",Request::instance()->param('id'));
	 	$data = PassagewayItem::with('passageway')->where('item_passageway='.Request::instance()->param('id'))->find();
	 	$member_group_info = MemberGroup::order("group_salt asc")->select();//用户分组数据
	 	$this->assign("member_group_info",$member_group_info);
	 	$this->assign('data',$data);
	 	return view('admin/passageway/also');
	}
	#通道下的交易订单列表
	public function passageway_details($id){
		$passageway=Passageways::get($id);
		$users=db('member')->column('member_id,member_nick');
		$list=[];
		$where=[];
		if(request()->ispost()){
			$r=request()->param();
			if($r['begin'])
				$where['order_update_time']=['between time',[$r['begin'],$r['end']]];
			if($r['order_state'])
				$where['order_state']=$r['order_state'];
			if($r['member_mobile'])
				$where['member_mobile']=['like','%'.$r['member_mobile'].'%'];
			if($r['member_nick'])
				$where['member_nick']=['like','%'.$r['member_nick'].'%'];
		}else{
			$r=[
				'begin'=>'',
				'end'=>'',
				'order_state'=>'',
				'member_mobile'=>'',
				'member_nick'=>'',
			];
		}
		$adminster=session('adminster');
        $group_id=db('auth_group_access')->where('uid',$adminster['id'])->value('group_id');
		//套现
		if($passageway->passageway_also==1){
			$where['order_passway']=$id;
			$where['order_state']=2;
			//运营商
			if($adminster['adminster_user_id'] && $group_id==5){
				$where['order_member']=["in",$adminster['children']];
				$passageway->profit=db('commission')->alias('c')
					->join('cash_order o','c.commission_from=o.order_id')
					->where('c.commission_member_id',$adminster['adminster_user_id'])
					->sum('commission_money');
			}else{
				$passageway->profit=db('commission')->alias('c')
					->join('cash_order o','c.commission_from=o.order_id')
					->where('c.commission_member_id',-1)
					->sum('commission_money');
			}
			$passageway->sum=db('cash_order')->where($where)->sum('order_money');
			$passageway->charge=db('cash_order')->where($where)->sum('order_charge');
			$list=db('cash_order')->alias('o')
				->join('member m','o.order_member=m.member_id')
				->where($where)
				->order('o.order_id desc')
		 	 	->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
			$passageway->fenrun=db('cash_order')->alias('o')
				->join('member m','o.order_member=m.member_id')
				->join('commission c','o.order_id=c.commission_from')
				->where($where)
				->sum('commission_money');
		#代还
		}else{
			$where['order_passway_id']=$id;
			$where['order_status']=2;
			//运营商
			if($adminster['adminster_user_id'] && $group_id==5){
				$where['order_member']=["in",$adminster['children']];
				$passageway->profit=db('commission')->alias('c')
					->join('generation_order o','c.commission_from=o.order_id')
					->where('c.commission_member_id',$adminster['adminster_user_id'])
					->sum('commission_money');
			}else{
				$passageway->profit=db('commission')->alias('c')
					->join('generation_order o','c.commission_from=o.order_id')
					->where('c.commission_member_id',-1)
					->sum('commission_money');
			}
			$passageway->sum=db('generation_order')->where($where)->sum('order_money');
			$passageway->charge=db('generation_order')->where($where)->sum('order_pound');
			$list=db('generation_order')->alias('o')
				->join('member m','o.order_member=m.member_id')
				->where($where)
				->order('o.order_id desc')
		 	 	->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);
		 	 	$where['commission_type']=3;
		 	 	if(isset($where['member_id']))
		 	 		unset($where['member_id']);
			$passageway->fenrun=db('generation_order')->alias('o')
				->join('commission c','o.order_id=c.commission_from')
				->where($where)
				->sum('commission_money');
		}
		$this->assign('r',$r);
		$this->assign('order_state',$this->order_state);
		$this->assign('passageway',$passageway);
		$this->assign('list',$list);
	 	return view('admin/passageway/passageway_details');
	}
	#通道下单个订单详情 
	#id 订单id type =1 套现 =3 代还
	public function passageway_details_info($id,$type){
		$users=db('member')->column('member_id,member_nick');
		if($type==1){
			$order=db('cash_order')->where('order_id',$id)->find();
			$fenrun=db('commission')->where(['commission_type'=>1,'commission_from'=>$id])->select();
			$order['order_state']=$this->order_state[$order['order_state']];
		}
		$level=['直接','间接','三级'];
		foreach ($fenrun as $k => $v) {
			$fenrun[$k]['level']=array_shift($level);
		}
		$this->assign('order',$order);
		$this->assign('users',$users);
		$this->assign('fenrun',$fenrun);
	 	return view('admin/passageway/passageway_details_info');
	}
}
