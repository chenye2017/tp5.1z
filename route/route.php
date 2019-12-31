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

use app\common\model\Cy;
use \think\facade\Request;

error_reporting(E_ALL);
ini_set('display_errors', 1);

/*try {

    $cyc = new \Cyconcrete();
} catch (\Exception $e) {
    var_dump($e->getMessage());
}*/

Route::get('/hupu1', function () {
    $z = new \app\index\controller\Behavior();
});

$cyc = 1;

Route::get('/hupu/:xixi', function ($t1, $t2, \Cy $haha, $test = 'cy', $xixi) use ($cyc) {
    //  var_dump(13);
    var_dump('hello world');
    return;

    $cyc->test();
    var_dump($haha->test());

    return 'cy';

    return \think\facade\Response::create(['name' => '陈野'], 'json');

    return json(['name' => '陈野']);

    return ['name' => 'cy'];
    /*\think\facade\Response::contentType('application/json');

    \think\facade\Response::data()*/

    $json = new \think\response\Json();
    $json->data(['name' => 'cy']);
    return $json;

    // var_dump(container()->get('response'));
    //return json([12=>11]);

    // return 12; // 本质上还是装的response 对象 // 这个地方还是不能返回数组的
});


// 这个文件的名称可以随便命名，后期会统一加载

// miss 方法也能支持匿名函数
Route::miss(function () {
    var_dump('路由找不到啦');
});

Route::get('/ss', 'index/Test/index');


Route::resource('blog', 'index/blog'); // 出现错误，不知道默认调用的是谁

Route::get('blog/:id', function () {

    var_dump('read');
    exit;
});

Route::rule('/test3', 'index/Index/hello')->method('get|post'); // method 最终还是要转换成 ruleltem (继承于 rule ,rule 中有method 的方法) ,option 属性

Route::get('/test1/:name/:sex', function ($sex, $name) {
    var_dump($name, $sex);
});


Route::get('/', function () {

    $options = [
        // 缓存配置为复合类型
        'type'    => 'complex',
        'default' => [
            'type'   => 'file',
            // 全局缓存有效期（0为永久有效）
            'expire' => 0,
            // 缓存前缀
            'prefix' => 'think', // file 文件中就是文件夹前缀
            // 缓存目录
            'path'   => '../runtime/cache/',
        ],
        'redis'   => [
            'type'   => 'redis',
            'host'   => '127.0.0.1',
            // 全局缓存有效期（0为永久有效）
            'expire' => 0,
            // 缓存前缀
            'prefix' => 'think',
        ],
        // 添加更多的缓存类型设置
    ];

    \think\facade\Cache::setConfig($options);
    \think\facade\Cache::store('default')->set('cyname', 'llllssss');
    var_dump(1111);
    var_dump(\think\facade\Cache::handler());
    \think\facade\Cache::store('redis')->set('cyname', 'xxxx');
    //var_dump(1111,\think\facade\Cache::handler());
    exit;


    try {
        var_dump(12);
    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }


    exit;
    $redis = new \think\cache\driver\Redis();

    $redis->set('name12', 'cy');

    try {
        $tmp = Cache::serialize(['name' => 'cy']);

        Cache::unserialize($tmp);
    } catch (\Exception $e) {
        var_dump($e->getMessage());
        exit;
    }
    exit;

    var_dump(Cache::get('name'));

    exit;

    Cache::set('name', 'cy');
    Cache::inc('name');
    try {

    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }
    exit;

    Cache::inc('name', 1);

    exit;

    Cache::rm('name');
    exit;

    Cache::set('name', 'cy', 3600);
    exit;

    var_dump(11);

    var_dump('nihao');

    exit;

    $a = 1;
    $b = 2;

    return 11;
    //trigger_error('eUSER', E_USER_NOTICE); 能捕获
    // require_once 'ss.php'; 不能捕获
    throw new \Exception('test'); // 能捕获 ，所以也是有try catch 的
    //var_dump(\think\Container::get('app')->config('exception_tmpl'));
    var_dump(111);
    exit;

    \think\Container::get('log')->write('test');
    exit;
    // var_dump(interface_exists('Psr\Log\LoggerInterface'));exit;

    //facecade 中获取的是容器中的实例，config() 这种获取的都不是容器中的实例
    //
    //
    // require_once './pp.php';
    //  var_dump(1111, $a);
    var_dump(2);
    exit;
    var_dump(app('hook')->get());
    exit;

    var_dump(11);
    exit;
    var_dump(\think\facade\Config::get());
    exit;
    $c = new \think\Config();
    var_dump($c->get());
    exit;

    $cy = new \Cy();
    var_dump($cy->test());
});


