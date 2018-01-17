<?php
/**
*  @version Base Model  基类模型
 * @author  $bill 755969423@qq.com
 * @time      2018-1-13 10:22
 * @return  定义基类模型的方法 数据处理 自动完成 AutoLoad
 */
 namespace app\index\model;
 use think\{Model, Loader};
 
 abstract class Base extends Model
 {
      static public function showReturnCode($code = '', $data = [], $msg = ''){
           return \app\index\controller\Base::showReturnCode($code, $data, $msg);
      }
      
      static public function showReturnCodeWithOutData($code = '', $msg = '')
      {
           return \app\index\controller\Base::showReturnCode($code, [], $msg);
      }

      protected function editData($parameter = false, $validate_name = false, $model_name = false, $save_data = [])
      {
           if (empty($save_data))
                $data = ($parameter != false && is_array($parameter)) ? $this->buildParam($parameter) : $this->request->post();
           else
                $data = $save_data;
           if (!$data) 
                return $this->showReturnCode(1004);
           if ($validate_name != false) 
           {
                if (true !== $this->validate($data, $validate_name) ) 
                     return $this->showReturnCodeWithOutData(1003, $result);
           }
           $model_edit = Loader::model($model_name);
           return !$model_edit ? $this->showReturnCode(1010) : $model_edit->editData($data);
      }

      protected function doModelAction($param_data,$validate_name = false, $model_name = false,$action_name='editData')
      {
           if ($validate_name != false) 
           {
                if (true !== $this->validate($param_data, $validate_name) ) 
                     return $this->showReturnCodeWithOutData(1003,  $result);
           }
           $model_edit = Loader::model($model_name);
           return  !$model_edit ?  $this->showReturnCode(1010) : $model_edit->$action_name($param_data);
      }

      public function editData($data){
           if (isset($data['id']))
                $save = (is_numeric($data['id']) && $data['id']>0) ? $this->allowField(true)->save($data,[ 'id' => $data['id']]) : $this->allowField(true)->save($data);
           else
                $save  = $this->allowField(true)->save($data);
           return ( $save == 0 || $save == false) ? [  'code'=> 1009,  'msg' => '数据更新失败' ] : [  'code'=> 1001,  'msg' => '数据更新成功' ];
      }
 }
