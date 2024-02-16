<script nonce="{{nonce}}">
    (function() {
        let token = document.querySelector('meta[name="csrf-token"]')
            .getAttribute('content')

        function updateUser(id) {
            fetch(`/user/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': token
                },
                body: JSON.stringify(Object.fromEntries(
                    new FormData(document.querySelector('form'))
                ))
            }).then(response => {
                response.clone().json().then(json => {
                    token = json?.token
                    if (!json?.ok) return dialog(json)
                    window.location.href = json?.redirect || `/`
                }).catch(error => {
                    response.text().then(text => {
                        dialog({
                            title: `{{lang=error}}`,
                            text
                        })
                    })
                })
            })
        }

        document.querySelectorAll('.usr-upd-btn[data-id]').forEach(button => {
            button.addEventListener('click', () => {
                updateUser(button.dataset.id)
            })
        })
    })()
</script>