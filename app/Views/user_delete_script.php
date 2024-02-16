<script nonce="{{nonce}}">
    (function() {
        let token = document.querySelector('meta[name="csrf-token"]')
            .getAttribute('content')

        function deleteUser(id) {
            if (!confirm('{{lang=confirm_delete}}')) return

            fetch(`/user/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': token
                }
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

        document.querySelectorAll('.usr-del-btn[data-id]').forEach(button => {
            button.addEventListener('click', () => {
                deleteUser(button.dataset.id)
            })
        })
    })()
</script>