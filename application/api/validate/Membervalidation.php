<?php
/**
 * Membervalidation Validate Model / 会员银行卡四元素实名认证接口所需要参数检验
 * @authors 摇耳朵的小布丁 (755969423@qq.com)
 * @date    2017-12-4 14:30:23
 * @version $系统配置自动验证 Bill$
 */
namespace app\api\validate;

use think\Validate;

class Membervalidation extends Validate
{
	 #验证规则
      protected $rule = [
        	 ['card_bankno', 'require', '银行卡号格式不正确~'],
        	 ['name','chs', '姓名格式不正确~'],
        	 ['card_phone', 'length:11,11', '预留手机号格式不正确~'],
        	 ['smsCode','require','验证码不能为空~'],
        	 ['card_idcard', 'require', '身份证号不能为空~'],
        	 ['card_bank_province','require','所属省份没有选择~'],
        	 ['card_bank_city','require','所属城市没有选择~'],
        	 ['card_bank_area','require','所属区域没有选择~'],
        	 ['card_bank_address', 'require', '支行没有选择~'],
        	 ['card_bankname', 'require', '银行名称找不到~'],
      ];
      #定义验证场景
     protected $scene = [
         'creat' =>  ['card_bankno','card_phone','smsCode','card_bank_province','card_bank_city','card_bank_area','card_bank_address','card_bankname','card_idcard'],
          'edit' =>  ['card_bankno','card_phone','smsCode','card_bank_province','card_bank_city','card_bank_area','card_bank_address','card_bankname'],
     ];
}