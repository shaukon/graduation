<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\user;
use think\Db;
use think\Session;

class Index extends Home
{
    /**
     * @return \think\response\View
     * 首页操作
     */
    public function index ()
    {
        if(!isset($_COOKIE['nickname'])){
            $nickname = "";
        }else{
            $nickname = $_COOKIE['nickname'];
        }
        $this->assign('nickname',$nickname);
//

//        echo $path;

        return view('index');
    }

    /**
     * @return \think\response\View
     * 登陆控制器
     */
    public function login(){
        return view();
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 登陆检查
     */
    public function loginCheck(){
        if($_POST){
            //获取登陆的信息
            $nickname = $_POST['nickname'];
            //对密码进行加密
            $password = md5($_POST['password']);

            //实例化用户的对象
            $user = new user();
            //条件数组
            $map =[
                'nickname'=> $nickname,
                'password'=> $password
            ];
            //根据用户昵称和密码，查询数据库集
            $userInfo = $user->where($map)
                ->find();
            if($userInfo){
                //将用户昵称放入cookie中
                setcookie('nickname',$nickname,time()+2*60*60);
                Session::set('nickname',$nickname);

                return $this->success("欢迎您:$nickname",'index/index/index');
            }else{
                return $this->error("账号或密码错误！");
            }
        }

    }

//      用户信息注册控制器
    public function register(){
        return view();
    }

//      用户信息注册
    public function reg(){
        $user = new user; //实例化user对象
//        对密码进行md5加密
        $_POST['password'] = md5($_POST['password']);

//        allowField(true),过滤post数组中的非数据表字段数据
        $result = $user->allowField(true)->save($_POST);
        if($result){
            //设置成功后跳转页面的地址，默认返回的页面是$_SERVER['HTTP_REFERER']
            $this->success('注册成功,欢迎您加入小康之家！','index/index/index');
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('注册失败');
        }

    }

    public function userInfo(){
        $data = Db::table('user')->select();
        echo "<pre>";
        print_r($data);
//        var_dump($data);
    }


//    物业管家
    public function wuYeGuanJia(){
        $data = Db::name('wygj')->where('is_use',1)->select();
//        foreach ($data as &d){
//            $d['icon'] = get_file_path($d['icon']);
//        }
        foreach ($data as &$data1){
            $data1['icon'] = get_file_path($data1['icon']);
        }
        $this->assign('wuyeguanjia',$data);
        return view();
    }

    //手机开门
    public function sjkm(){
        return view();
    }
    //通知告示
    public function tzgs(){
        $data = Db::name('tzgs')->select();
        foreach ($data as &$d){
            $d['img'] = get_file_path($d['img']);
        }
        $this->assign('data',$data);
        return view();
    }

    public function tzgsDetail($id=''){
        $data = Db::name('tzgs')
            ->alias('t')
            ->join('admin_user a','t.poster = a.id','left')
            ->field('t.*,a.username')
            ->where('t.id',$id)->find();
        $data['img'] = get_file_path($data['img']);
        $this->assign('data',$data);
        return view();
    }
    //联系物业
    public function lxwy(){
        $data = Db::name('lxwy')->select();
        foreach ($data as &$d){
            $d['head'] = get_file_path($d['head']);
        }
        $this->assign('data',$data);
        return view();
    }
    //访客通行
    public function fktx(){
        return view();
    }
    //物业报修
    public function wybx(){
        return view();
    }
    //快递代收
    public function kdds(){
        return view();
    }
    //管理处私信
    public function glcsx(){
        return view();
    }
    //账单缴费
    public function zdjf(){
        return view();
    }
    //门禁授权
    public function mjsq(){
        return view();
    }
    //访客授权
    public function fksq(){
        return view();
    }




//    生活管家
    public function shengHuoGuanJia(){
        $moren    = Db::name('shgj')->where('type',0)->select();
        foreach ($moren as &$data1){
            $data1['icon'] = get_file_path($data1['icon']);
        }
        $shenghuo = Db::name('shgj')->where('type',1)->select();
        foreach ($shenghuo as &$data1){
            $data1['icon'] = get_file_path($data1['icon']);
        }
        $jujia    = Db::name('shgj')->where('type',2)->select();
        foreach ($jujia as &$data1){
            $data1['icon'] = get_file_path($data1['icon']);
        }
        $xiuxian  = Db::name('shgj')->where('type',3)->select();
        foreach ($xiuxian as &$data1){
            $data1['icon'] = get_file_path($data1['icon']);
        }
        $gonggong = Db::name('shgj')->where('type',4)->select();
        foreach ($gonggong as &$data1){
            $data1['icon'] = get_file_path($data1['icon']);
        }
        $this->assign('moren',$moren);
        $this->assign('shenghuo',$shenghuo);
        $this->assign('jujia',$jujia);
        $this->assign('xiuxian',$xiuxian);
        $this->assign('gonggong',$gonggong);
        return view();
    }
//    智能管家
    public function zhiNengGuanJia(){
        $zhineng = Db::name('zngj')->select();
        foreach ($zhineng as &$data1){
            $data1['icon'] = get_file_path($data1['icon']);
        }
        $this->assign('zhineng',$zhineng);
        return view();
    }
//    智能家居
    public function zhiNengJiaJu(){
        return view();
    }

    /**
     * 绑定智能设备
     */
    public function bdznsb(){
        if($_POST){
            $_POST['password'] = md5($_POST['password']);
            $re = Db::name('znjj')->insert($_POST);

            if($re){
                return $this->success('绑定成功');
            }else{
                return $this->error('绑定失败');
            }
        } else {
            echo "hello world";
        }
    }
}
