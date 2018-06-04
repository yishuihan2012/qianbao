<?php
/**
 *  @version Sms controller / Api 运营页面
 *  @author $bill$(755969423@qq.com)
 *   @datetime    2017-12-08 10:13:05
 *   @return 
 */
 namespace app\opera\controller;
 use think\{Db,Request,Controller,Config};
 class Operate extends Controller
 {
 	//前台提交页面
 	public function index()
 	{
 		if(Request::instance()->isPost()){
 			$param = Request::instance()->param();
 			$where['operate_mobile'] = $param['telphone'];
 			if(Db::table("wt_operate")->where($where)->find())
 				exit(json_encode(['status' => 202 , "msg" => "此手机号已申请"]));

 			$data = array(
 				"operate_nick"    => $param['username'],
 				"operate_mobile"  => $param['telphone'],
 				"operate_add_time" =>date("Y-m-d H:i:s")
 			);
 			if(Db::table("wt_operate")->insert($data))
 				exit(json_encode(['status' => 200 , "msg" => "注册成功"]));
 			else
 				exit(json_encode(['status' => 201 , "msg" => "注册失败"]));
 		}
 		return view("Operate/index");
 	}
 	//后台页面
 	public function admin(){
 		$list = Db::table("wt_operate")->order("operate_id desc")->paginate(Config::get('page_size'), false, ['query'=>Request::instance()->param()]);;
 		$this->assign("list",$list);
 		return view("Operate/admin");
 	}
 }

