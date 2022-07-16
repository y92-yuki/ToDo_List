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

    //時間指定有りのタスクを登録
    protected function includeTimeStore($user_id, $task, $date, $time) {
        try {
            $stmt = $this->connect->prepare("INSERT INTO tasks (user_id, task, notification_at) VALUES (:user_id, :task,:date_time)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':task' => $task,
                ':date_time' => "{$date} {$time}",
            ]);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }

    //時間指定無しのタスクを登録
    protected function notIncludeTimeStore($user_id, $task) {
        try {
            $stmt = $this->connect->prepare("INSERT INTO tasks (user_id, task) VALUES (:user_id, :task)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':task' => $task,
            ]);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }
    /*
        登録済みのタスクを更新・削除するためのidを取得
        ※ログイン機能実装時に修正が必要
    */
    public function getNewTaskId($user_id) {
        try {
            $stmt = $this->connect->prepare("SELECT id FROM tasks where user_id = :user_id ORDER BY id DESC LIMIT 1");
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
            return null;
        }
    }

    //タスクの新規登録後にJavaScriptで差し込むためのテンプレート
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
                    <button type="button" class="btn btn-sm btn-success update_open m-1">変更</button>
                </div>
                <div class="mask d-none"></div>
                <div class="modal_window d-none">
                    <h4></h4>
                    <button type="button" value="{$id}" class="btn m-3 execute"></button>
                    <button type="button" class="btn btn-dark close">閉じる</button> 
                </div>
            </div>
        </div>
        EOD;

        echo $new_task;
    }

    //タスクを新規登録して指定時間に応じて色を変える
    public function storeGetNewTask($user_id, $task, $date, $time) {
        $text = '';
        $color = '';

        $cb = new Carbon($date . $time);

        if (empty($date)) {
            $this->notIncludeTimeStore($user_id, $task);
        } else {
            if ($cb->isPast()) {
                $color = 'text-danger';
            }
            $this->includeTimeStore($user_id, $task, $date, $time);
            $text = '時間指定:';
        }

        $id = $this->getNewTaskId($user_id);

        $this->newTask($task, $text, $date, $time, $color, $id['id']);
    }

    //登録済みのタスクを削除
    public function deleteTask($id) {
        try {
            $stmt = $this->connect->prepare("DELETE FROM tasks WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }

    //タスクを更新
    public function updateTask($id, $update_task, $update_date, $update_time) {
        if (empty($update_date)) {
            $this->notIncludeTimeUpdate($id, $update_task);
        } else {
            $this->includeTimeUpdate($id, $update_task, $update_date, $update_time);
        }
    }

    //時間指定ありのタスク更新
    protected function includeTimeUpdate($id, $update_task, $update_date, $update_time) {
        try {
            $stmt = $this->connect->prepare("UPDATE tasks SET task = :update_task,notification_at = :notification_at  WHERE id = :id");
            $stmt->execute([
                ':update_task' => $update_task,
                ':notification_at' => "{$update_date} {$update_time}",
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }

    //時間指定無しのタスク更新
    protected function notIncludeTimeUpdate($id, $update_task) {
        try {
            $stmt = $this->connect->prepare("UPDATE tasks SET task = :update_task WHERE id = :id");
            $stmt->execute([
                ':update_task' => $update_task,
                ':id' => $id,
            ]);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }
}