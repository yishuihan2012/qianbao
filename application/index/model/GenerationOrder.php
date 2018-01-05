<?php
/**
*  @version 代还 => 还款明细订单模型
 * @author  $bill 755969423@qq.com
 * @time      2017-12-27 15:45
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;
use think\Request;
class GenerationOrder extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_generation_order';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'order_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'order_add_time';
      #定义时间戳字段名 信息修改时间
      protected $updateTime = 'order_edit_time';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
      }
      
      #关联模型 一对一关联 (Generation) 反向关联计划表
      public function generation()
      {
           return $this->belongsTo('Generation','generation_id','order_no')->setEagerlyType(0);
      }
      #获取还款列表
      public static function list($page = 1){
        //查询数据总条数
        $count = Db::view("GenerationOrder")
            ->view("Generation","","Generation.generation_id=GenerationOrder.order_no")
            ->view("Member m","","m.member_id=GenerationOrder.order_member")
            ->view("Member","","Member.member_id=Generation.generation_member")
            ->where("generation_state",">",1)
            ->count();
        $start =  ($page-1)*10;
        $list = Db::view("GenerationOrder")
            ->view("Generation","*","Generation.generation_id=GenerationOrder.order_no")
            ->view("Member m","member_nick as o_member_nick,member_mobile as o_member_mobile","m.member_id=GenerationOrder.order_member")
            ->view("Member","member_nick,member_mobile","Member.member_id=Generation.generation_member")
            ->where("generation_state",">",1)->order("order_id  desc")
            ->limit($start,10)->select();
         $pages =  self::Pages($count,$page);
            return array('list' =>$list,'page' => $pages);
      }
      #分页
      public static function Pages($count=0,$p){
        
        $page = ceil($count/10);#计算总共多少页
        #获取连接地址
        $http_host = $_SERVER['PATH_INFO'];
        $upper = ($p-1>=0)?1:($p--);
        $lower = ($p+1>=$page)?$page:($p++);
        $str = '<ul class="pager">';
        if($upper==1){
          $str .= '<li class="disabled"><span>«</span></li> ';
        }else{
          $str .= '<li><a href="/index/member/index.html?page='.$upper.'">«</a></li>';
        }
        for($i = 1; $i <=$page ; $i++ ){
          if($p!=$i){
           $str .= '<li><a href="'.$http_host.'?page='.$i.'">'.$i .'</a></li> ';
          }else{
            $str .= '<li class="active"><span>'.$i .'</span>
                    </li>';
          }
        }
        if($lower == $page){
          $str .= '<li class="disabled"><span>»</span></li>';
        }else{
          $str .= '<li><a href="/index/member/index.html?page='.$lower.'">»</a></li>';
        }
        $str .= '</ul>';
        return $str;
      }
}
