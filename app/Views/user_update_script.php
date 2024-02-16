<script nonce="{{nonce}}">
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

    document.querySelectorAll('.usr-upd-btn[data-id]').forEach(button => {
        button.addEventListener('click', function() {
            fetch(`/user/${button.dataset.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': token
                },
                body: JSON.stringify(Object.fromEntries(new FormData(document.querySelector('form'))))
            }).then(response => {
                response.clone().json().then(json => {
                    token = json?.token

                    if (!json?.ok) {
                        if (json?.redirect) return window.location.href = json?.redirect
                        return dialog(json?.title || `{{lang=error}}`, json?.text)
                    }

                    window.location.href = json?.redirect || `/`
                }).catch(error => {
                    response.text().then(text => {
                        dialog(`{{lang=error}}`, text)
                    })
                })
            })
        })
    })
</script>