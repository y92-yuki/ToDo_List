<?php
    require_once('../vendor/autoload.php');

    use Carbon\Carbon;
    Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->load();


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>ToDoリスト</title>
    <link rel="stylesheet" href="../css/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/index_stylesheet.css">
</head>
<body>
    <div class="container">
        <form action="#" method="POST" id="task_form" class="mt-10">
            <div class="form-group d-inline-block col-md-6">
                <p class="h3">タスク</p>
                <p class="validate_message d-none text-danger">予定を入力してください</p>
                <input type="text" name="task" class="form-control">
            </div>
            <div class="form-group mt-3">
                <p class="h3 d-inline">時間通知を有効</p>
                <input type="checkbox" id="apply_time_notification">
            </div>
            <div class="form-group d-none" id="time_notification">
                <input type="date" name="date" value="">
                <input type="time" name="time" value="">
            </div>
            <!-- 
                CSRF対策用。後で実装する
                <input type="hidden" name="token" value="">
            -->
            <button type="submit" class="btn btn-primary my-3">登録</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="../css/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="js/task_register.js"></script>
</body>
</html>