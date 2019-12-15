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

namespace think\cache;

use think\Container;

/**
 * 缓存基础类
 */
abstract class Driver
{
    /**
     * 驱动句柄
     * @var object
     */
    public $handler = null;

    /**
     * 缓存读取次数
     * @var integer
     */
    protected $readTimes = 0;

    /**
     * 缓存写入次数
     * @var integer
     */
    protected $writeTimes = 0;

    /**
     * 缓存参数
     * @var array
     */
    protected $options = [];

    /**
     * 缓存标签
     * @var string
     */
    protected $tag; // 感觉只有一个

    /**
     * 序列化方法
     * @var array
     */
    protected static $serialize = ['serialize', 'unserialize', 'think_serialize:', 16]; // 默认的序列化方案

    /**
     * 判断缓存是否存在
     * @access public
     * @param  string $name 缓存变量名
     * @return bool
     */
    abstract public function has($name);

    /**
     * 读取缓存
     * @access public
     * @param  string $name 缓存变量名
     * @param  mixed  $default 默认值
     * @return mixed
     */
    abstract public function get($name, $default = false);

    /**
     * 写入缓存
     * @access public
     * @param  string    $name 缓存变量名
     * @param  mixed     $value  存储数据
     * @param  int       $expire  有效时间 0为永久
     * @return boolean
     */
    abstract public function set($name, $value, $expire = null);

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长
     * @return false|int
     */
    abstract public function inc($name, $step = 1);

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长
     * @return false|int
     */
    abstract public function dec($name, $step = 1);

    /**
     * 删除缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return boolean
     */
    abstract public function rm($name);

    /**
     * 清除缓存
     * @access public
     * @param  string $tag 标签名
     * @return boolean
     */
    abstract public function clear($tag = null);

    /**
     * 获取有效期
     * @access protected
     * @param  integer|\DateTime $expire 有效期
     * @return integer
     */
    protected function getExpireTime($expire)
    {
        // 也不知道第一种是给谁用的
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }

        return $expire;
    }

    /**
     * 获取实际的缓存标识, redis 的基本没改变，主要就是文件缓存的时候，文件名改变的比较多
     * @access protected
     * @param  string $name 缓存名
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $this->options['prefix'] . $name;
    }

    /**
     * 读取缓存并删除
     * @access public
     * @param  string $name 缓存变量名
     * @return mixed
     */
    public function pull($name)
    {
        $result = $this->get($name, false);

        if ($result) {
            $this->rm($name);
            return $result;
        } else {
            return;
        }
    }

    /**
     * 如果不存在则写入缓存
     * 简单的类似分布式锁
     * @access public
     * @param  string    $name 缓存变量名
     * @param  mixed     $value  存储数据
     * @param  int       $expire  有效时间 0为永久
     * @return mixed
     */
    public function remember($name, $value, $expire = null)
    {
        if (!$this->has($name)) {
            $time = time();

            // 执行5s 每次间隔 0.2 s
            while ($time + 5 > time() && $this->has($name . '_lock')) {
                // 存在锁定则等待
                usleep(200000); // 0.2 s
            }

            try {
                // 锁定
                $this->set($name . '_lock', true);

                if ($value instanceof \Closure) {
                    // 获取缓存数据
                    $value = Container::getInstance()->invokeFunction($value);
                }

                // 缓存数据
                $this->set($name, $value, $expire);

                // 解锁
                $this->rm($name . '_lock');

                // 因为现在是 php7 了，所以感觉现在 try catch 都分成两种
                // 这块不能交给统一处理，因为需要自身删除锁

            } catch (\Exception $e) {
                $this->rm($name . '_lock');
                throw $e;
            } catch (\throwable $e) {
                $this->rm($name . '_lock');
                throw $e;
            }
        } else {
            $value = $this->get($name);
        }

        return $value;
    }

    /**
     * 缓存标签
     * @access public
     * @param  string        $name 标签名
     * @param  string|array  $keys 缓存标识
     * @param  bool          $overlay 是否覆盖
     * @return $this
     */
    public function tag($name, $keys = null, $overlay = false)
    {


        if (is_null($name)) {

        } elseif (is_null($keys)) {
            $this->tag = $name;
        } else {



            $key = 'tag_' . md5($name);

            if (is_string($keys)) {
                $keys = explode(',', $keys);
            }
           // var_dump($keys);
            $keys = array_map([$this, 'getCacheKey'], $keys);



            if ($overlay) {
                $value = $keys;
            } else {

                // 以前的 tag 和 现在的tag 合并
                $value = array_unique(array_merge($this->getTagItem($name), $keys));
            }

            $this->set($key, implode(',', $value), 0); // 1个tag对应的多个值连接成string
        }

        return $this;
    }

    /**
     * 更新标签
     * @access protected
     * @param  string $name 缓存标识
     * @return void
     */
    protected function setTagItem($name)
    {
        // 这个name 就是要设置的key
        if ($this->tag) {
            $key       = 'tag_' . md5($this->tag);
            $prev      = $this->tag;
            $this->tag = null;

            if ($this->has($key)) {
                $value   = explode(',', $this->get($key));

                $value[] = $name;
                $value   = implode(',', array_unique($value));
            } else {
                $value = $name;
            }

            // 拼接
            $this->set($key, $value, 0);
            $this->tag = $prev;
        }
    }

    /**
     * 获取标签包含的缓存标识
     * @access protected
     * @param  string $tag 缓存标签
     * @return array
     */
    protected function getTagItem($tag)
    {
        $key   = 'tag_' . md5($tag);
        $value = $this->get($key);

        if ($value) {
            return array_filter(explode(',', $value));
        } else {
            return [];
        }
    }

    /**
     * 序列化数据
     * @access protected
     * @param  mixed $data
     * @return string
     */
    public function serialize($data)
    {


        if (is_scalar($data) || !$this->options['serialize']) {
           // var_dump($data);
            return $data;
        }

        $serialize = self::$serialize[0];
       // var_dump(self::$serialize[2], serialize($data), $data);

        return self::$serialize[2] . $serialize($data); // 这个地方是否可以传入额外的参数，比如json_encode  中文
    }

    /**
     * 反序列化数据
     * @access protected
     * @param  string $data
     * @return mixed
     */
    public function unserialize($data)
    {

        if ($this->options['serialize'] && 0 === strpos($data, self::$serialize[2])) {
            $unserialize = self::$serialize[1];

            return $unserialize(substr($data, self::$serialize[3]));
        } else {
            return $data;
        }
    }

    /**
     * 注册序列化机制
     * @access public
     * @param  callable $serialize      序列化方法
     * @param  callable $unserialize    反序列化方法
     * @param  string   $prefix         序列化前缀标识
     * @return $this
     */
    public static function registerSerialize($serialize, $unserialize, $prefix = 'think_serialize:')
    {
        self::$serialize = [$serialize, $unserialize, $prefix, strlen($prefix)]; // 想象自身序列化和反序列化的实现
    }

    /**
     * 返回句柄对象，可执行其它高级方法
     *
     * @access public
     * @return object
     */
    public function handler()
    {
        return $this->handler;
    }

    public function getReadTimes()
    {
        return $this->readTimes;
    }

    public function getWriteTimes()
    {
        return $this->writeTimes;
    }
}
