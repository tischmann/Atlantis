import Dialog from './atlantis.dialog.min.js'

export default class Upload {
    constructor({
        url = '/',
        data = null,
        progress = function () {},
        success = function () {},
        failure = function () {},
        method = 'POST'
    } = {}) {
        new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest()

            xhr.open(method.toUpperCase(), url)

            xhr.upload.addEventListener('progress', (event) => {
                if (event.lengthComputable) {
                    const percent = (event.loaded / event.total) * 100
                    progress(percent)
                }
            })

            xhr.onload = () => {
                const json = JSON.parse(xhr.response)

                if (xhr.status === 200) {
                    resolve(json)
                } else {
                    new Dialog({
                        title: json.title,
                        text: json.message
                    })

                    reject(json)
                }
            }

            xhr.onerror = () => {
                reject(new Error('Network error'))
            }

            xhr.send(data)
        })
            .then(success)
            .catch(failure)
    }
}
