window.addEventListener('load', () => {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
    }

    if (window.location.protocol === 'http:') {
        const requireHTTPS = document.getElementById('requireHTTPS')
        const link = requireHTTPS.querySelector('a')
        link.href = window.location.href.replace('http://', 'https://')
        requireHTTPS.classList.remove('hidden')
    }

    document.head.querySelectorAll('link[rel="preload"]').forEach((link) => {
        link.setAttribute('rel', 'stylesheet')
    })

    document
        .querySelector('input[type="search"][name="query"]')
        ?.addEventListener('search', (event) => {
            if (event.target.value != '') return

            let search = window.location.search.split('?')[1]?.split('&')

            search = search.filter(function (value, index, arr) {
                return value.split('=')[0] != 'query'
            })

            search = search.length > 0 ? '?' + search.join('&') : ''

            window.location.assign(`${window.location.origin}/search${search}`)
        })
})
