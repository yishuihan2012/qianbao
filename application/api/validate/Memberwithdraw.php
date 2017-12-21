<?php
/**
 * Memberwithdraw Validate Model / 申请提现所需参数
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-12-4 14:30:23
 * @version $申请提现参数自动验证 Bill$
 */
namespace app\api\validate;

use think\Validate;

class Memberwithdraw extends Validate
{
	 #验证规则
      protected $rule = [
        	 ['thirdPartType', 'require', '请选择提现到账账号~'],
        	 ['payPlatformId','require', '请选择提现通道~'],
        	 ['money','require','请输入提现金额~']
      ];
}