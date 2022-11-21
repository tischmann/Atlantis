const divInstall = document.getElementById('installContainer')

const butInstall = document.getElementById('butInstall')

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
}

if (window.location.protocol === 'http:') {
    const requireHTTPS = document.getElementById('requireHTTPS')
    const link = requireHTTPS.querySelector('a')
    link.href = window.location.href.replace('http://', 'https://')
    requireHTTPS.classList.remove('hidden')
}

window.onload = () => {
    document.head.querySelectorAll('link[rel="preload"]').forEach((link) => {
        link.setAttribute('rel', 'stylesheet')
    })
}
