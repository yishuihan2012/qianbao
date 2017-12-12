<?php
 /**
 * System controller / 系统配置控制器
 * @authors GongKe(755969423@qq.com)
 * @date    2017-12-04 10:27:05
 * @version $Bill$
 */
 namespace app\index\controller;

 use app\index\model\System as Systems;
 use think\Controller;
 use think\Request;
 use think\Session;

 use think\Config;
 use think\Loader;


 class System extends Common
 {
    	 #系统核心配置
	public function basic()
	{
		 #如果是提交更新
		 if(Request::instance()->isPost())
		 {
			 $result = false;
			 #验证器验证 触发update事件验证
			 $validate = Loader::validate('SystemregisterValidate');
			 #如果验证不通过
			 if(!$validate->check(Request::instance()->param())){
				 #查询基本参数设置
	 	 		 $setting = Systems::where('system_type','register')->whereOr('system_type','login')->order('system_id', 'asc')->select()->toArray();
	 	 		 $this->assign('setting', $setting);
				 #数据是否提交成功
				 Session::set('jump_msg', ['type'=>'error','msg'=>$validate->getError()]);
	 	 		 $this->redirect($this->history['0']);
			      exit;
			 }
			 #获取提交的所有数据
			 $array =  Request::instance()->param();
			 #遍历提交的数据
			 foreach ($array as $key => $value) {
				 #读取出数据库中本项的值
				 if(Systems::getName($key) !=  $value){
					 $result = Systems::setName($key, $value);
					 $content=$result===false ? ['type'=>'error','msg'=>$key.'修改失败!'] : ['type'=>'success','msg'=>'修改成功!'];
				 	 Session::set('jump_msg', $content);
	 	 		 	 
				 }
			 }
			 $this->redirect($this->history['0']);
		 }
		 #查询核心参数设置之注册登录配置
	 	 $setting = Systems::order('system_id', 'asc')->select()->toArray();
	 	 // var_dump(count($setting));die;
	 	 $this->assign('setting', $setting);

		 #查询核心参数设置之配置
	 	 //$setting = Systems::where('system_type','register')->whereOr('system_type','login')->order('system_id', 'asc')->select()->toArray();
	 	 //$this->assign('setting', $setting);
		 #渲染视图
		 return view('admin/system/basic');
	}
}
