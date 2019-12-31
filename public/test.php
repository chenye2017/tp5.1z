<?php
var_dump(str_replace(' ',  '_', date('Y-m-d H:i:s')));

exit;

$test = [1,2,3,4];

var_dump(array_chunk($test, 2));
exit;

$arr = range(2,30, 1);

var_dump(count($arr));exit;


var_dump(round(5/2));
exit;

var_dump(http_build_query(['name' => 1,'sex' => 2]));exit;

var_dump(unserialize('a:3:{s:8:"required";b:1;s:4:"path";s:7:"appZone";s:8:"max_size";s:7:"2000000";}'));exit;

$id = 'cy';
$name = 'cy1';
var_dump(compact('id', 'name'));
exit;

var_dump(array_key_exists('x1', ['x'=>1]));

exit;

var_dump(filter_var_array([1,2.1], FILTER_VALIDATE_INT) );exit;

echo 2;


error_reporting(-1);


/*header("Access-Control-Allow-Origin: http://tp51t.test");
header("Access-Control-Allow-Methods", "POST,GET");
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers : X-Requested-With');*/

header('Content-type: application/json');
var_dump(['sss']);

fastcgi_finish_request();

exit;


/*header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods", "POST,GET");
header('Access-Control-Allow-Credentials:true');  //允许访问Cookie
header('Access-Control-Allow-Headers : X-Requested-With'); //设置Headers, 允许jq 的 ajax*/

var_dump(11, $_COOKIE);

setcookie('test', 'cy', time() + 60 * 60 * 24);


return json_encode(['test' => 'cy'], true);