<?php
require_once("../vendor/autoload.php");

use Src\Getdb;

//PDOでデータベースに接続
$pdo = new Getdb();

$pdo->updateTask($_POST['id'], $_POST['update_task'], $_POST['update_date'], $_POST['update_time']);
echo json_encode($_POST);