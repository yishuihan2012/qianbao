<?php

namespace app\api\controller;
use think\Db;
use think\Config;
use think\Controller;
use app\index\model\Member as Members;
/**
 *  此处放置一些固定的web地址
 */
class Userurl extends Controller
{
      protected $param;
      public $error=0;
      public function __construct()
      {
      		parent::__construct();
           $this->param=request()->param();
            try{
                 if(!isset($this->param['uid']) || empty($this->param['uid']) || !isset($this->param['token']) ||empty($this->param['token']))
                       $this->error=314;
                 #查找到当前用户
                 $member=Members::haswhere('memberLogin',['login_token'=>$this->param['token']])->where('member_id', $this->param['uid'])->find();
                 if(!$member && !$this->error)
                       $this->error=317;
            }catch (\Exception $e) {
                 $this->error=317;
            }
            if($this->error){
				$msg=Config::get('response.'.$this->error) ? Config::get('response.'.$this->error) : "系统错误~";
	            exit(json_encode(['code'=>$this->error, 'msg'=>$msg, 'data'=>'']));
	        }
      }
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:01:55+0800
	 * @version  [专属二维码]
	 * @return   [type]
	 */
	public function exclusive_code(){
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [用户还款计划]
	 * @return   [type]
	 */
	public function repayment_plan_list(){
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [还款计划已完成列表]
	 * @return   [type]
	 */
	public function repayment_history(){
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [还款计划详情]
	 * @return   [type]
	 */
	public function repayment_plan_detail(){
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [消息]
	 * @return   [type]
	 */
	public function notify(){
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [平台公告列表]
	 * @return   [type]
	 */
	public function notify_list(){
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [公告详情]
	 * @return   [type]
	 */
	public function notify_list_detail(){
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [动账交易列表]
	 * @return   [type]
	 */
	public function deal_list(){
		return $this->fetch();
	}
	/**
	 * @Author   Star(794633291@qq.com)
	 * @DateTime 2017-12-25T14:10:55+0800
	 * @version  [平台福利列表]
	 * @return   [type]
	 */
	public function welfare_list(){
		return $this->fetch();
	}
}