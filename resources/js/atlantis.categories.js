$('#categories-list').sortable({
    placeholder: 'ui-state-highlight',
    update: function () {
        fetch('/categories/sort', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                categories: Array.from(
                    document
                        .getElementById('categories-list')
                        .querySelectorAll('li')
                ).map((el) => el.dataset.id)
            })
        })
    }
})

$('#categories-list').disableSelection()
