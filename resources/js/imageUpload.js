import Atlantis from '/js/atlantis.js'

const atlantis = new Atlantis()

document
    .querySelectorAll('img[data-atlantis-image-load]')
    .forEach((imgElement) => {
        const fileElement = imgElement.parentElement.querySelector(
            'input[type="file"][data-atlantis-image-load]'
        )

        const inputElement = imgElement.parentElement.querySelector(
            'input[type="hidden"][data-atlantis-image-load]'
        )

        const deleteElement = imgElement.parentElement.querySelector(
            'button[data-atlantis-image-load]'
        )

        let token = imgElement.dataset.token

        atlantis.on(fileElement, 'change', (event) => {
            const file = event.target.files[0]

            const width = imgElement.getAttribute('width')

            const height = imgElement.getAttribute('height')

            if (!file || !width || !height) return

            const formData = new FormData()

            formData.append('width', width)

            formData.append('height', height)

            formData.append('file', file, file.name)

            atlantis.fetch(imgElement.dataset.url, {
                headers: {
                    'X-Csrf-Token': token
                },
                body: formData,
                success: (json) => {
                    inputElement.value = json.image
                    imgElement.src = json.location
                    token = json.token
                },
                failure: (message) => {
                    atlantis
                        .dialog({
                            message,
                            onclose: () => window.location.reload()
                        })
                        .show()
                }
            })
        })

        atlantis.on(imgElement, 'click', () => {
            fileElement.click()
        })

        atlantis.on(deleteElement, 'click', () => {
            atlantis.attr(imgElement, {
                src:
                    imgElement.dataset?.placeholder || '/images/placeholder.svg'
            })
            inputElement.value = ''
        })
    })
