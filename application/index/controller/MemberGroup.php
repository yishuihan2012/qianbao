<?php
/**
 * MemberGroup controller / 会员用户组控制器
 * @authors GongKe(755969423@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\index\controller;

use app\index\model\MemberGroup as MemberGroups;
use app\index\model\Member as Members;
use app\index\model\PacketType as PacketTypes;

use think\Controller;
use think\Request;
use think\Session;
use think\Config;

class MemberGroup extends Common
{

    //-------------------------------------------------------

    			#会员用户组列表(membergroup/index)

    //-------------------------------------------------------
	public function index()
	{

		#查询出会员用户组列表
		$list = MemberGroups::order('group_id', 'desc')->paginate(Config::get('page_size'));
		#查询出所有用户组
		$lists = MemberGroups::all();

	    #数据传递给视图
		$this->assign('list', $list);

		#将所有用户组传到视图
		$this->assign('lists', $lists);

		$this->assign('button', ['text'=>'新增用户组', 'remote'=>url('/index/member_group/group_creat'), 'modal'=>'modal']);

		return view('admin/membergroup/index');
		//return view('admin/index/index',['jump_msg'=>['type'=>'warning','msg'=>'错误的请求信息！']]);

	}

    //-------------------------------------------------------

    			#会员用户组新增(membergroup/group_creat)

    //-------------------------------------------------------
	public function group_creat()
	{
		#提交更改信息
		if(Request::instance()->isPost())
		{
			#处理一下红包权限字段 将其转为时间戳
			$_POST['group_packet']=isset($_POST['group_packet']) ? implode(",", $_POST['group_packet']) : '';

			$group = new MemberGroups($_POST);
			// post数组中只有name和email字段会写入
			$result = $group->allowField(true)->save();

			$content = $result ? ['type'=>'success','msg'=>'用户组添加成功'] : ['type'=>'warning','msg'=>'用户组添加失败'];

			Session::set('jump_msg', $content);

			$this->redirect('member_group/index');
		}

		#查询到所有的红包分类
		$packet = PacketTypes::all();

		$this->assign('packet', $packet);

		return view('admin/membergroup/creat');
	}

    //-------------------------------------------------------

    			#会员用户组修改(membergroup/group_edit)

    //-------------------------------------------------------
	public function group_edit(Request $request)
	{

		#提交更改信息
		if(Request::instance()->isPost())
		{
			#处理一下红包权限字段 将其转为时间戳
			$_POST['group_packet']=isset($_POST['group_packet']) ? implode(",", $_POST['group_packet']) : '';

			$group = MemberGroups::get(Request::instance()->param('id'));

			$result= $group->allowField(true)->save($_POST,['group_id' => Request::instance()->param('id')]);

			$content = $result ? ['type'=>'success','msg'=>'用户组修改成功'] : ['type'=>'warning','msg'=>'用户组修改失败'];

			Session::set('jump_msg', $content);

			$this->redirect('member_group/index');
		}

		#获取要更改的组ID
		$id=$request::instance()->param('id');

		#查询出会员用户组列表
		$group = MemberGroups::get($id);

		$group['group_packet']=explode(',', $group['group_packet']);

	    //$ab=new \app\index\model\Admin;
		$this->assign('group', $group);

		#查询到所有的红包分类
		$packet = PacketTypes::all();

		$this->assign('packet', $packet);

		return view('admin/membergroup/edit');
	}

    //-------------------------------------------------------

    			#会员用户组删除(membergroup/group_remove)

    //-------------------------------------------------------
	public function group_remove()
	{
		#接收要删除的用户组ID
		$id=input('post.id');

		#接收转移到的目的地用户组
		$result=input('post.result');

		#删除用户组之前要转移会员所属的用户组 首先应查找到符合条件的数据
		$member = new Members;

	 	$remove	= $member->save(['member_group'  => $result],['member_group' => $id]);

	    $remove = MemberGroups::destroy($id);

		#返回状态
		$context=$remove ? '1' : '0';

		#输出json
		echo json_encode($context);
	}

}
