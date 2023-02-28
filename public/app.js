import Atlantis from '/js/atlantis.js'

const atlantis = new Atlantis()

atlantis.on(window, 'load', () => {
    const useDarkMode = window.matchMedia(
        '(prefers-color-scheme: dark)'
    ).matches

    document.head.querySelectorAll('link[rel="preload"]').forEach((link) => {
        link.setAttribute('rel', 'stylesheet')
    })

    // Lazy fetch
    // document.body
    //     .querySelectorAll('.intersection-loader-container')
    //     .forEach((container) => new LazyFetch(container))

    atlantis.on(window, 'scroll', () => {
        const classes = useDarkMode
            ? ['shadow-lg', 'border-b', 'border-b-gray-700']
            : ['shadow-lg', 'border-b', 'border-b-gray-200']

        if (window.scrollY > 16) {
            document.querySelector('header').classList.add(...classes)
        } else {
            document.querySelector('header').classList.remove(...classes)
        }
    })
})
