import Atlantis from '/js/atlantis.js'

const atlantis = new Atlantis()

atlantis.on(window, 'load', () => {
    const textareaElement = document.querySelector(`[data-tinymce-textarea]`)

    let token = textareaElement.dataset.token

    const useDarkMode = window.matchMedia(
        '(prefers-color-scheme: dark)'
    ).matches

    tinymce.init({
        language: textareaElement.dataset.locale,
        target: textareaElement,
        plugins:
            'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
        editimage_cors_hosts: ['picsum.photos'],
        menubar: 'file edit view insert format tools table help',
        toolbar:
            'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
        height: 600,
        quickbars_selection_toolbar:
            'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
        noneditable_class: 'mceNonEditable',
        toolbar_mode: 'floating',
        contextmenu: 'link image table',
        image_caption: true,
        skin: useDarkMode ? 'oxide-dark' : 'oxide',
        content_css: useDarkMode ? 'dark' : 'default',
        image_advtab: true,
        images_upload_handler: (blobInfo, progress) =>
            new Promise((resolve, reject) => {
                const formData = new FormData()

                formData.append('file', blobInfo.blob(), blobInfo.filename())

                const xhr = new XMLHttpRequest()

                xhr.withCredentials = true

                xhr.open(
                    'POST',
                    `/upload/article/image/${textareaElement.dataset.id}`
                )

                xhr.setRequestHeader('Accept', 'application/json')

                xhr.setRequestHeader('X-Csrf-Token', token)

                xhr.upload.onprogress = (e) => {
                    progress((e.loaded / e.total) * 100)
                }

                xhr.onload = () => {
                    if (xhr.status === 403) {
                        reject({
                            message: 'HTTP Error: ' + xhr.status,
                            remove: true
                        })
                        return
                    }

                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status)
                        return
                    }

                    console.log(xhr.responseText)

                    const json = JSON.parse(xhr.responseText)

                    if (!json || typeof json.thumb_location != 'string') {
                        reject('Invalid JSON: ' + xhr.responseText)
                        return
                    }

                    token = json.token

                    resolve(json.thumb_location)
                }

                xhr.onerror = () => {
                    reject(
                        'Image upload failed due to a XHR Transport error. Code: ' +
                            xhr.status
                    )
                }

                xhr.send(formData)
            })
    })
})
