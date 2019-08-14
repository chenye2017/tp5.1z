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

class Facade
{
    /**
     * 绑定对象
     * @var array
     */
    protected static $bind = [];

    /**
     * 始终创建新的对象实例
     * @var bool
     */
    protected static $alwaysNewInstance;

    /**
     * 绑定类的静态代理
     * 一般用不到，一般用的是getfacadeclass 方法，
     * 作用 添加 face 和 concrete 连接，通过face 去找concrete,
     * 如果上面两种寻找concrete 的方法都存在，默认第一个
     * @static
     * @access public
     * @param  string|array  $name    类标识
     * @param  string        $class   类名
     * @return object
     */
    public static function bind($name, $class = null)
    {
        if (__CLASS__ != static::class) { //

            return self::__callStatic('bind', func_get_args()); // 魔术方法不仅可以触发，还可以直接调用
        }

        if (is_array($name)) {
            self::$bind = array_merge(self::$bind, $name);
        } else {
            self::$bind[$name] = $class;
        }
    }

    /**
     * 创建Facade实例 ，实例化concrete出来，或者没有abstract -》concrete， 直接就是创造concrete出来
     * @static
     * @access protected
     * @param  string    $class          类名或标识
     * @param  array     $args           变量
     * @param  bool      $newInstance    是否每次创建新的实例
     * @return object
     */
    protected static function createFacade($class = '', $args = [], $newInstance = false)
    {
        $class = $class ?: static::class;


        $facadeClass = static::getFacadeClass();

        if ($facadeClass) {
            $class = $facadeClass;
        } elseif (isset(self::$bind[$class])) { // 如果getfacadeclass 中没有指明，也可以自己绑定到facade 的bind上
            // 这个地方self 用的很好，和static 会根据类变化

            $class = self::$bind[$class];
        }

        if (static::$alwaysNewInstance) {
            $newInstance = true;
        }

        return Container::getInstance()->make($class, $args, $newInstance); // 也是从容器中取数据
    }

    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识），因为类标识也需要转换成实际对象，所以直接返回实际类名也是可以的
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {}

    /**
     * 带参数实例化当前Facade类，这个虽然判断条件一样，但这个会不自己调用自己
     * facecade 默认没有construct，但感觉直接new facade 差不多
     * @access public
     * @return mixed
     */
    public static function instance(...$args)
    {
        if (__CLASS__ != static::class) {
            return self::createFacade('', $args);
        }
    }

    /**
     * 调用类的实例,感觉这个方法和上面的bind 就是专门给facade自己用的
     * @access public
     * @param  string        $class          类名或者标识
     * @param  array|true    $args           变量
     * @param  bool          $newInstance    是否每次创建新的实例
     * @return mixed
     */
    public static function make($class, $args = [], $newInstance = false)
    {
        if (__CLASS__ != static::class) {
            return self::__callStatic('make', func_get_args());
        }

        if (true === $args) {
            // 总是创建新的实例化对象
            $newInstance = true;
            $args        = [];
        }

        return self::createFacade($class, $args, $newInstance);
    }

    // 调用实际类的方法
    public static function __callStatic($method, $params)
    {

        return call_user_func_array([static::createFacade(), $method], $params);
    }
}
