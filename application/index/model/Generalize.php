<?php
/**
*  @version 素材模型
 * @author  $bill 755969423@qq.com
 * @time      2017-11-24 09:20
 * @return  
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;

class Generalize extends Model{
      #定义模型数据表 默认为Class名加前缀 如不一样 可自己定义
      #protected $table = 'wt_article';
      #定义主键信息  可留空 默认主键
      protected $pk 	 = 'generalize_id';
      #定义自动写入时间字段开启 格式为时间格式
      protected $autoWriteTimestamp = 'datetime';
      #定义时间戳字段名 信息添加时间
      protected $createTime = 'generalize_creat_time';
      #定义时间戳字段名 信息修改时间
      // protected $updateTime = 'article_update_time';
      #初始化模型
      protected function initialize()
      {
           #需要调用父类的`initialize`方法
           parent::initialize();
           #TODO:自定义的初始化
      }
      /**
       * [getPaginate 查询素材详情表]
       * @return [type] [description]
       */
      // public function getPaginate(){
      //   $
      // }
      /**
       * [remove 删除素材详情]
       * @param  integer $data [description]
       * @return [type]        [description]
       */
      public static function remove($data = 0){
        $where['generalize_id'] = $data;

        //查询图片地址
        $url = Db::table("wt_generalize")->where($where)->find();
        //图片是多图分割成数组
        
        $arr = explode("#",$url['generalize_thumb']);
       
               //删除数据
        if(Db::table("wt_generalize")->where($where)->delete()){
          foreach ($arr as $key => $value) {
            $img = $value;
            @unlink(".".$img);
          }
          return true;
        }else{
          return false;
        }
      }
      /**
       * [generalizelist 查询推荐素材列表]
       * @return [type] [description]
       */
      public static function generalizelist(){
        $list = Db::table("wt_generalize")->order("generalize_id desc")->select();
        if($list){
          foreach ($list as $key => $value) {
            $list[$key]['thumbarr'] = explode("#",$value['generalize_thumb']);
            $list[$key]['generalize_time'] = date("Y-m-d",strtotime($value['generalize_creat_time']));
          }
        }
        return $list;
      }
      /**
       * [generalizenum 修改素材下载次数]
       * @return [type] [description]
       */
      public static function generalizenum($data = 0){
        $where['generalize_id'] = $data;
        //获取当前下载次数
        $generalizenum = Db::table("wt_generalize")->where($where)->find();
        //然后把当前次数加一
        $num = $generalizenum['generalize_download_num'] + 1;
        //更新数据库
        if(Db::table("wt_generalize")->where($where)->update(["generalize_download_num"=>$num])){
            return true;
        }else{
            return false;
        }
      }
      /**
       * [edit 推广详情]
       * @return [type] [description]
       */
      public static function edits($data = 0){
        $where['generalize_id'] = $data;
        $info = Db::table("wt_generalize")->where($where)->find();
        $info['arrImg'] = explode("#",$info['generalize_thumb']);
        return $info ;
      }
      #修改素材详情
      public static function saves($data){
        $where['generalize_id'] = $data['generalize_id'];
        $datas['generalize_title'] = $data['generalize_title'];
        $datas['generalize_contents'] = $data['generalize_contents'];
        if($data['generalize_thumb']){
          $datas['generalize_thumb'] = $data['generalize_thumb'];
        }
         return Db::table("wt_generalize")->where($where)->update($datas);
      }
}

