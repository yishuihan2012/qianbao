<?php
/**
 *  @version ServerItem controller / Api 自定义模块
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-08 10:13:05
 *   @return 
 */
 namespace app\api\controller;
 use think\Db;
 use think\Config;
 use think\Request;
 use app\index\model\System;
 use app\index\model\ServiceItem;
 use app\index\model\ServiceItemList;
 use app\index\model\Member as Members;
 class ServerItem
 {
      protected $param;
      public $error;
      public function __construct($param)
      {
        	 $this->param=$param;
      }

      /**
 	 *  @version list method / Api 自定义模块列表服务信息
 	 *  @author $bill$(755969423@qq.com)
 	 *  @datetime    2017-12-08 11:19:05
 	 *  @param 无参数 
      **/
      public function list()
      {
          $where['member_id'] = $this->param['uid'];
          $userinfo = Members::with("membergroup")->where($where)->find();
           try{
                 $data=array();
                 #获取到所有显示的模块
                 $model=ServiceItem::where('item_state','1')->order('item_weight','asc')->select();
                 foreach ($model as $key => $value) {
                       $data[$key]['serviceName']=$value['item_name'];
                       $data[$key]['serviceIcon']=System::getName('system_url').$value['item_icon'];
                       $data[$key]['serviceItems']=ServiceItemList::where(['list_state'=>'1','list_item_id'=>$value['item_id'],"list_parent_id"=>0])->order('list_weight','asc')->limit(6)->select();
                       foreach ($data[$key]['serviceItems'] as $k => $v) {
                           $data[$key]['serviceItems'][$k]['list_icon'] =System::getName('system_url').$data[$key]['serviceItems'][$k]['list_icon'];
                          if($v['list_authority'] == 0){
                            $data[$key]['serviceItems'][$k]['list_status'] = 0;
                          }else{
                            $data[$key]['serviceItems'][$k]['list_status'] = ($userinfo['group_salt']==1)?1:0;
                          }
                          if(!strpos($v['list_url'],"http")){
                            $data[$key]['serviceItems'][$k]['list_url'] = "http://".$_SERVER['HTTP_HOST'].$v['list_url'];
                          }
                       }
                 }
                  return ['code'=>200, 'msg'=>'获取成功~', 'data'=>$data];
          } catch (\Exception $e) {
                 Db::rollback();
                 return ['code'=>308,'msg'=>$e->getMessage()];
          }
      }

 }
