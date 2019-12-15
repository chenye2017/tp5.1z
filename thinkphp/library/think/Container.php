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

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use think\exception\ClassNotFoundException;

/**
 * @package think
 * @property Build          $build
 * @property Cache          $cache
 * @property Config         $config
 * @property Cookie         $cookie
 * @property Debug          $debug
 * @property Env            $env
 * @property Hook           $hook
 * @property Lang           $lang
 * @property Middleware     $middleware
 * @property Request        $request
 * @property Response       $response
 * @property Route          $route
 * @property Session        $session
 * @property Template       $template
 * @property Url            $url
 * @property Validate       $validate
 * @property View           $view
 * @property route\RuleName $rule_name
 * @property Log            $log
 */
class Container implements ArrayAccess, IteratorAggregate, Countable
{


    /**
     * 容器对象实例
     * @var Container
     */
    public static $instance; // 自身的单例

    /**
     * 容器中的对象实例
     * @var array
     */
    public $instances = []; // 用来装各个类的实例

    /**
     * 容器绑定标识
     * instance 属性中的key 对应的具体的类
     * @var array
     */
    protected $bind = [
        'app'                   => App::class,
        'build'                 => Build::class,
        'cache'                 => Cache::class,
        'config'                => Config::class,
        'cookie'                => Cookie::class,
        'debug'                 => Debug::class,
        'env'                   => Env::class,
        'hook'                  => Hook::class,
        'lang'                  => Lang::class,
        'log'                   => Log::class,
        'middleware'            => Middleware::class,
        'request'               => Request::class,
        'response'              => Response::class,
        'route'                 => Route::class,
        'session'               => Session::class,
        'template'              => Template::class,
        'url'                   => Url::class,
        'validate'              => Validate::class,
        'view'                  => View::class,
        'rule_name'             => route\RuleName::class,
        // 接口依赖注入
        'think\LoggerInterface' => Log::class,
    ];



    /**
     * 容器标识别名
     * @var array
     */
    protected $name = []; // 对应属性（不是静态属性）instance 中key -> value（类实例）， 这些实例的别名

