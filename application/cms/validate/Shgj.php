<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/3
 * Time: 下午9:47
 */

namespace app\cms\validate;


use think\Validate;

/**
 * 生活管家验证器
 * Class Shgj
 * @package app\cms\validate
 */
class Shgj extends Validate
{
    protected $rule = [
        'name|选项名' => 'require'
    ];

}