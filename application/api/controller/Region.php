<?php
 /**
 *  @version Region controller / Api 地区接口
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-14 13:40:05
 *   @return 
 */
 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use think\Loader;
 use app\index\model\System;
 use app\index\model\RegionProvince;
 use app\index\model\RegionCity;
 use app\index\model\RegionArea;
 use app\index\model\SystemBank;
 use app\index\model\BankIdent;
 use app\index\model\Payplatform;
 use app\index\model\CustomerService;
 use app\index\model\Announcement;

 class Region 
 {
      protected $param;
      public $error;
      public function __construct($param)
      {
        	 $this->param=$param;
      }
 	
      /**
 	 *  @version getRegion method / Api 获取地区接口 获取省 市 区
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-14 13:41:05
 	 *  @param provinceId='省份ID' cityId='城市ID'
	 *  '当provinceId、cityId为空时，表示返回省份列表',
	 *  '当provinceId有值、cityId为空时，表示返回该省下的城市列表',
	 *  '当provinceId、cityId都不为空时，表示返回该省、该市对应下的列表',
	 *  'id表示对应的项id，name表示对应的项名称'
      **/ 
      public function  getRegion()
      {
      	 #检查参数是否完全
      	 if(!isset($this->param['provinceId']) || !isset($this->param['cityId']))
      	 	 return ['code'=>421];
      	 #如果参数都为空的情况下 查找所有的省
      	 if(empty($this->param['provinceId']) && empty($this->param['cityId']))
      	 {
      	 	 $province=RegionProvince::all();
      	 	 if(!$province)
      	 	 	 return ['code'=>499];
      	 	 return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$province]; 
      	 }
      	 #如果有省份ID 并且没有城市ID的话 返回省份内所有城市列表
      	 if(!empty($this->param['provinceId']) && empty($this->param['cityId']))
      	 {
      	 	 $city=RegionCity::where('pro_id',$this->param['provinceId'])->select();
      	 	 if(!$city)
      	 	 	 return ['code'=>499];
      	 	 return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$city];
      	 }
      	 #如果省份ID和城市ID同时存在的话 查找该城市下的所有区县
      	 if(!empty($this->param['provinceId']) && !empty($this->param['cityId']))
      	 {
                   	 $city_info=RegionCity::get($this->param['cityId']);
                   	 if($city_info['pro_id']!=$this->param['provinceId'])
                   	 	 return ['code'=>498];
                   	 $area= RegionArea::where(['pro_id'=>$this->param['provinceId'], 'city_id'=>$this->param['cityId']])->select();
                   	 if(!$area)
                   	 	 return ['code'=>497];
                   	 return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$area];
      	 }
      }

      /**
      *  @version getRegion method / Api 获取地区接口 获取省 市 区
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-14 13:41:05
      *  @param provinceId='省份ID' cityId='城市ID'  districtId='区域ID' keyword='关键词'
      **/ 
      public function getbranch()
      {
           #验证器验证 验证参数合法性
           $validate = Loader::validate('GetBranch');
           #如果验证不通过 返回错误代码 及提示信息
           if(!$validate->check($this->param))
                 return ['code'=>433, 'msg'=>$validate->getError()];
           $where=array();
           #查找到省份
           $province=RegionProvince::get($this->param['provinceId']);
           #查找到城市
           $city=RegionCity::get($this->param['cityId']);
           if(!$province || !$city || $city['pro_id']!=$this->param['provinceId'])
                 return ['code'=>434];
           $where['province']=$province['Name'];
           $where['city']=$city['Name'];
           #是否有关键词搜索
           if(isset($this->param['keyword']) && !empty($this->param['keyword'])){
              $where['bank_name']=array('like','%'.$this->param['bankName'].'%'.$this->param['keyword'].'%');
          }else{
             $where['bank_name']=array('like','%'.$this->param['bankName'].'%');
          }
                 
           #查找符合条件的数据
           $list=SystemBank::where($where)->field('id,bank_code,bank_name')->select();
           if($list===false)
                 return ['code'=>433];
           return ['code'=>200, 'msg'=>'获取支行成功~', 'data'=>$list];
      }

      /**
      *  @version getBankName method / Api 获取银行名称 通过银行卡号前六位自动识别
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-14 13:41:05
      *  @param cardNo 银行卡号
      **/ 
      public function getBankName()
      {
           if(!isset($this->param['cardNo']) || empty($this->param['cardNo']))
                 return ['code'=>431];
           $fixcard=substr($this->param['cardNo'], 0, 6); 
           #获取识别的银行卡信息
           $result=BankIdent::field('ident_id,ident_name,ident_type,ident_desc,ident_code')->where(['ident_code'=>$fixcard])->find();
           if(empty($result))
                 return ['code'=>445];
           #将银行卡识别次数加1
           $memberSetInc=BankIdent::where(['ident_code'=>$fixcard])->setInc('ident_count');
           return ['code'=>200, 'msg'=>'银行卡识别成功', 'data'=>$result];
      }


      /**
      *  @version get_info method / Api App公共通用信息查询
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-14 13:41:05
      *  @param 
      **/ 
      public function get_info()
      {

        
           $info=System::where("system_key='min_withdrawals'")->find();
           $data['minWithdraw']='注：最低提现金额为'.$info['system_val'].'元';
           $data['min_money']=$info['system_val'];
           $info=System::where("system_key='max_withdrawals'")->find();
           $data['max_withdrawals']=$info['system_val'];
           $service=CustomerService::order('service_id desc')->select();

           $data['customerser']=$service;

           $tel=CustomerService::where('service_type=1')->order('service_id desc')->find();
           
           $data['CSTel']=$tel['service_contact'];

           #支付平台信息
           $Payplatform=Payplatform::where('payplatform_state=1')->field('payplatform_id, payplatform_name, payplatform_icon')->select();
           $data['payPlatforms']=$Payplatform;
           return ['code'=>200, 'msg'=>'获取信息成功', 'data'=>$data];
      }


      /**
      *  @version advertisement method / Api 公告
      *  @author $bill$(755969423@qq.com)
      *  @datetime    2017-12-14 13:41:05
      *  @param 
      **/ 
      public function advertisement()
      {
        $announcement=new Announcement();
        $announcement=$announcement->select();

        foreach ($announcement as $key => $value) {
          $data[$key]=$value['announcement_title'];
        }

        return ['code'=>200, 'msg'=>'获取公告成功~', 'data'=>$data];
        
          
           // return ['code'=>200, 'msg'=>'获取公告成功', 'data'=>];
      }


 }