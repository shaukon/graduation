<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/4
 * Time: 上午11:00
 */

namespace app\cms\validate;


use think\Validate;

class Zngj extends Validate
{
    protected $rule = [
        'name|选项名' => 'require'
    ];

}