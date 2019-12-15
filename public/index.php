<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------



// [ 应用入口文件 ]
namespace think;

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('OS', 'Win');
}else{
    define('OS', 'Linux');
}


//putenv('SystemRoot=cy');

//var_dump(getenv('SystemRoot', true) ?: getenv('SystemRoot'));exit;

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

if (OS == 'Win') {
    $cy = realpath(dirname(__DIR__).'\cy');
} else {
    $cy = dirname(__DIR__).'/cy';
}

Loader::addAutoLoadDir($cy); // 添加cy目录


header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
