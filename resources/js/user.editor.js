import Dialog from './atlantis.dialog.min.js'

const form = document.getElementById('user-form')

const saveButton = document.getElementById('save-user')

const deleteButton = document.getElementById('delete-user')

const addButton = document.getElementById('add-user')

function sendRequest(url, method, body = null, onclose = null) {
    if (onclose === null) {
        onclose = function () {
            window.location.reload()
        }
    }

    fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json'
        },
        body
    }).then((response) => {
        response.json().then((json) => {
            new Dialog({
                title: json.title,
                text: json.message,
                onclose: () => {
                    onclose(json)
                }
            })
        })
    })
}

saveButton?.addEventListener('click', function () {
    sendRequest(
        `/user/${form.dataset.id}`,
        'PUT',
        JSON.stringify(Object.fromEntries(new FormData(form)))
    )
})

addButton?.addEventListener('click', function () {
    sendRequest(
        `/user`,
        'POST',
        JSON.stringify(Object.fromEntries(new FormData(form))),
        function ({ id }) {
            window.location.href = `/user/${id}`
        }
    )
})

deleteButton?.addEventListener('click', function () {
    if (confirm(form.dataset.confirm)) {
        sendRequest(`/user/${form.dataset.id}`, 'DELETE', null, function () {
            window.location.href = '/users'
        })
    }
})
