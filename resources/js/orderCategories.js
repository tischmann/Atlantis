import Atlantis, { Sortable } from '/js/atlantis.js'

const atlantis = new Atlantis()

atlantis.on(window, 'load', () => {
    document
        .querySelectorAll('[data-atlantis-categories]')
        .forEach((container) => {
            new Sortable(container, {
                ondragend: () => {
                    const children = []

                    container
                        .querySelectorAll('li[data-id]')
                        .forEach((el, index) => children.push(el.dataset.id))

                    atlantis.fetch(`/categories/order`, {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Csrf-Token': container.dataset.token
                        },
                        body: { children },
                        success: (json) => {
                            container.dataset.token = json.token
                        }
                    })
                }
            })
        })
})
