<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/4/30
 * Time: 下午4:40
 * 门户里面的用户控制器
 */

namespace app\cms\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use think\Db;
use think\Hook;
use app\cms\model\User as UserModel;

class User extends Admin
{
    public function index(){

        $map = $this->getMap();
        $data_list = Db::name('user')->where($map)->select();

        return ZBuilder::make('table')
            ->setPageTips('我是户主管理界面,请慎重操作！','danger')
            ->setPageTitle('户主管理')
            ->setTableName('user')
            ->setSearch(['id'=>'ID','name'=>'用户名','shenhe'=>'审核状态'])//设置搜索参数
            ->addColumns([//批量添加列
                ['id', 'ID'],
                ['name', '用户名'],
                ['nickname', '昵称'],
                ['mobile', '手机号'],
                ['status', '审核状态', 'switch'],
                ['signature','个性签名'],
                ['profession','职业'],
                ['constellation','星座'],
                ['affective_state','情感状态'],
                ['interests','兴趣爱好'],
                ['right_button','操作','btn']
            ])
            ->addTopButtons('add,enable,disable,delete')//批量添加顶部按钮
            ->addRightButtons('edit,delete')
            ->setRowList($data_list)//设置表格数据
            ->fetch();
    }

//    新增户主操作表单
    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['password'] = md5($data['password']);
            // 验证
            $result = $this->validate($data, 'User');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($user = UserModel::create($data)) {
                Hook::listen('user_add', $user);
                // 记录行为
                action_log('user_add', 'admin_user', $user['id'], UID);
                $this->success('新增成功la ', url('index'));
            } else {
                $this->error('新增失败');
            }
        }

//        使用ZBuilder构建表单页面，并将页面标题设置为"添加"
//        默认的表单提交地址是当前页面url
        return ZBuilder::make('form')
            ->setPageTitle('添加户主信息')
            ->setPageTips('这里是户主添加页面提示信息','danger')
            ->setBtnTitle('submit','添加户主')
            ->setBtnTitle('back','返回前一页')
            ->addFormItems([
                ['text','name','户主姓名'],
                ['text','nickname','昵称'],
                ['password','password','密码','必填6-12位'],
                ['text','signature','个性签名'],
                ['text','mobile','手机号码'],
                ['radio','sex','性别','',['0'=>'女','1'=>'男','2'=>'保密']],
                ['text','constellation','星座'],
                ['text','affective_state','情感状态','','保密'],
                ['text','profession','职业'],
                ['text','interests','兴趣爱好']
            ])
            ->fetch();
    }


}