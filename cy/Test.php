<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/8/4
 * Time: 13:08
 */

//require_once '../vendor/autoload.php';
require_once '../thinkphp/library/think/Loader.php';
require_once '../thinkphp/helper.php';



$nowDir = realpath(__DIR__);



//var_dump($nowDir);

\think\Loader::register(); // 把composer 的自动加载接管过来;
\think\Loader::addAutoLoadDir(realpath(__DIR__));



// 注册类库别名
\think\Loader::addClassAlias([
    'Config'      => \think\facade\Config::class,
    'Env' => \think\facade\Env::class
]);

$data = [
   // 'name'  => 'thinkphp',
  //  'email' => 'thinkphp@qq.com',
];

\think\Error::register();

require_once "./think.php"; // log interface

require_once './error.php';



//trigger_error('warning', E_USER_NOTICE);

exit;

\think\Container::get('app');exit;

\think\Container::get('log')->log(100,'222');exit;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25',
        'age'   => 'require|number|between:1,120',
        'email' => 'email',
    ];

    protected $message  =   [
        'name.require' => '名称必须',
        'name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在1-120之间',
        'email'        => '邮箱格式错误',
    ];

}
$validate = new User();

if (!$validate->batch(true)->check($data)) {
    try {
        throw new \think\exception\ValidateException($validate->getError());
    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }
}
exit;


$dbms='mysql';     //数据库类型
$host='127.0.0.1'; //数据库主机名
$dbName='zerg';    //使用的数据库
$user='root';      //数据库连接用户名
$pass='';          //对应的密码
$dsn="$dbms:host=$host;dbname=$dbName";


try {
    $con = new \PDO($dsn, $user, $pass);
    $con->prepare('bogus sql');
    var_dump($con->errorInfo());
} catch (\PDOException $e) {
    var_dump($e->errorInfo, 11);
    exit;
}

exit;

try {
    throw new \Exception('test', 0, new \Exception('test1'));
} catch (\Exception $e) {
    $tmp = $e->getPrevious();
    var_dump($tmp->getMessage());
}

exit;


class test extends RuntimeException
{

}
$var =1;
try {
    1/0;
    require 'some-file-with-syntax-error.php';
    assert(2 < 1, 'Two is less than one');
    var_dump(1/0);
   // throw  new Exception('test');
    $var->method();
} catch (\Exception $e) {
    var_dump($e->getMessage());
    exit;
} catch (\Throwable $e) {
    var_dump($e->getMessage());
    exit;
} catch (\Error $e) {
    var_dump($e->getMessage());
    exit;
}

exit;

$c = new \think\Config();
$c->load(realpath('./test.ini'));
var_dump(var_dump($c->test()),$c['cy']);exit;

\think\Config::load(realpath('./test.ini'));


\think\Container::get('app')->run();exit;

Cyface::test();exit;

var_dump(\think\Facade::make('Cyface'));exit;

var_dump(Cyface::instance());exit;

//Cyface::bind('cy', '\Cyconcrete');

//$z = app('\Cyface');




$z1 = app('Cy');
$z1->num  = 2;

$z = \think\Container::get('Cy');

var_dump($z->num);exit;

\think\facade\Config::test();exit;

$config = app('config'); //这个里面获取的也是实例（除了container，app 别的还不一定是单例），感觉这个方法明应该去container(), app() 容易造成误解，和app 类

$config->load($nowDir.'/test.ini', 'test');

var_dump(app('config'));exit;

var_dump(new \think\App());exit;

\think\Container::get('app');
var_dump(\think\Container::getInstance());exit;

\think\Container::get('Cy');


\think\facade\Env::load($nowDir.'/test.ini');

var_dump(\think\facade\Env::get());exit;


\think\facade\Config::load($nowDir.'/test.ini', 'test');

var_dump(\think\facade\Config::get());exit;


//\think\facade\Env::load($nowDir.'/test.ini');

\think\facade\Env::set('name.pp', 1);

\think\facade\Env::set(['nAme' => 'cy', 'ES' => 1, 'arr' => [1,2]]);



var_dump(\think\facade\Env::get('PEAR_SYSCONF_DIR'));EXIT;




config(['name' => 'cy', 'sex'=>'boy'], 'app');

//\think\facade\Config::setYaconf('tp51');

var_dump(\think\facade\Config::pull('app'));exit;

var_dump(config('?name'));exit;

var_dump(config('1.'));exit;

    var_dump(config('.','cy'), \Config::get(), \think\facade\Config::get());exit;

var_dump(\think\facade\Config::get());exit;

error_reporting(E_ALL);

$c = new \think\Config(__DIR__, 'json');

$c->load($nowDir.'/ini.yaml', 'ini');

//$c->load($nowDir.'/ini.php', 'ini');
var_dump($c->get());exit;

$c->parse($nowDir.'/test.ini', 'ini');exit;
//$c->parse($nowDir.'/test.json', 'json');

//var_dump(Yaconf::get('test'));exit;

//$c->load('ini.yaml');
$c->setYaconf(true);
$c->load('test.json', 'test');

$c->set('1111',[12,12]);
var_dump($c['1111']);exit;

$c->remove('12.14.19.19');

var_dump($c->get('test.name1'));
exit;

var_dump($c->pull('test'), $c->get(), $c->has('test.name1'));

exit;

$t = new TestArrayAccess(); // 因为这块属于tp的扩展文件夹加载，所以得用tp的自动加载方式

$t['name'] = 'cy';  // set

var_dump($t['name']); // set

unset($t['name']); // unset

var_dump($t['name'] ?? false); //get

