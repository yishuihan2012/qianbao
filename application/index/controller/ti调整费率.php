 public function rate()
	 {
	  	 #获取通道
	  	 if(!Request::instance()->param('id'))
	  	 	 return '参数错误';
	 	 if(Request::instance()->isPost())
	 	 {
	 	 	 #获取提交的数据
	 	 	 $post=Request::instance()->post();
	 	 	 $result=true;
	 	 	 foreach ($post as $key => $value) {
	 	 		 #拆分Key键
	 	 	 	 $group_id=strrev(strstr(strrev($key),strrev('_'),true));
	 	 	 	 $key_fix=strtok($key,'_');
	 	 	 	 $field='item_'.$key_fix;
	 	 	 	 #查询库中是否存在本条数据
	 	 	 	 $passage=PassagewayItem::where(['item_passageway'=>Request::instance()->param('id'),'item_group'=>$group_id])->find();
	 	 	 	 if($passage){
	 	 	 	 	 #如果存在的话 对比一下之前和之后的值 如果不一致 则进行修改
	 	 	 	 	 if($passage[$field]!=$value){
	 	 	 	 	 	    PassagewayItem::where(['item_passageway'=>Request::instance()->param('id'),'item_group'=>$group_id])->setField($field, $value);
	 	 	 	 	 	    #修改完费率，对需要重新修改入网费率的通道进行重新报备
	 	 	 	 	 		 $update_passageway=array(
			 	 	 	 	 	'item_passageway'	=>Request::instance()->param('id'),
			 	 	 	 	 	'item_group'			=>$group_id,
			 	 	 	 	 	'item_rate'			=>$post['rate_'.$group_id],
			 	 	 	 	 	'item_also'			=>$post['also_'.$group_id]
			 	 	 	 	 );
			 	 	 	 	 $plan=new \app\api\controller\Planaction();
			 	 	 	 	 $res=$plan->update_passway_rate($update_passageway);
	 	 	 	 	 }
	 	 	 	 }else{
	 	 	 	 	 $data=array(
	 	 	 	 	 	'item_passageway'	=>Request::instance()->param('id'),
	 	 	 	 	 	'item_group'			=>$group_id,
	 	 	 	 	 	'item_rate'			=>$post['rate_'.$group_id],
	 	 	 	 	 	'item_also'			=>$post['also_'.$group_id]
	 	 	 	 	 );
	 	 	 	 	 $newpass=new PassagewayItem($data);
	 	 	 	 	 $result = $newpass->allowField(true)->save();
	 	 	 	 	 #存储完毕，对需要重新修改入网费率的通道进行重新报备
	 	 	 	 	 $plan=new \app\api\controller\Planaction();
			 	 	 $res=$plan->update_passway_rate($update_passageway);
	 	 	 	 }
	 	 	 	 if(!$result)
	 	 	 	 	 continue;
	 	 	 }

		 	 $content = $result ? ['type'=>'success','msg'=>'税率调整成功'] : ['type'=>'warning','msg'=>'税率调整失败'];
		 	 Session::set('jump_msg', $content);
		 	 $this->redirect($this->history['0']);
	 	 }
	  	 #查询出当前通道对会员组的原始税率
	  	 $list=PassagewayItem::where('item_passageway',Request::instance()->param('id'))->select();
	  	 $this->assign('list', $list);
	  	 #查询出所有的用户组
	  	 $group=MemberGroup::all();
	  	 $this->assign('group', $group);
	  	 $this->assign('id', Request::instance()->param('id'));
	  	 return view('admin/passageway/rate');
	 }