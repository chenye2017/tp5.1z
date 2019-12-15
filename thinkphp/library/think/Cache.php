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

use think\cache\Driver;

/**
 * Class Cache
 *
 * @package think
 *
 * @mixin Driver
 * @mixin \think\cache\driver\File
 */
class Cache
{
    /**
     * 缓存实例
     * @var array
     */
    protected $instance = []; // 这里面装着一个个connect 实例， 这个一般根据一个配置生成一个连接

    /**
     * 缓存配置
     * @var array
     */
    protected $config = []; // 连接配置，options 参数

    /**
     * 操作句柄
     * @var object
     */
    protected $handler; // 像 redis memcache 的处理连接

    public function __construct(array $config = [])
    {

        $this->config = $config; // 不同的handle类的options 参数
        $this->init($config);
    }

    /**
     * 连接缓存。还具有连接切换的作用
     * 生成实际处理类
     * @access public
     * @param  array         $options  配置数组
     * @param  bool|string   $name 缓存连接标识 true 强制重新连接
     * @return Driver
     */
    public function connect(array $options = [], $name = false)
    {
        if (false === $name) {
            $name = md5(serialize($options));
        }

        if (true === $name || !isset($this->instance[$name])) {

            $type = !empty($options['type']) ? $options['type'] : 'File';

            if (true === $name) {
                $name = md5(serialize($options));
            }
           // var_dump($options);
            $this->instance[$name] = Loader::factory($type, '\\think\\cache\\driver\\', $options); // driver 驱动类下面的初始化，比如 file， 比如redis，redis 的操作都需要 使用呢handler
        }

        return $this->instance[$name]; // 实际处理的类
    }

    /**
     * 自动初始化缓存, 包含多配置文件的初始化
     * @access public
     * @param  array         $options  配置数组
     * @param  bool          $force    强制更新
     * @return Driver
     */
    public function init(array $options = [], $force = false)
    {
        // 如果真的想切换缓存类的话，还是得用force 为1
        if (is_null($this->handler) || $force) {

            if ('complex' == $options['type']) {
                $default = $options['default'];
                $options = isset($options[$default['type']]) ? $options[$default['type']] : $default;
            }

            $this->handler = $this->connect($options);
        }

        return $this->handler;
    }

    public static function __make(Config $config)
    {
        return new static($config->pull('cache'));
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 切换缓存类型 需要配置 cache.type 为 complex
     *  init 方法也能重连缓存，但是没有这个方便
     * @access public
     * @param  string $name 缓存标识
     * @return Driver
     */
    public function store($name = '')
    {

        if ('' !== $name && 'complex' == $this->config['type']) {

            return $this->connect($this->config[$name], strtolower($name));
        }

        // 这个init 只有在原先缓存类不存在或者强制生成新缓存的时候才能生效
        return $this->init();
    }

    public function __call($method, $args)
    {
       // var_dump($this->init());
        return call_user_func_array([$this->init(), $method], $args);
    }

}
