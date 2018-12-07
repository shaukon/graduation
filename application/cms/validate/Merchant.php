<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/9
 * Time: 下午6:26
 */

namespace app\cms\validate;


use think\Validate;

class Merchant extends Validate
{
    protected $rule = [
        'name|商家名称' => 'require'
    ];

    protected $scene = [
        'name' => ['name']
    ];

}