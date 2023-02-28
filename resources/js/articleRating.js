import Atlantis from '/js/atlantis.js'

const atlantis = new Atlantis()

atlantis.on(window, 'load', () => {
    document
        .querySelectorAll(`[data-atlantis-article-rating]`)
        .forEach((form) => {
            const rating = atlantis.toInt(form.dataset?.rating)

            let token = form.dataset.token

            for (let i = 5; i >= 1; i--) {
                const id = `atlantis-rating-${i}-${atlantis.uniqueid()}`

                const label = atlantis.tag('label', {
                    attr: {
                        for: id
                    }
                })

                const input = atlantis.tag('input', {
                    attr: {
                        name: `rating`,
                        type: `radio`,
                        value: i,
                        id
                    },
                    data: {
                        id: form.dataset.id
                    },
                    on: {
                        change: (event) => {
                            atlantis.fetch(
                                `/rating/${form.dataset.id}/${event.target.value}`,
                                {
                                    body: {
                                        uuid: atlantis.getUUID()
                                    },
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-Csrf-Token': token
                                    },
                                    success: (json) => {
                                        token = json.token
                                    }
                                }
                            )
                        }
                    }
                })

                if (rating == i) input.checked = true

                form.append(input, label)
            }
        })
})
