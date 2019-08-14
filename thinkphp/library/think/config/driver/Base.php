<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/8/5
 * Time: 23:47
 */

namespace think\config\driver;

/**
 * 我自己写的
 * Interface Base
 * @package think\config\driver
 */
interface Base
{
    public function __construct($config);


    public function parse();

}