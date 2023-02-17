<script src="/js/sortable.js" nonce="{{nonce}}"></script>
<script nonce="{{nonce}}">
    window.addEventListener("load", function(event) {
        document.querySelectorAll('.sortable-categories-container').forEach((container) => {
            let csrf = `{{csrf-token}}`;

            new Sortable(container, {
                handle: '.handle',
                animation: 150,
                ghostClass: 'bg-sky-200',
                onEnd: (event) => {
                    const children = []

                    event.target.querySelectorAll('li[data-id]')
                        .forEach((el, index) => children.push(el.dataset.id))

                    const href = `/{{env=APP_LOCALE}}/admin/categories`

                    fetch(`/categories/order`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': `XMLHttpRequest`,
                            'X-Csrf-Token': csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            children
                        }),
                    }).then(response => response.json().then(data => {
                        if (!data?.status) {
                            alert(data.message)
                            window.location.href = href
                        }

                        csrf = data.csrf
                    }).catch(error => {
                        alert(error)
                        window.location.href = href
                    })).catch(error => {
                        alert(error)
                        window.location.href = href
                    })
                },
            })
        })
    }, false);
</script>