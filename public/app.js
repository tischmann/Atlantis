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
