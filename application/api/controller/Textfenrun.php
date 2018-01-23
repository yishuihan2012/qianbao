<?php
 namespace app\api\controller;
 use app\api\controller\Commission;
 use app\index\model\BankIdent;
 use think\Db;
 use think\Config;
 use think\Request;
 use think\Loader;
class Textfenrun 
{
	public function demo()
	{
		 $fenrun= new \app\api\controller\Commission();
	 	 $fenrun_result=$fenrun->MemberFenRun(42,'1000',1,1,'交易手续费分润',94);
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
}

