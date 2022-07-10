'use strict';

window.addEventListener('DOMContentLoaded',() => {

    //削除ボタンをクリックしたらモーダルウィンドウで確認
    $(document).on('click', '.update_open', e => {

        //モーダルウィンドウを閉じた後とタスク更新後の処理
        const modal_remove = () => {
            const validate_message = modal_window.querySelector('.validate_message');
            mask.classList.add('d-none');
            modal_window.classList.add('d-none');
            execute.classList.remove('btn-success')
            input_update_task.remove();
            input_update_date.remove();
            input_update_time.remove();
            if (validate_message) {
                validate_message.remove();
            }
        }

        const parent = e.currentTarget.parentNode.parentNode;
        const close = parent.querySelector('.close');
        const mask = parent.querySelector('.mask');
        const modal_window = parent.querySelector('.modal_window');
        const execute = parent.querySelector('.execute');
        const add_text_box = document.createElement('input');
        const add_date_box = document.createElement('input');
        const add_time_box = document.createElement('input');
        const date = new Date(parent.querySelector('.notification_at').textContent);
        const initial_date = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
        const initial_time = `${String(date.getHours()).padStart(2,'0')}:${String(date.getMinutes()).padStart(2,'0')}`;
        const initial_task = parent.querySelector('.card-text');
        const card_title = parent.querySelector('.card-title');

        //タスク更新用の入力欄
        add_text_box.setAttribute('type', 'text');
        add_text_box.classList.add('update_task','d-block','form-control');
        add_text_box.setAttribute('name','update_task');
        add_text_box.value = initial_task.textContent.trim();
        execute.insertAdjacentElement('beforebegin',add_text_box);

        //追加したtextbox
        const input_update_task = parent.querySelector('.update_task');

        //日付更新用の入力欄
        add_date_box.setAttribute('type','date');
        add_date_box.classList.add('update_date');
        add_date_box.setAttribute('name','update_date');
        add_date_box.value = initial_date;
        input_update_task.insertAdjacentElement('afterend',add_date_box);

        //追加した日付入力欄
        const input_update_date = parent.querySelector('.update_date');

        //時間更新用の入力欄
        add_time_box.setAttribute('type','time');
        add_time_box.classList.add('update_time','d-block');
        add_time_box.setAttribute('name','update_time');
        add_time_box.value = initial_time;
        input_update_date.insertAdjacentElement('afterend',add_time_box);

        //追加した時間入力欄
        const input_update_time = parent.querySelector('.update_time');

        //モーダルウィンドウのボタンを更新ボタンへ変更
        execute.textContent = '更新する';
        execute.classList.add('btn-success');
        document.querySelector('h4').textContent = initial_task.textContent;

        parent.querySelector('.modal_window').classList.remove('d-none');
        parent.querySelector('.mask').classList.remove('d-none');

        //閉じるボタン
        close.onclick = () => {
            modal_remove();
        };

        execute.onclick = e => {
            const formData = new FormData();
            formData.append("id",e.currentTarget.value);
            formData.append("update_task",input_update_task.value);
            formData.append("update_date",input_update_date.value);
            formData.append("update_time",input_update_time.value);

            //タスク入力欄のバリデーション
            if (!formData.get('update_task').trim()) {
                const validate_message = document.createElement('p');
                if (!modal_window.querySelector('.validate_message')) {
                    validate_message.textContent = 'タスクを入力してください';
                    validate_message.classList.add('text-danger','validate_message');
                    input_update_task.insertAdjacentElement('beforebegin',validate_message);
                }
                input_update_task.classList.add('is-invalid');
            } else {
                fetch('../public/task_update.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    console.log(input_update_date.value);
                    console.log(input_update_time.value);

                    modal_remove();
                    initial_task.textContent = res['task'];
                    parent.querySelector('.notification_at').textContent = `${input_update_date.value} ${input_update_time.value}`;

                    if (new Date(`${input_update_date.value}  ${input_update_time.value}`).getTime() <= Date.now()) {
                        card_title.classList.add('text-danger');
                    } else {
                        card_title.classList.remove('text-danger');
                    }
                })
                .catch(res => console.error(res))
            }
        };
    })
});