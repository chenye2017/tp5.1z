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

namespace think;

use think\exception\ClassNotFoundException;
use think\exception\HttpResponseException;
use think\route\Dispatch;

/**
 * App 应用管理
 */
class App extends Container
{
    const VERSION = '5.1.29 LTS';

    /**
     * 当前模块路径
     * @var string
     */
    protected $modulePath;

    /**
     * 应用调试模式
     * @var bool
     */
    protected $appDebug = true;

    /**
     * 应用开始时间
     * @var float
     */
    protected $beginTime;

    /**
     * 应用内存初始占用
     * @var integer
     */
    protected $beginMem;

    /**
     * 应用类库命名空间
     * @var string
     */
    protected $namespace = 'app';

    /**
     * 应用类库后缀
     * @var bool
     */
    protected $suffix = false;

    /**
     * 严格路由检测
     * @var bool
     */
    protected $routeMust;

    /**
     * 应用类库目录
     * @var string
     */
    protected $appPath; // application 文件夹位置

    /**
     * 框架目录
     * @var string
     */
    protected $thinkPath;

    /**
     * 应用根目录
     * @var string
     */
    protected $rootPath; // 项目目录

    /**
     * 运行时目录
     * @var string
     */
    protected $runtimePath;

    /**
     * 配置目录
     * @var string
     */
    protected $configPath;

    /**
     * 路由目录
     * @var string
     */
    protected $routePath;

    /**
     * 配置后缀
     * @var string
     */
    protected $configExt;

    /**
     * 应用调度实例
     * @var Dispatch
     */
    protected $dispatch;

    /**
     * 绑定模块（控制器）
     * @var string
     */
    protected $bindModule;

    /**
     * 初始化
     * @var bool
     */
    protected $initialized = false;

    public function __construct($appPath = '')
    {
        $this->thinkPath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR; // 设置thinkpath

        $this->path($appPath); // app_path
       // var_dump(App::$instance);exit;
    }

    /**
     * 绑定模块或者控制器
     * @access public
     * @param  string $bind
     * @return $this
     */
    public function bind($bind)
    {
        $this->bindModule = $bind;
        return $this;
    }

    /**
     * 设置应用类库目录
     * @access public
     * @param  string $path 路径
     * @return $this
     */
    public function path($path)
    {
        $this->appPath = $path ? realpath($path) . DIRECTORY_SEPARATOR : $this->getAppPath();

        return $this;
    }

    public function showInstances()
    {
        var_dump($this->instances);
    }

    /**
     * 初始化应用
     * @access public
     * @return void
     */
    public function initialize()
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;
        $this->beginTime   = microtime(true);
        $this->beginMem    = memory_get_usage();



            $this->rootPath = dirname($this->appPath) . DIRECTORY_SEPARATOR; // 定义 root path



        $this->runtimePath = $this->rootPath . 'runtime' . DIRECTORY_SEPARATOR; // 定义 runtime path
        $this->routePath   = $this->rootPath . 'route' . DIRECTORY_SEPARATOR;  // 定义route path
        $this->configPath  = $this->rootPath . 'config' . DIRECTORY_SEPARATOR;
      //  $this->routePath = 11;

        //$app = Container::get('app');

        //var_dump($app->showInstance(), 222);exit;
        //var_dump(static::$instance);


        //var_dump(App::$instance);

        static::setInstance($this); // 这个地方static 是 app， 可以打印 new static, 前面 Container::get('app'), 这个地方就是 app 类了， app 继承于 container， 所以 static instance 属性也是唯一的，这个地方把 app 的 instance 属性修改成自己的



       // var_dump(static::$instance);exit;
//var_dump(Container::showInstance());exit;
  //      var_dump(Container::get('app')->showInstances());
        $this->instance('app', $this); //把 app 的实例加入到 instances 属性数组中 (app 本身也是一个容器， app 继承于 container)
//var_dump(Container::get('app')->showInstances());exit;
        //var_dump()

