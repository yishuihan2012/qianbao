<?php
/**
 * WalletLog Model / 钱包日志模型
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-09-29 17:14:23
 * @version $钱包日志模型 Bill$
 */
namespace app\index\model;

use think\Db;
use think\Model;

class WalletLog extends Model{
        #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
        //protected $table = 'wt_wallet_log';
        #定义主键信息  可留空 默认主键
        protected $pk 	 = 'log_id';
        #定义自动写入时间字段开启 格式为时间格式
        protected $autoWriteTimestamp = 'datetime';
        #定义时间戳字段名 信息添加时间
        protected $createTime = 'log_add_time';
        #定义时间戳字段名 信息修改时间
        protected $updateTime = false;
        #初始化模型
        protected function initialize()
        {
            #需要调用`Model`的`initialize`方法
            parent::initialize();
            #TODO:自定义的初始化
        }
        #定义相对的模型关联
        public function wallet()
        {
            return $this->belongsTo('Wallet', 'log_wallet_id', 'wallet_id')->bind("wallet_member")->setEagerlyType(0);
        }
        #分页
    public static function pages($uid,$page =1,$data = [],$month = null){
       
        $data['month'] = $month;
        $limit = ($page-1)*10;
        $list=db('wallet_log')->alias('l')
        ->join('wallet w','l.log_wallet_id=w.wallet_id')
        ->where(['w.wallet_member'=>$uid])
        ->where('log_add_time','between time',[$monthstart,$monthend])
        ->order('log_add_time desc')->limit($limit,10)
        ->select();
        foreach ($list as $k => $v) {
            $state=db('withdraw')->where('withdraw_id',$v['log_relation_id'])->value('withdraw_state');
            if($v['log_wallet_type']==1 || $state == -12){
                $data['in'] += $v['log_wallet_amount'];
            }else{
                $data['out'] += $v['log_wallet_amount'];
            }
            switch ($v['log_relation_type']) {
                //提现操作
                case 2:
        
                    if($state)$list[$k]['info']=state_info($state);
                    break;
                default:
                    # code...
                    break;
            }
        }
        $count = db('wallet_log')->alias('l')
        ->join('wallet w','l.log_wallet_id=w.wallet_id')
        ->where(['w.wallet_member'=>$uid])
        ->where('log_add_time','between time',[$monthstart,$monthend])
        ->order('log_add_time desc')
        ->count();
        $allpage = ceil($count/10);
        return ['list' => $list , 'data' => $data , 'allpage' => $allpage];
    }
}