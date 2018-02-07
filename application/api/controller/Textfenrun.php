<?php
 namespace app\api\controller;
 use app\api\controller\Commission;
use app\index\model\CashOrder;
use app\index\model\BankIdent;
 use think\Db;
 use think\Config;
 use think\Request;
 use think\Loader;
use app\index\model\Member;
use app\index\model\MemberCreditcard;
use app\index\model\MemberCashcard;
class Textfenrun 
{
	public function demo()
	{
		$r=input();
		$id=isset($r['id']) ? $r['id'] : 42;
		 $fenrun= new \app\api\controller\Commission();
	 	 $fenrun_result=$fenrun->MemberFenRun($id,'10000',10,1,'交易手续费分润',94);
	 	 //$fenrun_result=$fenrun->MemberCommis(9,'980','会员升级');
	 	 dump($fenrun_result);
	}

	public function editBank()
	{
		$result=BankIdent::all();
		foreach ($result as $key => $value) {
			$icon=str_replace("images", "bank", $value['ident_icon']);
			$res=BankIdent::where('ident_id',$value['ident_id'])->setField('ident_icon',$icon);
		}
	}
	public function make_cash_order(){
		$r=input();
		$id=isset($r['id']) ? $r['id'] : 42;
      	 // $member_info=Member::get($id)->toArray();
      	 $member_info=Member::get($id);
          $creditcard=MemberCreditcard::get(['card_member_id'=>$id]);
      	 $member_cashcard=MemberCashcard::get(['card_member_id'=>$id]);
      	 $also=db('passageway_item')->where(['item_passageway'=>10,'item_group'=>$member_info->member_group_id])->value('item_rate');
      	 $charge=10000*$also/100;
	      $data=array(
	      	 'order_no'=>make_order(),
	      	 'order_thead_no'=>make_order(),
	      	 'order_member' =>$id,
	      	 'order_passway'=>10,
	      	 'order_money'	=>10000,
	      	 'order_charge'	=>$charge,//手续费
	      	 'order_also'		=>$also,
	      	 'order_idcard'	=>$creditcard->card_idcard,
	      	 'order_name'		=>$creditcard->card_name,
	      	 'order_creditcard'=>$creditcard->card_bankno,
	      	 'order_card'		=>$member_cashcard->card_bankno,
	      	 'order_state'		=>2,
	      	 'order_desc'		=>'test',
	      );
	      $data_result=new CashOrder();
	 	 $order_id=$data_result->allowField(true)->insertGetId($data);

		 $fenrun= new \app\api\controller\Commission();
	 	 $fenrun_result=$fenrun->MemberFenRun($id,'10000',10,1,'test',$order_id);
	 	 dump($fenrun_result);
	}
}

