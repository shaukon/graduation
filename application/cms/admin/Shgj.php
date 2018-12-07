<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/3
 * Time: 下午9:41
 * 生活管家
 */

namespace app\cms\admin;


use app\admin\controller\Admin;
use think\Db;
use app\common\builder\ZBuilder;
use app\cms\model\Shgj as ShgjModel;
class Shgj extends Admin
{
    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 生活管家主页
     */
    public function index(){
        $type = [
            '0'=>'默认类型',
            '1'=>'生活服务',
            '2'=>'居家便利',
            '3'=>'休闲娱乐',
            '4'=>'公共服务'
        ];

        //添加好筛选字段后，在查询数据之前获取筛选的数据
        //获取筛选
        $map = $this->getMap();
        //读取用户数据,分页数据使用thinkPHP的paginate()方法
        $data_list = Db::name('shgj')->where($map)->paginate();
        //添加图片直接传ID，不用设置路径
        return ZBuilder::make('table')
            ->setPageTitle('生活管家')
            //设置表名，不然会操作对应模型前缀的表
            ->setTableName('shgj')
            ->setPageTips('您好，我是生活管家界面')
            ->addColumns([
                ['id','ID'],
                ['icon','图标','picture'],
                ['name','选项名'],
                ['url','连接地址'],
                ['is_use','启用','switch'],
                ['type','类型','select',$type],
                ['right_button','操作','btn']
            ])
            ->addFilter('type',$type) //添加筛选
            ->addTopButtons('add,delete')
            ->addRightButtons('edit,delete')
            ->setRowList($data_list)
            ->fetch();
    }


    /**
     * @return mixed
     * @throws \think\Exception
     * 添加控制器
     */
    public function add(){
        if($this->request->isPost()){
            $data = $this->request->Post();
            //验证
            $result = $this->validate($data,'Wygj');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(ShgjModel::create($data)){
                $this->success('新增成功',url('index'));
            }else{
                $this->error('新增失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('添加物业管家选项')
            ->setPageTips('您好，这里是物业管家添加页面！')
            ->addFormItems([
                ['image','icon','图标'],
                ['text','name','选项名'],
                ['text','url','链接'],
                ['radio','type','类型','',['0'=>'默认类型','1'=>'生活服务','2'=>'居家便利','3'=>'休闲娱乐','4'=>'公共服务'],'0']
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
     * 修改选项
     */
    public function edit($id=''){
        //查找数据
        $data_list = Db::name('shgj')->where('id',$id)->find();
        //更新数据
        if($this->request->isPost()){
            $data = $this->request->Post();
            //验证
            $result = $this->validate($data,'Shgj');
            //验证失败，输出错误信息
            if(true !== $result) $this->error($result);
            if(ShgjModel::update($data,"id=$id")){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTitle('修改选项')
            ->setPageTips('您好，这里是生活管家修改页面！')
            ->addFormItems([
                ['image','icon','图标'],
                ['text','name','选项名'],
                ['text','url','链接'],
                ['radio','type','类型','',['0'=>'默认类型','1'=>'生活服务','2'=>'居家便利','3'=>'休闲娱乐','4'=>'公共服务']]
            ])
            ->setFormData($data_list)
            ->fetch();
    }


}

















