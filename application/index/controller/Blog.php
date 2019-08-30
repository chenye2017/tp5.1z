<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/8/28
 * Time: 18:44
 */

namespace app\index\controller;


class Blog
{
    public function index()
    {
        var_dump('index444');
    }

    public function read($id)
    {
        var_dump('read', $id);
    }
}