<script nonce="{{nonce}}">
    (function() {
        let token = document.querySelector('meta[name="csrf-token"]')
            .getAttribute('content')

        function onClick() {
            if (!confirm('{{lang=confirm_delete}}')) return

            fetch(`/user/${this.dataset.id}`, {
                method: 'DELETE',
                headers: {
                    'Cross-Origin-Resource-Policy': 'same-origin',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': token
                }
            }).then(response => {
                response.clone().json().then(json => {
                    token = json?.token

                    if (!json?.ok) {
                        return document.dialog(json)
                    }

                    if (json?.redirect) {
                        return window.location.href = json.redirect
                    }

                    window.location.reload()
                }).catch(error => {
                    response.text().then(text => {
                        document.dialog({
                            title: `{{lang=error}}`,
                            text
                        })
                    })
                })
            })
        }

        function addListeners(el) {
            el.addEventListener('click', onClick)
        }

        document.querySelectorAll('.usr-del-btn[data-id]').forEach(addListeners)
    })()
</script>