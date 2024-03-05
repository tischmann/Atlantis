import Sortable from './atlantis.sortable.min.js'

const categoriesSotrable = new Sortable(
    document.getElementById('categories-list'),
    {
        ondragend: () => {
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
    }
)

document.querySelectorAll('[data-sort="up"]').forEach((el) => {
    el.addEventListener('click', function () {
        const parent = this.closest('li')
        const prev = parent.previousElementSibling

        if (!prev) return

        parent.classList.remove('!border-red-600', '!border-green-600')
        prev.classList.remove('!border-red-600', '!border-green-600')
        parent.classList.add('!border-green-600')
        prev.classList.add('!border-red-600')

        setTimeout(() => {
            parent.classList.remove('!border-green-600')
            prev.classList.remove('!border-red-600')
        }, 2000)

        prev.before(parent)

        categoriesSotrable.ondragend()
    })
})

document.querySelectorAll('[data-sort="down"]').forEach((el) => {
    el.addEventListener('click', function () {
        const parent = this.closest('li')
        const next = parent.nextElementSibling

        if (!next) return

        parent.classList.remove('!border-red-600', '!border-green-600')
        next.classList.remove('!border-red-600', '!border-green-600')
        parent.classList.add('!border-red-600')
        next.classList.add('!border-green-600')

        setTimeout(() => {
            parent.classList.remove('!border-red-600')
            next.classList.remove('!border-green-600')
        }, 2000)

        next.after(parent)
        categoriesSotrable.ondragend()
    })
})
