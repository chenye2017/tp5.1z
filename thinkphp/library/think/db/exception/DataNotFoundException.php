<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://zjzit.cn>
// +----------------------------------------------------------------------

namespace think\db\exception;

use think\exception\DbException;

class DataNotFoundException extends DbException
{
    protected $table;

    /**
     * DbException constructor.
     * @access public
     * @param  string $message
     * @param  string $table
     * @param  array $config
     */
    public function __construct($message, $table = '', array $config = [])
    {
        // 表的数据查找不到的exception
        // 其实我之前一直想重写 getMessage 这些方法，其实这些方法是final， 不能重写，但他们获取的是message 属性，所以我们其实只要修改message 属性，就能达到重写这个方法的作用
        $this->message = $message;
        $this->table   = $table;

        $this->setData('Database Config', $config);
    }

    /**
     * 获取数据表名
     * @access public
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
}
