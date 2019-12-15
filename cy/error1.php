<?php

require 'error.php';

var_dump($a);

exit;


set_error_handler(function () {
    throw new \ErrorException('test');
});

set_exception_handler(function ($e) {
    var_dump(111);
    var_dump($e->getMessage());
});

set_exception_handler(function ($e) {
    var_dump(14);
});

register_shutdown_function(function () {
    var_dump('hahah');
    var_dump(1111111, error_get_last());
});

try {
    throw new \Error('wwww');
} catch (\Exception $e) {
    var_dump(111);
}

var_dump(111);
