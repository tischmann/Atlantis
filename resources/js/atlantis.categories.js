$('[data-categories]').each(function () {
    $(this).sortable({
        placeholder: 'ui-state-highlight',
        handle: '.handle',
        update: function (event, ui) {
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