/*Route::get('/yilai', function (\app\index\controller\Behavior $obj) {
    $obj->test();
});*/

// 千万不能这么写，太傻比了
\think\facade\Route::get('/name/:id', function ($id, $name) {
    // var_dump("id is $id, name is $name");
    return json_encode(['test' => 'test']);
});


Route::get('/m', function () {
    $mc = new \Memcached('mc');  //创建一个 memcached 线程池
    $mc->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
    $mc->addServers(array(
        array('127.0.0.1', 11211)
    ));

    $is_ok = $mc->set("site", "www.twle.cn");  // 设置一个 key 为 site value 为 www.twle.cn 的缓存

    var_dump($is_ok);

    $site = $mc->get("site");        // 从缓存中取出 key 为 site 的值

    var_dump($site);
});


Route::get('/gaibian', function () {

    $options = [
        // 驱动方式
        'type'   => 'Redis',
        // 缓存保存目录
        'path'   => '',
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ];

    $cache = new \think\Cache($options);
    //$cache->setOption($options);
    $cache->set('ceshi', 'cycycyy');

    $options = [
        // 驱动方式
        'type'   => 'Memcached',
        // 缓存保存目录
        'path'   => '',
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ];

    $cache->connect($options)->set('n1', 'cy111');


});

Route::get('/xuliehua', function () {

    $options = [
        // 驱动方式
        'type'   => 'Redis',
        // 缓存保存目录
        'path'   => '',
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ];
    $cache   = new \think\Cache($options);

    $cache->registerSerialize('json_encode', 'json_decode', ''); // 改变序列化规则

    $cache->set('o2', ['name' => 'cy']);


});

Route::get('/tag', function () {


    $options = [
        // 驱动方式
        'type'   => 'Redis',
        // 缓存保存目录
        'path'   => '',
        // 缓存前缀
        'prefix' => 'cy_',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
        'name'   => 'cy'
    ];

    $driver = Cache::init($options, 1);

    Cache::registerSerialize('json_encode', 'json_decode', '', 0);
// 获取缓存对象句柄
    Cache::set('cybody', ['name' => 'cy', 'age' => 18]);

    exit;


    $cache = new \think\Cache($options); // redis 这块是利用属性handler 去写入，所以不用返回值
    $cache->clear();
    exit;
    $cache->set('name', 'cy');

    exit;

    $cache->tag('tag');
    $cache->set('name-cy13', 'cy1');
    $cache->set('name-cy2', 'cy2');
    $cache->tag('tag', ['name-cy1', 'name-cy2']);

    $cache->set('name-cy1', 'xinde');


});

Route::get('/shujuku', function () {
    //    $res =  Db::table('test1')->value('name');

    $res1 = Db::table('test1')->where('id = 1 or id = 2')->column('name');

    $res2 = Db::table('test1')->where('id = 1 or id = 2')->order('id', 'asc')->value('name'); // 这个和 find 一样只能返回一条记录的一个字段的值

    $res4 = Db::name('test1')->where('id = 1')->find();

    var_dump($res1, $res2, $res4);


});


Route::get('/testhook', '\app\index\controller\Index@testhook');

Route::get('/test', '/');


