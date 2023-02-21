window.onload = () => {
    document.head.querySelectorAll('link[rel="preload"]').forEach((link) => {
        link.setAttribute('rel', 'stylesheet')
    })

    // Lazy fetch
    document.body
        .querySelectorAll('.intersection-loader-container')
        .forEach((container) => new LazyFetch(container))

    new LazyLoad()

    window.addEventListener('scroll', () => {
        if (window.scrollY > 16) {
            document.querySelector('header').classList.add('shadow-lg')
        } else {
            document.querySelector('header').classList.remove('shadow-lg')
        }
    })
}
