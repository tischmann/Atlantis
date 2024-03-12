function orderCategories() {
    const children = document
        .getElementById('categories-list')
        .querySelectorAll('li')

    fetch('/categories/sort', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            categories: Array.from(children).map((el) => el.dataset.id)
        })
    })
}

$('#categories-list').sortable({
    placeholder: 'ui-state-highlight',
    update: () => {
        orderCategories()
    }
})

$('#categories-list').disableSelection()

$('[data-sort="up"]').each(function () {
    $(this).on('click', function () {
        const li = $(this).closest('li')
        const prev = li.prev()
        if (!prev) return
        li.insertBefore(prev)
        orderCategories()
    })
})

$('[data-sort="down"]').each(function () {
    $(this).on('click', function () {
        const li = $(this).closest('li')
        const next = li.next()
        if (!next) return
        li.insertAfter(next)
        orderCategories()
    })
})
