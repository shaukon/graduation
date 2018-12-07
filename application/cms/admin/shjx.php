<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/5
 * Time: 上午9:19
 * 生活精选
 */

namespace app\cms\admin;


use app\admin\controller\Admin;
use think\Db;
use app\common\builder\ZBuilder;
use app\cms\model\Shjx as ShjxModel;
class shjx extends Admin
{
    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 生活精选主页面
     */
    public function index(){
        $data_list = Db::name('shjx')->select();
        //添加图片直接传ID，不用设置路径
        return ZBuilder::make('table')
            ->setPageTitle('生活精选')
            //设置表名，不然会操作对应模型前缀的表
            ->setTableName('Kxyk')
            ->setPageTips('您好，我是生活精选管理界面')
            ->addColumns([
                ['id','ID'],
                ['title','标题'],
                ['img','配图','picture'],
                ['content','内容'],
                ['create_time','创建时间'],
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
     * 生活精选添加页面
     */
    public function add(){
        if($this->request->isPost()){
            $data = $this->request->Post();
            //验证
            $result = $this->validate($data,'Shjx');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(ShjxModel::create($data)){
                $this->success('新增成功',url('index'));
            }else{
                $this->error('新增失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('添加生活精选')
            ->setPageTips('您好，这里是生活精选添加页面！')
            ->addFormItems([
                ['text','title','标题'],
                ['Ueditor','content','内容'],
                ['image','img','配图']
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
     * 内容编辑
     */
    public function edit($id=''){
        //查找数据
        $data_list = Db::name('shjx')->where('id',$id)->find();
        //更新数据
        if($this->request->isPost()){
            $data = $this->request->Post();
            //验证
            $result = $this->validate($data,'Shjx');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(ShjxModel::update($data,"id=$id")){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('添加开心一刻')
            ->setPageTips('您好，这里是开心一个添加页面！')
            ->addFormItems([
                ['text','title','标题'],
                ['Ueditor','content','内容'],
                ['image','img','配图']
            ])
            ->setFormData($data_list)
            ->fetch();
    }

}