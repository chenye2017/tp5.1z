<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/8/30
 * Time: 18:09
 */

namespace app\index\controller;


use app\index\model\Danmu;
use think\Controller;
use think\Request;

class Test extends Controller
{
    public function index(Request $request)
    {
        try {
            $danmu = new Danmu();
            var_dump($danmu->all());
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        exit;

        return ['ss'=>111];

        var_dump(getallheaders());exit;

        var_dump($_SERVER);
       // var_dump(111555);exit;
       // var_dump(11, $_SERVER['HTTP_REFERER']);
        $this->error('success');

        var_dump('111111sss');
        return 'test';
    }
}