<?php
namespace app\index\controller;

use think\Container;
use think\facade\Hook;
use think\Request;

class Index
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function hello($name = 'ThinkPHP5')
    {
        var_dump(111);
        return 'hello,' . $name;
    }

    public function testhook(Request $req)
    {
        var_dump(app('be'));exit;
      //  var_dump('start');
        var_dump($req->name);exit;

        Hook::add('test_1', [Behavior::class, Behavior1::class]); // 添加钩子和行为绑定
        Hook::add('test_2', Behavior::class);

      //  var_dump(Hook::get());
        Hook::listen('test_1',['name'=>'test_1', 'test'=>11]); // 设置钩子(，执行到这，就去tags 里面找对应的类，然后执行)；


    }

    /**
     * 注解路由
     * @route('cy3')
     */
    public function test3()
    {
        var_dump('e');
    }
}
