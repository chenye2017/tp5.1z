<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/10/27
 * Time: 19:54
 */
namespace app\index\model;

class Question extends \think\Model
{
    // table 定义的是表名，这个定义之后别的那些前缀就不生效了，比如 prefix


    public $pk = 'question_id';
}