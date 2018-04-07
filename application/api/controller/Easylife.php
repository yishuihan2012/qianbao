<?php
 namespace app\api\controller;
 use think\Db;
 use app\index\model\Member;
 use app\index\model\System;
 use app\index\model\Wallet;
 use app\index\model\WalletLog;
 use app\index\model\MemberGroup;
 use app\index\model\PassagewayItem;
 use app\index\model\MemberRelation;
 use app\index\model\MemberCert;
 use app\index\model\MemberCashcard;
 use app\index\model\Passageway;
 use app\index\model\Generation;
 use app\index\model\GenerationOrder;
 use app\index\model\Reimbur;
 use app\index\model\MemberNet as MemberNets;
 use app\index\model\MemberCreditcard;
 use app\index\model\BankInfo;
 use app\index\model\MemberCreditPas;
 /**
 *  @version Huilianjinchuang controller / Api 代还入网
 *  @author 许成成(1015571416@qq.com)
 *   @datetime    2018-02-23 15:13:05
 *   @return 
 */
 class Easylife{
 	#1.	商户材料上传
	#2.	商户注册
	#3.	商户结算账户设置
	#4.	商户产品开通
	#5.	商户产品费率修改
	#6.	交易创建
	#7.	交易支付请求
	#8.	交易查询（单笔）
	#9.	服务器异步通知接口
	#10.客户端同步跳转接口
	public function upload_material(){
		
	}
 }

