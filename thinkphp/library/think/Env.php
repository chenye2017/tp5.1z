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

class Env
{
    /**
     * 环境变量数据
     * @var array
     */
    protected $data = [];

    public function __construct()
    {
        $this->data = $_ENV;
    }
    public function show()
    {
        var_dump($this->data);
    }


    /**
     * 读取环境变量定义文件
     * @access public
     * @param  string    $file  环境变量定义文件
     * @return void
     */
    public function load($file)
    {
        $env = parse_ini_file($file, true); // .env 是 ini 文件
        $this->set($env);
    }

    /**
     * 获取环境变量值
     * 现在自己的data 属性中寻找，没找到去getenv
     * @access public
     * @param  string    $name 环境变量名
     * @param  mixed     $default  默认值
     * @return mixed
     */
    public function get($name = null, $default = null, $php_prefix = true)
    {
        if (is_null($name)) {
            return $this->data;
        }

        $name = strtoupper(str_replace('.', '_', $name));

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return $this->getEnv($name, $default, $php_prefix);
    }

    protected function getEnv($name, $default = null, $php_prefix = true)
    {

        if ($php_prefix) {
            $name = 'PHP_' . $name;
        }

        $result = getenv($name);

        if (false === $result) {
            return $default;
        }

        // 不知道下面这些什么情况走
        if ('false' === $result) {
            $result = false;
        } elseif ('true' === $result) {
            $result = true;
        }

        if (!isset($this->data[$name])) {
            $this->data[$name] = $result; // 下次再次查找的时候就不用走到这了
        }

        return $result;
    }

    /**
     * 设置环境变量值
     * @access public
     * @param  string|array  $env   环境变量
     * @param  mixed         $value  值
     * @return void
     */
    public function set($env, $value = null)
    {
        if (is_array($env)) {
            $env = array_change_key_case($env, CASE_UPPER);

            foreach ($env as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $this->data[$key . '_' . strtoupper($k)] = $v;
                    }
                } else {
                    $this->data[$key] = $val; // 感觉 .env 会覆盖之前的环境变量
                }
            }
          //  var_dump($this->data);
        } else {
            $name = strtoupper(str_replace('.', '_', $env));

            $this->data[$name] = $value;
        }
    }
}
