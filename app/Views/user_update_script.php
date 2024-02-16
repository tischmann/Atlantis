<script nonce="{{nonce}}">
    document.querySelectorAll('.usr-upd-btn[data-id]').forEach(button => {
        button.addEventListener('click', function() {
            const form = new FormData(document.querySelector('form'))

            fetch(`/user/${button.dataset.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(Object.fromEntries(form))
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