<script nonce="{{nonce}}">
    document.querySelectorAll('.usr-del-btn[data-id]').forEach(button => {
        button.addEventListener('click', function() {
            if (!confirm('{{lang=confirm_delete}}')) return

            fetch(`/user/${button.dataset.id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(response => {
                if (response.ok) {
                    response.text().then(text => {
                        if (!text) return window.location.href = `/users`
                        showDialog(`{{lang=error}}`, text)
                    })
                } else {
                    response.text().then(text => {
                        showDialog(`{{lang=error}}`, text)
                    })
                }
            })
        })
    })
</script>