        // 上面两行代码的作用就是把 container 的实例从 container 换成 app, 然后修改 app 这个实例的内容，比如绑定自身到 instances 属性中。 app 可以看做 container 的扩展，因为新的instance 实例 没哟 app 对象，所以重新把app对象塞了进去

        // 利用的是魔术方法， __get,
        // env 是container 中的 container ->imagees->env , 也就是 Env的实例 （）
        // app 容器中，实例化 env
        $this->configExt = $this->env->get('config_ext', '.php'); // 先在自己的data属性中查找，找不到再去getenv() 查找

        //  $this->configExt = 'yaml';


        // 加载惯例配置文件
        // app 容器中 实例化config， instances 属性中添加这个实例
        $this->config->set(include $this->thinkPath . 'convention.php');

        // 设置路径环境变量 ,这都是系统计算的属性值，往 env实例 中写入
        $this->env->set([
            'think_path'   => $this->thinkPath,   // 定义thinkphp 核心代码库 src
            'root_path'    => $this->rootPath, // 定义 root path (并不是 public path)
            'app_path'     => $this->appPath, // 定义 application path
            'config_path'  => $this->configPath, // 定义 config path
            'route_path'   => $this->routePath, // 定义 route path
            'runtime_path' => $this->runtimePath, // 定义 runtime path
            'extend_path'  => $this->rootPath . 'extend' . DIRECTORY_SEPARATOR, // 定义 extend path
            'vendor_path'  => $this->rootPath . 'vendor' . DIRECTORY_SEPARATOR, // 定义 vendor path
        ]); // 设置env 的data
        //  var_dump($this->env->show());exit;

        // 加载环境变量配置文件
        if (is_file($this->rootPath . '.env')) {

            $this->env->load($this->rootPath . '.env'); // 根目录下.env文件 （ini 规则）
        }


        $this->namespace = $this->env->get('app_namespace', $this->namespace); // 这个地方namespace 也是可以修改的
        // psr4 只规定了某个文件夹里面的文件需要按照文件夹包含的规则，并没有规定某个初始文件夹的名称一定要和命名空间一样，比如这块文件夹名称就是 application， 命名空间是 app
        // composer.json 中已经有了 app -》 application 这个命名空间，如果要修改这个app 的话，看来需要额外再添加命名空间，并不一定要修改composer.json, 也可以通过代码来修改composer 中的几个属性
        $this->env->set('app_namespace', $this->namespace);


        // 注册应用命名空间 ,感觉这块完全没有用 （composer.json 中已经加在了这个内容）
     //   Loader::addNamespace($this->namespace, $this->appPath); //因为没有用composer __autoload, 所以得自己加载,这个是prs4 ,就相当于往  $prefixLengthsPsr4 $prefixDirsPsr4 ，这两个属性里面写内容


        // 初始化应用
        $this->init();



        // 开启类名后缀
        $this->suffix = $this->config('app.class_suffix'); // 以前像一些控制器都喜欢加Controller 后缀，model 加 Model 后缀



        // 应用调试模式
        $this->appDebug = $this->env->get('app_debug', $this->config('app.app_debug'));



        $this->env->set('app_debug', $this->appDebug);

        if (!$this->appDebug) {
            ini_set('display_errors', 'Off'); // 默认php 开启，但感觉为了严谨，这块最好 true, on, false, off
        } elseif (PHP_SAPI != 'cli') {
            //重新申请一块比较大的buffer
            if (ob_get_level() > 0) {
                $output = ob_get_clean();
            }
            ob_start();
            if (!empty($output)) {
                echo $output;
            }
        }



        // 注册异常处理类
        if ($this->config('app.exception_handle')) {
            Error::setExceptionHandler($this->config('app.exception_handle'));
        }


        // 注册根命名空间
        if (!empty($this->config('app.root_namespace'))) {
            Loader::addNamespace($this->config('app.root_namespace'));
        }

