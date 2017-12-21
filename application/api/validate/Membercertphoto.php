<?php
/**
 * Membercertphoto Validate Model / 更细实名后的身份证 人像等图片信息
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-12-4 14:30:23
 * @version $系统配置自动验证 Bill$
 */
namespace app\api\validate;

use think\Validate;

class Membercertphoto extends Validate
{
	 #验证规则
      protected $rule = [
        	 ['IdPositiveImgUrl', 'require', '请上传身份证正面照~'],
        	 ['IdNegativeImgUrl','require', '请上传身份证反面照~'],
            ['IdPortraitImgUrl','require', '请上传身份证人像照~'],
      ];
}