<script nonce="{{nonce}}">
    document.querySelectorAll('.usr-add-btn').forEach(el => {
        el.addEventListener('click', function() {
            fetch(`/user`, {
                method: 'POST',
                headers: {
                    'Cross-Origin-Resource-Policy': 'same-origin',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(Object.fromEntries(
                    new FormData(document.querySelector('form'))
                ))
            }).then(response => {
                response.clone().json().then(json => {
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
        })
    })
</script>