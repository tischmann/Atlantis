Document.prototype.dialog = function ({
    title = 'Dialog title',
    text = 'Dialog text',
    redirect = null,
    onclose = function () {}
}) {
    const dialogElement = document.createElement('dialog')

    const containerElement = document.createElement('div')

    const closeElement = document.createElement('button')

    const titleElement = document.createElement('h2')

    titleElement.textContent = title

    titleElement.classList.add(
        'text-xl',
        'font-semibold',
        'mb-4',
        'select-none',
        'pr-4'
    )

    containerElement.classList.add('overflow-auto')

    containerElement.innerHTML = text

    closeElement.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>`

    closeElement.classList.add(
        'absolute',
        'text-gray-400',
        'hover:text-red-600',
        'top-4',
        'right-4',
        'outline-none',
        'transition',
        'select-none'
    )

    closeElement.addEventListener(
        'click',
        () => {
            if (redirect) return (window.location.href = redirect)
            dialogElement.close()
            onclose()
        },
        { once: true }
    )

    dialogElement.classList.add(
        'relative',
        'p-8',
        'rounded-xl',
        'shadow',
        'mx-4',
        'bg-white',
        'md:mx-auto',
        'max-w-[90vw]'
    )

    dialogElement.append(titleElement, containerElement, closeElement)

    document.body.append(dialogElement)

    dialogElement.showModal()
}

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
