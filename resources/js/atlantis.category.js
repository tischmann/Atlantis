import Select from './atlantis.select.min.js'

export default class Category {
    constructor({ form = null } = {}) {
        this.form = form

        if (this.form === null) {
            this.form = document.querySelector('[data-category]')
        }

        if (!this.form) {
            return console.error(
                'Форма с атрибутом [data-category] не найдена!'
            )
        }

        this.id = parseInt(this.form.dataset.category)
        ;['locale', 'parent_id'].forEach((field) => {
            new Select(document.querySelector(`select[name="${field}"]`))
        })

        document
            .getElementById('save-category')
            ?.addEventListener('click', () => {
                this.save()
            })

        document
            .getElementById('add-category')
            ?.addEventListener('click', () => {
                this.add()
            })

        document
            .getElementById('delete-category')
            ?.addEventListener('click', (event) => {
                this.delete({ message: event.target.dataset.message })
            })
    }

    save() {
        this.fetch({
            url: `/category/${this.id}`,
            method: 'PUT',
            body: JSON.stringify(Object.fromEntries(new FormData(this.form))),
            onclose: () => window.location.reload()
        })
    }

    add() {
        this.fetch({
            url: `/category`,
            method: 'POST',
            body: JSON.stringify(Object.fromEntries(new FormData(this.form))),
            onclose: function ({ id }) {
                if (id) {
                    window.location.href = `/edit/category/${id}`
                } else {
                    window.location.reload()
                }
            }
        })
    }

    delete({
        message = 'Вы уверены, что хотите удалить категорию и все дочерние категории? Это действие нельзя отменить!',
        confirmation = true
    } = {}) {
        if (confirmation) {
            if (!confirm(message)) return
        }

        this.fetch({
            url: `/category/${this.id}`,
            method: 'DELETE',
            onclose: function () {
                window.location.href = '/edit/categories'
            }
        })
    }

    fetch({ url, method, body = null, onclose = null } = {}) {
        if (onclose === null) {
            onclose = function () {
                window.location.reload()
            }
        }

        fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json'
            },
            body
        }).then((response) => {
            response.json().then((json) => {
                alert(json.message)
                onclose(json)
            })
        })
    }
}
