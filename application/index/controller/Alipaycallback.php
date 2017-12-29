<?php
/**
 * @version  AlipayCallBack 支付宝付费升级回调 
 * @authors John(1160608332@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;
use app\index\model\CashOrder;
use app\index\model\Passageway;
use app\index\model\PassagewayItem;
use app\index\model\Cashout;
use app\index\model\Member;
use app\index\model\MemberRelation;
use app\index\model\MemberGroup;
use app\index\model\MemberNet;
use app\index\model\MemberCert;
use app\index\model\Upgrade;
use think\Request;
class Alipaycallback
{

	 public function callback()
	 {
	     $data = file_get_contents("php://input");
	 	 $data = trim($data);
	 	 file_put_contents('datas1.txt', $data);
    	 file_put_contents('filecontent.txt',$data);
    	 $data = json_decode($data, true);
    	 file_put_contents('success.txt',$data['state']);
    	 // Upgrade::where('upgrade_no='.$post['upgrade_member_id'])
	 	$post['upgrade_member_id']=20;
	 	$post['upgrade_group_id']=3;

    	 #修改会员等级
    	 $member=Member::where('member_id='.$post['upgrade_member_id'])->update(['member_group_id'=>$post['upgrade_group_id']]);

    	 #修改入网
    	 $member_net=MemberNet::where('net_member_id='.$post['upgrade_member_id'])->find();

    	 $passageway=Passageway::where('passageway_status=1 and passageway_also=1')->find();

    	 #查询费率
    	 $rate=PassagewayItem::where('item_passageway='.$passageway['passageway_id'].' and item_group='.$post['upgrade_group_id'])->find();

    	 $member_info=MemberCert::where('cert_member_id='.$post['upgrade_member_id'])->find();

    	 #执行修改入网信息
    	 $arr=mishuaedit($passageway, $rate, $member_info, $member['member_moblie'], $member_net[$passageway['passageway_no']]);
    	 $add_net=MemberNet::where('net_member_id='.$post['upgrade_member_id'])->update($arr);

	 }

}