<?php
/**
 * GetBranch Validate Model / 获取支行 联行号列表参数检验
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-12-4 14:30:23
 * @version $系统配置自动验证 Bill$
 */
namespace app\api\validate;

use think\Validate;

class GetBranch extends Validate
{
	 #验证规则
      protected $rule = [
        	 ['provinceId', 'require', '请选择省份~'],
        	 ['cityId','require', '请选择城市~']
      ];
}