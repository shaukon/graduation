<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/9
 * Time: 下午6:54
 */

namespace app\cms\validate;


use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        'name' =>'require',
        'brief'=>'require',
        'price'=>'require',
        'reserves'=>'require'
    ];
}