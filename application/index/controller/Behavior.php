<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/8/8
 * Time: 17:32
 */

namespace app\index\controller;


class Behavior
{
    public $name = 'be';

    public function run($params)
    {
        var_dump('hahah');exit;
        return false;
        var_dump($params, 11);
    }

    public function test()
    {
        var_dump('wo yeshi yilai');
    }


}