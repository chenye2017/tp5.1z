<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/8/8
 * Time: 11:30
 */

class Cyface extends \think\Facade
{
    protected static $bind = ['Cyface' => 'Cyconcrete'];

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

        return \think\Container::getInstance()->make($class, $args, $newInstance); // 也是从容器中取数据
    }

    /*protected static function getFacadeClass()
    {
        return '\Cyconcrete';
    }*/
}