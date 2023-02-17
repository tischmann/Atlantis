<ul class="sortable-container list-none flex flex-wrap gap-4">
    <?php

    use App\Models\{Category};

    use Tischmann\Atlantis\{Template};

    foreach ($category->children as $child) {
        assert($child instanceof Category);

        Template::echo(
            template: 'admin/category-item',
            args: [
                'category' => $child,
            ]
        );
    }
    ?>
</ul>
<script src="/js/sortable.js" nonce="{{nonce}}"></script>
<script nonce="{{nonce}}">
    document.querySelectorAll('.sortable-container').forEach((container) => {
        new Sortable(container, {
            handle: '.handle',
            animation: 150,
            ghostClass: 'bg-sky-200',
            onEnd: (event) => {
                const children = []

                event.target.querySelectorAll('li[data-id]')
                    .forEach((el, index) => children.push(el.dataset.id))

                const href = `/{{env=APP_LOCAL}}/admin/categories`

                fetch(`/categories/order`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': `XMLHttpRequest`,
                        'X-Csrf-Token': `{{csrf-token}}`,
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
</script>