Route::post('/excel', function () {
    $file = $_FILES['file'];


    $uploadfile = $file['tmp_name'];
    // $reader        = PHPExcel_IOFactory::createReader('excel2007'); //设置以Excel5格式(Excel97-2003工作簿)
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');


    $PHPExcel      = $reader->load($uploadfile); // 载入excel文件
    $sheet         = $PHPExcel->getSheet(0); // 读取第一個工作表
    $highestRow    = $sheet->getHighestRow(); // 取得总行数
    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
    $data          = [];

    $p = '';
    for ($row = 2; $row <= $highestRow; $row++) //行号从1开始
    {
        $tmp = [];
        for ($column = 'A'; $column <= $highestColumm; $column++) //列数是以A列开始
        {

            $c = $sheet->getCell($column . $row)->getValue();

            if ($column == 'A') {
                if ($c) {
                    $p = $c;
                } else {
                    $c = $p;
                }
            }
            $tmp[] = $c;
        }
        $data[] = $tmp;
    }

    file_put_contents('/home/code/tp51z/tmp.txt', json_encode($data, JSON_UNESCAPED_UNICODE));
    exit;

    $myfile = fopen($uploadwork . $file_name . ".vcf", "w") or die("Unable to open file!");
    foreach ($data as $key => $value) {
        $all_str = $this->make_vcard($value['name'], $value['mobile']);
        fwrite($myfile, $all_str);
    }
    fclose($myfile);

    // var_dump(11);exit;

});


Route::get('/db', function () {

    var_dump(Db::name('question')->column('question_id'));
    exit;

    Db::name('question')->chunk(2, function () {
        var_dump(1);
        return false;
    });

    exit;

    $res1 = [];

    Db::name('question')->select();

    $res2 = Db::name('question')->chunk(2, function () {

    });

    var_dump($res2);
    exit;

    var_dump($res1);
    exit;


    // $res = Db::name('question')->where('question_id = 100001')->column('question_id');


    //var_dump($res);

    exit;

    $model = new \app\index\model\Question();
    var_dump($model->find(90)); // 找不到对象就是空的

    exit;

    $res = $model->findOrFail(90);

    var_dump($res);

    exit;

    // 这个DB 没有做facade 映射，直接调用的实体类
    // connect 这个方法就是静态方法，所以也就没有实例化类
    // 而且注意到 tp 在加载过程中并没有和 加载别的类库一样加载 db 这个类，
    // 这个Db 类的connect 方法实际返回的是一个query 类
    var_dump(Db::connect('mysql://root:wyqnkxk2012_CY@127.0.0.1:3306/peixun#utf8')
        ->table('gm_question')
        ->findOrFail(9008));

    exit;

    var_dump(Db::connect('mysql://root:wyqnkxk2012_CY@127.0.0.1:3306/peixun#utf8')
        ->table('gm_question')
        ->where('answer', '=', 'D')->select());

    // 这个到头来 query 的 options 还是始终没有清空

    // 这个connect
    sleep(100);
});

Route::get('/lianjie', function () {

    // $a = new \PDO('mysql://127.0.0.1:3306/peixun', 'root', 'wyqnkxk2012_CY');

    $a = new \PDO('mysql:host=127.0.0.1;dbname=peixun', 'root', 'wyqnkxk2012_CY');

    $b = new \PDO('mysql:host=127.0.0.1;dbname=peixun', 'root', 'wyqnkxk2012_CY');

    // new Pdo 肯定会生成一个新的连接， 所以我们不能直接new， 应该封装。把每次new 的结果保存起来 （根据配置名字保存），需要的时候直接用这个

    sleep(100);
});

Route::get('/insert', function () {

    var_dump(Db::name('cy')->find(10));

    Db::name('cy')->data('name', 'cussss')->where('id', 10)->update();

    // Db::name('cy')->data('name', 'cyooo')->where('id', 10)->setInc('age', 2, 20);

    exit;
    //var_dump(Db::name('cy')->find(10));exit;

    $res = [
        ['name' => 'cy', 'age' => 20],
        ['name' => 'cy1', 'age' => 21]
    ];

    Db::name('cy')->insertAll($res);

    exit;
    $res = Db::name('cy')->insert(['name' => 'cy', 'age' => 19], false, true);
    var_dump($res);
    exit;
});

Route::get('/update', function () {

    //var_dump(Db::name('cy')->data('age', 'round(8.3)')->where('id', 10)->update());

    var_dump(Db::name('cy')->data('name', 'cy111')->where('id', 10)->setInc('age', '1', 2)->update());
    exit;

    var_dump(Db::name('cy')->find(10));

    exit;

    var_dump(Db::name('cy')->inc('age', 12)->where('id', 10)->update());

    exit;

    var_dump(Db::name('cy')->where('id', 10)->update(['name' => 'cy1']));
});

