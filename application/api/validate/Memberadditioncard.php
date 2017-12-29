<?php
/**
 * Memberadditioncard Validate Model / 会员信用卡参数认证
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-12-4 14:30:23
 * @version $系统配置自动验证 Bill$
 */
namespace app\api\validate;

use think\Validate;

class Memberadditioncard extends Validate
{
	 #验证规则
      protected $rule = [
        	 ['creditCardNo', 'require', '请输入信用卡卡号~'],
        	 ['phone','require', '请输入银行预留手机号~'],
        	 ['bank_name', 'require', '请输入银行名称~'],
        	 ['cvv','require','请输入信用卡背面三位CVV号~'],
        	 ['expireDate','require','请输入您的信用卡有效期'],
        	 ['billDate','require','请输入信用卡账单日~'],
        	 ['deadline','require','请输入信用卡最后还款日~'],
        	 // ['isRemind','require','是否提醒您呢~']
      ];
      #定义验证场景
	 protected $scene = [
	 	 //'bind' =>  ['thirdPartType','thirdPartToken'],
	      //'ubind'  =>  ['thirdPartType'],
	 ];
}