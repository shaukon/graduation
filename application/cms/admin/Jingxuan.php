<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/2
 * Time: 下午8:46
 */

namespace app\cms\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\index\controller\Home;
use think\Db;

class Jingxuan extends Admin
{
    public function kaixinyike(){
        $data_list = Db::name('joke')->select();
        return ZBuilder::make('table')
            ->setPageTitle('开心一刻')
//            ->setTableName('joke')
            ->setPageTips('您好，我是开心一刻管理界面')
            ->addColumns([
                ['id','ID'],
                ['title','标题'],
                ['content','内容'],
                ['comment','评论'],
                ['create_time','创建时间'],
                ['right_button','操作','btn']
            ])
            ->addTopButtons(['addKaiXinYiKe,delete'])
            ->addRightButtons('edit,delete')
            ->setRowList($data_list)
            ->fetch();
    }
    public function addKaiXinYiKe(){
        return ZBuilder::make('form')
            ->setPageTitle('添加开心一刻')
            ->setPageTips('您好，这里是开心一个添加页面！')
            ->addFormItems([
                ['text','title','标题'],
                ['Ueditor','content','内容'],
                ['image','img','配图']
            ])
            ->fetch();
    }
    public function detail(){
        return view();
    }
}