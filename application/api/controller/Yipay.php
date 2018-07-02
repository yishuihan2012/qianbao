<?php
 namespace app\api\controller;
 use think\Db;
 use think\Session;
 use think\Config;
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
 *  @version Huilianluodi controller / Api 代还入网
 *  @author 许成成(1015571416@qq.com)
 *   @datetime    2018-02-23 15:13:05
 *   @return 
 */
 class Yipay{
    protected $url;
    private $version;
    public function __construct(){
        $this->version='1.0';
        $this->orgno='TEST0001';
        $this->merno='990581000011018';
        $this->secretkey='c93fa4ef77f6ab3a4674595348046985';
        $this->url='http://cgi.yiyoupay.net/cgi-bin/nps/action';
        $this->branchId='70900000';
    }
    /**
     * 商户入网
     * @return [type] [description]
     */
    public function mech_income(){
        $data=array(
            'userId'=>'10086',//商户编号
            'userName'=>'易水寒',//商户名称
            'userNick'=>"易水寒",//商户昵称
            'userPhone'=>"16605383329",//法人电话
            'userAccount'=>"许伟",//法人姓名
            'userCert'=>'370983199109202832',//法人身份证号
            'userEmail'=>"1015571416@qq.com",//商户邮箱
            'userAddress'=>"山东省济南市",//商户地址
            'userMemo'=>"测试商户",//商户备注
            'settleBankNo'=>"6215590200003242971",//结算卡号
            'settleBankPhone'=>"17569615504",//结算卡预留手机号
            'settleBankCnaps'=>"102100099996",//结算卡联行号
            'branchId'=>$this->branchId,
        );
        // echo json_encode($data);die;
        $res=$this->request('SdkUserStoreBind',$data);
        return $res;
    }
    /**
     * 商户信息变更 
     * @return [type] [description]
     */
    public function mech_update(){
        $data=array(
            'userCode'=>'d00515e65a3be38af5bfe38dbe5999f2 ',// 商户编号               
            'settleBankNo'=>"",// 绑定结算卡号                 
            'settleBankPhone'=>"",//绑定结算卡手机           
            'settleBankCnaps'=>"",// 绑定结算卡联行号
        );
        $res=$this->request('SdkUserStoreModify',$data);
        return $res;
    }
    public function mech_rate_update(){
        $data=array(
            'userCode'=>"",// 商户编号 系统返回商户编号                                                                    
            'payType ' =>"",// 交易类型                                                                            
            'orderRateT0'=>'',//交易费率  0.36（费率0.36/100）                                                                    
            'settleChargeT0'=>"",//提现附加费用 单位：分（200）                                                                   
        );
        $res=$this->request('SdkUserStoreRate',$data);
        return $res;
    }
    /**
     * 获取请求字符串
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function get_sign($arr){
        $private_key="./static/rsakey/1001034_prv.pem";
        $pub_key="./static/rsakey/1001034_pub.pem";
        $arr=$this->SortByASCII($arr);
        $string=http_build_query($arr);
        $string=urldecode($string);
        $res=$this->pri_encode($string);
        // echo $res;die;
        // $rsa=new \app\api\controller\Rsa($pub_key,$private_key);
        // $res=$rsa->encrypt($string);
        return $res;  
    }
    /**
     * 加密
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    function pri_encode($data){
        $encrypted='';
        $private_key=file_get_contents('./static/rsakey/yipay/JN000001_private_key.pem');  //秘钥
        $pi_key =  openssl_pkey_get_private($private_key);  //这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id  
        $str='';
        foreach (str_split($data, 117) as $chunk) {
            openssl_private_encrypt($chunk,$encryptedTemp,$pi_key);  //私钥加密  
            $str .= $encryptedTemp;
        }
        $encrypted = base64_encode($str);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
        return $encrypted;
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
    /**
     * aes加密
     * @param [type] $plainText [description]
     * @param [type] $aesKey    [description]
     */
    public function AESencode($plainText,$aesKey){
        $encryptStr = openssl_encrypt($plainText, 'AES-128-ECB', $aesKey, OPENSSL_RAW_DATA);
        $encryptStr = strtoupper(bin2hex($encryptStr));
        return $encryptStr;
    }
    /**
     * data数据解密
     * @param  [type] $encryptData [description]
     * @return [type]              [description]
     */
    public function dataDecrypt($encryptData) {
        $encryptKey = $encryptData['encryptkey'];
        $data = $encryptData['data'];

        //base64解码
        $encryptKey = base64_decode($encryptKey);
        //rsa解密aes密钥
        $priKey=file_get_contents('./static/rsakey/yipay/JN000001_private_key.pem');  //秘钥
        $privateKey = openssl_pkey_get_private($priKey);
        openssl_private_decrypt($encryptKey,$aesKey, $privateKey);

        //BCD解码
        $data = pack("H*", $data);

        //aes解码
        $encryptStr = openssl_decrypt($data, 'AES-128-ECB', $aesKey, OPENSSL_RAW_DATA);

        return json_decode($encryptStr, TRUE);
    }
    /**
     * 发送请求
     * @param  [type] $url [description]
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function request($action,$data){
        $rand_string=generate_password(16);
        // var_dump(base64_decode(AESencode(json_encode($data),$rand_string)));die;
        $params=array(
            'version'=>"2.0",
            'orgNo'=>$this->orgno,
            'merNo'=>$this->merno,
            'action'=>$action,
            'data'=>$this->AESencode(json_encode($data),$rand_string),
            'encryptkey'=>$this->pri_encode($rand_string),
        );
        // echo json_encode($params);die;
        // version+orgNo+merNo+action+data+商户蜜钥KEY
        $params['sign']=md5($params['version'].$this->orgno.$this->merno.$action.$params['data'].$this->secretkey);

        $res=curl_post($this->url,'post',$params,0);
        $result=json_decode($res,true);
        if($result && $result['data']){
            //解密
            $return=$this->dataDecrypt($result);
            if($return['code']=='0000'){
                $return['code']=200;
            }
        }else{
            $back=json_decode($result['data']);
            $return['code']=-1;
            $return['msg']=$back['respCode'];
        }
        return $return;
    }
 }