    /**
     * 获取当前容器的实例（单例）
     * @access public
     * @return static
     */
    public static function getInstance()
    {

        if (is_null(static::$instance)) {

            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * 设置当前容器的实例
     * @access public
     * @param  object        $instance
     * @return void
     */
    public static function setInstance($instance)
    {
        /*var_dump($instance, 1111111);
        $instance->cname = 'cy2';
        var_dump($instance);exit;*/
        static::$instance = $instance;
    }

    public static function showInstance()
    {
        var_dump(static::$instance);
    }

    /**
     * 获取容器中的对象实例
     * @access public
     * @param  string        $abstract       类名或者标识，相当于container 单例中 instance 属性 instance (一个key value 数组)， abstract 相当于 key
     * @param  array|true    $vars           变量
     * @param  bool          $newInstance    是否每次创建新的实例
     * @return object
     */
    public static function get($abstract, $vars = [], $newInstance = false)
    {
       // var_dump(12);exit; 为什么这个地方会打印两次 12, 一次 app， 一次 log， 感觉有两个进程，但php-fpm 应该都是单进程的呀
        $z = static::getInstance()->make($abstract, $vars, $newInstance);



        return $z;
    }

    /**
     * 绑定一个类、闭包、实例、接口实现到容器
     * @access public
     * @param  string  $abstract    类标识、接口
     * @param  mixed   $concrete    要绑定的类、闭包或者实例
     * @return Container
     */
    public static function set($abstract, $concrete = null)
    {
        return static::getInstance()->bindTo($abstract, $concrete);
    }

    /**
     * 移除容器中的对象实例
     * @access public
     * @param  string  $abstract    类标识、接口
     * @return void
     */
    public static function remove($abstract)
    {
        return static::getInstance()->delete($abstract);
    }

    /**
     * 清除容器中的对象实例
     * @access public
     * @return void
     */
    public static function clear()
    {
        return static::getInstance()->flush();
    }

    /**
     * 绑定一个类、闭包、实例、接口实现到容器
     * @access public
     * @param  string|array  $abstract    类标识、接口
     * @param  mixed         $concrete    要绑定的类、闭包或者实例
     * @return $this
     */
    public function bindTo($abstract, $concrete = null) // 注意这个bindto 除了对于对象处理，剩下的那些类名处理的时候，并没有主动去生成对象
    {
        if (is_array($abstract)) { // 可能是绑定多个类
            $this->bind = array_merge($this->bind, $abstract); // 也没有生成实例
        } elseif ($concrete instanceof Closure) {
            $this->bind[$abstract] = $concrete; // 绑定一个闭包，也没有生成实例
        } elseif (is_object($concrete)) {
            if (isset($this->bind[$abstract])) {
                $abstract = $this->bind[$abstract]; // 这个地方并没有修改 $this->bind 中的值，如果没有的话
            }
            $this->instances[$abstract] = $concrete; // 绑定instance中实例
        } else {
            $this->bind[$abstract] = $concrete; // 类名，没有生成实例
        }

        return $this;
    }

    public function showInstance1()
    {
        var_dump($this->instances);exit;
    }

    /**
     * 绑定一个类实例当容器
     * @access public
     * @param  string           $abstract    类名或者标识
     * @param  object|\Closure  $instance    类的实例
     * @return $this
     */
    public function instance($abstract, $instance)
    {

        if ($instance instanceof \Closure) {
            $this->bind[$abstract] = $instance;
        } else {
            if (isset($this->bind[$abstract])) {
                $abstract = $this->bind[$abstract]; // 把别名转换成类名
            }

            $this->instances[$abstract] = $instance; // instances 属性中key 是具体的类名
        }

        return $this;
    }

    /**
     * 判断容器中是否存在类及标识
     * @access public
     * @param  string    $abstract    类名或者标识
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->bind[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * 判断容器中是否存在对象实例
     * @access public
     * @param  string    $abstract    类名或者标识
     * @return bool
     */
    public function exists($abstract)
    {
        if (isset($this->bind[$abstract])) {
            $abstract = $this->bind[$abstract];
        }

        return isset($this->instances[$abstract]);
    }

    /**
     * 判断容器中是否存在类及标识
     * @access public
     * @param  string    $name    类名或者标识
     * @return bool
     */
    public function has($name)
    {
        return $this->bound($name);
    }

    /**
     * 创建类的实例, 返回创建的实例
     *
     * @access public
     * @param  string        $abstract       类名或者标识
     * @param  array|true    $vars           变量
     * @param  bool          $newInstance    是否每次创建新的实例
     * @return object
     */
    public function make($abstract, $vars = [], $newInstance = false) // 主要是解决依赖关系
    {
        if (true === $vars) { // 把第三个变量提前，交换第三个变量和第二个变量位置，在js中看到过
            // 总是创建新的实例化对象
            $newInstance = true;
            $vars        = [];
        }

        $abstract = isset($this->name[$abstract]) ? $this->name[$abstract] : $abstract; // 获取容器中对应的key

        if (isset($this->instances[$abstract]) && !$newInstance) { // 如果有就提前返回, 最开始都在instance 中找
            return $this->instances[$abstract];
        }

        if (isset($this->bind[$abstract])) { // 如果在预定义的类实例当中
            $concrete = $this->bind[$abstract];

            if ($concrete instanceof Closure) {
                $object = $this->invokeFunction($concrete, $vars);
            } else {

                // bind 中都是类（回调函数也是一种类closure,） 可能不全，直接绑定到instance 上
                $this->name[$abstract] = $concrete; // name 和 bind 区别是啥，name 不能装匿名函数吗
                // bind 和 name 中就是装别名的嘛
                // 感觉这个make 可以处理类名。但是对于回调函数不太好处理，需要用到bindto
                // 把别名换成实际的类再执行

                return $this->make($concrete, $vars, $newInstance);
            }
        } else { // 不在预定义的类实例当中

            $object = $this->invokeClass($abstract, $vars);
        }

        if (!$newInstance) {
            $this->instances[$abstract] = $object; // 之前不存在，保存到容器，下次直接从容器取， 保证了instance中肯定都会有
        }

        return $object;
    }

    /**
     * 删除容器中的对象实例
     * @access public
     * @param  string|array    $abstract    类名或者标识
     * @return void
     */
    public function delete($abstract)
    {
        // 修改了name 和 instance ，没有动 bind 属性
        foreach ((array) $abstract as $name) {
            $name = isset($this->name[$name]) ? $this->name[$name] : $name;

            if (isset($this->instances[$name])) {
                unset($this->instances[$name]);
            }
        }
    }

    /**
     * 获取容器中的对象实例
     * @access public
     * @return array
     */
    public function all()
    {
        return $this->instances;
    }

    /**
     * 清除容器中的对象实例
     * @access public
     * @return void
     */
    public function flush()
    {
        $this->instances = [];
        $this->bind      = [];
        $this->name      = [];
    }

    /**
     * 执行函数或者闭包方法 支持参数调用
     * @access public
     * @param  mixed  $function 函数或者闭包
     * @param  array  $vars     参数
     * @return mixed
     */
    public function invokeFunction($function, $vars = [])
    {
        try {
            $reflect = new ReflectionFunction($function); // 对于函数获取闭包

            $args = $this->bindParams($reflect, $vars);

            return call_user_func_array($function, $args);
        } catch (ReflectionException $e) {
            throw new Exception('function not exists: ' . $function . '()');
        }
    }

    /**
     * 调用反射执行类的方法 支持参数绑定
     * @access public
     * @param  mixed   $method 方法
     * @param  array   $vars   参数
     * @return mixed
     */
    public function invokeMethod($method, $vars = [])
    {
        try {
            if (is_array($method)) { // [类名，方法名]
                $class   = is_object($method[0]) ? $method[0] : $this->invokeClass($method[0]);
                $reflect = new ReflectionMethod($class, $method[1]);
            } else {
                // 静态方法
                $reflect = new ReflectionMethod($method);// 静态方法，执行不想需要对象
            }

            $args = $this->bindParams($reflect, $vars);

            return $reflect->invokeArgs(isset($class) ? $class : null, $args);
        } catch (ReflectionException $e) {
            if (is_array($method) && is_object($method[0])) {
                $method[0] = get_class($method[0]);
            }

            throw new Exception('method not exists: ' . (is_array($method) ? $method[0] . '::' . $method[1] : $method) . '()');
        }
    }

    /**
     * 调用反射执行类的方法 支持参数绑定
     * @access public
     * @param  object  $instance 对象实例
     * @param  mixed   $reflect 反射类
     * @param  array   $vars   参数
     * @return mixed
     */
    public function invokeReflectMethod($instance, $reflect, $vars = [])
    {
        $args = $this->bindParams($reflect, $vars); // 获取反射方法所需要的参数

        return $reflect->invokeArgs($instance, $args); // 反射方法执行
    }

    /**
     * 调用反射执行callable 支持参数绑定
     * @access public
     * @param  mixed $callable
     * @param  array $vars   参数
     * @return mixed
     */
    public function invoke($callable, $vars = [])
    {
        if ($callable instanceof Closure) { // 执行回调方法

            return $this->invokeFunction($callable, $vars);
        }

        return $this->invokeMethod($callable, $vars); // 执行普通方法
    }

    /**
     *  真正的实例化类
     * 调用反射执行类的实例化 支持依赖注入
     * @access public
     * @param  string    $class 类名
     * @param  array     $vars  参数
     * @return mixed
     */
    public function invokeClass($class, $vars = []) // 实际生成实例
    {

        try {
            $reflect = new ReflectionClass($class); // 获取一个类的反射实例

            if ($reflect->hasMethod('__make')) { // 根据类反射实例确定类中是否有这个方法

                $method = new ReflectionMethod($class, '__make'); // 根据类，和方法名获取一个反射方法实例

                if ($method->isPublic() && $method->isStatic()) { // 根据反射方法实例判断方法的属性和是否公共
                    $args = $this->bindParams($method, $vars); // 获取方法的参数
                    return $method->invokeArgs(null, $args); // 调用静态方法
                }
            }

            $constructor = $reflect->getConstructor();

            $args = $constructor ? $this->bindParams($constructor, $vars) : []; // 获取构造函数要的参数

            $a = $reflect->newInstanceArgs($args);



            return $a; // 参数是数组， newInstance, 参数不一定
        } catch (ReflectionException $e) {
            throw new ClassNotFoundException('class not exists: ' . $class, $class);
        }
    }

    /**
     * 绑定参数， 根据构造函数，准备构造函数实例化需要的参数
     * @access protected
     * @param  \ReflectionMethod|\ReflectionFunction $reflect 反射类
     * @param  array                                 $vars    参数
     * @return array
     */
    protected function bindParams($reflect, $vars = [])
    {
        if ($reflect->getNumberOfParameters() == 0) { //获取方法参数数目，包括可选参数
            return [];
        }

        // 判断数组类型 数字数组时按顺序绑定参数
        reset($vars);
        $type   = key($vars) === 0 ? 1 : 0; // 判断传入的参数是索引数组还是关联数组 （像url传递都是带参数名称的）
        $params = $reflect->getParameters(); // 获取参数名称

    //    var_dump($params, $type, key($vars), $vars);

        // 这个type 就计算一次，真的蛮傻的

        // type 0 的情况下
        // 如果路由是callback， 以来的普通参数都通过参数名称，在获取的参数中 通过 $param[$name] 形式查找，所以没有顺序要求，但要求名称一致
        // 如果是 依赖类，会直接获取第一个参数，判断是否是，不是的话生成，是的话直接array_shift 使用

        foreach ($params as $param) {
        //    var_dump($vars, 'end');
            $name      = $param->getName(); // 参数名称
            $lowerName = Loader::parseName($name); // 参数名称标准化
            $class     = $param->getClass(); // 参数的类型
//var_dump($name, $vars);
            if ($class) {
            //    var_dump($class);
                $args[] = $this->getObjectParam($class->getName(), $vars); // 有用类实例的，就不分关联和索引了
            } elseif (1 == $type && !empty($vars)) {
                $args[] = array_shift($vars); // 索引数组，每次从头部取一个数据放到参数里面
            } elseif (0 == $type && isset($vars[$name])) {
                $args[] = $vars[$name];
             //   var_dump($name,$args);
            } elseif (0 == $type && isset($vars[$lowerName])) {

                $args[] = $vars[$lowerName];
              //  var_dump($args);
            } elseif ($param->isDefaultValueAvailable()) {
           //     var_dump(11111);
                $args[] = $param->getDefaultValue();
            } else {
                var_dump(11111);
                throw new InvalidArgumentException('method param miss:' . $name);
            }
        }

        return $args;
    }

    /**
     * 获取对象类型的参数值， 对于对象类型参数的处理
     * @access protected
     * @param  string   $className  类名
     * @param  array    $vars       参数
     * @return mixed
     */
    protected function getObjectParam($className, &$vars) // 参数如果传入，就不用自己生成，如果没传入，还得走之前的make
    {
        $array = $vars;
        $value = array_shift($array);

//var_dump($array, $value, $value instanceof $className, $className);
        if ($value instanceof $className) { // 如果传入的参数和对应的类类型一致，参考laravel controller，，可以定义参数 Request $request, 这时候就不用自动注入了，就是直接传入了，
            $result = $value;
            array_shift($vars); // 如果传入的是依赖类，所有的都往前添加参数
        } else {
         //   var_dump($className);
            $result = $this->make($className); // 自动注入
        }

        return $result;
    }

    public function __set($name, $value)
    {
        $this->bindTo($name, $value); // 操作的属性，是对于bind属性
    }

    public function __get($name)
    {
        return $this->make($name);
    }

    public function __isset($name)
    {
        return $this->bound($name);
    }

    public function __unset($name)
    {
        $this->delete($name);
    }

    public function offsetExists($key)
    {
        return $this->__isset($key);
    }

    public function offsetGet($key)
    {
        return $this->__get($key);
    }

    public function offsetSet($key, $value)
    {
        $this->__set($key, $value);
    }

    public function offsetUnset($key)
    {
        $this->__unset($key);
    }

    //Countable
    public function count()
    {
        return count($this->instances);
    }

    //IteratorAggregate
    public function getIterator()
    {
        return new ArrayIterator($this->instances);
    }

    public function __debugInfo()
    {
        // var_dump 一个实例对象的时候会执行
        $data = get_object_vars($this); // 返回由对象属性组成的关联数组
      //  unset($data['instances'], $data['instance']); // 删除容器中的所有实例，删除容器自身单例对象，不太明白为啥要删除这两个

        return $data;
    }
}
