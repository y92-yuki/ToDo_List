<?php
namespace Src;
require_once("../vendor/autoload.php");

//.envファイルの読み込み
use Dotenv\Dotenv;
use \PDO;
use PDOException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

Dotenv::createImmutable(__DIR__ . '/../')->load();

class Getdb {

    public static function db_connect() {
        $log = new Logger('connect_PDO');
        $log->pushHandler(new StreamHandler(__DIR__ . '/../log/todo_list.log',Logger::WARNING));

        try {
            $pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'],$_ENV['DB_USER'],$_ENV['DB_PASSWORD']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            $log->error($e->getMessage());
        }
    }

    public static function include_time_insert($pdo, $task, $date_time) {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, task, notification_at) VALUES (3, :task,:date_time)");
        $stmt->execute([
            ':task' => $task,
            ':date_time' => $date_time,
        ]);
    }

    public static function not_include_time_insert($pdo, $task) {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, task) VALUES (3, :task)");
        $stmt->execute([
            ':task' => $task,
        ]);
    }
}