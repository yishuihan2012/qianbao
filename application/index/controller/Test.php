<?php
namespace app\index\controller;
use app\index\model\Member;
use think\Db;
use think\Loader;
class Test{
	public function find_repeat_peopel(){
		$local=Db::connect('mysql://root:root@127.0.0.1/test#utf8');
		$lists=Member::where('member_nick ="任爱芬"')->select();
		$lists=$lists->toArray();
		// dump($lists);die;
		foreach ($lists as $key => $value) {
			$name = $value['member_nick'];
			$mobile= $value['member_mobile'];
			$member_id = $value['member_id'];
			$member_image = $value['member_image'];
			$parent_name = 0;
			$hierarchys = "第一级别";
			$local->query("INSERT INTO member (name,mobile,member_id,member_image,parent_name,cengji,object,shuidexiaji) values('{$name}','$mobile','{$member_id}','{$member_image}','{$parent_name}','$hierarchys','联云宝','".$name."下级')");
			$parent_id = $value['member_id'];
			$lists[$key]['find'] = Db::query("SELECT m.* FROM wt_member_relation as mr LEFT JOIN wt_member as m on mr.relation_member_id=m.member_id where relation_parent_id={$parent_id}");
			if(!empty($lists[$key]['find'])){
				foreach ($lists[$key]['find'] as $k => $v) {
					$name1 = $v['member_nick'];
					$mobile1= $v['member_mobile'];
					$member_id1 = $v['member_id'];
					$member_image1 = $v['member_image'];
					$parent_name1 = $value['member_nick'];
					$hierarchys1 = "第二级别";
					$local->query("INSERT INTO member (name,mobile,member_id,member_image,parent_name,cengji,object,shuidexiaji) values('{$name1}','$mobile1','{$member_id1}','{$member_image1}','{$parent_name1}','$hierarchys1','联云宝','".$name."下级')");
					$parent_id2 = $v['member_id'];
					$lists[$key]['find'][$k]['find2'] = Db::query("SELECT m.* FROM wt_member_relation as mr LEFT JOIN wt_member as m on mr.relation_member_id=m.member_id where relation_parent_id={$parent_id2}");
					if(!empty($lists[$key]['find'][$k]['find2'])){
						foreach ($lists[$key]['find'][$k]['find2'] as $k2 => $v2) {
							$name2 = $v2['member_nick'];
							$mobile2= $v2['member_mobile'];
							$member_id2 = $v2['member_id'];
							$member_image2 = $v2['member_image'];
							$parent_name2 = $v['member_nick'];
							$hierarchys2 = "第三级别";
							$local->query("INSERT INTO member (name,mobile,member_id,member_image,parent_name,cengji,object,shuidexiaji) values('{$name2}','$mobile2','{$member_id2}','{$member_image2}','{$parent_name2}','$hierarchys2','联云宝','".$name."下级')");
							$parent_id3 = $v2['member_id'];
							$lists[$key]['find'][$k]['find2'][$k2]['find3'] = Db::query("SELECT m.* FROM wt_member_relation as mr LEFT JOIN wt_member as m on mr.relation_member_id=m.member_id where relation_parent_id={$parent_id3}");
							if(!empty($lists[$key]['find'][$k]['find2'][$k2]['find3'])){
								foreach ($lists[$key]['find'][$k]['find2'][$k2]['find3'] as $k3 => $v3) {
									$name3 = $v3['member_nick'];
									$mobile3= $v3['member_mobile'];
									$member_id3 = $v3['member_id'];
									$member_image3 = $v3['member_image'];
									$parent_name3 = $v2['member_nick'];
									$hierarchys3 = "第四级别";
									$local->query("INSERT INTO member (name,mobile,member_id,member_image,parent_name,cengji,object,shuidexiaji) values('{$name3}','$mobile3','{$member_id3}','{$member_image3}','{$parent_name3}','$hierarchys3','联云宝','".$name."下级')");
								}
							}
						}
					}
				}
			}
		}
		// echo "<pre>";
		// print_r($lists);die;
		$lists_mobile=array_column($lists, 'member_mobile');
		$str='(';
		foreach ($lists_mobile as $key => $v) {
			if($key==count($lists_mobile)-1){
				$str.=$v;
			}else{
				$str.=$v.',';
			}	
		}
		$str.=")";
		// echo $str;die;
		// 旧平台重读的数据
		$connnect_xj=Db::connect('mysql://root:chfuck~>d5@47.104.4.73:3306/wallet');
		$res= $connnect_xj->query("select *from wt_member where member_mobile in {$str}");

		foreach ($res as $key => $value) {
			$name = $value['member_nick'];
			$mobile= $value['member_mobile'];
			$member_id = $value['member_id'];
			$member_image = $value['member_image'];
			$parent_name = 0;
			$hierarchys = "第一级别";
			$local->query("INSERT INTO member (name,mobile,member_id,member_image,parent_name,cengji,object,shuidexiaji) values('{$name}','$mobile','{$member_id}','{$member_image}','{$parent_name}','$hierarchys','喜家钱包','".$name."下级')");
			$parent_id = $value['member_id'];
			$res[$key]['find'] = $connnect_xj->query("SELECT m.* FROM wt_member_relation as mr LEFT JOIN wt_member as m on mr.relation_member_id=m.member_id where relation_parent_id={$parent_id}");
			if(!empty($res[$key]['find'])){
				foreach ($res[$key]['find'] as $k => $v) {
					$name1 = $v['member_nick'];
					$mobile1= $v['member_mobile'];
					$member_id1 = $v['member_id'];
					$member_image1 = $v['member_image'];
					$parent_name1 = $value['member_nick'];
					$hierarchys1 = "第二级别";
					$local->query("INSERT INTO member (name,mobile,member_id,member_image,parent_name,cengji,object,shuidexiaji) values('{$name1}','$mobile1','{$member_id1}','{$member_image1}','{$parent_name1}','$hierarchys1','喜家钱包','".$name."下级')");
					$parent_id2 = $v['member_id'];
					$res[$key]['find'][$k]['find2'] = $connnect_xj->query("SELECT m.* FROM wt_member_relation as mr LEFT JOIN wt_member as m on mr.relation_member_id=m.member_id where relation_parent_id={$parent_id2}");
					if(!empty($res[$key]['find'][$k]['find2'])){
						foreach ($res[$key]['find'][$k]['find2'] as $k2 => $v2) {
							$name2 = $v2['member_nick'];
							$mobile2= $v2['member_mobile'];
							$member_id2 = $v2['member_id'];
							$member_image2 = $v2['member_image'];
							$parent_name2 = $v['member_nick'];
							$hierarchys2 = "第三级别";
							$local->query("INSERT INTO member (name,mobile,member_id,member_image,parent_name,cengji,object,shuidexiaji) values('{$name2}','$mobile2','{$member_id2}','{$member_image2}','{$parent_name2}','$hierarchys2','喜家钱包','".$name."下级')");
							$parent_id3 = $v2['member_id'];
							$res[$key]['find'][$k]['find2'][$k2]['find3'] = $connnect_xj->query("SELECT m.* FROM wt_member_relation as mr LEFT JOIN wt_member as m on mr.relation_member_id=m.member_id where relation_parent_id={$parent_id3}");
							if(!empty($res[$key]['find'][$k]['find2'][$k2]['find3'])){
								foreach ($res[$key]['find'][$k]['find2'][$k2]['find3'] as $k3 => $v3) {
									$name3 = $v3['member_nick'];
									$mobile3= $v3['member_mobile'];
									$member_id3 = $v3['member_id'];
									$member_image3 = $v3['member_image'];
									$parent_name3 = $v2['member_nick'];
									$hierarchys3 = "第四级别";
									$local->query("INSERT INTO member (name,mobile,member_id,member_image,parent_name,cengji,object,shuidexiaji) values('{$name3}','$mobile3','{$member_id3}','{$member_image3}','{$parent_name3}','$hierarchys3','喜家钱包','".$name."下级')");
								}
							}
						}
					}
				}
			}
		}
		die;
        $path = dirname(__FILE__);//找到当前脚本所在路径
        Loader::import('PHPExcel.Classes.PHPExcel');//手动引入PHPExcel.php
        Loader::import('PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory');//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $phpexcel = new \PHPExcel();//实例
        $common_mobile=array_column($res, 'member_mobile');
		// 新平台重复会员
		$new_members=Member::where(['member_mobile'=>['in',$common_mobile]])->select();
		$member_ids=array_column($new_members->toArray(), 'member_id');
		#删除member表
		#删除Login表	
		#删除实名
		#删除信用卡
		#
	}
}

?>