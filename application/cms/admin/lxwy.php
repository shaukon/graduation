<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/12
 * Time: 上午11:01
 * 联系物业，管理所有的物业工作人员
 */

namespace app\cms\admin;
use app\admin\controller\Admin;
use think\Db;
use app\common\builder\ZBuilder;
use app\cms\model\Lxwy as LxwyModel;

class lxwy extends Admin
{
    public function index(){

        //读取用户数据,分页数据使用thinkPHP的paginate()方法
        $data_list = Db::name('lxwy')->select();
        //添加图片直接传ID，不用设置路径
        return ZBuilder::make('table')
            ->setPageTitle('物业人员管理')
            //设置表名，不然会操作对应模型前缀的表
            ->setTableName('lxwy')
            ->setPageTips('您好，我是物业人员界面')
            ->addColumns([
                ['id','ID'],
                ['head','头像','picture'],
                ['name','名称'],
                ['work_time','工作时间'],
                ['status','启用','switch'],
                ['mobile','手机'],
                ['right_button','操作','btn']
            ])
            ->addTopButtons('add,delete')
            ->addRightButtons('edit,delete')
            ->setRowList($data_list)
            ->fetch();
    }

    public function add(){
        if($this->request->isPost()){
            $data = $this->request->Post();

            if(LxwyModel::create($data)){
                $this->success('新增成功',url('index'));
            }else{
                $this->error('新增失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('添加物业人员选项')
            ->setPageTips('您好，这里是物业人员添加页面！')
            ->addFormItems([
                ['image','head','头像'],
                ['text','name','名称'],
                ['text','work_time','工作时间'],
                ['text','mobile','手机']
            ])
            ->fetch();

    }

    public function edit($id=''){
        //查找数据
        $data_list = Db::name('lxwy')->where('id',$id)->find();
        //更新数据
        if($this->request->isPost()){
            $data = $this->request->Post();

            if(LxwyModel::update($data,"id=$id")){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('修改选项')
            ->setPageTips('您好，这里是物业人员修改页面！')
            ->addFormItems([
                ['image','head','头像'],
                ['text','name','名称'],
                ['text','work_time','工作时间'],
                ['text','mobile','手机']
            ])
            ->setFormData($data_list)
            ->fetch();
    }

}