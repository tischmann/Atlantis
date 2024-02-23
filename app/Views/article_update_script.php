<script nonce="{{nonce}}">
    document.getElementById('save-article').addEventListener('click', function() {
        const form = document.getElementById('article-form')

        const formData = new FormData(form)

        fetch(`/article/<?= $article->id ?>`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '{{csrf-token}}',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(json => {
                        document.dialog({
                            title: `{{lang=error}}: ${response.status}`,
                            text: json.message,
                            onclose: () => {
                                window.location.reload()
                            }
                        })
                    })
                }

                response.json().then(json => {
                    document.dialog({
                        title: `{{lang=attention}}`,
                        text: `{{lang=article_saved}}`,
                        onclose: () => {
                            window.location.reload()
                        }
                    })
                })
            })
    })
</script>