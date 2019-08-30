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

// 这个文件的名称可以随便命名，后期会统一加载

// miss 方法也能支持匿名函数
Route::miss(function() {
    var_dump('ww');
});



Route::resource('blog', 'index/blog'); // 出现错误，不知道默认调用的是谁

Route::get('blog/:id', function() {
    var_dump('read');exit;
});

Route::rule('/test3', 'index/Index/hello')->method('get|post'); // method 最终还是要转换成 ruleltem (继承于 rule ,rule 中有method 的方法) ,option 属性

Route::get('/test1/:name/:sex', function($sex, $name) {
    var_dump($name, $sex);
});



Route::get('/', function () {
    //trigger_error('eUSER', E_USER_NOTICE); 能捕获
   // require_once 'ss.php'; 不能捕获
    throw new \Exception('test'); // 能捕获 ，所以也是有try catch 的
    //var_dump(\think\Container::get('app')->config('exception_tmpl'));
    var_dump(111);exit;

    \think\Container::get('log')->write('test');exit;
   // var_dump(interface_exists('Psr\Log\LoggerInterface'));exit;

 //facecade 中获取的是容器中的实例，config() 这种获取的都不是容器中的实例
   //
    //
    // require_once './pp.php';
  //  var_dump(1111, $a);
var_dump(2);exit;
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