        // 加载composer autofile文件
        Loader::loadComposerAutoloadFiles();

        // 注册类库别名
        Loader::addClassAlias($this->config->pull('alias'));

        // 数据库配置初始化
        Db::init($this->config->pull('database'));

        // 设置系统时区
        date_default_timezone_set($this->config('app.default_timezone'));

        // 读取语言包
        $this->loadLangPack();

        // 路由初始化
        $this->routeInit();
    }

    /**
     * 初始化应用或模块
     * 被调用两次，第一次加载全局的，第二次才加载module 中的
     * @access public
     * @param  string $module 模块名
     * @return void
     */
    public function init($module = '')
    {
        // 定位模块目录
        $module = $module ? $module . DIRECTORY_SEPARATOR : '';
        $path   = $this->appPath . $module; // module 为空就加载全局的， 否则加载某个模块下面的

        // 加载初始化文件
        if (is_file($path . 'init.php')) {
            include $path . 'init.php';
        } elseif (is_file($this->runtimePath . $module . 'init.php')) {
            include $this->runtimePath . $module . 'init.php';
        } else {

            // 加载行为扩展文件
            // 添加钩子函数
            if (is_file($path . 'tags.php')) {
                $tags = include $path . 'tags.php';

                if (is_array($tags)) {
                    $this->hook->import($tags); // 修改hook 中tags 属性
                }
            }

            // 添加公共文件
            if (is_file($path . 'common.php')) {
                include_once $path . 'common.php';
            }

            if ('' == $module) {
                // 添加系统助手函数
                include $this->thinkPath . 'helper.php';
            }

            // 添加中间件 (我们自定义的路由中间件)
            if (is_file($path . 'middleware.php')) {
                $middleware = include $path . 'middleware.php';

                if (is_array($middleware)) {
                    $this->middleware->import($middleware);
                }
            }


            // 注册服务的容器对象实例
            if (is_file($path . 'provider.php')) {
                $provider = include $path . 'provider.php';
                if (is_array($provider)) {
                    // 添加bind 属性内容
                    $this->bindTo($provider); // 这个地方返回的肯定是数组，所以只会走merge
                }
            }

            // 自动读取配置文件 （理解成执行吧）
            if (is_dir($path . 'config')) {
                $dir = $path . 'config' . DIRECTORY_SEPARATOR;
            } elseif (is_dir($this->configPath . $module)) {
                $dir = $this->configPath . $module;
            }

            $files = isset($dir) ? scandir($dir) : [];

            foreach ($files as $file) {
                if ('.' . pathinfo($file, PATHINFO_EXTENSION) === $this->configExt) { // 默认加载php文件
                    // 具体的执行步骤
                    $this->config->load($dir . $file, pathinfo($file, PATHINFO_FILENAME));
                }
            }
        }



        if (!$module) {
            // 对容器中的对象实例进行配置更新
            $this->containerConfigUpdate($module);
        }


    }

    protected function containerConfigUpdate($module)
    {

        $config = $this->config->get();//var_dump(111,$config);,加载module 的时候，用module的config覆盖全局的config

        // 注册异常处理类
        // 加载用户自定义的异常处理
        if ($config['app']['exception_handle']) {
            Error::setExceptionHandler($config['app']['exception_handle']);
        }

        // 下面这些配置针对的都是不同模块不一样
        Db::init($config['database']);
        $this->middleware->setConfig($config['middleware']); // middleware 的配置
        $this->route->setConfig($config['app']); // route 的配置 ,app 竟然是给route 的配置
        $this->request->init($config['app']); // request 也用的 app 配置
        $this->cookie->init($config['cookie']);
        $this->view->init($config['template']);
        $this->log->init($config['log']);
        $this->session->setConfig($config['session']);
        $this->debug->setConfig($config['trace']);
        $this->cache->init($config['cache'], true);

        // 加载当前模块语言包，对于错误提示进行修改
        $this->lang->load($this->appPath . $module . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $this->request->langset() . '.php');

        // 模块请求缓存检查
        $this->checkRequestCache(
            $config['app']['request_cache'],
            $config['app']['request_cache_expire'],
            $config['app']['request_cache_except']
        );
    }

    /**
     * 执行应用程序
     * @access public
     * @return Response
     * @throws Exception
     */
    public function run()
    {
        try {
            // 初始化应用
            $this->initialize();

            // 监听app_init
            $this->hook->listen('app_init'); // hook 狗仔 app_init 绑定的执行



            if ($this->bindModule) {
                // 模块/控制器绑定
                $this->route->bind($this->bindModule);
            } elseif ($this->config('app.auto_bind_module')) {
                // 入口自动绑定
                $name = pathinfo($this->request->baseFile(), PATHINFO_FILENAME);
                if ($name && 'index' != $name && is_dir($this->appPath . $name)) {
                    $this->route->bind($name);
                }
            }

            // 监听app_dispatch
            $this->hook->listen('app_dispatch'); // app dispatch 绑定执行

            $dispatch = $this->dispatch;



            if (empty($dispatch)) {
                // 路由检测
                $dispatch = $this->routeCheck()->init();
            }

            // 记录当前调度信息
            $this->request->dispatch($dispatch);

            // 记录路由和请求信息
            if ($this->appDebug) {
                $this->log('[ ROUTE ] ' . var_export($this->request->routeInfo(), true));
                $this->log('[ HEADER ] ' . var_export($this->request->header(), true));
                $this->log('[ PARAM ] ' . var_export($this->request->param(), true));
            }

            // 监听app_begin
            $this->hook->listen('app_begin'); // 钩子 app_begin 执行

            // 请求缓存检查
            $this->checkRequestCache(
                $this->config('request_cache'),
                $this->config('request_cache_expire'),
                $this->config('request_cache_except')
            );

            $data = null;
        } catch (HttpResponseException $exception) {
            $dispatch = null;
            $data     = $exception->getResponse();
        }

       // var_dump($this->middleware->all());exit;

        $this->middleware->add(function (Request $request, $next) use ($dispatch, $data) {
            // var_dump('ppp');
           // var_dump($data);
            // 没错误，直接走的run 方法
            // 这个地方因为 next 用不到了，所以
            return is_null($data) ? $dispatch->run() : $data;
        });


        $response = $this->middleware->dispatch($this->request); // 中间件的执行，包括控制器的执行都在这里面

        // 监听app_end
        $this->hook->listen('app_end', $response); // 钩子 app end 执行

        return $response;
    }

    protected function getRouteCacheKey()
    {
        if ($this->config->get('route_check_cache_key')) {
            $closure  = $this->config->get('route_check_cache_key');
            $routeKey = $closure($this->request);
        } else {
            $routeKey = md5($this->request->baseUrl(true) . ':' . $this->request->method());
        }

        return $routeKey;
    }

    // 切换不同类型的错误提示
    protected function loadLangPack()
    {
        // 读取默认语言

        $this->lang->range($this->config('app.default_lang'));

        if ($this->config('app.lang_switch_on')) { // 默认是关闭多语言
            // 开启多语言机制 检测当前语言
            $this->lang->detect();
        }

        $this->request->setLangset($this->lang->range()); // 设定request 中lang 是 zh-cn


        // 加载系统语言包
        // 就是把错误提示修改成 zh-cn
        $this->lang->load([
            $this->thinkPath . 'lang' . DIRECTORY_SEPARATOR . $this->request->langset() . '.php',
            $this->appPath . 'lang' . DIRECTORY_SEPARATOR . $this->request->langset() . '.php',
        ]);

    }

    /**
     * 设置当前地址的请求缓存
     * @access public
     * @param  string $key 缓存标识，支持变量规则 ，例如 item/:name/:id
     * @param  mixed $expire 缓存有效期
     * @param  array $except 缓存排除
     * @param  string $tag 缓存标签
     * @return void
     */
    public function checkRequestCache($key, $expire = null, $except = [], $tag = null)
    {
        $cache = $this->request->cache($key, $expire, $except, $tag);

        if ($cache) {
            $this->setResponseCache($cache);
        }
    }

    public function setResponseCache($cache)
    {
        list($key, $expire, $tag) = $cache;

        if (strtotime($this->request->server('HTTP_IF_MODIFIED_SINCE')) + $expire > $this->request->server('REQUEST_TIME')) {
            // 读取缓存
            $response = Response::create()->code(304);
            throw new HttpResponseException($response);
        } elseif ($this->cache->has($key)) {
            list($content, $header) = $this->cache->get($key);

            $response = Response::create($content)->header($header);
            throw new HttpResponseException($response);
        }
    }

    /**
     * 设置当前请求的调度信息
     * @access public
     * @param  Dispatch $dispatch 调度信息
     * @return $this
     */
    public function dispatch(Dispatch $dispatch)
    {
        $this->dispatch = $dispatch;
        return $this;
    }

    /**
     * 记录调试信息
     * @access public
     * @param  mixed $msg 调试信息
     * @param  string $type 信息类型
     * @return void
     */
    public function log($msg, $type = 'info')
    {
        $this->appDebug && $this->log->record($msg, $type);
    }

    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string $name 配置参数名（支持二级配置 .号分割）
     * @return mixed
     */
    public function config($name = '')
    {
        // 这个config 应该就是对应该config 文件夹下的内容
        return $this->config->get($name);
    }

    public function setConfig($key, $value)
    {
        $this->config->set($key, $value);
    }

    /**
     * 路由初始化 导入路由定义规则
     * @access public
     * @return void
     */
    public function routeInit()
    {
        // 路由检测
       try {
           $files = scandir($this->routePath);
       } catch (\Exception $e) {
           var_dump($e->getMessage());exit;
       }
        foreach ($files as $file) {
            if (strpos($file, '.php')) {
                $filename = $this->routePath . $file;
                // 导入路由配置
                $rules = include $filename; // 默認的返回都是空数组，所以这块 rules 都是 []
                if (is_array($rules)) {
                    $this->route->import($rules);
                }
            }
        }

        if ($this->route->config('route_annotation')) {
            // 自动生成路由定义
            if ($this->appDebug) {
                $suffix = $this->route->config('controller_suffix') || $this->route->config('class_suffix');
                $this->build->buildRoute($suffix);
            }

            $filename = $this->runtimePath . 'build_route.php';

            if (is_file($filename)) {
                include $filename;
            }
        }
    }

    /**
     * URL路由检测（根据PATH_INFO)
     * @access public
     * @return Dispatch
     */
    public function routeCheck()
    {
        // 检测路由缓存

        if (!$this->appDebug && $this->config->get('route_check_cache')) {
            $routeKey = $this->getRouteCacheKey();
            $option   = $this->config->get('route_cache_option');

            if ($option && $this->cache->connect($option)->has($routeKey)) {
                return $this->cache->connect($option)->get($routeKey);
            } elseif ($this->cache->has($routeKey)) {
                return $this->cache->get($routeKey);
            }
        }

        // 获取应用调度信息
        $path = $this->request->path();

        // 是否强制路由模式
        $must = !is_null($this->routeMust) ? $this->routeMust : $this->route->config('url_route_must');

        // 路由检测 返回一个Dispatch对象
        $dispatch = $this->route->check($path, $must);

        if (!empty($routeKey)) {
            try {
                if ($option) {
                    $this->cache->connect($option)->tag('route_cache')->set($routeKey, $dispatch);
                } else {
                    $this->cache->tag('route_cache')->set($routeKey, $dispatch);
                }
            } catch (\Exception $e) {
                // 存在闭包的时候缓存无效
            }
        }

        return $dispatch;
    }

    /**
     * 设置应用的路由检测机制
     * @access public
     * @param  bool $must 是否强制检测路由
     * @return $this
     */
    public function routeMust($must = false)
    {
        $this->routeMust = $must;
        return $this;
    }

    /**
     * 解析模块和类名
     * @access protected
     * @param  string $name 资源地址
     * @param  string $layer 验证层名称
     * @param  bool $appendSuffix 是否添加类名后缀
     * @return array
     */
    protected function parseModuleAndClass($name, $layer, $appendSuffix)
    {
        if (false !== strpos($name, '\\')) {
            $class  = $name;
            $module = $this->request->module();
        } else {
            if (strpos($name, '/')) {
                list($module, $name) = explode('/', $name, 2);
            } else {
                $module = $this->request->module();
            }

            $class = $this->parseClass($module, $layer, $name, $appendSuffix);
        }

        return [$module, $class];
    }

    /**
     * 实例化应用类库
     * @access public
     * @param  string $name 类名称
     * @param  string $layer 业务层名称
     * @param  bool $appendSuffix 是否添加类名后缀
     * @param  string $common 公共模块名
     * @return object
     * @throws ClassNotFoundException
     */
    public function create($name, $layer, $appendSuffix = false, $common = 'common')
    {
        $guid = $name . $layer;

        if ($this->__isset($guid)) {
            return $this->__get($guid);
        }

        list($module, $class) = $this->parseModuleAndClass($name, $layer, $appendSuffix);

        if (class_exists($class)) {
            $object = $this->__get($class);
        } else {
            $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);
            if (class_exists($class)) {
                $object = $this->__get($class);
            } else {
                throw new ClassNotFoundException('class not exists:' . $class, $class);
            }
        }

        $this->__set($guid, $class);

        return $object;
    }

    /**
     * 实例化（分层）模型
     * @access public
     * @param  string $name Model名称
     * @param  string $layer 业务层名称
     * @param  bool $appendSuffix 是否添加类名后缀
     * @param  string $common 公共模块名
     * @return Model
     * @throws ClassNotFoundException
     */
    public function model($name = '', $layer = 'model', $appendSuffix = false, $common = 'common')
    {
        return $this->create($name, $layer, $appendSuffix, $common);
    }

    /**
     * 实例化（分层）控制器 格式：[模块名/]控制器名
     * @access public
     * @param  string $name 资源地址
     * @param  string $layer 控制层名称
     * @param  bool $appendSuffix 是否添加类名后缀
     * @param  string $empty 空控制器名称
     * @return object
     * @throws ClassNotFoundException
     */
    public function controller($name, $layer = 'controller', $appendSuffix = false, $empty = '')
    {
        list($module, $class) = $this->parseModuleAndClass($name, $layer, $appendSuffix);

        if (class_exists($class)) {
            return $this->__get($class);
        } elseif ($empty && class_exists($emptyClass = $this->parseClass($module, $layer, $empty, $appendSuffix))) {
            return $this->__get($emptyClass);
        }

        throw new ClassNotFoundException('class not exists:' . $class, $class);
    }

    /**
     * 实例化验证类 格式：[模块名/]验证器名
     * @access public
     * @param  string $name 资源地址
     * @param  string $layer 验证层名称
     * @param  bool $appendSuffix 是否添加类名后缀
     * @param  string $common 公共模块名
     * @return Validate
     * @throws ClassNotFoundException
     */
    public function validate($name = '', $layer = 'validate', $appendSuffix = false, $common = 'common')
    {
        $name = $name ?: $this->config('default_validate');

        if (empty($name)) {
            return new Validate;
        }

        return $this->create($name, $layer, $appendSuffix, $common);
    }

    /**
     * 数据库初始化
     * @access public
     * @param  mixed $config 数据库配置
     * @param  bool|string $name 连接标识 true 强制重新连接
     * @return \think\db\Query
     */
    public function db($config = [], $name = false)
    {
        return Db::connect($config, $name);
    }

    /**
     * 远程调用模块的操作方法 参数格式 [模块/控制器/]操作
     * @access public
     * @param  string $url 调用地址
     * @param  string|array $vars 调用参数 支持字符串和数组
     * @param  string $layer 要调用的控制层名称
     * @param  bool $appendSuffix 是否添加类名后缀
     * @return mixed
     * @throws ClassNotFoundException
     */
    public function action($url, $vars = [], $layer = 'controller', $appendSuffix = false)
    {
        $info   = pathinfo($url);
        $action = $info['basename'];
        $module = '.' != $info['dirname'] ? $info['dirname'] : $this->request->controller();
        $class  = $this->controller($module, $layer, $appendSuffix);

        if (is_scalar($vars)) {
            if (strpos($vars, '=')) {
                parse_str($vars, $vars);
            } else {
                $vars = [$vars];
            }
        }

        return $this->invokeMethod([$class, $action . $this->config('action_suffix')], $vars);
    }

    /**
     * 解析应用类的类名
     * @access public
     * @param  string $module 模块名
     * @param  string $layer 层名 controller model ...
     * @param  string $name 类名
     * @param  bool $appendSuffix
     * @return string
     */
    public function parseClass($module, $layer, $name, $appendSuffix = false)
    {
        $name  = str_replace(['/', '.'], '\\', $name);
        $array = explode('\\', $name);
        $class = Loader::parseName(array_pop($array), 1) . ($this->suffix || $appendSuffix ? ucfirst($layer) : '');
        $path  = $array ? implode('\\', $array) . '\\' : '';

        return $this->namespace . '\\' . ($module ? $module . '\\' : '') . $layer . '\\' . $path . $class;
    }

    /**
     * 获取框架版本
     * @access public
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * 是否为调试模式
     * @access public
     * @return bool
     */
    public function isDebug()
    {
        return $this->appDebug;
    }

    /**
     * 获取模块路径
     * @access public
     * @return string
     */
    public function getModulePath()
    {
        return $this->modulePath;
    }

    /**
     * 设置模块路径
     * @access public
     * @param  string $path 路径
     * @return void
     */
    public function setModulePath($path)
    {
        $this->modulePath = $path;
        $this->env->set('module_path', $path);
    }

    /**
     * 获取应用根目录
     * @access public
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * 获取应用类库目录
     * @access public
     * @return string
     */
    public function getAppPath()
    {

        if (is_null($this->appPath)) {
            $this->appPath = Loader::getRootPath() . 'application' . DIRECTORY_SEPARATOR;
        }

        return $this->appPath;
    }

    /**
     * 获取应用运行时目录
     * @access public
     * @return string
     */
    public function getRuntimePath()
    {
        return $this->runtimePath;
    }

    /**
     * 获取核心框架目录
     * @access public
     * @return string
     */
    public function getThinkPath()
    {
        return $this->thinkPath;
    }

    /**
     * 获取路由目录
     * @access public
     * @return string
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    /**
     * 获取应用配置目录
     * @access public
     * @return string
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

    /**
     * 获取配置后缀
     * @access public
     * @return string
     */
    public function getConfigExt()
    {
        return $this->configExt;
    }

    /**
     * 获取应用类库命名空间
     * @access public
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * 设置应用类库命名空间
     * @access public
     * @param  string $namespace 命名空间名称
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * 是否启用类库后缀
     * @access public
     * @return bool
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * 获取应用开启时间
     * @access public
     * @return float
     */
    public function getBeginTime()
    {
        return $this->beginTime;
    }

    /**
     * 获取应用初始内存占用
     * @access public
     * @return integer
     */
    public function getBeginMem()
    {
        return $this->beginMem;
    }

}
