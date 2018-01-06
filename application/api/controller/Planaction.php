<?php
 // Member controller / Api 会员接口

 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use think\Loader;
 use app\index\model\Member ;
 use app\index\model\MemberGroup;
 use app\index\model\MemberRelation;
 use app\index\model\MemberLogin;
 use app\index\model\System;
 use app\index\model\SmsCode;
 use app\index\model\MemberSuggestion;
 use app\index\model\MemberAccount;
 use app\index\model\MemberTeam;
 use app\index\model\MemberCreditcard;
 use app\index\model\MemberCashcard;
 use app\index\model\ChannelRate;
 use app\index\model\ChannelType;
 use app\index\model\MemberCert;
 use app\index\model\Passageway;
 use app\index\model\PassagewayItem;
 use app\index\model\Wallet;
 use app\index\model\Recomment;
 use app\index\model\Commission;
 use app\index\model\Upgrade;
 use app\index\model\Notice;
 use app\index\model\Announcement;
 
 class Planaction{
 	//修改通道费率，需要重新报备费率的重新报备
 	public function update_passway_rate($params){
 		$Passageway_detail=Passageway::where(['passageway_id'=>]);
 	}
 }
