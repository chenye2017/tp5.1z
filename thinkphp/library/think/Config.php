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

use Yaconf;

class Config implements \ArrayAccess
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [];

    /**
     * 配置前缀
     * @var string
     */
    protected $prefix = 'app';

    /**
     * 配置文件目录
     * @var string
     */
    protected $path;

    /**
     * 配置文件后缀
     * @var string
     */
    protected $ext;

    /**
     * 是否支持Yaconf
     * @var bool
     */
    protected $yaconf;

    /**
     * 构造方法
     * 给属性 path(路径位置), ext(后缀) 赋值
     * @access public
     */
    public function __construct($path = '', $ext = '.php')
    {
        $this->path   = $path;
        $this->ext    = $ext;
        $this->yaconf = class_exists('Yaconf');
    }

    public  function test()
    {
        var_dump($this->config);
    }

    /**
     * 根据 app 中配置的路径后后缀生成配置文件
     * @param App $app
     * @return Config
     */
    public static function __make(App $app)
    {
        $path = $app->getConfigPath();
        $ext  = $app->getConfigExt();
        return new static($path, $ext);
    }

    /**
     * 设置开启Yaconf (布尔值)
     * @access public
     * @param  bool|string    $yaconf  是否使用Yaconf
     * @return void
     */
    public function setYaconf($yaconf)
    {
        if ($this->yaconf) {
            $this->yaconf = $yaconf;
        }
    }

    public function getYaconf()
    {
        return $this->yaconf;
    }

    /**
     * 属性赋值
     * 设置配置参数默认前缀
     * @access public
     * @param string    $prefix 前缀
     * @return void
     */
    public function setDefaultPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * 解析配置文件或内容 把文件解析到config 类的属性 config 中
     * @access public
     * @param  string    $config 配置文件路径或内容
     * @param  string    $type 配置解析类型
     * @param  string    $name 配置名（如设置即表示二级配置）
     * @return mixed
     */
    public function parse($config, $type = '', $name = '')
    {
        if (empty($type)) {
            $type = pathinfo($config, PATHINFO_EXTENSION); // 获取文件后缀 ini, json, xml
        }

        $object = Loader::factory($type, '\\think\\config\\driver\\', $config); // 某种配置类型的实例对象
        //var_dump($object->parse());exit;

        return $this->set($object->parse(), $name); // $object parse 都是返回一个数组， driver下面，但是string也是可以的，就是感觉都在driver 下面不规范
    }

    /**
     * 加载配置文件（多种格式）
     * @access public
     * @param  string    $file 配置文件名
     * @param  string    $name 一级配置名
     * @return mixed
     */
    public function load($file, $name = '')
    {
        if (is_file($file)) {
            $filename = $file;
        } elseif (is_file($this->path . $file . $this->ext)) {
            $filename = $this->path . $file . $this->ext;
        }

        if (isset($filename)) {
            return $this->loadFile($filename, $name);
        } elseif ($this->yaconf && Yaconf::has($file)) {

            return $this->set(Yaconf::get($file), $name); // yaconf 中的file  只要存在，一定会返回一个 [],不会返回字符串
        }

        return $this->config;
    }

    /**
     * 获取实际的yaconf配置参数
     * 这个第一次获取就是属性名，后面要等着yaconf 给值~
     * @access protected
     * @param  string    $name 配置参数名
     * @return string
     */
    protected function getYaconfName($name)
    {
        if ($this->yaconf && is_string($this->yaconf)) {

            return $this->yaconf . '.' . $name;
        }

        return $name;
    }

    /**
     * 获取yaconf配置
     * @access public
     * @param  string    $name 配置参数名
     * @param  mixed     $default   默认值
     * @return mixed
     */
    public function yaconf($name, $default = null)
    {
        if ($this->yaconf) {
            $yaconfName = $this->getYaconfName($name);

            if (Yaconf::has($yaconfName)) {
                return Yaconf::get($yaconfName);
            }
        }

        return $default;
    }

    /**
     * 把文件加载进内存解析成内容，赋值给config 类的 config 属性， 相比较上面那个load ,这个对除了yaconf 之外进行处理
     * @param $file
     * @param $name
     * @return mixed
     */
    protected function loadFile($file, $name)
    {
        $name = strtolower($name);
        $type = pathinfo($file, PATHINFO_EXTENSION);

        if ('php' == $type) {
            return $this->set(include $file, $name);
        } elseif ('yaml' == $type && function_exists('yaml_parse_file')) {

            return $this->set(yaml_parse_file($file), $name);
        }

        return $this->parse($file, $type, $name);
    }

    /**
     * 检测配置是否存在 (看这个检测，我们添加的配置文件一定要加前缀（文件名），否则都会归到app 下面)
     * @access public
     * @param  string    $name 配置参数名（支持多级配置 .号分割）
     * @return bool
     */
    public function has($name)
    {
        if (false === strpos($name, '.')) {
            $name = $this->prefix . '.' . $name;
        }

        return !is_null($this->get($name));
    }

    /**
     * 获取一级配置
     * 蛮奇怪这个命名
     * 专门为获取某个文件中所有配置变量而生
     * @access public
     * @param  string    $name 一级配置名
     * @return array
     */
    public function pull($name)
    {

        $name = strtolower($name); // 文件名称首字母都小写

        if ($this->yaconf) {
            $yaconfName = $this->getYaconfName($name);

            if (Yaconf::has($yaconfName)) {
                $config = Yaconf::get($yaconfName);
                return isset($this->config[$name]) ? array_merge($this->config[$name], $config) : $config;
            }
        }

        return isset($this->config[$name]) ? $this->config[$name] : [];
    }

    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string    $name      配置参数名（支持多级配置 .号分割）
     * @param  mixed     $default   默认值
     * @return mixed
     */
    public function get($name = null, $default = null)
    {
        if ($name && false === strpos($name, '.')) {
            $name = $this->prefix . '.' . $name; // 没有前缀，就默认拼上前缀
        }

        // 无参数时获取所有
        if (empty($name)) {
            return $this->config;
        }

        if ('.' == substr($name, -1)) { // 注意要是不是文件名称，千万不要在后面加上 .

            return $this->pull(substr($name, 0, -1)); // 获取所有
        }

        if ($this->yaconf) {
            $yaconfName = $this->getYaconfName($name);

            if (Yaconf::has($yaconfName)) {
                return Yaconf::get($yaconfName);
            }
        }

        $name    = explode('.', $name);
        $name[0] = strtolower($name[0]);
        $config  = $this->config;

        // 按.拆分成多维数组进行判断
        foreach ($name as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return $default;
            }
        }

        return $config;
    }

    /**
     * 设置配置参数 name为数组则为批量设置, value 是以及参数，也就是配置文件名
     * 这个set 包含两种配置方式，第一种是传入数组，value代表文件名称
     * 第二种传入string， 文件名称通过点分割，value 代表配置的key 对应的value
     * @access public
     * @param  string|array  $name 配置参数名（支持三级配置 .号分割）
     * @param  mixed         $value 配置值
     * @return mixed
     */
    public function set($name, $value = null)
    {
        if (is_string($name)) {
            if (false === strpos($name, '.')) {
                $name = $this->prefix . '.' . $name;
            }
            $name = explode('.', $name, 3);

            if (count($name) == 2) {

                $this->config[strtolower($name[0])][$name[1]] = $value;

            } else {
                $this->config[strtolower($name[0])][$name[1]][$name[2]] = $value;
            }

            return $value;
        } elseif (is_array($name)) {
            // 批量设置
            if (!empty($value)) {
                if (isset($this->config[$value])) {
                    $result = array_merge($this->config[$value], $name);
                } else {
                    $result = $name;
                }

                $this->config[$value] = $result;
            } else {
                $result = $this->config = array_merge($this->config, $name);
            }
        } else {
            // 为空直接返回 已有配置
            $result = $this->config;
        }

        return $result;
    }

    /**
     * 移除配置
     * @access public
     * @param  string  $name 配置参数名（支持三级配置 .号分割）
     * @return void
     */
    public function remove($name)
    {
        if (false === strpos($name, '.')) {
            $name = $this->prefix . '.' . $name;
        }

        $name = explode('.', $name, 3);

        if (count($name) == 2) {
            unset($this->config[strtolower($name[0])][$name[1]]);
        } else {
            unset($this->config[strtolower($name[0])][$name[1]][$name[2]]);
        }
    }

    /**
     * 重置配置参数
     * @access public
     * @param  string    $prefix  配置前缀名
     * @return void
     */
    public function reset($prefix = '')
    {
        if ('' === $prefix) {
            $this->config = [];
        } else {
            $this->config[$prefix] = [];
        }
    }

    /**
     * 设置配置
     * @access public
     * @param  string    $name  参数名
     * @param  mixed     $value 值
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * 获取配置参数
     * @access public
     * @param  string $name 参数名
     * @return mixed
     */
    public function __get($name)
    {

        return $this->get($name);
    }

    /**
     * 检测是否存在参数
     * @access public
     * @param  string $name 参数名
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    // ArrayAccess
    public function offsetSet($name, $value)
    {
        $this->set($name, $value);
    }

    public function offsetExists($name)
    {
        return $this->has($name);
    }

    public function offsetUnset($name)
    {
        $this->remove($name);
    }

    public function offsetGet($name)
    {

        return $this->get($name);
    }
}
