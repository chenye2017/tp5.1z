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
namespace think;


// 载入Loader类
require __DIR__ . '/library/think/Loader.php';

// 注册自动加载
Loader::register();

// 注册错误和异常处理机制
Error::register(); // 这个地方并没有把用户自定义的异常加载进来


// 实现日志接口
if (interface_exists('Psr\Log\LoggerInterface')) {
    interface LoggerInterface extends \Psr\Log\LoggerInterface
    {
    }
} else {
    interface LoggerInterface
    {
    }
}

/*class a extends b {

}*/
try {

   // throw new \Exception('sss'); // 被 exception_handle 处理 （throw 的 exception 只会被 try catch  或者 exception_handle 处理，error_handle,但是不崩溃了， 就是不能接着往后执行）

} catch (\Exception $E) {

}
//var_dump('222ss');


//trigger_error('eUSER', E_USER_WARNING); //被 error_handle 处理, error-hanlde 又抛出exception， 给exception_hanlde 处理
//require_once 'ss.php'; // 这个地方为什么能捕获到, 因为发出了两个错误信息， 一个warning ,一个error， warning 被 exception_handle 处理， error 是崩溃了， 被register 的函数处理 （即使用了try catch 也不能捕获这个error, 最多捕获warning，这个warning 即使被捕获到，也会php 标准错误处理）

// error 可以返回 false 接着处理

// exception 不可以返回false， throw 的 exception 不处理都是 fatal error

// require 或者 本文件的拼写错误，都会造成无法挽回的fatal error

// exception_handle 相比较 try catch 就是程序一定会停止

// error_handle 是互补的作用，捕获warning

// register 是在 php 标准处理之后才会被执行

// 注册类库别名
Loader::addClassAlias([
    'App'      => facade\App::class,
    'Build'    => facade\Build::class,
    'Cache'    => facade\Cache::class,
    'Config'   => facade\Config::class,
    'Cookie'   => facade\Cookie::class,
    'Db'       => Db::class,
    'Debug'    => facade\Debug::class,
    'Env'      => facade\Env::class,
    'Facade'   => Facade::class,
    'Hook'     => facade\Hook::class,
    'Lang'     => facade\Lang::class,
    'Log'      => facade\Log::class,
    'Request'  => facade\Request::class,
    'Response' => facade\Response::class,
    'Route'    => facade\Route::class,
    'Session'  => facade\Session::class,
    'Url'      => facade\Url::class,
    'Validate' => facade\Validate::class,
    'View'     => facade\View::class,
]);
