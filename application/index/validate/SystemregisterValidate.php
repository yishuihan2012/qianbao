<?php
/**
 * Systemregister Validate Model / 系统配置自动验证
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-12-4 14:30:23
 * @version $系统配置自动验证 Bill$
 */
namespace app\index\validate;

use think\Validate;

class SystemregisterValidate extends Validate
{
	 #验证规则
      protected $rule = [
        	 ['open_reg_code', 'boolean', '验证码配置错误'],
        	 ['open_reg_email' , 'boolean', '邮件开启设置错误'],

      ];

      #场景设置 ，不同场景可以使用不同的验证方法
      protected $scene = [
    	 	 #新建信息场景
        	 'add' => ['project_desc_short'], 
      ];

}