<?php
/**
 * Memberaccount Validate Model / 会员第三方账户参数验证
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-12-4 14:30:23
 * @version $系统配置自动验证 Bill$
 */
namespace app\api\validate;

use think\Validate;

class Memberaccount extends Validate
{
	 #验证规则
      protected $rule = [
        	 ['thirdPartType', 'require', '请选择账户绑定类型~'],
        	 ['thirdPartToken','require', '获取不到账号~~'],
      ];
      #定义验证场景
	 protected $scene = [
	 	 'bind' =>  ['thirdPartType','thirdPartToken'],
	      'ubind'  =>  ['thirdPartType'],
	 ];
}