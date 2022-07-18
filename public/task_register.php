<?php
require_once("../vendor/autoload.php");

use Src\Getdb;

//PDOでデータベースに接続
$pdo = new Getdb();

//タスクを新規登録してJavaScriptで画面に挿入するためのテキストを返す
$result = $pdo->storeGetNewTask($_POST['user_id'], $_POST['task'], $_POST['date'], $_POST['time']);

//例外が発生していればJavaScriptにはnull(空白)を返す
if (is_null($result)) {
    echo null;
} else {
    echo $result;
}