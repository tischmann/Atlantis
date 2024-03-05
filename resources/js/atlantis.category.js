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
            new Select(document.getElementById(`select_field_${field}`))
        })
    }
}
