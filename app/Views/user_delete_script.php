<script nonce="{{nonce}}">
    document.querySelectorAll('.usr-del-btn[data-id]').forEach(el => {
        el.addEventListener('click', function() {
            if (!confirm('{{lang=confirm_delete}}')) return

            fetch(`/user/${this.dataset.id}`, {
                method: 'DELETE',
                headers: {
                    'Cross-Origin-Resource-Policy': 'same-origin',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                response.clone().json().then(json => {
                    if (!json?.ok) return document.dialog(json)
                    if (json?.redirect) return window.location.href = json.redirect
                })
            })
        })
    })
</script>