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
 /**
 *  @version Huilianjinchuang controller / Api 代还入网
 *  @author 许成成(1015571416@qq.com)
 *   @datetime    2018-02-23 15:13:05
 *   @return 
 */
 class Huilianjinchuang{
 	protected $url;
 	public function __construct(){
 		$this->url='http://120.77.180.22:8089/v1.0/facade';
 		
 	}
 	/**
 	 * 进件请求
 	 * @return [type] [description]
 	 */
 	public function income($Passageway,$card_id){
 		$card_info=MemberCreditcard::where(['card_id'=>$card_id])->find();
 		if(!$card_info){
 			exit();
 		}
 		$member_info=Member::where(['member_id'=>$card_info['card_member_id']])->find();
 		if(!$member_info){
 			exit();
 		}
 		$bank_name=mb_substr($card_info['card_bankname'],-4,2);
 		// echo $bank_name;die;
 		$BankInfo=BankInfo::where('info_sortname','like','%'.$bank_name.'%')->find();
 		// print_r($BankInfo);die;
 		$agentId=1001001;
 		$idcard=$member_info->membercert->cert_member_idcard;
 		$name=$card_info['card_name'];
 		$bankId=$BankInfo['info_pab'];
 		$bankCard=$card_info['card_bankno'];
 		$bankName=$BankInfo['info_name'];

 		$rate=PassagewayItem::where(['item_passageway'=>$Passageway,'item_group'=>$member_info['member_group_id']])->find();
        $also=($rate->item_also)*10;
        $daikou=($rate->item_charges);
 		$arr=array(
 			'version'=>'1.0',
			'charset'=>'UTF-8',//	编码方式UTF-8
			'agentId'=>$agentId,//受理方预分配的渠道代理商标识
			'nonceStr'=>make_rand_code(),//随机字符串，字符范围a-zA-Z0-9
			'signType'=>'RSA',//签名方式，固定RSA
			'isCompay'=>'0',//对公对私标识0为对私，1为对公
			'idcardType'=>'01',//证件类型 暂只支持 01 身份证
			'idcard'=>$idcard,//证件号码
			'name'=>$name,//姓名
			'phone'=>$member_info['member_mobile'],//手机号
			'bankId'=>$bankId,//联行号
			'bankCard'=>$bankCard,//银行卡号
			'bankName'=>$bankName,//开户行名称
			'bankNo'=>$BankInfo['info_pab'],//开户行代码(PAB)
			'rate'=>$also,//费率‱ ，不小于代理商费率
			'extraFee'=>$daikou,//手续费(分)
			// 'address'=>'',//N(String)	地址
			'remark'=>'汇联金创代还进件',//备注
 		);
 		$sign=$this->get_string($arr);
 		$arr['sign']=$sign;//签名数据
 		$url=$this->url.'/report';
 		$res=curl_post($url,'post',json_encode($arr));
 	}
 	/**
 	 * 下单支付
 	 * @return [type] [description]
 	 */
 	public function pay(){

 	}
 	/**
 	 * 代付
 	 * @return [type] [description]
 	 */
 	public function qfpay(){

 	}
 	/**
 	 * 订单状态查询
 	 * @return [type] [description]
 	 */
 	public function order_status(){

 	}
 	/**
 	 * 余额查询
 	 * @return [type] [description]
 	 */
 	public function query_remain(){

 	}
 	/**
 	 * 验签
 	 * @return [type] [description]
 	 */
 	public function get_sign(){

 	}
 	/**
 	 * 获取请求字符串
 	 * @param  [type] $arr [description]
 	 * @return [type]      [description]
 	 */
 	public function get_string($arr){
 		$arr=$this->SortByASCII($arr);
 		$string=http_build_query($arr);
 		$rsa=new \app\api\controller\Rsa();
 		$res=$rsa->encrypt($string);
 		echo $string;die;
 	}
 	/**
     * 数组按照ASCII码排序
     * @return [type] [description]
     */
    public function SortByASCII($arr){
        $keys=array_keys($arr);
        $newrr=[];
        foreach ($keys as $k => $v) {
            if(!$v){
                exit(json_encode(['code'=>101,'msg'=>'参数'.$k.'获取失败','data'=>'']));
            }
            $newrr[$k]['asc']=ord($v);
            $newrr[$k]['key']=$v;
            $keys[$k]=ord($v);
        }
        array_multisort($keys, SORT_ASC, $newrr);
        $return=[];
        foreach ($newrr as $k => $v) {
           $return[$v['key']]=$arr[$v['key']];
        }
        return $return;
    }
 }