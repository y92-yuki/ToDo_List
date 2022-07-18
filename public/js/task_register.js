'use strict';

window.addEventListener('DOMContentLoaded',() => {

    //現在時刻を取得
    const now_date = new Date();
    const notification_at = document.querySelectorAll('.notification_at');

    //指定している通知時間を表示して、現在時刻以降のものは赤文字にする
    for (let p of notification_at) {
        if (now_date.getTime() >= Date.parse(p.textContent)) {
            p.parentNode.classList.remove('d-none');
            p.parentNode.classList.add('text-danger');
        } else if (p.textContent) {
            p.parentNode.classList.remove('d-none');
        }
    }

    //現在時刻から1時間後が初期値
    now_date.setHours(now_date.getHours() + 1);

    const year = now_date.getFullYear();
    const month = String(now_date.getMonth() + 1).padStart(2,'0');
    const date = String(now_date.getDate()).padStart(2,'0');
    const hour = String(now_date.getHours()).padStart(2,'0');

    //時間通知の有効・無効のチェックボックス
    const apply_time_notification = document.querySelector('#apply_time_notification');

    //日付・時間のdiv要素
    const time_noticication = document.querySelector('#time_notification');

    //日付
    const input_date = document.querySelector('input[name=date]');

    //時間
    const input_time = document.querySelector('input[name=time]');

    //「通知時間を有効」にチェックを入れると時刻と空白を入れ替える
    apply_time_notification.addEventListener('change',() => {
        if (input_date.value && input_time.value) {
            input_date.value = "";
            input_time.value = "";
        } else {
            input_date.value = `${year}-${month}-${date}`;
            input_time.value = `${hour}:00`;
        }
        time_noticication.classList.toggle('d-none');
    });

    //タスクの入力フォームを取得
    const task_form = document.querySelector('#task_form');

    task_form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(task_form);
        const task = document.querySelector('input[name=task]');
        const validate_message = document.querySelector('.validate_message');

        //タスク入力欄のバリデーション
        if (!formData.get('task').trim()) {
            validate_message.classList.remove('d-none');
            task.classList.add('is-invalid');
        } else {
            fetch('../public/task_register.php',{
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(res => {
                if (res === '') {
                    return Promise.reject('error is task_register.php');
                }
                document.querySelector('.task_list').insertAdjacentHTML('beforeend',res);
            })
            .then(() => {
                task.classList.remove('is-invalid');
                validate_message.classList.add('d-none');
                task.value = "";
                input_date.value = "";
                input_time.value = "";
                apply_time_notification.checked = false;
                time_noticication.classList.add('d-none');
            })
            .catch(e => {
                console.error(e);
            })
        }
    })
});