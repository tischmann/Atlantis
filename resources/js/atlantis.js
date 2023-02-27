export default class Atlantis {
    #handlers = new Map() // event handlers

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
            if (eventName !== event) continue

            for (const obj of set) {
                if (obj.handler !== handler) continue

                element.removeEventListener(eventName, obj.handler, obj.capture)

                set.delete(obj)

                if (handler) return this
            }

            if (event) return this
        }

        return this
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
                'Content-Type': 'application/json',
                Accept: 'application/json'
            },
            body = undefined,
            success = function () {},
            failure = function () {}
        } = {}
    ) {
        if (typeof body !== 'string') body = JSON.stringify(body)

        headers = {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            ...headers
        }

        if (body) {
            headers = {
                ...headers,
                'Content-Length': body.length.toString()
            }
        }

        fetch(url, { method, headers, body })
            .then((response) => {
                if (!response.ok) {
                    failure(response.statusText)

                    return console.error(
                        'Atlantis.fetch():',
                        response.statusText
                    )
                }

                switch (response.headers.get('Content-Type')) {
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
                    case 'text/html':
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
}
