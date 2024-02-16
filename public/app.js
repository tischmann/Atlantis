var dialog = function ({
    title = 'Dialog title',
    text = 'Dialog text',
    redirect
}) {
    const dialog = document.createElement('dialog')
    const container = document.createElement('div')
    const close = document.createElement('button')
    const titleElement = document.createElement('h2')

    titleElement.textContent = title

    titleElement.classList.add(
        'text-2xl',
        'font-bold',
        'mb-4',
        'select-none',
        'pr-4'
    )

    container.classList.add('overflow-auto')

    close.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>`

    close.classList.add(
        'absolute',
        'text-gray-400',
        'hover:text-red-600',
        'top-4',
        'right-4',
        'outline-none',
        'transition',
        'select-none'
    )

    close.addEventListener('click', () => {
        dialog.close()
        if (redirect) window.location.href = redirect
    })

    dialog.classList.add(
        'relative',
        'p-8',
        'rounded-xl',
        'shadow',
        'mx-4',
        'bg-white',
        'md:mx-auto',
        'max-w-[90vw]'
    )

    container.insertAdjacentHTML('beforeend', text)

    dialog.append(titleElement, container, close)

    document.body.append(dialog)

    dialog.showModal()
}

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
})
