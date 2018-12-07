<?php
/**
 * Created by PhpStorm.
 * User: xxk
 * Date: 2018/5/3
 * Time: 下午8:25
 *
 */

namespace app\cms\validate;


use think\Validate;

/**
 * 物业管家验证器
 * Class Wygj
 * @package app\cms\validate
 */
class Wygj extends Validate
{
    protected $rule = [
        'name|选项名' => 'require'
    ];

}