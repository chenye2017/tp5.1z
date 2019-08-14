<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/8/8
 * Time: 17:43
 */

namespace app\index\controller;


class Behavior1
{
    public function run($param)
    {
        var_dump(func_get_args());exit;
    }
}