<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=peixun;', 'root', 'wyqnkxk2012_CY');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // pdo 设置异常模式

$sql = sprintf('select * from ms_admin where username = %s', 'admin');

$sql = sprintf('insert into gm_question_record (question_id, user_id, user_answer, right_or_no, status)  values (5, 1, %s, 1, 1)', $pdo->quote('B'));



$pdo->exec($sql);

sleep(10);

var_dump($pdo->lastInsertId());exit;



$res =  $pdo->query($sql);

foreach ($res as $key => $value) {
    var_dump($value);
}

exit;

//$res1 = $pdo->query('select * from gm_question'); // exec 好像不能执行query  select

$res1 = $pdo->prepare('select * from ms_admin where admin_id = ? ');

$res1 = $res1->execute([1]);

foreach ($res1 as $value) {
    var_dump($value);


}

exit;

//$sql = sprintf('insert into gm_question_record(user_id, user_answer, right_or_no, status, question_id) values (1, "%s", 0, 1, 5)', 'B');
// sprintf 不能给字符串加上双引号吗 ？？ mysql 中没有双引号的代表column

/*
try {
    $res = $pdo->exec($sql);
} catch (\Exception $e) {
    var_dump($e->getMessage());exit;
}*/

foreach ($res1 as $key => $value) {
    var_dump($value);
}

exit;