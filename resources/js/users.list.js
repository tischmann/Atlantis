import Select from './atlantis.select.min.js'
;['role', 'status', 'order', 'direction'].forEach((field) => {
    new Select(document.getElementById(`select_field_${field}`), {
        onchange: (value) => {
            const url = new URL(window.location.href)
            url.searchParams.set(field, value)
            window.location.href = url.toString()
        }
    })
})
