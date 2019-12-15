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

namespace think\route\dispatch;

use think\route\Dispatch;

class Callback extends Dispatch
{
    public function exec()
    {
        /*// 执行回调方法
        $cy = new \Cy();
        try {
           // $this->request->param['oo'] = $cy;
            //var_dump($this->request->param());exit;
           // $this->request->setP('OO', $cy);
        }catch (\Exception $e) {
            var_dump($e->getMessage());
        }*/
      //  var_dump($this->request);exit;
      //  $this->request->oo = 'cy';  这样设置传入参数 oo 不在最前面，这样设置的参数 oo 会集中放在request 的 param 属性中管理
        // 对于参数的调用，依据参数名称， 并不依赖顺序

        $vars = array_merge( $this->request->param(), $this->param);

       // var_dump(111111111,$vars);

        return $this->app->invoke($this->dispatch, $vars);
    }

}
