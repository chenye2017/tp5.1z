<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://zjzit.cn>
// +----------------------------------------------------------------------

namespace think;

use think\console\Output as ConsoleOutput;
use think\exception\ErrorException;
use think\exception\Handle;
use think\exception\ThrowableError;

class Error
{
    /**
     * 配置参数
     * @var array
     */
    protected static $exceptionHandler;

    /**
     * 注册异常处理
     * @access public
     * @return void
     */
    public static function register()
    {
        error_reporting(E_ALL); // php 标准错误处理级别
        set_error_handler([__CLASS__, 'appError']); // 对于warning 级别的处理
        set_exception_handler([__CLASS__, 'appException']); // 用户自定义的try catch , 会在执行完之后停止，但是不会交给php 标准错误处理
        register_shutdown_function([__CLASS__, 'appShutdown']); // 感觉就是只要注册了，不管怎样都会执行
    }

    /**
     * 所有错误的入口,try catch 能捕获到的处理都用这个
     * Exception Handler
     * @access public
     * @param  \Exception|\Throwable $e
     */
    public static function appException($e)
    {
       // var_dump('exception');
        if (!$e instanceof \Exception) {
            // php7 中throwable 分成 exception 和 error，error 能通过try catch 捕获到，所以这个appException也能捕获到，捕获到error ，当做 ErrorException 来处理
            $e = new ThrowableError($e);
        }

        self::getExceptionHandler()->report($e); // 继承官方handle 的一个好处是，记录日志方法不用自己重写，直接用官方的就好了



        if (PHP_SAPI == 'cli') {
            self::getExceptionHandler()->renderForConsole(new ConsoleOutput, $e); // cli 模式下官方handle 的处理
        } else {
            self::getExceptionHandler()->render($e)->send();
            // 这就是web 模式下面我们需要写render 的原因， 这个地方是实际render 的执行
            // 这个send 方法是Response 的，所以render 处理后一定要产生response 对象
        }
    }

    /**
     * Error Handler
     * @access public
     * @param  integer $errno   错误编号
     * @param  integer $errstr  详细错误信息
     * @param  string  $errfile 出错的文件
     * @param  integer $errline 出错行号
     * @throws ErrorException
     */
    public static function appError($errno, $errstr, $errfile = '', $errline = 0)
    {
        // var_dump('error');
        $exception = new ErrorException($errno, $errstr, $errfile, $errline); // try catch 抓不到的直接转成 exception （也就是实际的fatal error）
        if (error_reporting() & $errno) { // error_reporting 获取错误级别
            // 将错误信息托管至 think\exception\ErrorException

            throw $exception; // 把try catch 无法捕获的错误抛出，用于捕获, 交给上面的 app_exception 处理
        }

        self::getExceptionHandler()->report($exception); // 如果不处理就直接记录 （warning notice 那些确实不咋重要）
    }

    /**
     * Shutdown Handler
     * @access public
     */
    public static function appShutdown()
    {
       // var_dump(error_get_last());exit;
       // var_dump('shut', error_get_last());
        // 这个只有那些没有被捕获到的， 才会有error_get_last;
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {
            // 将错误信息托管至think\ErrorException
            $exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);

            self::appException($exception); // 交给 error_exception 处理
        }

        // 写入日志
        Container::get('log')->save();
    }

    /**
     * 确定错误类型是否致命
     *
     * @access protected
     * @param  int $type
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]); // 致命的错误
    }

    /**
     * 设置异常处理类
     *
     * @access public
     * @param  mixed $handle
     * @return void
     */
    public static function setExceptionHandler($handle)
    {
        self::$exceptionHandler = $handle; // 自定义的错误处理
    }

    /**
     * Get an instance of the exception handler.
     * 获取某个处理异常的实际类
     * @access public
     * @return Handle
     */
    public static function getExceptionHandler()
    {
        // 如果没有配置异常处理，就用官方的handle ,如果写了而且是匿名函数，就把这个匿名函数赋值给官方handle 的 render 属性
        // 如果自己写了类而且继承自官方handle， 就实例化这个类

        static $handle;

        if (!$handle) {
            // 异常处理handle
            $class = self::$exceptionHandler; // app.php 那块配置的exception handler

            if ($class && is_string($class) && class_exists($class) && is_subclass_of($class, "\\think\\exception\\Handle"))  { // 这个类的子类
                $handle = new $class;
            } else {
                $handle = new Handle;
                if ($class instanceof \Closure) {
                    $handle->setRender($class);
                }
            }
        }

        return $handle;
    }
}
