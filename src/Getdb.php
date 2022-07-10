<?php
namespace Src;
require_once("../vendor/autoload.php");

use \PDO;
use PDOException;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//.envファイルの読み込み
Dotenv::createImmutable(__DIR__ . '/../')->load();

class Getdb {
    protected $log;
    protected $connect;

    //インスタンスを生成するとPDO接続
    public function __construct() {
        $this->log = new Logger('PDO');
        $this->log->pushHandler(new StreamHandler(__DIR__ . '/../log/todo_list.log',Logger::WARNING));

        try {
            $this->connect = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'],$_ENV['DB_USER'],$_ENV['DB_PASSWORD']);
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }

    //index.phpで登録済みのタスク一覧を取得
    public function getTasks() {
        try {
            $stmt = $this->connect->prepare("SELECT id, user_id, task, SUBSTRING(notification_at, 1, 16) as notification_at FROM tasks");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
            return null;
        }
    }

    //時間指定無しのタスクを登録
    protected function includeTimeStore($task, $date, $time) {
        try {
            $stmt = $this->connect->prepare("INSERT INTO tasks (user_id, task, notification_at) VALUES (3, :task,:date_time)");
            $stmt->execute([
                ':task' => $task,
                ':date_time' => "{$date} {$time}",
            ]);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }

    //時間指定有りのタスクを登録
    protected function notIncludeTimeStore($task) {
        try {
            $stmt = $this->connect->prepare("INSERT INTO tasks (user_id, task) VALUES (3, :task)");
            $stmt->execute([
                ':task' => $task,
            ]);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }

    protected function getNewTaskId($date, $time) {
        try {
            $stmt = $this->connect->prepare("SELECT id FROM tasks where created_at = {$date} {$time}");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
            return null;
        }
    }


    protected function newTask($task, $text, $date, $time, $color, $id){
        $new_task = <<<EOD
        <div class="card my-2">
            <div class="card-body">
                <div class="card-title {$color}">{$text}<span class="notification_at">{$date} {$time}</span></div>
                <div class="card-text">
                    {$task}
                </div>
            </div>
                <div class="delete">
                    <button class="btn btn-sm btn-danger delete_open m-1">削除</button>
                </div>
                <div class="mask d-none"></div>
                <div class="modal_window d-none">
                    <h4>$task</h4>
                    <button type="button" value="{$id}" class="btn btn-danger m-3 delete_execute">削除する</button>
                    <button type="button" class="btn btn-dark close">閉じる</button> 
                </div>
            </div>
        </div>
        EOD;

        echo $new_task;
    }

    public function storeGetNewTask($task, $date, $time) {
        $text = '';
        $color = '';

        $cb = new Carbon($date . $time);

        if (empty($date)) {
            $this->notIncludeTimeStore($task);
        } else {
            if ($cb->isPast()) {
                $color = 'text-danger';
            }
            $this->includeTimeStore($task, $date, $time);
            $text = '時間指定:';
        }

        $id = $this->getNewTaskId($date, $time);

        $this->newTask($task, $text, $date, $time, $color, $id);
    }

    public function deleteTask($id) {
        try {
            $stmt = $this->connect->prepare("DELETE FROM tasks WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }
}