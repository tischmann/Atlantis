export default class Atlantis {
    #handlers = new Map() // event handlers

    #lazyimageObserver = this.enter((target) => {
        if (target.dataset?.atlantisLazyImage == 1) return

        const image = new Image()

        image.onload = function () {
            if (target.dataset.hasOwnProperty('src')) {
                target.src = this.src
            } else if (target.dataset.hasOwnProperty('bg')) {
                target.style.backgroundImage = `url(${this.src})`
            }

            target.dataset.atlantisLazyImage = 1
        }

        if (target.dataset.hasOwnProperty('src')) {
            image.src = target.dataset.src
        } else if (target.dataset.hasOwnProperty('bg')) {
            image.src = target.dataset.bg
        }
    })

    constructor({ log = false } = {}) {
        this.log = log

        this.uuid = this.getUUID() || this.setUUID()

        new MutationObserver((mutations) => {
            this.#removeEventListeners(mutations[0]?.removedNodes)
        }).observe(document, { childList: true, subtree: true })
    }

    #removeEventListeners(nodeList) {
        if (nodeList instanceof NodeList) {
            nodeList.forEach((node) => {
                if (node.nodeType != 1) return

                this.off(node)

                this.#removeEventListeners(node.childNodes)
            })
        }

        return this
    }

    // Add event listener
    on(element, event, handler, capture = false) {
        const handlers = this.#handlers.get(event) || new Set()

        element.addEventListener(event, handler, capture)

        handlers.add({ element, handler, capture })

        this.#handlers.set(event, handlers)

        return this
    }

    // Remove event listener
    off(element, event = undefined, handler = undefined) {
        for (const [eventName, set] of this.#handlers) {
            if (eventName !== event && event !== undefined) continue

            for (const obj of set) {
                if (obj.handler !== handler && handler !== undefined) continue

                element.removeEventListener(eventName, obj.handler, obj.capture)

                set.delete(obj)

                if (handler !== undefined) return this
            }

            if (event !== undefined) return this
        }

        return this
    }

    find(parent, selector) {
        return parent.querySelector(selector)
    }

    findAll(parent, selector) {
        return parent.querySelectorAll(selector)
    }

    // Create HTML element
    tag(
        tagName,
        {
            className = null,
            classList = [],
            css = {},
            data = {},
            attr = {},
            text = null,
            html = null,
            append = [],
            on = {}
        } = {}
    ) {
        const element = document.createElement(tagName)

        if (className) element.className = className

        if (classList.length) element.classList.add(...classList)

        if (css) this.css(element, css)

        if (data) this.data(element, data)

        if (attr) this.attr(element, attr)

        if (text) element.textContent = text
        else if (html) element.innerHTML = html

        if (append?.length) element.append(...append)

        Object.entries(on).forEach(([event, listener]) => {
            this.on(element, event, listener)
        })

        return element
    }

    css(element, properties = {}) {
        if (properties instanceof Object) {
            Object.entries(properties).forEach(([key, value]) => {
                element.style[key] = value
            })
        }

        return this
    }

    data(element, properties = {}) {
        Object.entries(properties).forEach(([key, value]) => {
            element.dataset[key] = value
        })

        return this
    }

    attr(element, properties = {}) {
        Object.entries(properties).forEach(([key, value]) => {
            element.setAttribute(key, value)
        })

        return this
    }

    handleEvent(event) {
        switch (event.type) {
            case 'change':
                this.setArticleRating(
                    event.target.closest(`form[data-id]`).dataset.id,
                    event.target.value
                )
                break
        }
    }

    // Send request to server
    fetch(
        url,
        {
            method = `POST`,
            headers = {
                Accept: 'application/json'
            },
            body = undefined,
            success = function () {},
            failure = function () {}
        } = {}
    ) {
        if (typeof body !== 'string' && body instanceof FormData === false) {
            body = JSON.stringify(body)
        }

        headers = {
            Accept: 'application/json',
            ...headers
        }

        if (typeof body === 'string') {
            headers = {
                ...headers,
                'Content-Length': body.length.toString()
            }
        }

        fetch(url, { method, headers, body })
            .then((response) => {
                if (!response.ok) {
                    failure(response.status)

                    return console.error('Atlantis.fetch():', response.status)
                }

                switch (headers.Accept) {
                    case 'application/json':
                        response
                            .json()
                            .then((json) => {
                                if (this.log) {
                                    console.log('Atlantis.fetch():', json)
                                }

                                success(json)
                            })
                            .catch((error) => {
                                failure(error)
                                console.error('Atlantis.fetch():', error)
                            })
                        break
                    default:
                        response
                            .text()
                            .then((html) => {
                                if (this.log)
                                    console.log('Atlantis.fetch():', html)

                                success(html)
                            })
                            .catch((error) => {
                                failure(error)
                                console.error('Atlantis.fetch():', error)
                            })
                        break
                }
            })
            .catch((error) => {
                failure(error)
                console.error('Atlantis.fetch():', error)
            })
    }

    uniqueid() {
        return self.crypto.randomUUID()
    }

    toInt(value) {
        return parseInt(value, 10)
    }

    getCookie(name) {
        const matches = document.cookie.match(
            new RegExp(
                `(?:^|; )${name.replace(
                    /([\.$?*|{}\(\)\[\]\\\/\+^])/g,
                    '\\$1'
                )}=([^;]*)`
            )
        )

        return matches ? decodeURIComponent(matches[1]) : undefined
    }

    setCookie(name, value, options = {}) {
        options = {
            path: '/',
            secure: true,
            domain: window.location.hostname,
            samesite: 'strict',
            expires: new Date(Date.now() + 1.21e9).toUTCString(),
            ...options
        }

        let cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)}`

        Object.entries(options).forEach(([key, val]) => {
            cookie += `; ${key}=${val ? val : ''}`
        })

        document.cookie = cookie
    }

    getUUID(name = 'atlantis_uuid') {
        return this.getCookie(name)
    }

    setUUID(name = 'atlantis_uuid') {
        this.setCookie(name, self.crypto.randomUUID())
    }

    dialog({ title, message, buttons = [], onclose = function () {} } = {}) {
        const id = `atlantis-dialog-${this.uniqueid()}`

        const dialogElement = this.tag('dialog', {
            classList: [
                'm-0',
                'rounded',
                'shadow-xl',
                'fixed',
                'md:w-96',
                'w-full',
                'top-1/2',
                'left-1/2',
                'transform',
                '-translate-x-1/2',
                '-translate-y-1/2'
            ],
            attr: {
                id
            },
            on: {
                close: onclose
            }
        })

        const buttonsContainer = this.tag('div', {
            classList: ['flex', 'items-center', 'gap-4']
        })

        buttons.forEach((button) => {
            const buttonElement = this.tag('button', {
                attr: { type: 'button' },
                className:
                    button?.class ||
                    'bg-sky-600 text-white hover:bg-sky-500 focus:bg-sky-500 active:bg-sky-500',
                classList: [
                    'inline-block',
                    'w-full',
                    'px-6',
                    'py-2.5',
                    'text-white',
                    'font-medium',
                    'text-xs',
                    'leading-tight',
                    'uppercase',
                    'rounded',
                    'shadow-md',
                    'hover:shadow-lg',
                    'focus:shadow-lg',
                    'focus:outline-none',
                    'focus:ring-0',
                    'active:shadow-lg',
                    'transition',
                    'duration-150',
                    'ease-in-out'
                ],
                html: button?.text || 'Button',
                on: {
                    click: () => {
                        if (typeof button?.callback === 'function') {
                            button?.callback()
                        }

                        dialogElement.close()
                    }
                }
            })

            buttonsContainer.append(buttonElement)
        })

        dialogElement.append(
            this.tag('form', {
                attr: { method: 'dialog' },
                append: [
                    this.tag('button', {
                        classList: [
                            'absolute',
                            'top-4',
                            'right-4',
                            'ring-0',
                            'focus:ring-0',
                            'outline-none',
                            'text-gray-500'
                        ],
                        append: [
                            this.tag('i', {
                                classList: ['fas', 'fa-times', 'text-xl'],
                                attr: { value: 'cancel' }
                            })
                        ]
                    }),
                    this.tag('span', {
                        classList: [
                            'block',
                            'text-xl',
                            'font-medium',
                            'leading-normal',
                            'text-gray-800',
                            'pr-12',
                            'mb-4',
                            'truncate'
                        ],
                        text: title
                    }),
                    this.tag('div', { classList: ['mb-4'], html: message }),
                    buttonsContainer
                ]
            })
        )

        document.body.append(dialogElement)

        return dialogElement
    }

    enter(callback) {
        return new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        callback(entry.target)
                    }
                })
            },
            {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            }
        )
    }

    lazyimage() {
        const selectors = [
            `[data-atlantis-lazy-image][data-src]`,
            `[data-atlantis-lazy-image][data-bg]`
        ]

        this.#lazyimageObserver.takeRecords().forEach((record) => {
            this.#lazyimageObserver.unobserve(record.target)
        })

        selectors.forEach((selector) => {
            document.querySelectorAll(selector).forEach((element) => {
                if (element.dataset?.atlantisLazyImage != 1) {
                    this.#lazyimageObserver.observe(element)
                }
            })
        })
    }

    lazyload(
        container,
        {
            url = '',
            page = 1,
            next = 1,
            last = 1,
            limit = 1,
            sort = '',
            order = '',
            search = '',
            callback = function () {}
        } = {}
    ) {
        const target = this.tag('div', {
            classList: ['flex', 'justify-center', 'items-center'],
            attr: {
                'data-atlantis-lazyload-target': true
            },
            append: [
                this.tag('div', {
                    classList: [
                        'spinner-grow',
                        'inline-block',
                        'w-8',
                        'h-8',
                        'bg-sky-500',
                        'rounded-full',
                        'opacity-0'
                    ],
                    attr: { role: 'status' }
                })
            ]
        })

        container.appendChild(target)

        const observer = this.enter((element) => {
            if (page == last) {
                if (this.log) {
                    console.log('Atlantis.lazyload(): No more pages to load')
                }

                observer.disconnect()

                return target.remove()
            }

            this.fetch(url, {
                body: {
                    page,
                    next,
                    last,
                    limit,
                    sort,
                    order,
                    search
                },
                success: (json) => {
                    target.insertAdjacentHTML(`beforebegin`, json.html)

                    page = json.page

                    next = json.next

                    last = json.last

                    container.appendChild(target)

                    callback()

                    this.lazyimage()
                }
            })
        })

        observer.observe(target)
    }

    lightbox(container, thumbPrefix = 'thumb_') {
        const close = this.tag('button', {
            classList: [
                'absolute',
                'top-4',
                'right-4',
                'ring-0',
                'focus:ring-0',
                'outline-none',
                'text-gray-400',
                'hover:text-gray-300',
                'z-[1080]'
            ],
            attr: { value: 'cancel' },
            append: [
                this.tag('i', {
                    classList: ['fas', 'fa-times', 'text-2xl']
                })
            ],
            on: {
                click: hide
            }
        })

        const blur = this.tag('div', {
            classList: [
                'w-full',
                'h-full',
                'absolute',
                'top-0',
                'left-0',
                'z-[1040]',
                'backdrop-blur-sm'
            ]
        })

        const next = this.tag('button', {
            classList: [
                'absolute',
                'top-[calc(50%-(3.5rem))]',
                'right-0',
                'ring-0',
                'w-14',
                'h-14',
                'm-0',
                'flex',
                'items-center',
                'justify-center',
                'focus:ring-0',
                'outline-none',
                'text-gray-400',
                'hover:text-gray-300',
                'z-[1080]',
                'hover:bg-gray-800'
            ],
            attr: { value: 'next' },
            append: [
                this.tag('i', {
                    classList: ['fas', 'fa-chevron-right', 'text-2xl']
                })
            ],
            on: {
                click: () => {
                    const active = carousel.querySelector('[data-active]')

                    const next = active?.nextElementSibling

                    if (next) next.click()
                }
            }
        })

        const prev = this.tag('button', {
            classList: [
                'absolute',
                'top-[calc(50%-(3.5rem))]',
                'left-0',
                'ring-0',
                'w-14',
                'h-14',
                'm-0',
                'flex',
                'items-center',
                'justify-center',
                'focus:ring-0',
                'outline-none',
                'text-gray-400',
                'hover:text-gray-300',
                'z-[1080]',
                'hover:bg-gray-800'
            ],
            attr: { value: 'prev' },
            append: [
                this.tag('i', {
                    classList: ['fas', 'fa-chevron-left', 'text-2xl']
                })
            ],
            on: {
                click: () => {
                    const active = carousel.querySelector('[data-active]')

                    const prev = active?.previousElementSibling

                    if (prev) prev.click()
                }
            }
        })

        const frame = this.tag('div', {
            classList: [
                'h-[calc(100vh-(100px+3.5rem))]',
                'flex',
                'items-center',
                'justify-center',
                'm-14',
                'mb-0',
                'relative',
                'z-[1080]'
            ]
        })

        const carousel = this.tag('div', {
            classList: [
                'h-[calc(100px-2rem)]',
                'flex',
                'items-center',
                'gap-4',
                'm-4',
                'relative',
                'z-[1080]'
            ]
        })

        const wrapper = this.tag('div', {
            classList: [
                'hidden',
                'fixed',
                'top-0',
                'left-0',
                'w-screen',
                'h-screen',
                'z-[1040]',
                'bg-black/75',
                'transition-all',
                'ease-in-out'
            ],
            append: [blur, close, next, prev, frame, carousel]
        })

        document.body.append(wrapper)

        const makeActive = (element) => {
            element.dataset.active = true
            element.classList.remove('brightness-50')
        }

        const makeInactive = (element) => {
            delete element.dataset.active
            element.classList.add('brightness-50')
        }

        const refreshCarousel = () => {
            const currentSrc = sanitizeSrc(frame.querySelector('img').src)

            carousel.querySelectorAll('img').forEach((el) => {
                if (sanitizeSrc(el.src) == currentSrc) {
                    makeActive(el)
                } else {
                    makeInactive(el)
                }
            })
        }

        const populateCarousel = () => {
            container.querySelectorAll(`img`).forEach((element) => {
                const image = this.tag('img', {
                    classList: [
                        'object-cover',
                        'w-auto',
                        'h-full',
                        'cursor-pointer'
                    ],
                    attr: {
                        width: element.width,
                        height: element.height,
                        src: element.src
                    },
                    on: {
                        click: function () {
                            const img = frame.querySelector('img')

                            img.src = sanitizeSrc(this.src)

                            img.width = this.width

                            img.height = this.height

                            refreshCarousel()
                        }
                    }
                })

                carousel.append(image)
            })
        }

        function sanitizeSrc(src) {
            return src.replace(new RegExp(thumbPrefix), '')
        }

        function show() {
            document.body.classList.add('overflow-hidden')

            wrapper.classList.remove('hidden')

            if (carousel.scrollWidth > carousel.clientWidth) {
                carousel.classList.remove('justify-center')
                carousel.classList.add('overflow-x-auto', 'justify-start')
            } else {
                carousel.classList.add('justify-center')
            }
        }

        function hide(event) {
            wrapper.classList.add('hidden')
            document.body.classList.remove('overflow-hidden')
        }

        const onClick = (event) => {
            const element = event.target

            frame.innerHTML = ''

            const image = this.tag('img', {
                classList: ['object-contain', 'mx-auto', 'w-full', 'h-full'],
                attr: {
                    width: element.width,
                    height: element.height,
                    src: sanitizeSrc(element.src)
                }
            })

            frame.appendChild(image)

            if (carousel.children.length == 0) populateCarousel()

            refreshCarousel()

            show()
        }

        container.querySelectorAll(`img`).forEach((element) => {
            this.on(element, 'click', onClick)
        })
    }
}

export class Sortable {
    draggingElement = undefined
    items = new Set()

    constructor(
        container,
        {
            ondragstart = function () {},
            ondragover = function () {},
            ondragend = function () {}
        } = {}
    ) {
        this.container = container

        this.ondragstart = ondragstart

        this.ondragover = ondragover

        this.ondragend = ondragend

        this.container.querySelectorAll('li').forEach((item) => {
            this.items.add(item)
            item.draggable = true
            item.addEventListener('dragenter', this)
        })

        this.container.addEventListener('dragstart', this)
    }

    handleEvent(event) {
        switch (event.type) {
            case 'dragstart':
                return this.#dragStartHandler(event)
            case 'dragenter':
                return this.#dragEnterHandler(event)
            case 'dragend':
                return this.#dragEndHandler(event)
            case 'dragover':
                return this.#dragOverHandler(event)
        }
    }

    #dragOverHandler(event) {
        event.preventDefault()
    }

    #dragStartHandler(event) {
        const target = event.target.closest('li')

        if (!target) return

        document.addEventListener('dragover', this)

        this.draggingElement = target

        event.dataTransfer.effectAllowed = 'move'

        this.container.addEventListener('dragend', this)

        this.draggingElement.style.opacity = 0.7

        this.ondragstart(this, event)
    }

    #appendPlaceholder(event) {
        const target = event.target.closest('li')

        if (!target) return

        const nextSibling = target.nextSibling

        const previousSibling = target.previousSibling

        if (!previousSibling) {
            this.container.insertBefore(this.draggingElement, target)
        } else if (!nextSibling) {
            this.container.insertBefore(target, this.draggingElement)
        } else if (previousSibling === this.draggingElement) {
            this.container.insertBefore(
                this.draggingElement,
                target.nextSibling
            )
        } else {
            this.container.insertBefore(this.draggingElement, target)
        }

        return this
    }

    #dragEnterHandler(event) {
        event.preventDefault()

        const target = event.target.closest('li')

        if (!target) return

        event.dataTransfer.dropEffect = 'move'

        if (target === this.draggingElement) return

        this.#appendPlaceholder(event)

        this.ondragover(this, event)
    }

    #dragEndHandler(event) {
        event.preventDefault()

        document.removeEventListener('dragover', this)

        if (!this.draggingElement) return

        this.draggingElement.style.opacity = 1

        this.draggingElement = undefined

        this.container.removeEventListener('dragend', this)

        this.ondragend(this, event)
    }

    destroy() {
        this.container.removeEventListener('dragstart', this)

        this.items.forEach((item) =>
            item.removeEventListener('dragenter', this)
        )

        this.items.clear()
    }
}
