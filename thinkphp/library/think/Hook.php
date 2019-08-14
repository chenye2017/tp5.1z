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

class Hook
{
    /**
     * 钩子行为定义
     * 钩子点和 class 类的绑定。是这个类的核心
     * @var array
     */
    private $tags = [];

    /**
     * 绑定行为列表
     * @var array
     */
    protected $bind = [];

    public function showBind()
    {
        var_dump($this->bind);
    }

    /**
     * 入口方法名称
     * @var string
     */
    private static $portal = 'run'; // 因为默认是run, 所以要是行为类只有一个方法就没有必要写了

    /**
     * 应用对象
     * @var App
     */
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 指定入口方法名称
     * @access public
     * @param  string  $name     方法名
     * @return $this
     */
    public function portal($name)
    {
        self::$portal = $name;
        return $this;
    }

    /**
     * 指定行为标识 便于调用
     * @access public
     * @param  string|array  $name     行为标识
     * @param  mixed         $behavior 行为
     * @return $this
     */
    public function alias($name, $behavior = null)
    {
        if (is_array($name)) {
            $this->bind = array_merge($this->bind, $name);
        } else {
            $this->bind[$name] = $behavior;
        }

        return $this;
    }

    /**
     * 动态添加行为扩展到某个标签
     * @access public
     * @param  string    $tag 标签名称
     * @param  mixed     $behavior 行为名称
     * @param  bool      $first 是否放到开头执行
     * @return void
     */
    public function add($tag, $behavior, $first = false)
    {
        isset($this->tags[$tag]) || $this->tags[$tag] = []; // 初始化

        //php7  $this->tags[$tag] = $this->tags[$tag] ?? [];

        if (is_array($behavior) && !is_callable($behavior)) {

            if (!array_key_exists('_overlay', $behavior)) { // 我们一般设置的behavior都是索引，没有到还可以设置关联
                $this->tags[$tag] = array_merge($this->tags[$tag], $behavior);
            } else {
                unset($behavior['_overlay']);
                $this->tags[$tag] = $behavior;
            }
        } elseif ($first) { // 添加在头部
            array_unshift($this->tags[$tag], $behavior);
        } else {
            $this->tags[$tag][] = $behavior;
        }

    }

    /**
     * 批量导入插件
     * @access public
     * @param  array     $tags 插件信息
     * @param  bool      $recursive 是否递归合并， 感觉一般都要true, 要不然
     * @return void
     */
    public function import(array $tags, $recursive = true)
    {

        if ($recursive) { // 把我们给的arr 和 默认的tags 同key 的合并(没有overly 的merge， 有的覆盖)，不同key的添加进去
            foreach ($tags as $tag => $behavior) {

                $this->add($tag, $behavior);
            }
        } else {
            $this->tags = $tags + $this->tags; // 前面的覆盖后面的
        }

    }

    /**
     * 获取插件信息
     * @access public
     * @param  string $tag 插件位置 留空获取全部
     * @return array
     */
    public function get($tag = '')
    {
        if (empty($tag)) {
            //获取全部的插件信息
            return $this->tags;
        }

        return array_key_exists($tag, $this->tags) ? $this->tags[$tag] : [];
    }

    /**
     * 监听标签的行为 ，设置断点，并且执行断点包含的behavior
     * @access public
     * @param  string $tag    标签名称 .listen 的点名称
     * @param  mixed  $params 传入参数
     * @param  bool   $once   只获取一个有效返回值
     * @return mixed
     */
    public function listen($tag, $params = null, $once = false)
    {
        $results = [];
        $tags    = $this->get($tag);

        foreach ($tags as $key => $name) { // name 是类的名称



            $results[$key] = $this->execTag($name, $tag, $params);

            if (false === $results[$key] || (!is_null($results[$key]) && $once)) {
                break;
            }
        }

        return $once ? end($results) : $results;
    }

    /**
     * 执行行为
     * @access public
     * @param  mixed     $class  行为
     * @param  mixed     $params 参数
     * @return mixed
     */
    public function exec($class, $params = null)
    {
        if ($class instanceof \Closure || is_array($class)) {
            $method = $class;
        } else {
            if (isset($this->bind[$class])) {
                $class = $this->bind[$class]; // bind 感觉也是让类更好找，给类去别名
            }
            $method = [$class, self::$portal];
        }

        return $this->app->invoke($method, [$params]);
    }

    /**
     * 执行某个标签的行为
     * @access protected
     * @param  mixed     $class  要执行的行为 类的名称
     * @param  string    $tag    方法名（标签名） listen 的点的名称
     * @param  mixed     $params 参数
     * @return mixed
     */
    protected function execTag($class, $tag = '', $params = null)
    {
        $method = Loader::parseName($tag, 1, false);

        if ($class instanceof \Closure) {
            $call  = $class;
            $class = 'Closure';
        } elseif (is_array($class) || strpos($class, '::')) {
            $call = $class;
        } else {
            $obj = Container::get($class); // 获取类实例

            if (!is_callable([$obj, $method])) {
                $method = self::$portal;
            }

            $call  = [$class, $method];
            $class = $class . '->' . $method;
        }

        $result = $this->app->invoke($call, [$params]); // 解析变量，可以依赖注入

        return $result;
    }

    public function __debugInfo()
    {
        $data = get_object_vars($this);
        unset($data['app']);

        return $data;
    }
}
