<?php
require_once("../vendor/autoload.php");

use Src\Getdb;

//PDOでデータベースに接続
$pdo = new Getdb();

//タスクを新規登録してtask_register.jsに返す
$pdo->storeGetNewTask($_POST['user_id'], $_POST['task'], $_POST['date'], $_POST['time']);

