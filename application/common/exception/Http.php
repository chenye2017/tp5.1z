<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/8/29
 * Time: 20:24
 */

namespace app\common\exception;


use Exception;
use think\exception\Handle;

class Http extends Handle
{
    public function render(Exception $e)
    {
        var_dump('222');exit;
    }
}