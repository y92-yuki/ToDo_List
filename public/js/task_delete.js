'use strict';

window.addEventListener('DOMContentLoaded',() => {

    //削除ボタンをクリックしたらモーダルウィンドウで確認
    $(document).on('click', '.delete_open', e => {
        const parent = e.currentTarget.parentNode.parentNode;
        const close = parent.querySelector('.close');
        const mask = parent.querySelector('.mask');
        const modal_window = parent.querySelector('.modal_window');
        const execute = parent.querySelector('.execute');
        const initial_task = parent.querySelector('.card-text');

        execute.textContent = '削除する';
        execute.classList.add('btn-danger');
        parent.querySelector('h4').textContent = initial_task.textContent;

        parent.querySelector('.modal_window').classList.remove('d-none');
        parent.querySelector('.mask').classList.remove('d-none');

        //モーダルウィンドウを閉じるボタン
        close.onclick = () => {
            mask.classList.add('d-none');
            modal_window.classList.add('d-none');
            execute.classList.remove('btn-danger');
            parent.querySelector('h4').textContent = '';
        };

        //削除決定ボタン
        execute.onclick = e => {
            const formData = new FormData();
            formData.append("id",e.currentTarget.value);

            fetch('../public/task_delete.php', {
                method: 'POST',
                body: formData
            })
            .then(() => {
                parent.classList.add('d-none');
                execute.classList.remove('btn-danger');
            })
            .catch(res => console.error(res))
        };

    })
});