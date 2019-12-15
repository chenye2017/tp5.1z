<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=peixun;', 'root', 'wyqnkxk2012_CY');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // pdo 设置异常模式

// $sql = sprintf('select * from ms_admin where username = %s', $pdo->quote('admin'));

//$sql = sprintf('insert into gm_question_record (question_id, user_id, user_answer, right_or_no, status)  values (5, 1, %s, 1, 1)', $pdo->quote('B'));



$pdoStatement = $pdo->prepare('select * from ms_admin_role');

$pdoStatement->execute();

$res =  $pdo->query('select FOUND_ROWS()');
var_dump($res->fetchAll());exit;


//var_dump($pdoStatement->fetchAll(PDO::FETCH_COLUMN, 6));

var_dump($pdoStatement->getColumnMeta(6));exit;

var_dump($pdoStatement->fetchColumn(6));exit;



exit;

var_dump($pdoStatement->debugDumpParams(), 'end');exit;

 $pdoStatement->execute(['admin']);

 var_dump($pdoStatement->debugDumpParams());exit;

 var_dump($pdoStatement->columnCount());exit;

while ($row = $pdoStatement->fetch(PDO::FETCH_ASSOC)) {
    var_dump($row);
}
exit;

//$pdo->exec($sql);
$res = $pdo->query($sql);

foreach ($res as $k => $v) {
    var_dump($v);
}


exit;


var_dump($pdo->lastInsertId());exit;