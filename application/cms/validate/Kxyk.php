<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/3
 * Time: 上午9:28
 */

namespace app\cms\validate;


use think\Validate;

class Kxyk extends Validate
{
    //定义规则
    protected $rule = [
        'title|标题' => 'require'
    ];

}