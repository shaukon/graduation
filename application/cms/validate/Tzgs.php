<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/12
 * Time: 上午9:14
 */

namespace app\cms\validate;


use think\Validate;

class tzgs extends Validate
{
    protected $rule = [
        'title|标题' => 'require'
    ];


}