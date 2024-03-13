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

if (!document.getCookie('uuid')) {
    document.setCookie('uuid', self.crypto.randomUUID())
}
