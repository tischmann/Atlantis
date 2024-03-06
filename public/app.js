Document.prototype.getCookie = function (name) {
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

Document.prototype.setCookie = function (name, value, options = {}) {
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

    return value
}

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

if (!document.getCookie('uuid')) {
    document.setCookie('uuid', self.crypto.randomUUID())
}

document.querySelectorAll('.print-page').forEach((el) => {
    el.addEventListener('click', (event) => {
        window.print()
    })
})
