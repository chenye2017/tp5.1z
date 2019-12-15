<?php
namespace app\index\controller;

use app\index\model\Danmu;
use Rakit\Validation\Rule;
use think\Container;
use think\Controller;
use think\Db;
use think\facade\Hook;
use think\Request;
use think\Validate;
use think\validate\ValidateRule;

class Index extends Controller
{
  //  protected $failException = true;
    protected $batchValidate = true;


    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function hello1()
    {
        return 'cy';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 11;

        var_dump((new Danmu())->all());exit;
        $res = Db::table('danmu')->select();
        var_dump($res);

        exit;

        $res = Db::query('select * from danmu');
        var_dump($res);

        /*try {
            $this->success('success');
        } catch (\Exception $e) {
            return $e;
        }*/
      /*  try {
            spl_autoload_register(function ($class) {
                var_dump($class);
            }, true, true);
            $cy = new \Test();

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }*/

       // var_dump(111);
        return 'hello,' . $name;
    }

    public function testhook(Request $req)
    {
        var_dump(app('be'));exit;
      //  var_dump('start');
        var_dump($req->name);exit;

        Hook::add('test_1', [Behavior::class, Behavior1::class]); // 添加钩子和行为绑定
        Hook::add('test_2', Behavior::class);

      //  var_dump(Hook::get());
        Hook::listen('test_1',['name'=>'test_1', 'test'=>11]); // 设置钩子(，执行到这，就去tags 里面找对应的类，然后执行)；


    }

    /**
     * 注解路由
     * @route('cy3')
     */
    public function test3()
    {
        var_dump('e');
    }

    public function val()
    {



        try {

            $_SESSION['think'] = ['name1' => 'thinkphp'];
            $result = $this->validate(
                [
                    'name1'  => 'thinkphp',
                    'email' => 'thinkphp@qq.com',

                ],
                'app\index\validator\User');
            var_dump($result);exit;
            $validate = new Validate();
            $check = $validate->batch(true)->rule([
                'age1|年龄' => 'max:1,2',
                'sex1|性别' => 'require',
                'zidingyi' => function ($val, $rule, $data) {
                    return 'zidingyieroro'; // 自定义错误就没有必要自定义错误信息啦，因为不是 === true,都会被判定成error的
                },
                'zidingyi2' => ValidateRule::isRequire('', 'require must')->max(25, '', 'must 小于 5')
            ])->message([
                'age1.max' => '属性:attribute  规则:rule  第一个参数:1' // 追源码，这里只能用那几个参数
            ])->check([
              //  'age1' => 12,
                // 这些验证规则必须包含require， 否则没传的话，将不会触发max等验证规则
                ['ll' => 'cy'],
                ['llls' => 'cy1']
            ]);
            var_dump($validate->getError());exit;




            if (true !== $result) {
                // 验证失败 输出错误信息
                // dump($result);

                var_dump($result);
            }

            $result = $this->validate(
                [
                    'name'  => 'thinkphp',
                    'email' => 'thinkphp@qq.com',
                ],
                'app\index\validator\User');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
