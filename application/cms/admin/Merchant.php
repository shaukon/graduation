<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/9
 * Time: 下午6:20
 * 商家管理
 */

namespace app\cms\admin;
use app\admin\controller\Admin;
use think\Db;
use app\common\builder\ZBuilder;
use app\cms\model\Merchant as MerchantModel;

class Merchant extends Admin
{
    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 商家信息表
     */
    public function index(){
        $data_list = Db::name('merchant')->select();
        //添加图片直接传ID，不用设置路径
        return ZBuilder::make('table')
            ->setPageTitle('商家管理')
            //设置表名，不然会操作对应模型前缀的表
            ->setTableName('merchant')
            ->setPageTips('您好，我是商家管理界面。')
            ->addColumns([
                ['id','ID'],
                ['logo','LOGO','picture'],
                ['name','商家名称']
            ])
            ->addTopButtons('add,delete')
            ->addRightButtons('edit,delete')
            ->setRowList($data_list)
            ->fetch();
    }

    /**
     * @return mixed
     * @throws \think\Exception
     *
     */
    public function add(){
        if($this->request->isPost()){
            $data = $this->request->Post();
            //验证
            $result = $this->validate($data,'Merchant');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(MerchantModel::create($data)){
                $this->success('新增成功',url('index'));
            }else{
                $this->error('新增失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('添加商家')
            ->setPageTips('您好，这里是商家添加页面！')
            ->addFormItems([
                ['text','name','商家名'],
                ['image','logo','LOGO']
            ])
            ->fetch();
    }

    public function edit($id=''){
        //查找数据
        $data_list = Db::name('merchant')->where('id',$id)->find();
        //更新数据
        if($this->request->isPost()){
            $data = $this->request->Post();
            //验证
            $result = $this->validate($data,'merchant');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(MerchantModel::update($data,"id=$id")){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('修改选项')
            ->setPageTips('您好，这里是商家修改页面！')
            ->addFormItems([
                ['image','LOGO','LOGO'],
                ['text','name','商家名称']
            ])
            ->setFormData($data_list)
            ->fetch();
    }


}