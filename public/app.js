import Cookie from '/js/atlantis.cookie.min.js'

const cookie = new Cookie()

if (!cookie.get('uuid')) {
    cookie.set('uuid', self.crypto.randomUUID(), {
        expires: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000)
    })
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
    switch (link.getAttribute('as')) {
        case 'style':
            link.setAttribute('rel', 'stylesheet')
            break
        case 'script':
            link.setAttribute('rel', 'script')
            break
    }
})
