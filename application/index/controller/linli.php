<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/4/18
 * Time: 下午8:44
 */

namespace app\index\controller;


use think\Controller;
use think\Cookie;
use think\Db;
use think\Session;

class linli extends Home
{
    public function index(){

        //生活精选数据
        $kxyk = Db::name('kxyk')->select();
        foreach ($kxyk as &$data){
            $data['img'] = get_file_path($data['img']);
        }
        //开心一刻数据
        $shjx = Db::name('shjx')->select();
        foreach ($shjx as &$data){
            $data['img'] = get_file_path($data['img']);
        }
        //圈子分享数据
        $share = Db::name('circle')->where('type',0)->order('create_time','desc')->select();
        foreach ($share as &$data){
            $user = Db::name('user')->where('id',$data['u_id'])->find();

            $data['nickname'] = $user['nickname'];
            $data['head_img'] = $user['head_img'];
        }
        //圈子关注数据
        $guanzhu = 6;
        $attention = Db::name('circle')
            ->where('u_id',$guanzhu)
            ->order('create_time','desc')
            ->limit(3)
            ->select();
        foreach ($attention as &$data){
            $user = Db::name('user')->where('id',$data['u_id'])->find();

            $data['nickname'] = $user['nickname'];
            $data['head_img'] = $user['head_img'];
        }

        //圈子二手数据
        $second_hand = Db::name('circle')->where('type',1)->order('create_time','desc')->select();
        foreach ($second_hand as &$data){
            $user = Db::name('user')->where('id',$data['u_id'])->find();

            $data['nickname'] = $user['nickname'];
            $data['head_img'] = $user['head_img'];
        }

        //圈子话题数据
        $topic = Db::name('circle')->where('type',2)->order('create_time','desc')->select();
        foreach ($topic as &$data){
            $user = Db::name('user')->where('id',$data['u_id'])->find();

            $data['nickname'] = $user['nickname'];
            $data['head_img'] = $user['head_img'];
        }


        $this->assign('share',$share);
        $this->assign('attention',$attention);
        $this->assign('second_hand',$second_hand);
        $this->assign('topic',$topic);

        $this->assign('shjx',$shjx);
        $this->assign('kxyk',$kxyk);
        return view();
    }

    /**
     * @param $id
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 生活精选详情
     */
    public function shjxdetail($id){
        //获取ID参数
        $data = Db::name('shjx')->where('id',$id)->find();
        $data['img'] = get_file_path($data['img']);
        $this->assign('data',$data);
        return view();
    }


    /**
     * @return \think\response\View
     * 发布帖子
     */
    public function postMessage(){
        return view();
    }

    /**
     * 处理发布的帖子
     */
    public function doPost(){
        //获取登陆昵称
        $nickname = Session::get('nickname');
        //查得登陆用户的id
        $u_id= Db::name('user')->where('nickname',$nickname)->value('id');
        $_POST['u_id'] = $u_id;

        //获取上传的图片
        $img = $_FILES['img']['tmp_name'];
        if($img){
            //上传的路径，建议写物理路径
            $uploadDir = 'static/upload';
            //创建文件夹
            if(!file_exists($uploadDir)){
                mkdir($uploadDir,0777);
            }
            //用时间戳来保存图片，防止重复
            $targetFile = $uploadDir.'/'.time().$_FILES['img']['name'];
            //将临时文件 移动到我们指定的路径
            $upload_ret = move_uploaded_file($img,$targetFile);
        }

        $data = $_POST;
        $data['img'] = $targetFile;
        //将数据保存到数据库
        $re = Db::name('circle')->insert($data);
        if ($re) {
            return $this->success('发布帖子成功,即将跳转。。。');
        }else{
            return $this->error('发布失败');
        }
    }

    public function person($u_id=''){
//        $nickname = $_GET['nickname'];
        //获取小林的所有帖子
        //根据nickname获取发帖人的信息

//        var_dump($userInfo);
//        echo "<hr>";
//        var_dump($userInfo['id']);
//        die;
        //用户信息
        $userInfo = Db::name('user')
            ->where('id',$u_id)
            ->find();
        //性别处理
        if($userInfo['sex'] = 0){
            $userInfo['sex'] = "女";
        }else{
            $userInfo['sex'] = "男";
        }
        //帖子数量
        $count = Db::name('circle')
            ->where('u_id',$u_id)
            ->count();
//        var_dump($count);

        $tiezi = Db::name('circle')
            ->alias('c')
            ->join('user u','c.u_id = u.id','left')
            ->where('u_id',$u_id)
            ->order('create_time','desc')
            ->field('u.id uid,u.nickname,u.sex,u.head_img,c.*')
            ->select();



        $this->assign('userInfo',$userInfo);
        $this->assign('count',$count);
        $this->assign('tiezi',$tiezi);

        return view();
    }

    public function leaveWords($id=''){
//        var_dump($id);
//        $userId = Session::get('nickname');
//        var_dump($userId);
        $this->assign('id',$id);
        return view();
    }

    public function doLeave($id=''){
        //被留言的人的id
        var_dump($id);

        //从session中获取当前用户的nickname
        $user_nickname = Session::get('nickname');
        //获取当前用户的ID
        $userId = Db::name('user')->where('nickname',$user_nickname)->value('id');
        var_dump($userId);
//        var_dump($userId);
        $data = $_POST;
        var_dump($data);
        $data1 = ['bid'=>$id,'lid'=>$userId,'words'=>$data['leaveWords']];
        $re = Db::name('words')->insert($data1);
        if($re){
            return $this->success('留言成功！',"index/linli/person?u_id=$id");
        }else{
            return $this->error('留言失败');
        }

        //保存留言内容

    }




}