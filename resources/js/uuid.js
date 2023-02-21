class UUID {
    constructor(name = 'uuid') {
        this.name = name
        this.uuid = this.get() || this.set()
    }

    get() {
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

    set() {
        const options = {
            path: '/',
            secure: true,
            domain: window.location.hostname,
            samesite: 'strict',
            expires: new Date(Date.now() + 1.21e9).toUTCString()
        }

        let cookie = `${encodeURIComponent(this.name)}=${encodeURIComponent(
            self.crypto.randomUUID()
        )}`

        Object.entries(options).forEach(([key, val]) => {
            cookie += `; ${key}=${val ? val : ''}`
        })

        document.cookie = cookie
    }
}
