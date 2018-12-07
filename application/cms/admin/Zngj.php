<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/4
 * Time: 上午10:39
 */

namespace app\cms\admin;


use app\admin\controller\Admin;
use think\Db;
use app\common\builder\ZBuilder;
use app\cms\model\Zngj as ZngjModel;
class Zngj extends Admin
{
    //首页内容
    public function index(){
        $data_list = Db::name('zngj')->select();
        //添加图片直接传ID，不用设置路径
        return ZBuilder::make('table')
            ->setPageTitle('物业管家')
            //设置表名，不然会操作对应模型前缀的表
            ->setTableName('zngj')
            ->setPageTips('您好，我是智能管家界面')
            ->addColumns([
                ['id','ID'],
                ['icon','图标','picture'],
                ['name','选项名'],
                ['url','连接地址'],
                ['is_use','启用','switch'],
                ['right_button','操作','btn']
            ])
            ->addTopButtons('add,delete')
            ->addRightButtons('edit,delete')
            ->setRowList($data_list)
            ->fetch();
    }

    /**
     * @return mixed
     * @throws \think\Exception
     * 添加
     */
    public function add(){
        if($this->request->isPost()){
            $data = $this->request->Post();
            //验证
            $result = $this->validate($data,'zngj');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(ZngjModel::create($data)){
                $this->success('新增成功',url('index'));
            }else{
                $this->error('新增失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('添加智能管家选项')
            ->setPageTips('您好，这里是智能管家添加页面！')
            ->addFormItems([
                ['image','icon','图标'],
                ['text','name','选项名'],
                ['text','url','链接']
            ])
            ->fetch();

    }

    /**
     * @param string $id
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 修改
     */
    public function edit($id=''){
        //查找数据
        $data_list = Db::name('zngj')->where('id',$id)->find();
        //更新数据
        if($this->request->isPost()){
            $data = $this->request->Post();
            //验证
            $result = $this->validate($data,'Zngj');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(ZngjModel::update($data,"id=$id")){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('修改选项')
            ->setPageTips('您好，这里是智能管家修改页面！')
            ->addFormItems([
                ['image','icon','图标'],
                ['text','name','选项名'],
                ['text','url','链接']
            ])
            ->setFormData($data_list)
            ->fetch();
    }


}