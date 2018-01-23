<?php
/*
 * @Author: John(1160608332@qq.com)
 * @Date: 2017-01-16 09:36:35 
 * @Last Modified by: John
 * @Last Modified time: 2017-01-16 09:36:35
 */
namespace app\index\controller;
use think\Request;
use think\Config;
use think\Session;
use app\index\model\ActivationCode as ActivationCodeData;
use app\index\model\Adminster;
use app\index\model\MemberGroup;
use app\index\controller\MakeCode;

class  ActivationCode extends Common{
	 # 激活码列表
	 public function index()
	 {  
         $this->assign('button', [
            ['text'=>'生成激活码', 'link'=>url('/index/activation_code/add'),'icon'=>'tags','theme'=>'info','modal'=>'modal','size'=>'lg'],
            ['text'=>'导出激活码', 'link'=>url('/index/activation_code/export'),'icon'=>'tags','theme'=>'info','modal'=>'modal','size'=>'lg'],
        ]);
         return $this->getList(Request::instance());
     }
     # 激活码生成
     public function add(Request $request){
         if($request::instance()->isPost()){
             $option = ['code_count'    =>10,
                        'code_prefix'   =>1000,
                        'code_group'    =>1,
                        'code_for'      =>1 ];
            $count = $request::instance()->has('code_count','post')?$request::instance()->post('code_count'):$option['code_count'];
            $code_prefix = $request::instance()->has('code_prefix','post')?$request::instance()->post('code_prefix'):$option['code_prefix'];
            $code_group = $request::instance()->has('code_group','post')?$request::instance()->post('code_group'):$option['code_group'];
            $code_for = $request::instance()->has('code_for','post')?$request::instance()->post('code_for'):$option['code_for'];
            $code_factory = new MakeCode();
            $codes=[];
            for($i=1;$i<=$count;$i++){
                $rand = $i.time().rand(1,1000);
                $code_no = $code_factory->encodeID($rand,5);
                $card_vc = substr(md5($code_prefix.$code_no),0,2); 
                $card_vc = strtoupper($card_vc); 
                $code = $code_prefix.$code_no.$card_vc; 
                $codes[] = [
                    'activation_states'     =>1,
                    'activation_code_key'   =>$code,
                    'activation_code_pwd'   =>make_rand_code(),
                    'activation_code_for'   =>$code_for,
                    'activation_code_level' =>$code_group,
                    'activation_add_time'   =>date('Y-m-d H:i:s')
                ];
            }
            $ActivationCodeData = new ActivationCodeData;
            $ActivationCodeData->saveAll($codes);
            Session::set('jump_msg', ['type'=>'success','msg'=>'激活码已经生成']);
            $this->redirect('ActivationCode/index');
         }else{
            ## 获取全部用户组组别
            $group = MemberGroup::all();
            ## 获取代理
            $adminster = Adminster::all();
            $this->assign('adminster',$adminster);
            $this->assign('group',$group);
            return view('admin/activation_code/getForm'); 
         }
     }
     # 激活码使用
     public function delete(Request $request)
	 {   
            $activation = ActivationCodeData::get($request::instance()->param('id'));
            Session::set('jump_msg', ['type'=>'success','msg'=>'激活码已经删除']);
            if(false===$activation->delete()){
                Session::set('jump_msg', ['type'=>'warning','msg'=>'激活码删除失败']);
            }

            $this->redirect('ActivationCode/index');
     }
     # 生成激活码
    //  private function make_code($option){
    //     $max = ActivationCodeData::max();
    //     $key = sprintf('%02s', $number);
    //     $pwd = make_rand_code();
    //     return 
    //  }
     # 获取列表
     private function getList(Request $request){
         $list = ActivationCodeData::where([])->order('activation_code_id desc')->paginate(Config::get('page_size'), false);
         $this->assign('list',$list);
         $count =ActivationCodeData::where([])->count();
         $this->assign("count",$count);
        return view('admin/activation_code/getList');
     }
     # 获取表单
     private function getForm(Request $request){ 
       return view('admin/activation_code/getForm');
    }
    #   导出二维码 
    #   begin end 起止id 无则导出全部
    public function export(){
        if(request()->ispost()){
            $data=input();
            if(isset($data['begin']) && $data['begin']!=0 &&  isset($data['end']) && $data['end']!=0 ){
                $where=['activation_code_id'=>['between',[$data['begin'],$data['end']]]];
            }elseif(isset($data['begin']) && $data['begin']!=0){
                $where=['activation_code_id'=>['>=',$data['begin']]];
            }elseif(isset($data['end']) && $data['end']!=0 ){
                $where=['activation_code_id'=>['<=',$data['end']]];
            }else{
                $where=[];
            }
            $list=db('activation_code')->where($where)->select();
            $str='';
            foreach ($list as $k => $v) {
                $str.="{'activationNo':'".$v['activation_code_key']."','activationPwd':'".$v['activation_code_pwd']."'}\n";
            }
            $fileName="activation_code.txt";
            header("Content-Type: application/txt");
            header("Content-Disposition: attachment; filename=".$fileName);
            echo $str;
            return;
        }
        return view('admin/activation_code/export');
    }
}
