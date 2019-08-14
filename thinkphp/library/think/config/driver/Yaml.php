<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/8/5
 * Time: 23:54
 */

namespace think\config\driver;

/**
 * 自己写着玩
 * Class Yaml
 * @package think\config\driver
 */
class Yaml implements Base
{
    public  $config;
    public function __construct($config)
    {
       $this->config = $config;
    }

    public function parse()
    {
       return yaml_parse_file($this->config);
    }
}