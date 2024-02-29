const textElement = document.querySelector('input[name="text"]')
const galleryContainer = document.querySelector('.gallery-container')
const galleryInput = document.querySelector('input[name="gallery"]')
const uploadGalleryButton = document.getElementById('upload-gallery')
const videosContainer = document.querySelector('.videos-container')
const videosInput = document.querySelector('input[name="videos"]')
const uploadVideoButton = document.getElementById('upload-video')
const uploadImageButton = document.getElementById('upload-image')
const deleteImageButton = document.getElementById('delete-image')
const articleImage = document.getElementById('article-image')
const articleImageInput = document.querySelector('input[name="image"]')
const attachementsContainer = document.querySelector('.attachements-container')
const attachementInput = document.querySelector('input[name="attachements"]')
const uploadAttachementButton = document.getElementById('upload-attachement')
const tagsElement = document.querySelector('textarea[name="tags"]')
const saveButton = document.getElementById('save-article')
const addButton = document.getElementById('add-article')
const deleteButton = document.getElementById('delete-article')
const svgBin = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>`

// Text

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
    textElement.setAttribute('value', textEditor.root.innerHTML)
})

// Selects

const localeSelect = document.select(document.getElementById(`locale-select`), {
    onchange: (value) => {
        fetch(`/locale/categories/${value}`)
            .then((response) => response.json())
            .then(({ items }) => {
                categorySelect.update(items)
            })
    }
})

const categorySelect = document.select(
    document.getElementById(`category-select`)
)

// Image

uploadImageButton.addEventListener('click', function () {
    const file = document.createElement('input')
    file.hidden = true
    file.type = 'file'
    file.accept = '.jpg,.jpeg,.png,.webp,.gif,.bmp'
    file.multiple = false
    file.addEventListener(
        'change',
        (event) => {
            const data = new FormData()
            data.append('image', event.target.files[0])
            articleImage.src = `/images/placeholder.svg`
            document.upload('/article/image', data).then(({ image }) => {
                articleImage.src = `/images/articles/temp/${image}`
                articleImageInput.setAttribute('value', image)
                file.remove()
            })
        },
        {
            once: true
        }
    )
    file.click()
})

deleteImageButton.addEventListener('click', function () {
    articleImage.src = '/images/placeholder.svg'
    articleImageInput.setAttribute('value', '')
})

// Gallery

document.sortable(galleryContainer, {
    ondragend: () => {
        galleryInput.setAttribute(
            'value',
            Array.from(galleryContainer.querySelectorAll('li > img'))
                .map((img) => img.src.split('/').pop())
                .filter((src) => src !== '')
                .join(';')
        )
    }
})

function initGalleryItem(li) {
    const deleteButton = li.querySelector('.delete-gallery-image-button')

    deleteButton.addEventListener(
        'click',
        function () {
            li.classList.add('transition', 'scale-0')
            setTimeout(() => {
                li.remove()
                const values = []
                galleryContainer.querySelectorAll('li').forEach((li) => {
                    values.push(
                        li
                            .querySelector('img')
                            .src.split('/')
                            .pop()
                            .replace('thumb_', '')
                    )
                })
                galleryInput.setAttribute('value', values.join(';'))
            }, 200)
        },
        {
            once: true
        }
    )
}

galleryContainer.querySelectorAll(`li`).forEach(initGalleryItem)

uploadGalleryButton.addEventListener('click', () => {
    const file = document.createElement('input')
    file.hidden = true
    file.type = 'file'
    file.accept = '.jpg,.jpeg,.png,.webp,.gif,.bmp'
    file.multiple = true
    file.addEventListener('change', (event) => {
        Array.from(event.target.files).forEach((file) => {
            new Promise((resolve, reject) => {
                const data = new FormData()

                data.append('image[]', file)

                const progress = document.progress(0, galleryContainer)

                document
                    .upload('/article/gallery', data, function (percent) {
                        progress.update(percent)
                    })
                    .then(({ images }) => {
                        progress.destroy()

                        const values = galleryInput.value
                            .split(';')
                            .filter((src) => src !== '')

                        images.forEach((src) => {
                            const wrapper = document.createElement('div')

                            wrapper.insertAdjacentHTML(
                                'beforeend',
                                `<li class="text-sm select-none relative"><img src="/images/placeholder.svg" width="320" height="180" alt="..." decoding="async" loading="lazy" class="block w-full rounded-md"><div class="delete-gallery-image-button absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md hover:bg-red-500 cursor-pointer transition drop-shadow">${svgBin}</div></li>`
                            )

                            const li = wrapper.querySelector('li')

                            galleryContainer.append(li)

                            li.querySelector(
                                'img'
                            ).src = `/images/articles/temp/thumb_${src}`

                            values.push(src)

                            initGalleryItem(li)
                        })

                        galleryInput.setAttribute('value', values.join(';'))
                    })

                resolve()
            })
        })
    })

    file.click()
})

// Videos

document.sortable(videosContainer, {
    ondragend: () => {
        videosInput.setAttribute(
            'value',
            Array.from(videosContainer.querySelectorAll('li > video'))
                .map((video) => video.src.split('/').pop())
                .filter((src) => src !== '')
                .join(';')
        )
    }
})

function initVideosItem(li) {
    const deleteButton = li.querySelector('.delete-videos-button')

    deleteButton.addEventListener(
        'click',
        function () {
            li.classList.add('transition', 'scale-0')
            setTimeout(() => {
                li.remove()
                const values = []
                videosContainer.querySelectorAll('li').forEach((li) => {
                    values.push(li.querySelector('video').src.split('/').pop())
                })
                videosInput.setAttribute('value', values.join(';'))
            }, 200)
        },
        {
            once: true
        }
    )
}

videosContainer.querySelectorAll(`li`).forEach(initVideosItem)

uploadVideoButton.addEventListener('click', () => {
    const file = document.createElement('input')

    file.hidden = true
    file.type = 'file'
    file.accept = 'video/*'
    file.multiple = true

    file.addEventListener('change', (event) => {
        Array.from(event.target.files).forEach((file) => {
            new Promise((resolve, reject) => {
                const data = new FormData()

                data.append('video[]', file)

                const progress = document.progress(0, videosContainer)

                document
                    .upload('/article/videos', data, function (percent) {
                        progress.update(percent)
                    })
                    .then(({ videos }) => {
                        progress.destroy()

                        const values = videosInput.value
                            .split(';')
                            .filter((src) => src !== '')

                        videos.forEach((src) => {
                            const div = document.createElement('div')

                            div.insertAdjacentHTML(
                                'beforeend',
                                `<li class="text-sm select-none relative"><video src="" class="block w-full rounded-md" controls></video><div class="delete-videos-button absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md hover:bg-red-500 cursor-pointer transition drop-shadow" title="{{lang=delete}}">${svgBin}</div></li>`
                            )

                            const li = div.querySelector('li')

                            const video = li.querySelector('video')

                            initVideosItem(li)

                            video.src = `/uploads/articles/temp/${src}`

                            videosContainer.append(li)

                            values.push(src)
                        })

                        videosInput.setAttribute('value', values.join(';'))
                    })

                resolve()
            })
        })
    })

    file.click()
})

// Attachements

document.sortable(attachementsContainer, {
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
                attachementsContainer.querySelectorAll('li').forEach((li) => {
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

attachementsContainer.querySelectorAll(`li`).forEach(initAttachementItem)

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

                const progress = document.progress(0, attachementsContainer)

                document
                    .upload('/article/attachements', data, function (percent) {
                        progress.update(percent)
                    })
                    .then(({ files }) => {
                        progress.destroy()

                        const values = attachementInput.value
                            .split(';')
                            .filter((src) => src !== '')

                        files.forEach((file) => {
                            const div = document.createElement('div')

                            div.insertAdjacentHTML(
                                'beforeend',
                                `<li class="flex flex-nowrap gap-2 items-center justify-between text-gray-800 w-full bg-gray-200 hover:bg-gray-300 rounded-lg"><a href="" class="text-ellipsis hover:underline overflow-hidden whitespace-nowrap grow px-3 py-2" target="_blank"></a><div class="delete-attachement-button cursor-pointer text-white hover:bg-red-500 transition bg-red-600 rounded-lg p-3">${svgBin}</div></li>`
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

                        attachementInput.setAttribute('value', values.join(';'))
                    })

                resolve()
            })
        })
    })

    file.click()
})

// Tags

document.getElementById('generate-tags').addEventListener('click', function () {
    tagsElement.value = document.tags(
        textElement.value,
        document.getElementById('tags-limit').value
    )
})

// Save

saveButton?.addEventListener('click', function () {
    const form = document.getElementById('article-form')

    fetch(`/article/${form.dataset.id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(new FormData(form)))
    }).then((response) => {
        response.json().then((json) => {
            document.dialog({
                title: json.title,
                text: json.message,
                onclose: () => {
                    window.location.reload()
                }
            })
        })
    })
})
