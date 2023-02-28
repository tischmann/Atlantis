import Atlantis from '/js/atlantis.js'

const atlantis = new Atlantis()

document
    .querySelectorAll(`form.atlantis-rating[data-id][data-rating]`)
    .forEach((form) => {
        const article_id = form.dataset?.id
        const rating = atlantis.toInt(form.dataset?.rating)
        const uniqueid = atlantis.uniqueid()

        const onchange = function (event) {
            const url = `/rating/${article_id}/${event.target.value}`

            atlantis.fetch(url, {
                body: {
                    uuid: atlantis.getUUID()
                },
                headers: {
                    'X-Csrf-Token': form.dataset?.csrf
                },
                success: (json) => {
                    form.dataset.csrf = json.csrf
                }
            })
        }

        for (let i = 5; i >= 1; i--) {
            const id = `atlantis-rating-${i}-${uniqueid}`

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
                    id: article_id
                },
                on: {
                    change: onchange
                }
            })

            if (rating == i) input.checked = true

            form.append(input, label)
        }
    })
