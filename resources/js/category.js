import Atlantis from '/js/atlantis.js'

const atlantis = new Atlantis()

atlantis.on(window, 'load', () => {
    const selectElement = document.getElementById('categoryLocale')

    const categoryParentElement = document.getElementById(`categoryParent`)

    let token = selectElement.dataset.token

    atlantis.on(selectElement, 'change', (event) => {
        atlantis.fetch(`/admin/fetch/parent/categories`, {
            headers: {
                'X-Csrf-Token': token,
                'Content-Type': 'application/json'
            },
            body: {
                locale: event.target.value,
                id: parseInt(selectElement.dataset.id, 10)
            },
            success: (json) => {
                token = json.token
                categoryParentElement.innerHTML = json.html
            }
        })
    })
})
