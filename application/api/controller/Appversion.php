<?php

namespace app\api\controller;
use think\Db;
use think\Config;
use think\Loader;
use think\Controller;
use app\index\model\CustomerService;
use app\index\model\Share;
use app\index\model\Page;
use app\index\model\Generalize;
use app\index\model\Member as Members;
use app\index\model\MemberCash;
use app\index\model\Withdraw;
use app\index\model\CashOrder;
use app\index\model\Exclusive;
use app\index\model\Recomment;
use app\index\model\Announcement;
use app\index\model\Notice;
use app\index\model\MemberNovice; 
use app\index\model\Passageway; 
use app\index\model\PassagewayItem; 
use app\index\model\MemberGroup; 
use app\index\model\MemberRelation; 
use app\index\model\CreditCard;
use app\index\model\MemberCreditcard;
use app\index\model\Generation;
use app\index\model\GenerationOrder;
use app\index\model\NoviceClass as NoviceClasss; 
use app\index\model\Appversion as Appversions; 
/**
 *  此处放置一些固定的web地址
 */
class Appversion extends Controller
{
      protected $param;
      public $error=0;
      
    public function __construct($param)
	  {
	    	 $this->param=$param;
	  }
   /**
   *杨成志[3115317085@QQ.com]
   *app版本接口
   **/
   	public function index(){
   			
		    if($this->param['type'] == "ios"){

		      $versions= Appversions::where(['version_state'=>1,'version_type'=>$this->param['type']])->find();

		    }else{
		      $versions= Appversions::where(['version_state'=>1,'version_type'=>'android'])->find();
		    }

		    if($versions['version_code']>$this->param['version_code']){
		         return['code'=>100,'msg'=>'发现新的版本！','data'=>['link'=>$versions['version_link'],'info'=>$versions['version_desc'],"version_code" => $versions['version_code'],"version_name" => $versions['version_name'],"version_force"=>$versions['versions_force']]];
		      }else if($versions['version_code']<$this->param['version_code']){
		         return ['code'=>300,'msg'=>'正在审核中','data'=>[]];
		      }else if($versions['version_code']==$this->param['version_code']){
		          return ['code'=>200,'msg'=>'已经是最新版本！','data'=>[]];
		      }
		  // die;
   	}
}