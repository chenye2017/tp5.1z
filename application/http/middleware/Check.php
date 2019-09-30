<?php

namespace app\http\middleware;

class Check
{
    public function handle($request, \Closure $next, $pa1 = 1, $pa2 = 2) // 难道只能装一个参数吗
    {
        var_dump(12);
       // var_dump($pa1,'|', $pa2);exit;
       // $request->name = 'cy';
        //var_dump(11);exit;
      //  var_dump(response(['name' => 'cy'], 200, [], 'json'));


       // exit;
       // return response(['name' => 'cy'], 200, [], 'json'); 中间件只要返回 response对象就好了，中间件之所有能接着往下执行，都是因为这个 $next(), 这是一个closure，
        // 因为最后一个官方的closure 才会生成response 对象，所以之前对response 设置都没有擢用

     //   var_dump($next($request));exit;
        return $next($request); // response 对象
    }
}
