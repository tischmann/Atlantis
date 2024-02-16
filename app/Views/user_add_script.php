<script nonce="{{nonce}}">
    (function() {
        let token = document.querySelector('meta[name="csrf-token"]')
            .getAttribute('content')

        function addUser() {
            fetch(`/user`, {
                method: 'POST',
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

        document.querySelectorAll('.usr-add-btn').forEach(button => {
            button.addEventListener('click', addUser)
        })
    })()
</script>