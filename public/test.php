<?php



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