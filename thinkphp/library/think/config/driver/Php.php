<?php
namespace think\config\driver;


/**
 * 自己写着玩
 * Class Php
 * @package think\config\driver
 */
class  Php implements Base
{
    public function __construct($config)
    {
        $this->config = $config;
    }


    public function parse()
    {
        return include_once ($this->config);
    }

}