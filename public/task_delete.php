<?php
require_once('../vendor/autoload.php');

use Src\Getdb;

$pdo = new Getdb();
$pdo->deleteTask($_POST['id']);