export default class Cookie {
    constructor(name, value, options = {}) {
        this.name = name
        this.value = value
        this.options = options
    }

    get(name) {
        this.name = name ? name : this.name

        if (!this.name) return undefined

        const matches = document.cookie.match(
            new RegExp(
                `(?:^|; )${this.name.replace(
                    /([\.$?*|{}\(\)\[\]\\\/\+^])/g,
                    '\\$1'
                )}=([^;]*)`
            )
        )

        return matches ? decodeURIComponent(matches[1]) : undefined
    }

    set(name, value, options = {}) {
        this.name = name ? name : this.name

        this.value = value ? value : this.value

        this.options = options ? options : this.options

        if (!this.name) return undefined

        options = {
            path: '/',
            secure: true,
            domain: window.location.hostname,
            samesite: 'strict',
            expires: new Date(Date.now() + 1.21e9),
            ...options
        }

        let cookie = `${encodeURIComponent(this.name)}=${encodeURIComponent(
            this.value
        )}`

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString()
        }

        Object.entries(options).forEach(([key, val]) => {
            cookie += `; ${key}=${val ? val : ''}`
        })

        document.cookie = cookie
    }

    delete(name, options = {}) {
        this.set(name, '', {
            path: '/',
            secure: true,
            domain: window.location.hostname,
            samesite: 'strict',
            expires: new Date(0),
            ...options
        })
    }
}
