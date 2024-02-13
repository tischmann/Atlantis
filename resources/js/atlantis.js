export default class Atlantis {
    #handlers = new Map()

    #removeListenersObserver = new MutationObserver((mutations) => {
        const handler = (nodeList) => {
            if (nodeList instanceof NodeList === false) return

            nodeList.forEach((node) => {
                if (node.nodeType != 1) return

                this.off(node)

                handler(node.childNodes)
            })
        }

        mutations.forEach((mutation) => {
            if (mutation.type !== 'childList') return
            handler(mutation.removedNodes)
        })
    }).observe(document, { childList: true, subtree: true })

    isEmpty(value) {
        if (value === null || value === undefined) {
            return true
        } else if (value instanceof Object) {
            return Object.keys(value).length === 0
        } else if (value instanceof Array) {
            return value.length === 0
        } else if (typeof value === 'string') {
            return value.length === 0
        } else if (typeof value === 'number') {
            return value === 0
        } else if (typeof value === 'boolean') {
            return value === false
        }

        return false
    }

    uniqueid() {
        return self.crypto.randomUUID()
    }

    dialog({ title, message, buttons = [], onclose = function () {} } = {}) {
        const id = `atlantis-dialog-${this.uniqueid()}`

        const dialogElement = this.create('dialog', {
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

        const buttonsContainer = this.create('div', {
            classList: ['flex', 'items-center', 'gap-4']
        })

        buttons.forEach((button) => {
            const buttonElement = this.create('button', {
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
            this.create('form', {
                attr: { method: 'dialog' },
                append: [
                    this.create('button', {
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
                            this.create('i', {
                                classList: ['fas', 'fa-times', 'text-xl'],
                                attr: { value: 'cancel' }
                            })
                        ]
                    }),
                    this.create('span', {
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
                    this.create('div', { classList: ['mb-4'], html: message }),
                    buttonsContainer
                ]
            })
        )

        document.body.append(dialogElement)

        return dialogElement
    }

    shown(element) {
        if (!element) return false
        return (element?.offsetWidth ?? 0) + (element?.offsetHeight ?? 0) > 0
    }

    on(element, event, handler, capture = false) {
        if (!element) return this

        const handlers = this.#handlers.get(event) || new Set()

        element.addEventListener(event, handler, capture)

        handlers.add({ element, handler, capture })

        this.#handlers.set(event, handlers)

        return this
    }

    off(element, event = undefined, handler = undefined) {
        if (!element) return this

        for (const [eventName, set] of this.#handlers) {
            if (eventName !== event) continue

            for (const obj of set) {
                if (obj.handler !== handler) continue

                if (element) {
                    element.removeEventListener(
                        eventName,
                        obj.handler,
                        obj.capture
                    )
                }

                set.delete(obj)

                if (handler) return this
            }

            if (event) return this
        }

        return this
    }

    submit({ action = '', target = '_blank', method = 'post', data = {} }) {
        let form = this.create('form', {
            attr: {
                action,
                target,
                method
            },
            css: {
                display: 'none'
            }
        })

        const append = (key, value) => {
            if (value instanceof Object) {
                Object.entries(value).forEach(([k, v]) => {
                    append(`${key}[${k}]`, v)
                })
            } else {
                form.append(
                    this.create('input', {
                        attr: {
                            type: 'hidden',
                            name: key,
                            value: value
                        }
                    })
                )
            }
        }

        Object.entries(data).forEach(([key, value]) => {
            append(key, value)
        })

        document.body.append(form)

        form.submit()

        form.remove()

        form = undefined
    }

    trigger(element, event, options) {
        if (!element) return this

        element.dispatchEvent(new CustomEvent(event, options))

        return this
    }

    fetch({
        url = '',
        method = 'GET',
        headers = {
            'Content-Type': 'application/json;charset=utf8',
            Accept: 'application/json'
        },
        body = {},
        mode = 'cors',
        cache = 'no-cache',
        credentials = 'same-origin',
        redirect = 'follow',
        referrerPolicy = 'no-referrer'
    } = {}) {
        method = method.toUpperCase()

        if (['POST', 'PUT'].includes(method)) body = JSON.stringify(body)
        else body = undefined

        return fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json;charset=utf8',
                Accept: 'application/json',
                ...headers,
                'Content-Length': body?.length || 0
            },
            body,
            mode,
            cache,
            credentials,
            redirect,
            referrerPolicy
        })
    }

    async(callback = function () {}) {
        return new Promise((resolve) => {
            callback()
            resolve()
        })
    }

    onresize({ element, callback = function () {} }) {
        const observer = new ResizeObserver(callback)
        observer.observe(element)
        return observer
    }

    onadd({
        elements = [],
        callback = function () {},
        options = { childList: true, subtree: true }
    }) {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type !== 'childList') return

                mutation.addedNodes.forEach((node) => {
                    elements.forEach((element) => {
                        if (!element) return

                        if (element.contains(node)) {
                            return callback()
                        }
                    })
                })
            })
        })

        observer.observe(document, options)

        return observer
    }

    onremove({
        elements = [],
        callback = function () {},
        options = {
            childList: true,
            subtree: true
        }
    }) {
        const observer = new MutationObserver((mutations) => {
            let removed = false

            mutations.forEach((mutation) => {
                if (mutation.type !== 'childList') return

                if (!mutation.removedNodes.length) return

                if (removed) return

                callback()

                removed = true
            })
        })

        elements.forEach((element) => {
            if (!element) return

            if (element.parentElement) {
                observer.observe(element.parentElement, options)
            } else if (element) {
                observer.observe(element, options)
            }
        })

        return observer
    }

    onchange({
        elements = [],
        callback = function () {},
        options = {
            childList: true,
            attributes: true,
            characterData: true
        }
    }) {
        const observer = new MutationObserver((mutations) => {
            callback()
        })

        elements.forEach((element) => {
            if (!element) return
            observer.observe(element, options)
        })

        return observer
    }

    jfetch({
        url = '',
        method = 'GET',
        body = {},
        success = function () {},
        failure = function () {}
    } = {}) {
        this.fetch({
            url,
            method,
            body,
            headers: {
                'Content-Type': 'application/json;charset=utf8',
                Accept: 'application/json'
            }
        })
            .then((value) => {
                value
                    .json()
                    .then((value) => {
                        success(value)
                    })
                    .catch((reason) => {
                        console.error(reason)
                        failure(reason)
                    })
            })
            .catch((reason) => {
                console.error(reason)
                failure(reason)
            })
    }

    pad(value, length = 2) {
        return `${value}`.padStart(length, '0')
    }

    center(element) {
        if (!element) return this

        return this.css(element, {
            position: `absolute`,
            top: `${(window.innerHeight - element.offsetHeight) / 2}px`,
            left: `${(window.innerWidth - element.offsetWidth) / 2}px`,
            bottom: 'unset',
            right: 'unset',
            margin: 0
        })
    }

    eval(element) {
        if (!element) return this

        element.querySelectorAll('script').forEach((script) => {
            const range = document.createRange()

            const fragment = script.outerHTML

            script.remove()

            range.selectNode(element)

            element.append(range.createContextualFragment(fragment))
        })

        return this
    }

    create(
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

    createNS(tagName, properties = {}) {
        const svg = document.createElementNS(
            'http://www.w3.org/2000/svg',
            tagName
        )

        Object.entries(properties).forEach(([property, value]) => {
            svg.setAttributeNS(null, property, value.toString())
        })

        return svg
    }

    parseRegExp(string) {
        const matches = string.match(/^\/(.*)\/([giumx]*)$/)

        if (!matches) return new RegExp(string)

        if (matches.length > 2) return new RegExp(matches[1], matches[2])

        return new RegExp(matches[1])
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
            expires: new Date(Date.now() + 1.21e9),
            ...options
        }

        let cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)}`

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString()
        }

        Object.entries(options).forEach(([key, val]) => {
            cookie += `; ${key}=${val ? val : ''}`
        })

        document.cookie = cookie

        return this
    }

    deleteCookie(name) {
        this.setCookie(name, '', { expires: new Date(Date.now() - 1) })
        return this
    }

    log(instance, message) {
        console.log(`${instance.constructor.name}():`, message)
    }

    warn(instance, message) {
        console.warn(`${instance.constructor.name}():`, message)
    }

    error(instance, message) {
        console.error(`${instance.constructor.name}():`, message)
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

    highlight(
        element,
        value = '',
        { tag = 'span', className = 'highlight' } = {}
    ) {
        let count = 0

        if (!element) return count

        let regex = value
            ? new RegExp(`(${value})`, 'gim')
            : new RegExp(`/(<${tag} class="${className}">|<\/${tag}>)/`, 'gim')

        value = value.replace(/\\/, '')

        element.innerHTML = element.textContent.replace(
            regex,
            function (full, ...matches) {
                if (value) {
                    const wrapper = document.createElement(tag)
                    wrapper.className = className
                    wrapper.textContent = matches[0]
                    count++
                    return wrapper.outerHTML
                } else {
                    count++
                    return ''
                }
            }
        )

        return count
    }

    uniqid() {
        return Math.random().toString(36).slice(2, 11)
    }

    animate({
        timing = function (timeFraction) {
            return timeFraction
        },
        draw = function (progress) {},
        duration = 200,
        after = function () {}
    } = {}) {
        const start = performance.now()

        function animate(time) {
            let timeFraction = (time - start) / duration

            if (timeFraction > 1) timeFraction = 1

            draw(timing(timeFraction))

            if (timeFraction < 1) requestAnimationFrame(animate)
            else after()
        }

        requestAnimationFrame(animate)

        return this
    }
}
