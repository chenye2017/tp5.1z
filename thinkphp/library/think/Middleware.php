<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Slince <taosikai@yeah.net>
// +----------------------------------------------------------------------

namespace think;

use InvalidArgumentException;
use LogicException;
use think\exception\HttpResponseException;

class Middleware
{
    protected $queue = [];
    protected $app;
    protected $config = [
        'default_namespace' => 'app\\http\\middleware\\',
    ];

    public function __construct(App $app, array $config = [])
    {
        $this->app    = $app;
        $this->config = array_merge($this->config, $config);
    }

    public static function __make(App $app, Config $config)
    {
        return new static($app, $config->pull('middleware'));
    }

    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 导入中间件
     * @access public
     * @param  array  $middlewares, 所有的中间件
     * @param  string $type  中间件类型
     */
    public function import(array $middlewares = [], $type = 'route')
    {

        foreach ($middlewares as $middleware) {
            $this->add($middleware, $type);
        }
    }

    /**
     * 注册中间件
     * @access public
     * @param  mixed  $middleware
     * @param  string $type  中间件类型
     */
    public function add($middleware, $type = 'route')
    {
        if (is_null($middleware)) {
            return;
        }
        // 默认是路由中间件
        $middleware = $this->buildMiddleware($middleware, $type); // 返回数组，[[类, 方法 handle], 参数 ]

        if ($middleware) {

            $this->queue[$type][] = $middleware;
        }
    }

    /**
     * 注册控制器中间件
     * @access public
     * @param  mixed  $middleware
     */
    public function controller($middleware)
    {
        return $this->add($middleware, 'controller');
    }

    /**
     * 移除中间件
     * @access public
     * @param  mixed  $middleware
     * @param  string $type  中间件类型
     */
    public function unshift($middleware, $type = 'route')
    {
        if (is_null($middleware)) {
            return;
        }

        $middleware = $this->buildMiddleware($middleware, $type);

        if ($middleware) {
            array_unshift($this->queue[$type], $middleware);
        }
    }

    /**
     * 获取注册的中间件
     * @access public
     * @param  string $type  中间件类型
     */
    public function all($type = 'route')
    {
        return $this->queue[$type] ?: [];
    }

    /**
     * 清除中间件
     * @access public
     */
    public function clear()
    {
        $this->queue = [];
    }

    /**
     * 中间件调度
     * @access public
     * @param  Request  $request
     * @param  string   $type  中间件类型
     */
    public function dispatch(Request $request, $type = 'route')
    {
        $res = call_user_func($this->resolve($type), $request);
       // var_dump('ssss');
        return  $res;
    }

    /**
     * 解析中间件 ,中间类生成一个对象，用来执行handle 方法
     * @access protected
     * @param  mixed  $middleware
     * @param  string $type  中间件类型
     */
    protected function buildMiddleware($middleware, $type = 'route')
    {
        if (is_array($middleware)) {
            list($middleware, $param) = $middleware; // 中间件类（默认方法就是handle）和参数分开
        }

        if ($middleware instanceof \Closure) {
            return [$middleware, isset($param) ? $param : null];
        }

        if (!is_string($middleware)) {
            throw new InvalidArgumentException('The middleware is invalid');
        }

        if (false === strpos($middleware, '\\')) {
            if (isset($this->config[$middleware])) {
                $middleware = $this->config[$middleware];
            } else {
                $middleware = $this->config['default_namespace'] . $middleware;
            }
        }

        if (is_array($middleware)) {
            return $this->import($middleware, $type);
        }

        if (strpos($middleware, ':')) {
            list($middleware, $param) = explode(':', $middleware, 2);
        }

        return [[$this->app->make($middleware), 'handle'], isset($param) ? $param : null];
    }

    /**
     *  这个queue 队列第一个是注册的中间件，后面是官方控制器的执行
     *  中间件想执行到控制器，就是为了 $next(), 回调函数的执行
     * @param string $type
     * @return \Closure
     */
    protected function resolve($type = 'route')
    {
       // var_dump('start');
        return function (Request $request) use ($type) {
           // var_dump('kkk');
            try {
            $middleware = array_shift($this->queue[$type]);
          //  var_dump($this->queue['route']);
            if (null === $middleware) {
                throw new InvalidArgumentException('The queue was exhausted, with no response returned');
            }
         //   var_dump('ppppp');
            } catch (\Exception $e) {
                var_dump('lllll');
            }

            list($call, $param) = $middleware;
            // var_dump($middleware);
           // var_dump('pll');

            try {
                $response = call_user_func_array($call, [$request, $this->resolve($type), $param]);
                // 这个地方并没有执行callback  ($this->resolve, 只是返回了一个回调函数)
                // call_user_func($this->resolve($type))  这样才能执行
               // var_dump('finish');
              //  var_dump($response);
            } catch (HttpResponseException $exception) { //
                $response = $exception->getResponse();
            }

            if (!$response instanceof Response) {
                throw new LogicException('The middleware must return Response instance');
            }

            return $response;
        };
    }

    public function __debugInfo()
    {
        $data = get_object_vars($this);
        unset($data['app']);

        return $data;
    }
}
