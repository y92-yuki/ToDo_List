'use strict';

window.addEventListener('DOMContentLoaded',() => {

    const now_date = new Date();
    // 現在時刻が18時以降の場合は翌日が初期値
    if (now_date.getHours() > 17 && now_date.getHours() < 23) {
        now_date.setDate(now_date.getDate() + 1);
    }

    //現在時刻から1時間後が初期値
    now_date.setHours(now_date.getHours() + 1);

    const year = now_date.getFullYear();
    const month = String(now_date.getMonth() + 1).padStart(2,'0');
    const date = String(now_date.getDate()).padStart(2,'0');
    const hour = String(now_date.getHours()).padStart(2,'0');

    //通知時間を有効にチェックを入れる作動
    document.querySelector('#apply_time_notification').onchange = () => {
        const input_date = document.querySelector('input[name=date]');
        const input_time = document.querySelector('input[name=time]');

        //通知時間の初期値を制御
        if (input_date.value && input_time.value) {
            input_date.value = "";
            input_time.value = "";
        } else {
            input_date.value = `${year}-${month}-${date}`;
            input_time.value = `${hour}:00`;
        }

        document.querySelector('#time_notification').classList.toggle('d-none');
    }

    const task_form = document.querySelector('#task_form');

    task_form.onsubmit = e => {
        e.preventDefault();
        const formData = new FormData(task_form);
        const task = document.querySelector('input[name=task]');
        const validate_message = document.querySelector('.validate_message');

        for (let item of formData) {
            console.log(item);
        }

        //タスク入力欄のバリデーション
        if (!formData.get('task').trim() && validate_message.classList.contains('d-none')) {
            validate_message.classList.remove('d-none');
            task.classList.add('is-invalid');
        } else {
            fetch('../public/task_register.php',{
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                console.log(res);
                task.classList.remove('is-invalid');
                validate_message.classList.add('d-none');
                task.value = "";
            })
            .catch(e => {
                console.error(e);
            })
        }
    }
});