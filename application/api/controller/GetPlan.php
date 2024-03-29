<?php 

 namespace app\api\controller;
 class GetPlan
 {
        public  $rewardMoney="5000";       #总账单金额 单位元
        public  $rewardNum="10";           #总消费次数

        #执行生成还款额算法
    public function splitReward($rewardMoney, $rewardNum, $max, $min)
    {
        #传入红包金额和数量，因为小数在计算过程中会出现很大误差，所以我们直接把金额放大100倍，后面的计算全部用整数进行
        $min = $min * 10;
        $max = $max * 10;
        #预留出一部分钱作为误差补偿，保证每个红包至少有一个最小值
        $this->rewardMoney = $rewardMoney * 10 - $rewardNum * $min;
        $this->rewardNum = $rewardNum;
        #计算出发出红包的平均概率值、精确到小数4位。
        $avgRand = 1 / $this->rewardNum;
        $randArr = array();
        #定义生成的数据总合sum
        $sum = 0;
        $t_count = 0;
        while ($t_count < $rewardNum) {
            #随机产出四个区间的额度
            $c = rand(1, 100);
            if ($c < 15) {
                $t = round(sqrt(mt_rand(1, 1500)));
            } else if ($c < 65) {
                $t = round(sqrt(mt_rand(1500, 6500)));
            } else if ($c < 95) {
                $t = round(sqrt(mt_rand(6500, 9500)));
            } else {
                $t = round(sqrt(mt_rand(9500, 10000)));
            }
            ++$t_count;
            $sum += $t;
            $randArr[] = $t;
        }

        #计算当前生成的随机数的平均值，保留4位小数
        $randAll = round($sum / $rewardNum, 4);

        #为将生成的随机数的平均值变成我们要的1/N，计算一下每个随机数要除以的总基数mixrand。此处可以约等处理，产生的误差后边会找齐
        #总基数 = 均值/平均概率
        $mixrand = round($randAll / $avgRand, 4);

        #对每一个随机数进行处理，并乘以总金额数来得出这个红包的金额。
        $rewardArr = array();
        foreach ($randArr as $key => $randVal) {
            #单个红包所占比例randVal
            $randVal = round($randVal / $mixrand, 4);
            #算出单个红包金额
            $single = floor($this->rewardMoney * $randVal);
            #小于最小值直接给最小值
            if ($single < $min) {
                $single += $min;
            }
            #大于最大值直接给最大值
            if ($single > $max) {
                $single = $max;
            }
            #将红包放入结果数组
            $rewardArr[] = $single;
        }

        #对比红包总数的差异、将差值放在第一个红包上
        $rewardAll = array_sum($rewardArr);
        $rewardArr[0] = $rewardMoney * 10 - ($rewardAll - $rewardArr[0]);#此处应使用真正的总金额rewardMoney，$rewardArr[0]可能小于0

        #第一个红包小于0时,做修正
        if ($rewardArr[0] < 0) {
            rsort($rewardArr);
            $this->add($rewardArr, $min);
        }

        rsort($rewardArr);
        #随机生成的最大值大于指定最大值
        if ($rewardArr[0] > $max) {
            #差额
            $diff = 0;
            foreach ($rewardArr as $k => &$v) {
                if ($v > $max) {
                    $diff += $v - $max;
                    $v = $max;
                } else {
                    break;
                }
            }
            $transfer = round($diff / ($this->rewardNum - $k + 1));
            $this->diff($diff, $rewardArr, $max, $min, $transfer, $k);
        }
        return $rewardArr;
    }


    #处理所有超过最大值的红包
     function diff($diff, &$rewardArr, $max, $min, $transfer, $k)
    {
        #将多余的钱均摊给小于最大值的红包
        for ($i = $k; $i < $this->rewardNum; $i++) {
            #造随机值
            if ($transfer > $min * 20) {
                $aa = rand($min, $min * 20);
                if ($i % 2) {
                    $transfer += $aa;
                } else {
                    $transfer -= $aa;
                }
            }
            if ($rewardArr[$i] + $transfer > $max) continue;
            if ($diff - $transfer < 0) {
                $rewardArr[$i] += $diff;
                $diff = 0;
                break;
            }
            $rewardArr[$i] += $transfer;
            $diff -= $transfer;
        }
        if ($diff > 0) {
            $i++;
            $this->diff($diff, $rewardArr, $max, $min, $transfer, $k);
        }
    }

    #第一个红包小于0,从大红包上往下减
    function add(&$rewardArr, $min)
    {
        foreach ($rewardArr as &$re) 
        {
                $dev = floor($re / $min);
                if ($dev > 2) 
                {
                        $transfer = $min * floor($dev / 2);
                        $re -= $transfer;
                        $rewardArr[$this->rewardNum - 1] += $transfer;
                } elseif ($dev == 2) 
                {
                        $re -= $min;
                        $rewardArr[$this->rewardNum - 1] += $min;
                } else 
                        break;
        }
        if ($rewardArr[$this->rewardNum - 1] > $min || $rewardArr[$this->rewardNum - 1] == $min)
                return;
        else 
                $this->add($rewardArr, $min);
    }
    //根据总金额和次数随机每次金额is_int为空带小数点
    public function get_random_money($money,$num,$is_int=''){
        $count=$num;
        for ($i=0; $i <$num; $i++) { 
            if($i==$num-1){
                $arr[]=$money;
            }else{
                $avage=$money/$count;
                //判断奇偶，
                if($is_int){
                    if($i%2==0){//偶数随机在平均值上
                        $get=ceil(rand($avage,$avage*1.2));
                    }else{//奇数随机在平均值下
                        $get=ceil(rand($avage*0.8,$avage));
                    }
                }else{
                    if($i%2==0){//偶数随机在平均值上
                        $get=ceil(rand($avage,$avage*1.2)).'.'.rand(0,99);
                    }else{//奇数随机在平均值下
                        $get=ceil(rand($avage*0.8,$avage)).'.'.rand(0,99);
                    }
                }
                //判断十百千三位不能相同
                $int_num=intval($get);
                if(strlen($int_num)>2){
                    $first=substr($int_num,-1,1);
                    $second=substr($int_num,-2,1);
                    $third=substr($int_num,-3,1);

                    if($first==$second &&$first==$third){
                        $this->get_random_money($num,$money);
                    }
                }

                $count=$count-1;
                $money=$money-$get;
                $arr[]=$get;
            }
        }
        return $arr;
    }
}