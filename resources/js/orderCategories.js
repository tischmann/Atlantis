import Atlantis from '/js/atlantis.js'

const atlantis = new Atlantis()

atlantis.on(window, 'load', () => {
    document
        .querySelectorAll('[data-atlantis-categories]')
        .forEach((container) => {
            let token = container.dataset.token

            new Sortable(container, {
                handle: '.handle',
                animation: 150,
                ghostClass: 'bg-sky-200',
                onEnd: (event) => {
                    const children = []

                    event.target
                        .querySelectorAll('li[data-id]')
                        .forEach((el, index) => children.push(el.dataset.id))

                    atlantis.fetch(`/categories/order`, {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Csrf-Token': token
                        },
                        body: children,
                        success: (json) => {
                            token = json.token
                        }
                    })
                }
            })
        })
})
