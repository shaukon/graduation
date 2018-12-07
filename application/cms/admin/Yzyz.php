<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/12
 * Time: 下午12:00
 * 业主验证
 */

namespace app\cms\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use think\Db;
use think\Hook;
use app\cms\model\User as UserModel;


class Yzyz extends Admin
{
    public function index(){

        $map = $this->getMap();
        $data_list = Db::name('user')->where($map)->select();

        return ZBuilder::make('table')
            ->setPageTips('我是户主验证界面,请慎重操作！','danger')
            ->setPageTitle('业主验证')
            ->setTableName('user')
            ->setSearch(['id'=>'ID','name'=>'用户名'])//设置搜索参数
            ->addColumns([//批量添加列
                ['id', 'ID'],
                ['plot','小区'],
                ['name', '用户名'],
                ['building', '楼栋'],
                ['room_number','房号'],
                ['mobile', '手机号'],
                ['status', '审核状态', 'switch'],
            ])
            ->addTopButtons('enable,disable')//批量添加顶部按钮
            ->setRowList($data_list)//设置表格数据
            ->fetch();
    }


}