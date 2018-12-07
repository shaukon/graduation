<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/9
 * Time: 下午6:47
 */

namespace app\cms\admin;


use app\admin\controller\Admin;

use think\Db;
use app\common\builder\ZBuilder;
use app\cms\model\Goods as GoodsModel;
class goods extends Admin
{
    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 商品列表
     */
    public function index(){
        $data_list = Db::name('goods')->select();
        //添加图片直接传ID，不用设置路径
        return ZBuilder::make('table')
            ->setPageTitle('商品管理')
            //设置表名，不然会操作对应模型前缀的表
            ->setTableName('goods')
            ->setPageTips('您好，我是商品管理界面。')
            ->addColumns([
                ['id','ID'],
                ['name','商品名'],
                ['brief','商品简介'],
                ['price','商品价格'],
                ['old_price','旧价格'],
                ['sell_num','销量'],
                ['img1','图片一','picture'],
                ['img2','图片二','picture'],
                ['img3','图片三','picture'],
                ['reserves','储量'],
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
     * 商品添加
     */
    public function add(){
        if($this->request->isPost()){
            $data = $this->request->Post();
            //验证
            $result = $this->validate($data,'goods');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(GoodsModel::create($data)){
                $this->success('新增成功',url('index'));
            }else{
                $this->error('新增失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('添加商家')
            ->setPageTips('您好，这里是商家添加页面！')
            ->addFormItems([
                ['text','name','商品名'],
                ['text','brief','商品简介'],
                ['number','price','售价'],
                ['number','old_price','原价格'],
                ['number','reserves','储量'],
                ['image','img1','图片一'],
                ['image','img2','图片二'],
                ['image','img3','图片三']
            ])
            ->fetch();
    }

    public function edit($id=''){
        //查找数据
        $data_list = Db::name('goods')->where('id',$id)->find();
        //更新数据
        if($this->request->isPost()){
            $data = $this->request->Post();
            //验证
            $result = $this->validate($data,'goods');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(GoodsModel::update($data,"id=$id")){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('修改选项')
            ->setPageTips('您好，这里是商品修改页面！')
            ->addFormItems([
                ['text','name','商品名'],
                ['text','brief','商品简介'],
                ['number','price','售价'],
                ['number','old_price','原价格'],
                ['number','reserves','储量'],
                ['image','img1','图片一'],
                ['image','img2','图片二'],
                ['image','img3','图片三']
            ])
            ->setFormData($data_list)
            ->fetch();
    }


}