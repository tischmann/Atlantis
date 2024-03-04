import Dialog from './atlantis.dialog.min.js'
import Upload from './atlantis.upload.min.js'
import Progress from './atlantis.progress.min.js'
import Sortable from './atlantis.sortable.min.js'
import Select from './atlantis.select.min.js'

export default class Article {
    svgDelete = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>`

    constructor({} = {}) {
        this.form = document.getElementById('article-form')

        this.id = parseInt(this.form.dataset.id)

        const textEditor = new Quill('#quill-editor', {
            modules: {
                toolbar: [
                    [
                        {
                            header: 1
                        },
                        {
                            header: 2
                        }
                    ],
                    [
                        {
                            align: []
                        }
                    ],
                    ['bold', 'italic', 'underline', 'strike'],
                    [
                        {
                            color: []
                        },
                        {
                            background: []
                        }
                    ],
                    [
                        {
                            list: 'ordered'
                        },
                        {
                            list: 'bullet'
                        }
                    ],
                    [
                        {
                            script: 'sub'
                        },
                        {
                            script: 'super'
                        }
                    ],
                    [
                        {
                            indent: '-1'
                        },
                        {
                            indent: '+1'
                        }
                    ],
                    ['link', 'video', 'code-block'],
                    ['clean']
                ]
            },
            theme: 'snow'
        })

        textEditor.on('text-change', () => {
            this.form
                .querySelector('input[name="text"]')
                .setAttribute('value', textEditor.root.innerHTML)
        })

        const categorySelect = new Select(
            document.getElementById(`select_field_category_id`)
        )

        new Select(document.getElementById('locale-select'), {
            onchange: (value) => {
                fetch(`/locale/categories/${value}`)
                    .then((response) => response.json())
                    .then(({ items }) => {
                        categorySelect.update(items)
                    })
            }
        })

        this.imageSizeSelect = new Select(
            document.getElementById(`select_field_image_size`),
            {
                onchange: (value) => {
                    const imageElement = this.getImageElement()

                    const width = parseInt(imageElement.getAttribute('width'))

                    let height = width

                    switch (value) {
                        case '16_9':
                            height = Math.round((width / 16) * 9)
                            break
                        case '4_3':
                            height = Math.round((width / 4) * 3)
                            break
                    }

                    imageElement.src = `/images/placeholder_${value}.svg`

                    imageElement.setAttribute('height', height)
                }
            }
        )

        this.gallerySizeSelect = new Select(
            document.getElementById(`select_field_gallery_image_size`)
        )

        document.getElementById('pre-upload-image').addEventListener(
            'click',
            function () {
                this.remove()
                document
                    .getElementById('upload-image-container')
                    .classList.remove('hidden')
            },
            {
                once: true
            }
        )

        document
            .getElementById('upload-image')
            .addEventListener('click', () => {
                this.#uploadImageHandler()
            })

        document
            .getElementById('delete-image')
            .addEventListener('click', () => {
                this.deleteImage()
            })

        new Sortable(this.getGalleryContainer(), {
            ondragend: () => {
                this.updateGalleryInput()
            }
        })

        this.getGalleryContainer()
            .querySelectorAll(`li`)
            .forEach((li) => {
                this.initGalleryItem(li)
            })

        document.getElementById('pre-upload-gallery').addEventListener(
            'click',
            function () {
                this.remove()
                document
                    .getElementById('upload-gallery-container')
                    .classList.remove('hidden')
            },
            {
                once: true
            }
        )

        document
            .getElementById('upload-gallery')
            .addEventListener('click', () => {
                this.#uploadGalleryImageHandler()
            })

        new Sortable(this.getVideosContainer(), {
            ondragend: () => {
                this.updateGalleryInput()
            }
        })

        /////////////////////////////////////
        //////////////////////////////////////
        ////////////////////////////////////

        const attachementsContainer = document.querySelector(
            '.attachements-container'
        )

        const attachementInput = document.querySelector(
            'input[name="attachements"]'
        )

        const uploadAttachementButton =
            document.getElementById('upload-attachement')

        this.getVideosContainer()
            .querySelectorAll(`li`)
            .forEach((li) => {
                this.initVideosItem(li)
            })

        document
            .getElementById('upload-video')
            ?.addEventListener('click', () => {
                this.#uploadVideoHandler()
            })

        // Attachements

        new Sortable(attachementsContainer, {
            ondragend: () => {
                attachementInput.setAttribute(
                    'value',
                    Array.from(attachementsContainer.querySelectorAll('li > a'))
                        .map((a) => a.getAttribute('href').split('/').pop())
                        .filter((src) => src !== '')
                        .join(';')
                )
            }
        })

        function initAttachementItem(li) {
            const deleteButton = li.querySelector('.delete-attachement-button')

            deleteButton.addEventListener(
                'click',
                function () {
                    li.classList.add('transition', 'scale-0')
                    setTimeout(() => {
                        li.remove()
                        const values = []
                        attachementsContainer
                            .querySelectorAll('li')
                            .forEach((li) => {
                                values.push(
                                    li
                                        .querySelector('a')
                                        .getAttribute('href')
                                        .split('/')
                                        .pop()
                                )
                            })
                        attachementInput.setAttribute('value', values.join(';'))
                    }, 200)
                },
                {
                    once: true
                }
            )
        }

        attachementsContainer
            .querySelectorAll(`li`)
            .forEach(initAttachementItem)

        uploadAttachementButton.addEventListener('click', () => {
            const file = document.createElement('input')

            file.hidden = true
            file.type = 'file'
            file.accept = '*'
            file.multiple = true

            file.addEventListener('change', (event) => {
                Array.from(event.target.files).forEach((file) => {
                    new Promise((resolve, reject) => {
                        const data = new FormData()

                        data.append('file[]', file)

                        const progress = new Progress(attachementsContainer)

                        new Upload({
                            url: '/article/attachements',
                            data,
                            progress: function (value) {
                                progress.update(value)
                            },
                            success: ({ files }) => {
                                progress.destroy()

                                const values = attachementInput.value
                                    .split(';')
                                    .filter((src) => src !== '')

                                files.forEach((file) => {
                                    const div = document.createElement('div')

                                    div.insertAdjacentHTML(
                                        'beforeend',
                                        `<li class="flex flex-nowrap gap-2 items-center justify-between text-gray-800 w-full bg-gray-200 hover:bg-gray-300 rounded-lg"><a href="" class="text-ellipsis hover:underline overflow-hidden whitespace-nowrap grow px-3 py-2" target="_blank"></a><div class="delete-attachement-button cursor-pointer text-white hover:bg-red-500 transition bg-red-600 rounded-lg p-3">${this.svgDelete}</div></li>`
                                    )

                                    const li = div.querySelector('li')

                                    const a = li.querySelector('a')

                                    initAttachementItem(li)

                                    a.setAttribute(
                                        'href',
                                        `/uploads/articles/temp/${file}`
                                    )

                                    a.innerText = file

                                    attachementsContainer.append(li)

                                    values.push(file)
                                })

                                attachementInput.setAttribute(
                                    'value',
                                    values.join(';')
                                )
                            }
                        })

                        resolve()
                    })
                })
            })

            file.click()
        })

        document
            .getElementById('generate-tags')
            .addEventListener('click', () => {
                this.generateTags()
            })

        document
            .getElementById('save-article')
            ?.addEventListener('click', () => {
                this.save()
            })

        document
            .getElementById('add-article')
            ?.addEventListener('click', () => {
                this.add()
            })

        document
            .getElementById('delete-article')
            ?.addEventListener('click', () => {
                this.delete()
            })
    }

    generateTags() {
        const limit = parseInt(this.getTagsLimitElement().value)

        const tags = {}

        this.getTextElement()
            .value.split(/[\s,]+/)
            .forEach((tag, index) => {
                tag = tag.toLowerCase()
                if (tags[tag] === undefined) tags[tag] = 1
                else tags[tag]++
            })

        if (!Object.entries(tags).length) return ''

        this.getTagsElement().value = Object.entries(tags)
            .sort((a, b) => b[1] - a[1])
            .slice(0, limit)
            .map((tag) => tag[0])
            .join(', ')
    }

    getImageElement() {
        return document.getElementById('article-image')
    }

    getTagsLimitElement() {
        return document.getElementById('tags-limit')
    }

    getTextElement() {
        return this.form.querySelector('input[name="text"]')
    }

    getTagsElement() {
        return this.form.querySelector('textarea[name="tags"]')
    }

    getImageInput() {
        return this.form.querySelector('input[name="image"]')
    }

    getGalleryContainer() {
        return this.form.querySelector('.gallery-container')
    }

    getGalleryInput() {
        return this.form.querySelector('input[name="gallery"]')
    }

    getVideosContainer() {
        return this.form.querySelector('.videos-container')
    }

    getVideosInput() {
        return this.form.querySelector('input[name="videos"]')
    }

    save() {
        this.fetch({
            url: `/article/${this.id}`,
            method: 'PUT',
            body: JSON.stringify(Object.fromEntries(new FormData(this.form))),
            onclose: () => window.location.reload()
        })
    }

    add() {
        this.fetch({
            url: `/article`,
            method: 'POST',
            body: JSON.stringify(Object.fromEntries(new FormData(this.form))),
            onclose: function ({ id }) {
                if (id) {
                    window.location.href = `/edit/article/${id}`
                } else {
                    window.location.reload()
                }
            }
        })
    }

    delete() {
        if (!confirm(this.form.dataset.confirm)) return

        this.fetch({
            url: `/article/${this.id}`,
            method: 'DELETE',
            onclose: function () {
                window.location.href = '/edit/articles'
            }
        })
    }

    fetch({ url, method, body = null, onclose = null } = {}) {
        if (onclose === null) {
            onclose = function () {
                window.location.reload()
            }
        }

        fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json'
            },
            body
        }).then((response) => {
            response.json().then((json) => {
                new Dialog({
                    title: json.title,
                    text: json.message,
                    onclose: () => {
                        onclose(json)
                    }
                })
            })
        })
    }

    #uploadImageHandler({
        image = null,
        size = null,
        success = function () {}
    } = {}) {
        if (image === null) {
            const file = document.createElement('input')

            file.hidden = true

            file.type = 'file'

            file.accept = '.jpg,.jpeg,.png,.webp,.gif,.bmp'

            file.multiple = false

            if (size === null) size = this.imageSizeSelect.getValue()

            file.addEventListener(
                'change',
                (event) => {
                    const imageElement = this.getImageElement()

                    imageElement.src = `/images/placeholder_${size}.svg`

                    this.uploadImage({
                        image: event.target.files[0],
                        size,
                        success: ({ image }) => {
                            imageElement.src = `/images/articles/temp/${image}`
                            this.getImageInput().setAttribute('value', image)
                            this.changeImage({ size })
                            file.remove()
                        }
                    })
                },
                {
                    once: true
                }
            )

            file.click()
        } else {
            this.uploadImage({
                image,
                size,
                success
            })
        }
    }

    uploadImage({ image, size = '16_9', success = function () {} } = {}) {
        if (!image) return conseole.error('Image is missing')

        const data = new FormData()

        data.append('image', image)

        data.append('size', size)

        new Upload({
            url: '/article/image',
            data,
            success: (json) => {
                success(json)
            }
        })
    }

    deleteImage() {
        this.changeImage({ size: '16_9', placeholder: true })
    }

    changeImage({ size = '16_9', placeholder = false } = {}) {
        const imageElement = this.getImageElement()

        const width = parseInt(imageElement.getAttribute('width'))

        let height = width

        switch (size) {
            case '16_9':
                height = Math.round((width / 16) * 9)
                break
            case '4_3':
                height = Math.round((width / 4) * 3)
                break
        }

        if (placeholder) {
            imageElement.src = `/images/placeholder_${size}.svg`
            this.getImageInput().setAttribute('value', '')
        }

        imageElement.setAttribute('height', height)
    }

    initGalleryItem(li) {
        const deleteButton = li.querySelector('.delete-gallery-image-button')

        deleteButton.addEventListener(
            'click',
            () => {
                li.classList.add('transition', 'scale-0')
                setTimeout(() => {
                    li.remove()
                    this.updateGalleryInput()
                }, 200)
            },
            {
                once: true
            }
        )
    }

    updateGalleryInput() {
        const values = []

        this.getGalleryContainer()
            .querySelectorAll('li')
            .forEach((li) => {
                values.push(
                    li
                        .querySelector('img')
                        .src.split('/')
                        .pop()
                        .replace('thumb_', '')
                )
            })

        this.getGalleryInput().setAttribute('value', values.join(';'))
    }

    uploadGalleryImage({
        image,
        size = '16_9',
        success = function () {},
        progress = function () {}
    } = {}) {
        if (!image) return conseole.error('Image is missing')

        return new Promise((resolve, reject) => {
            const data = new FormData()

            data.append('image[]', image)

            data.append('size', size)

            new Upload({
                url: '/article/gallery',
                data,
                progress,
                success
            })

            resolve()
        })
    }

    #uploadGalleryImageHandler({
        image = null,
        size = null,
        success = function () {},
        progress = function () {}
    } = {}) {
        if (image === null) {
            const file = document.createElement('input')

            file.hidden = true

            file.type = 'file'

            file.accept = '.jpg,.jpeg,.png,.webp,.gif,.bmp'

            file.multiple = true

            file.addEventListener('change', (event) => {
                Array.from(event.target.files).forEach((file) => {
                    const galleryContainer = this.getGalleryContainer()

                    const progress = new Progress(galleryContainer)

                    const size = this.gallerySizeSelect.getValue()

                    let width = 320

                    let height = 180

                    switch (size) {
                        case '16_9':
                            height = 180
                            break
                        case '4_3':
                            height = 240
                            break
                        case '1_1':
                            height = 320
                            break
                    }

                    this.uploadGalleryImage({
                        image: file,
                        size,
                        progress: function (value) {
                            progress.update(value)
                        },
                        success: ({ images }) => {
                            progress.destroy()

                            images.forEach((src) => {
                                const wrapper = document.createElement('div')

                                wrapper.insertAdjacentHTML(
                                    'beforeend',
                                    this.getGalleryItemHtml({
                                        src,
                                        width,
                                        height
                                    })
                                )

                                const li = wrapper.querySelector('li')

                                galleryContainer.append(li)

                                this.initGalleryItem(li)
                            })

                            this.updateGalleryInput()
                        }
                    })
                })
            })

            file.click()
        } else {
            this.uploadGalleryImage({
                image,
                size,
                success,
                progress
            })
        }
    }

    getGalleryItemHtml({ src, width, height }) {
        return `<li class="text-sm select-none relative bg-gray-200 rounded-md"><img src="/images/articles/temp/thumb_${src}" width="${width}" height="${height}" alt="..." decoding="async" loading="auto" class="block w-full rounded-md"><div class="delete-gallery-image-button absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md hover:bg-red-500 cursor-pointer transition drop-shadow">${this.svgDelete}</div></li>`
    }

    updateVideosInput() {
        this.getVideosInput().setAttribute(
            'value',
            Array.from(this.getVideosContainer().querySelectorAll('li > video'))
                .map((video) => video.src.split('/').pop())
                .filter((src) => src !== '')
                .join(';')
        )
    }

    initVideosItem(li) {
        const deleteButton = li.querySelector('.delete-videos-button')

        deleteButton.addEventListener(
            'click',
            () => {
                li.classList.add('transition', 'scale-0')
                setTimeout(() => {
                    li.remove()
                    this.updateVideosInput()
                }, 200)
            },
            {
                once: true
            }
        )
    }

    #uploadVideoHandler({
        file = null,
        progress = function () {},
        success = function () {}
    } = {}) {
        if (file === null) {
            const file = document.createElement('input')

            file.hidden = true
            file.type = 'file'
            file.accept = 'video/*'
            file.multiple = true

            file.addEventListener('change', (event) => {
                Array.from(event.target.files).forEach((file) => {
                    const videosContainer = this.getVideosContainer()

                    const progress = new Progress(videosContainer)

                    this.uploadVideo({
                        file: file,
                        progress: function (value) {
                            progress.update(value)
                        },
                        success: ({ videos }) => {
                            progress.destroy()

                            videos.forEach((src) => {
                                const div = document.createElement('div')

                                div.insertAdjacentHTML(
                                    'beforeend',
                                    this.getVideoItemHtml({ src })
                                )

                                const li = div.querySelector('li')

                                this.initVideosItem(li)

                                videosContainer.append(li)
                            })

                            this.updateVideosInput()
                        }
                    })
                })
            })

            file.click()
        } else {
            this.uploadVideo({
                file,
                progress,
                success
            })
        }
    }

    uploadVideo({
        file = null,
        progress = function () {},
        success = function () {}
    }) {
        if (!file) return conseole.error('File is missing')

        new Promise((resolve, reject) => {
            const data = new FormData()

            data.append('video[]', file)

            new Upload({
                url: '/article/videos',
                data,
                progress,
                success
            })

            resolve()
        })
    }

    getVideoItemHtml({ src }) {
        return `<li class="text-sm select-none relative"><video src="/uploads/articles/temp/${src}" class="block w-full rounded-md" controls></video><div class="delete-videos-button absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md hover:bg-red-500 cursor-pointer transition drop-shadow" title="{{lang=delete}}">${this.svgDelete}</div></li>`
    }
}
