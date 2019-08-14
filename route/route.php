<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------



Route::get('/', function () {
 //facecade 中获取的是容器中的实例，config() 这种获取的都不是容器中的实例
    var_dump(1111);
var_dump(11);exit;
    var_dump(app('hook')->get());exit;

    var_dump(11);exit;
    var_dump(\think\facade\Config::get());exit;
    $c = new \think\Config();
    var_dump($c->get());exit;

    $cy = new \Cy();
    var_dump($cy->test());
});


Route::get('/yilai', function (\app\index\controller\Behavior $obj) {
    $obj->test();
});

\think\facade\Route::get('/:name/:id', function ($id, $name) {
   var_dump("id is $id, name is $name");
});



Route::get('/testhook', '\app\index\controller\Index@testhook');

Route::get('/test', '/');

return [

];
