<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/5
 * Time: 上午9:30
 */

namespace app\cms\validate;


use think\Validate;

class Shjx extends Validate
{
    protected $rule = [
        'title|标题' => 'require'
    ];

}