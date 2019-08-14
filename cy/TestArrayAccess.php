<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/8/4
 * Time: 13:04
 */

class TestArrayAccess implements ArrayAccess
{
    private $arr = [];

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
        $this->arr[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->arr[$offset]) ? true : false;
    }

    public function offsetUnset($offset)
    {
        unset($this->arr[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->arr[$offset];
    }
}