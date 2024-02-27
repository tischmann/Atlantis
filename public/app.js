Document.prototype.dialog = function ({
    title = 'Dialog title',
    text = 'Dialog text',
    redirect = null,
    onclose = function () {}
}) {
    const dialogElement = document.createElement('dialog')

    const containerElement = document.createElement('div')

    const closeElement = document.createElement('button')

    const titleElement = document.createElement('h2')

    titleElement.textContent = title

    titleElement.classList.add(
        'text-xl',
        'font-semibold',
        'mb-4',
        'select-none',
        'pr-4'
    )

    containerElement.classList.add('overflow-auto')

    containerElement.innerHTML = text

    closeElement.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>`

    closeElement.classList.add(
        'absolute',
        'text-gray-400',
        'hover:text-red-600',
        'top-4',
        'right-4',
        'outline-none',
        'transition',
        'select-none'
    )

    closeElement.addEventListener(
        'click',
        () => {
            if (redirect) return (window.location.href = redirect)
            dialogElement.close()
            onclose()
        },
        { once: true }
    )

    dialogElement.classList.add(
        'relative',
        'p-8',
        'rounded-xl',
        'shadow',
        'mx-4',
        'bg-white',
        'md:mx-auto',
        'max-w-[90vw]'
    )

    dialogElement.append(titleElement, containerElement, closeElement)

    document.body.append(dialogElement)

    dialogElement.showModal()
}

Document.prototype.sortable = function (
    container,
    { ondragend = function () {} } = {}
) {
    let draggedElement

    const getMouseOffset = (event) => {
        const targetRect = event.target.getBoundingClientRect()

        const offset = {
            x: event.pageX - targetRect.left,
            y: event.pageY - targetRect.top
        }

        return offset
    }

    const getElementVerticalCenter = (el) => {
        const rect = el.getBoundingClientRect()
        return (rect.bottom - rect.top) / 2
    }

    ;[].slice.call(container.querySelectorAll('li')).forEach(function (el) {
        el.draggable = true
    })

    function onDragOver(event) {
        event.preventDefault()

        event.dataTransfer.dropEffect = 'move'

        const target = event.target.closest('li')

        if (target && target !== draggedElement && target.nodeName === 'LI') {
            const offset = getMouseOffset(event)

            const middleY = getElementVerticalCenter(event.target)

            if (offset.y > middleY) {
                container.insertBefore(draggedElement, target.nextSibling)
            } else {
                container.insertBefore(draggedElement, target)
            }
        }
    }

    function onDragEnd(event) {
        event.preventDefault()
        draggedElement.classList.remove('opacity-50')
        container.removeEventListener('dragover', onDragOver, false)
        container.removeEventListener('dragend', onDragEnd, false)
        ondragend(event)
    }

    container.addEventListener(
        'dragstart',
        function (event) {
            draggedElement = event.target.closest('li')
            event.dataTransfer.effectAllowed = 'move'
            event.dataTransfer.setData('Text', draggedElement.textContent)
            container.addEventListener('dragover', onDragOver, false)
            container.addEventListener('dragend', onDragEnd, false)
            setTimeout(function () {
                draggedElement.classList.add('opacity-50')
            }, 0)
        },
        false
    )
}

Document.prototype.tags = function (text, limit = 5) {
    const tags = {}

    text.split(/[\s,]+/).forEach((tag, index) => {
        tag = tag.toLowerCase()
        if (tags[tag] === undefined) tags[tag] = 1
        else tags[tag]++
    })

    if (!Object.entries(tags).length) return ''

    return Object.entries(tags)
        .sort((a, b) => b[1] - a[1])
        .slice(0, limit)
        .map((tag) => tag[0])
        .join(', ')
}

Document.prototype.progress = function (percent = 0, container = null) {
    const wrapper = document.createElement('div')
    wrapper.classList.add('w-full', 'h-8', 'rounded-lg', 'bg-gray-200')
    const progress = document.createElement('div')
    progress.classList.add('h-8', 'rounded-lg', 'bg-sky-600', 'transition-all')
    progress.style.width = `${percent}%`
    wrapper.append(progress)
    if (container) container.append(wrapper)
    return {
        element: wrapper,
        update: (percent) => {
            if (!progress) return
            progress.style.width = `${percent}%`
        },
        destroy: () => {
            wrapper?.remove()
        }
    }
}

Document.prototype.upload = function (
    url,
    data,
    progress = function () {},
    method = 'POST'
) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest()

        xhr.open(method, url)

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
                document.dialog({
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
}

Document.prototype.select = function (
    fieldSelector = '[data-select]',
    optionsSelector = '[data-options]'
) {
    document.querySelectorAll(optionsSelector).forEach((ul) => {
        const parent = ul.parentElement

        const select = parent.querySelector(fieldSelector)

        const input = parent.querySelector('input')

        const options = ul.querySelectorAll('li')

        select.addEventListener('click', function (event) {
            this.classList.toggle('border-sky-600')

            ul.classList.toggle('hidden')

            event.stopPropagation()

            document.addEventListener(
                'click',
                () => {
                    this.classList.remove('border-sky-600')
                    ul.classList.add('hidden')
                },
                {
                    once: true
                }
            )
        })

        options.forEach((li) => {
            li.addEventListener('click', function (event) {
                input.setAttribute('value', this.dataset.value)

                select.textContent = this.textContent

                options.forEach((li) => {
                    li.classList.remove('bg-sky-600', 'text-white')
                })

                li.classList.add('bg-sky-600', 'text-white')
            })
        })
    })
}

window.addEventListener('load', () => {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.min.js')
    }

    if (window.location.protocol === 'http:') {
        const requireHTTPS = document.getElementById('requireHTTPS')

        requireHTTPS.querySelectorAll('a').forEach((link) => {
            link.href = window.location.href.replace('http://', 'https://')
        })

        requireHTTPS.classList.remove('hidden')
    }

    document.head.querySelectorAll('link[rel="preload"]').forEach((link) => {
        link.setAttribute('rel', 'stylesheet')
    })
})
