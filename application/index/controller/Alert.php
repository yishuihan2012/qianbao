<?php
/**
 * APP弹窗广告设置
 */
namespace app\index\controller;

use app\index\model\Alert as AlertModel;

class Alert extends Common
{
    /**
     * 弹窗列表
     * @return [type] [description]
     */
    public function index()
    {

        $this->assign('button', ['text' => '新增弹窗', 'link' => url('/index/alert/add'), 'modal' => 'modal']);
        $list = AlertModel::paginate(config('page_size'), false, ['query' => input()]);
        $data = [
            'count' => AlertModel::count(),
        ];
        $this->assign('data', $data);
        $this->assign('list', $list);
        return view('admin/alert/index');
    }

    /**
     *  增加 / 修改
     */
    public function add($id = null)
    {
        if (request()->ispost()) {
            if ($id) {
                $alert = AlertModel::get($id);
            } else {
                $alert = new AlertModel();
            }
            $result  = $alert->allowField(true)->save(input());
            $content = ($result === false) ? ['type' => 'error', 'msg' => '操作失败'] : ['type' => 'success', 'msg' => '操作成功'];
            session('jump_msg', $content);
            #重定向控制器 跳转到列表页
            $this->redirect('/index/alert/index');die;
        }
        if ($id) {
            $data = AlertModel::get($id);
            $this->assign('data', $data);
        }
        return view('admin/alert/add');
    }

    /**
     * 删除
     */
    public function delete($id)
    {
        $delete  = AlertModel::destroy($id);
        $content = ($delete === false) ? ['type' => 'error', 'msg' => '操作失败'] : ['type' => 'success', 'msg' => '操作成功'];
        session('jump_msg', $content);
        #重定向控制器 跳转到列表页
        $this->redirect('/index/alert/index');die;
    }
}
