<?php

namespace app\http\middleware;

class Check
{
    public function handle($request, \Closure $next, $pa1 = 1, $pa2 = 2) // 难道只能装一个参数吗
    {
       // var_dump($pa1,'|', $pa2);exit;
       // $request->name = 'cy';
        return $next($request);
    }
}
