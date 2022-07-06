<?php
require_once("../vendor/autoload.php");


use Src\Getdb;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('task_register.php');
$log->pushHandler(new StreamHandler(__DIR__ . '/../log/todo_list.log'));

$task = $_POST['task'];
$date_time = "{$_POST['date']} {$_POST['time']}";

try {
    $pdo = Getdb::db_connect();
    $_POST['date'] === '' ? Getdb::not_include_time_insert($pdo, $task) : Getdb::include_time_insert($pdo,$task,$date_time);

    echo json_encode([
        'aaa' => $task,
        'bbb' => $date_time
    ]);
} catch (PDOException $e) {
    $log->error($e->getMessage());
}