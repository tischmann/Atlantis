<ul class="grid grid-cols-1 gap-2" data-categories>
    <?php

    use App\Models\{Category};

    use Tischmann\Atlantis\{Template};

    foreach ($categories as $category) {
        assert($category instanceof Category);

        Template::echo(
            template: 'category_list_item',
            args: [
                'category' => $category
            ]
        );
    }

    ?>
</ul>
<script nonce="{{nonce}}">
    $('[data-categories]').each(function() {
        $(this).sortable({
            placeholder: 'ui-state-highlight',
            handle: '.handle',
            update: function(event, ui) {
                fetch('/categories/sort', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        categories: Array.from(
                            ui.item.closest('ul').children('li')
                        ).map((el) => el.dataset.id)
                    })
                })
            }
        })

        $(this).disableSelection()
    })
</script>