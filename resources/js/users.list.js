import Select from './atlantis.select.min.js'
;['role', 'status', 'order', 'direction'].forEach((field) => {
    new Select(document.querySelector(`select[name="${field}"]`), {
        onchange: (value) => {
            const url = new URL(window.location.href)
            url.searchParams.set(field, value)
            window.location.href = url.toString()
        }
    })
})