Route::get('/baoliu', function () {

    // Db::connect('')

    //  var_dump(Db::name('cy')->select());exit;

    $client = Db::name('cy');

//    var_dump($client->select());exit;

    //   var_dump($client->where('age', 10)->select());
    //  var_dump($client->select(), 'end');exit;


    $client = Db::name('cy');
    $res    = $client->where('id', 10)->select();

    var_dump($res);

    /*$res1 = $client->removeOption('where')->where('id', 12)->select();
    var_dump($res1);*/

});

Db::listen(function ($sql, $time, $explain) {
    // 记录SQL

    echo $sql . ' [' . $time . 's]';
    // 查看性能分析结果
    dump($explain);
});

Route::get('/learn', function () {
    $con = Db::connect(); // connect 的时候并没有生成链接


    $q = $con->name('cy');


    $q = $q->where('id', 10);


    var_dump('start');

    $q->select();
    exit;

    // sleep(100);
    // 调用 table 或者 name 的时候才会生成真的连接

    var_dump($con->name('cy')->select());
    //sleep(100);
});

Route::get('/ccc', function () {
    var_dump(11);
});

Route::get('/t11', function () {

    $d = new \think\Db();


    $d->table('cy')->select();

    Db::table('cy')->select();
});

Route::get('/get', function () {
    try {
        error_reporting(0);
        $client = \JonnyW\PhantomJs\Client::getInstance();
        var_dump(14);
//这一步非常重要，务必跟服务器的phantomjs文件路径一致
        $client->getEngine()->setPath('/usr/local/bin/phantomjs');
        $request  = $client->getMessageFactory()->createRequest();
        $response = $client->getMessageFactory()->createResponse();

//设置请求方法
        $request->setMethod('GET');
//设置请求连接
        $request->setUrl('https://mp.weixin.qq.com/s/nd2YAIOuT0f_FkdgCnJ5Ug');
//发送请求获取响应
        $client->send($request, $response);

        if ($response->getStatus() === 200) {
            //输出抓取内容

            echo htmlentities($response->getContent(), ENT_QUOTES, "UTF-8");
            exit;
            //获取内容后的处理
        }
    } catch (\Throwable $e) {
        var_dump(12, $e->getMessage(), 11);
    }
});

Route::get('/model', function (\think\Request $req) {

    $res = Cy::withAttr('id', function ($value, $data) {
        var_dump($value, $data); // 这个value 应该是key , data 是内容
    })->select(); // 原本是 query, 调用select 之后转换成collection (所以其实db 操作也能转换成collection)
    // 这个collection 对象封装了 withAttr 的回调，并不执行，只有在toArray 某些时候才会被触发
    var_dump($res->toArray());
    exit;
    exit;

    /*var_dump(Cy::get(100));
    exit;*/
    // orm 没查找到返回的是null, 和之前的还不一样

    $id = $req->id;
    $u  = Cy::where('id', $id)->select();

    var_dump($u->toArray()); // 感觉如果是 空的，就可以盘点是新增了 （因为有时候我们自己并不知道是新增还是修改，所以不能主动调用不同的save 方法）
});

Route::get('/node', function () {
    sleep(20);
    echo 1;

});

Route::get('/url', function (\think\Request $request) {
    $z = $_GET;
    return json($z);
});


