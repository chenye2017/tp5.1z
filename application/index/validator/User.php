<?php
/**
 * Created by PhpStorm.
 * User: cy
 * Date: 2019/12/13
 * Time: 17:22
 */

namespace app\index\validator;
use think\Validate;

class User extends Validate
{

    // rule 和 message 都会重写子类的rule 和 message
    protected $rule =   [
        'name1'  => 'token:name1',

        'age'   => 'require|number|between:1,120',
        'email' => 'require|email',
    ];

    protected $message  =   [
        'name.require' => '名称必须',
        'name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在1-120之间',
        'email'        => '邮箱格式错误',
    ];

    protected $scene = [
        'edit'  =>  [''],  // 这块这样可以不验证， [] 这样代表全验证，感觉很奇怪
    ];

    // 优先下面这种方式
  /*  public function sceneedit()
    {
        $this->only(['name'])
            ->append(['name' => 'min:5', 'sex' => 'require|max:3'])
          //  ->remove('age', 'between')
            ->append('age', 'require|max:100')
            ->remove('name', ['require'])
            ->remove('age'); // 移除age 的所有内容
    }*/

    // value 本次验证的内容的值
    // rule 验证规则的 data
    // 这次整个验证的内容
    public function checkName($value, $rule, $data = [])
    {

    }
}