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
        #完成 
        $finish=cache('finish'.$type);
        if($finish)
            return 'has finishd';
        #检测上次进件时间 相隔1分钟以上 开始运行本程序
        $lastmodify=cache('lastmodify'.$type);
        if($lastmodify && $lastmodify > time()-60)
            return 'its running';
        set_time_limit(0);
        ignore_user_abort(true);
        $n=0;
        $passname = $type == 1 ? 'rongbyjf' : 'rongbwjf';
        $passway  = Passageway::get(['passageway_true_name' => $passname]);
        $jump=cache('jump'.$type);

        $members  = db('member')->alias('m')
            ->join('member_net n', 'm.member_id=n.net_member_id')
            ->where('member_cert', 1)
            ->where($passway->passageway_no, "null");
        if($jump){
            $members=$members->where('member_id','not in',$jump);
        }
        $members=$members->limit(50)->select();
        w_log("数量:" . count($members));
        $jump=[];
        foreach ($members as $k => $v) {
            $jump=cache('jump'.$type);
            if($jump && in_array($v['member_id'], $jump))
                continue;
            try {
                $membernets = new Membernets($v['member_id'], $passway->passageway_id);
                $res        = $membernets->rongbangnet();
                if ($res === true) {
                    $res = $membernets->rongbang_in();
                    if (is_array($res)) {
                        cache('lastmodify'.$type,time());
                        w_log($type."ok ".$v['member_mobile'].date('H:i:s'));
                        continue;
                    }else{
                        w_log($type."has done,but not in because".$res.date('H:i:s').$n);
                    }
                }else{
                    w_log($type."not done,because ".$res.date('H:i:s').$n);
                }
                $n++;
            } catch (\Exception $e) {
                w_log($type.'err..'.$v['member_mobile'].date('H:i:s').':'.$n);
            }
            cache('lastmodify'.$type,time());
            $jump[]=$v['member_id'];
            cache('jump'.$type,$jump);
        }
        if($n==0)
            cache('finish'.$type,true);
        w_log($type.'finished,has done count:'.$n);
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