Route::post('/excel2', function () {
    $file = $_FILES['file'];


    $uploadfile = $file['tmp_name'];
    // $reader        = PHPExcel_IOFactory::createReader('excel2007'); //设置以Excel5格式(Excel97-2003工作簿)
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');


    $PHPExcel      = $reader->load($uploadfile); // 载入excel文件
    $sheet         = $PHPExcel->getSheet(0); // 读取第一個工作表
  //  $highestRow    = $sheet->getHighestRow(); // 取得总行数
    $highestRow = 305;
    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
    $data1          = [];
    $data2 = [];
    $data3 = [];
    $data4 = [];
    $data5 = [];

    $p = '';
    for ($row = 3; $row <= $highestRow; $row++) //行号从3开始
    {
        $c = $sheet->getCell('A' . $row)->getValue();
        $yushu = $row % 5;
        switch ($yushu) {
            case 0:
                $data1[] = $c;
                break;
            case 1:
                $data2[] = $c;
                break;
            case 2:
                $data3[] = $c;
                break;
            case 3:
                $data4[] = $c;
                break;
            case 4:
                $data5[] = $c;
                break;
        }
    }

    file_put_contents('/home/code/tp51z/tmp1.txt', json_encode($data1, JSON_UNESCAPED_UNICODE));
    file_put_contents('/home/code/tp51z/tmp2.txt', json_encode($data2, JSON_UNESCAPED_UNICODE));
    file_put_contents('/home/code/tp51z/tmp3.txt', json_encode($data3, JSON_UNESCAPED_UNICODE));
    file_put_contents('/home/code/tp51z/tmp4.txt', json_encode($data4, JSON_UNESCAPED_UNICODE));
    file_put_contents('/home/code/tp51z/tmp5.txt', json_encode($data5, JSON_UNESCAPED_UNICODE));
    exit;

    $myfile = fopen($uploadwork . $file_name . ".vcf", "w") or die("Unable to open file!");
    foreach ($data as $key => $value) {
        $all_str = $this->make_vcard($value['name'], $value['mobile']);
        fwrite($myfile, $all_str);
    }
    fclose($myfile);

    // var_dump(11);exit;

});

Route::any('/otherVal', function () {
    $validator = new \Rakit\Validation\Validator();

    $_POST = [
       'name:cy de  name ' => 'cy',
       'email' => '1967196626@qq.com'
    ];
// make it
    $validation = $validator->make($_POST + $_FILES, [
        'name'                  => 'required|min:6|max:10',
        'email'                 => 'required|email',
        'password'              => 'required|min:6',
        'confirm_password'      => 'required|same:password',
        'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
        'skills'                => 'array',
        'skills.*.id'           => 'required|numeric',
        'skills.*.percentage'   => 'required|numeric'
    ]);

// then validate
    $validation->validate();

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
        echo "<pre>";
        print_r($errors->firstOfAll());
        echo "</pre>";
        exit;
    } else {
        // validation passes
        echo "Success!";
    }
});

Route::any('/conVal', 'app\index\controller\Index@val');

Route::any('/vvv/:id', 'app\index\controller\Index@hello1');

Route::any('/val', function () {

    $data = [
        'name' => 'chs', // 这个空值在没有require 的情况下无敌
        'arr' => ['name' => ['age' => 10]]
    ];
    $userCheck = new \app\index\validator\User();
    $res = $userCheck->scene('edit')->batch()->check($data);

   // $res = $userCheck->batch()->check($data);

    var_dump($res, $userCheck->getError());exit;


    $validator = new \Rakit\Validation\Validator();

// make it
    $validation = $validator->make($_POST + $_FILES, [
        'name'                  => 'required',
        'email'                 => 'required|email',
        'password'              => 'required|min:6',
        'confirm_password'      => 'required|same:password',
        'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
        'skills'                => 'array',
        'skills.*.id'           => 'required|numeric',
        'skills.*.percentage'   => 'required|numeric'
    ]);

// then validate
    $validation->validate();

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
        echo "<pre>";
        print_r($errors->firstOfAll());
        echo "</pre>";
        exit;
    } else {
        // validation passes
        echo "Success!";
    }
});

Route::get('/redis', function() {
   $client = new \Predis\Client();
   var_dump($client->exists('name12'));exit;
});



Route::any('/controller', 'app\index\v1\Duo@test1')->cache('3600');

Route::get('/domain', function() {
    var_dump(Request::url().PHP_EOL);
// 获取完整URL地址 包含域名
   var_dump(Request::url(true).PHP_EOL);
// 获取当前URL（不含QUERY_STRING） 不带域名
   var_dump(Request::baseFile().PHP_EOL);
// 获取当前URL（不含QUERY_STRING） 包含域名
    var_dump(Request::baseFile(true));
// 获取URL访问根地址 不带域名
    var_dump(Request::root().PHP_EOL);
// 获取URL访问根地址 包含域名
    var_dump(Request::root(true));
});



return [

];
