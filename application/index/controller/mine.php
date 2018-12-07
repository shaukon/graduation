<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/4/25
 * Time: 上午10:15
 * 我的
 */

namespace app\index\controller;
use think\Cookie;
use think\Db;
use think\Session;

class mine extends Home
{
    public function index(){
        //首先判断是否已经登陆
        if (Session::has('nickname')){
            $nickname = Session::get('nickname');
            //根据该Nickname查询该户主的所有信息。
            $userData = Db::name('user')->where('nickname',$nickname)->find();
            //我发布的帖子
            $tiezi = Db::name('circle')
                ->alias('c')
                ->join('user u','c.u_id = u.id','left')
                ->field('c.*,u.head_img,u.nickname')
                ->where('u_id',$userData['id'])
                ->select();
            //我的留言
            $liuyan = Db::name('words')
                ->alias('w')
                ->join('user u','w.lid = u.id','left')
                ->field('w.*,u.head_img,u.nickname')
                ->where('w.bid',$userData['id'])
                ->select();
            $this->assign('liuyan',$liuyan);
            $this->assign('wdtz',$tiezi);
            $this->assign('userInfo',$userData);
            $this->assign('nickname',$nickname);


            return view();
        }else{
            //跳转到登陆界面
            $this->redirect('index/index/login');

        }
    }

    /**
     * 注销登陆
     */
    public function logout(){
        //清空session
        Session::delete('nickname');
        //清空cookie
        Cookie::delete('nickname');

        $this->success('注销成功，即将前往首页！','index/index/index');
    }

    /**
     * @return \think\response\View
     * 修改头像
     */
    public function head_img(){
        return view();
    }

    public function doHeadImg(){
        //获取登陆昵称
        $nickname = Session::get('nickname');
        //查得登陆用户的id
        $u_id= Db::name('user')->where('nickname',$nickname)->value('id');
        $_POST['u_id'] = $u_id;

        //获取上传的图片
        $img = $_FILES['img']['tmp_name'];
        if($img){
            //上传的路径，建议写物理路径
            $uploadDir = 'static/upload/head';
            //创建文件夹
            if(!file_exists($uploadDir)){
                mkdir($uploadDir,0777);
            }
            //用时间戳来保存图片，防止重复
            $targetFile = $uploadDir.'/'.time().$_FILES['img']['name'];
            //将临时文件 移动到我们指定的路径
            $upload_ret = move_uploaded_file($img,$targetFile);
        }

        //将数据保存到数据库
        $re = Db::name('user')
            ->where('id',$u_id)
            ->update(['head_img'=>$targetFile]);
        if($re){
            return $this->success('头像修改成功','index/mine/index');
        }else{
            return $this->error('修改失败');
        }
    }

    /**
     * @param string $id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 删除帖子
     */
    public function deleteTiezi($id=''){
        $re = Db::name('circle')
            ->where('id',$id)
            ->delete();
        if ($re){
            return $this->success('删除成功！');
        }else{
            return $this->error('删除失败！');
        }
    }

}