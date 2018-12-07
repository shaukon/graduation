<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/12
 * Time: 上午8:59
 */

namespace app\cms\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\cms\model\Tzgs as TzgsModel;

use think\Db;
use think\Hook;
use think\Session;

class Tzgs extends Admin
{
    public function index(){
        $map = $this->getMap();
        $data_list = Db::name('tzgs')
            ->alias('t')
            ->join('admin_user a','t.poster = a.id','left')
            ->field('t.*,a.username')
            ->where($map)->select();

        return ZBuilder::make('table')
            ->setPageTips('我是通知告示界面,请慎重操作！','danger')
            ->setPageTitle('通知告示')
            ->setTableName('tzgs')

            ->setSearch(['id'=>'ID','title'=>'标题'])//设置搜索参数
            ->addColumns([//批量添加列
                ['id', 'ID'],
                ['title', '标题'],
                ['img','配图','picture'],
                ['content', '内容'],
                ['create_time', '发布时间'],
                ['username','发布者'],
                ['right_button','操作','btn']
            ])
            ->addTopButtons('add,delete')//批量添加顶部按钮
            ->addRightButtons('edit,delete')
            ->setRowList($data_list)//设置表格数据
            ->fetch();
    }


    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['poster'] = Session::get('htuid');
            // 验证
            $result = $this->validate($data, 'Tzgs');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($user = TzgsModel::create($data)) {
                $this->success('发布成功la ','index');
            } else {
                $this->error('发布失败');
            }
        }

//        使用ZBuilder构建表单页面，并将页面标题设置为"添加"
//        默认的表单提交地址是当前页面url
        return ZBuilder::make('form')
            ->setPageTitle('发布通知信息')
            ->setPageTips('这里是发布通知信息页面','danger')
            ->setBtnTitle('submit','发布')
            ->setBtnTitle('back','返回前一页')
            ->addFormItems([
                ['text','title','标题'],
                ['ueditor','content','内容'],
                ['image','img','配图']
            ])
            ->fetch();
    }

    public function edit($id=''){
        //查找数据
        $data_list = Db::name('tzgs')->where('id',$id)->find();
        //更新数据
        if($this->request->isPost()){
            $data = $this->request->Post();
            $data['poster'] = Session::get('htuid');
            //验证
            $result = $this->validate($data,'Tzgs');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(TzgsModel::update($data,"id=$id")){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('修改选项')
            ->setPageTips('您好，这里是通知告示修改页面！')
            ->addFormItems([
                ['text','title','标题'],
                ['ueditor','content','内容'],
                ['image','img','配图']
            ])
            ->setFormData($data_list)
            ->fetch();
    }

}