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
    element,
    { onchange = function () {} } = {}
) {
    if (!element) return

    const uuid = self.crypto.randomUUID()

    const wrapper = document.createElement('div')

    wrapper.classList.add('relative', 'grow')

    const label = document.createElement('label')

    label.classList.add(
        'absolute',
        'select-none',
        '-top-3',
        'left-2',
        'mb-2',
        'text-sm',
        'text-gray-600',
        'bg-white',
        'px-1',
        'text-ellipsis',
        'overflow-hidden'
    )

    label.textContent = element.getAttribute('title')

    label.style.maxWidth = '-webkit-fill-available'

    const input = document.createElement('input')

    input.setAttribute('value', element.value)

    input.setAttribute('type', 'hidden')

    input.setAttribute('name', element.name)

    input.setAttribute('required', '')

    const button = document.createElement('button')

    button.setAttribute('type', 'button')

    button.id = `button-${uuid}`

    button.classList.add(
        'flex',
        'items-center',
        'justify-start',
        'px-3',
        'py-2',
        'outline-none',
        'border-2',
        'border-gray-200',
        'rounded-lg',
        'w-full',
        'focus:border-sky-600',
        'transition',
        'min-h-11',
        'z-10'
    )

    button.setAttribute('data-atlantis-select', '')

    button.addEventListener('click', selectClickHandler)

    const ul = document.createElement('ul')

    ul.id = `ul-${uuid}`

    ul.classList.add(
        'absolute',
        'select-none',
        'mt-1',
        'hidden',
        'bg-white',
        'rounded-lg',
        'shadow-lg',
        'max-h-[50vh]',
        'overflow-y-auto',
        'z-20'
    )

    ul.setAttribute('data-atlantis-options', '')

    function createOption({
        value = '',
        label = '',
        level = 0,
        selected = false,
        disabled = false
    }) {
        const li = document.createElement('li')

        li.classList.add(
            'px-4',
            'py-3',
            'cursor-pointer',
            'whitespace-nowrap',
            'min-h-12',
            'hover:bg-sky-600',
            'hover:text-white'
        )

        if (selected) {
            li.classList.add('bg-sky-600', 'text-white')
        }

        if (disabled) {
            li.classList.add('opacity-50', 'cursor-not-allowed')
        }

        switch (`${level}`) {
            case '1':
                li.classList.add('pl-8')
                break
            case '2':
                li.classList.add('pl-12')
                break
            case '3':
                li.classList.add('pl-16')
                break
            case '4':
                li.classList.add('pl-20')
                break
        }

        li.dataset.value = value

        li.dataset.level = level

        li.innerText = label

        li.addEventListener('click', optionClickHandler)

        return li
    }

    element.querySelectorAll('option').forEach((option) => {
        ul.append(
            createOption({
                value: option.value,
                label: option.textContent,
                level: option.dataset.level,
                selected: option.selected,
                disabled: option.disabled
            })
        )

        if (option.selected) {
            button.textContent = option.textContent
            input.setAttribute('value', option.value)
        }
    })

    wrapper.append(label, input, button, ul)

    element.parentElement.replaceChild(wrapper, element)

    function selectClickHandler(event) {
        document.querySelectorAll('[data-atlantis-select]').forEach((el) => {
            if (el !== this) el.classList.remove('border-sky-600')
        })

        document
            .querySelectorAll('[data-atlantis-options]:not(.hidden)')
            .forEach((el) => {
                if (el !== ul) el.classList.add('hidden')
            })

        this.classList.toggle('border-sky-600')

        ul.classList.toggle('hidden')

        event.stopPropagation()

        document.addEventListener('click', documentClickHandler, {
            once: true
        })
    }

    function documentClickHandler() {
        document.querySelectorAll('[data-atlantis-select]').forEach((el) => {
            el.classList.remove('border-sky-600')
        })
        document.querySelectorAll('[data-atlantis-options]').forEach((el) => {
            el.classList.add('hidden')
        })
    }

    function optionClickHandler() {
        input.setAttribute('value', this.dataset.value)

        button.textContent = this.textContent

        ul.querySelectorAll('li').forEach((li) => {
            if (li === this) this.classList.add('bg-sky-600', 'text-white')
            else li.classList.remove('bg-sky-600', 'text-white')
        })

        onchange(this.dataset.value)
    }

    return {
        update: (items) => {
            ul.querySelectorAll('li').forEach((li) => {
                li.removeEventListener('click', optionClickHandler)
                li.remove()
            })

            items.forEach(({ value, label, level, selected, disabled }) => {
                ul.append(
                    createOption({
                        value,
                        label,
                        level,
                        selected,
                        disabled
                    })
                )

                if (selected) {
                    button.textContent = label
                    input.setAttribute('value', value)
                }
            })
        }
    }
}
