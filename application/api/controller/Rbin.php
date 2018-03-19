<?php
/**
 *     荣邦批量进件入驻
 */
namespace app\api\controller;

use app\index\model\MemberNet; //入网模型
use app\index\model\Passageway;

class Rbin
{
    #1 有积分 2无积分
    public function in($type = 1)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $n=0;
        $passname = $type == 1 ? 'rongbyjf' : 'rongbwjf';
        $passway  = Passageway::get(['passageway_true_name' => $passname]);
        $members  = db('member')->alias('m')
            ->join('member_net n', 'm.member_id=n.net_member_id')
            ->where('member_cert', 1)
            ->where($passway->passageway_no, "null")
            ->select();
        w_log("数量:" . count($members));
        foreach ($members as $k => $v) {
            try {
                $membernets = new Membernets($v['member_id'], $passway->passageway_id);
                $res        = $membernets->rongbangnet();
                if ($res === true) {
                    $res = $membernets->rongbang_in();
                    if (is_array($res)) {
                        w_log("ok ".$v['member_mobile'].date('H:i:s'));
                    }else{
                        w_log("has done,but not in ".date('H:i:s').$n);
                    }
                }else{
                    w_log("not done ".date('H:i:s').$n);
                }
                $n++;
            } catch (\Exception $e) {
                w_log('err..'.$v['member_mobile'].date('H:i:s').':'.$n);
            }
        }
        w_log('finished,has done count:'.$n);
        echo "finished";
    }
    public function test()
    {
        ignore_user_abort(true);
        for ($i = 0; $i < 10; $i++) {
            sleep(1);
            w_log($i);
        }
        echo "finished";
    }
}
