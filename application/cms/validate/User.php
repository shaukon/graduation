<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/4/30
 * Time: 下午5:27
 */

namespace app\cms\validate;
use think\Validate;

class User extends Validate
{
    // 定义验证规则
    protected $rule = [
        'name|户主名称' => 'require',
    ];

    // 定义验证场景
    protected $scene = [
        'name' => ['name']
    ];

}