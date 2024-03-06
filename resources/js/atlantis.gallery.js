export default class Gallery {
    active = undefined

    constructor(container) {
        this.container = container

        if (!this.container) return

        this.carousel = this.#createCarousel()

        this.wrapper = this.#createWrapper()

        this.frame = this.#createFrame()

        this.wrapper.append(
            this.#createBlur(),
            this.#createCloseButton(),
            this.#createNextButton(),
            this.#createPrevButton(),
            this.frame,
            this.carousel
        )

        document.body.append(this.wrapper)

        this.container.querySelectorAll(`img.gallery-item`).forEach((img) => {
            img.addEventListener('click', (event) => {
                event.preventDefault()
                event.stopPropagation()
                this.active = img
                this.show()
            })
        })

        this.populateCarousel()
    }

    show() {
        let img = this.frame.querySelector('img')

        if (!img) {
            img = this.#createFrameImage()
            this.frame.appendChild(img)
        }

        img.setAttribute('src', this.active.src)

        img.setAttribute('width', this.active.width)

        img.setAttribute('height', this.active.height)

        img.setAttribute('alt', this.active.alt)

        document.body.classList.add('overflow-hidden')

        this.wrapper.classList.remove('hidden')

        if (this.carousel.scrollWidth > this.carousel.clientWidth) {
            this.carousel.classList.remove('justify-center')
            this.carousel.classList.add('overflow-x-auto', 'justify-start')
        } else {
            this.carousel.classList.add('justify-center')
        }
    }

    hide() {
        this.wrapper.classList.add('hidden')
        document.body.classList.remove('overflow-hidden')
    }

    sanitizeSrc(src) {
        return src.replace(new RegExp('thumb_'), '')
    }

    thumbizeSrc(src) {
        const filename = src.split('/').pop()
        return src.replace(filename, `thumb_${filename}`)
    }

    refreshCarousel = () => {
        const currentSrc = this.sanitizeSrc(this.frame.querySelector('img').src)

        this.carousel.querySelectorAll('img').forEach((img) => {
            if (this.sanitizeSrc(img.src) == currentSrc) {
                img.dataset.active = true
                img.classList.remove('brightness-50')
            } else {
                delete img.dataset.active
                img.classList.add('brightness-50')
            }
        })
    }

    populateCarousel = () => {
        this.container
            .querySelectorAll(`img.gallery-item`)
            .forEach((element) => {
                const img = document.createElement('img')

                img.classList.add(
                    'object-cover',
                    'w-auto',
                    'h-full',
                    'cursor-pointer'
                )

                img.setAttribute('loading', 'auto')

                img.setAttribute('decoding', 'async')

                img.setAttribute('src', this.thumbizeSrc(element.src))

                img.setAttribute('width', element.width)

                img.setAttribute('height', element.height)

                img.addEventListener('click', (event) => {
                    const image = this.frame.querySelector('img')

                    image.src = this.sanitizeSrc(img.src)

                    image.setAttribute('width', img.width)

                    image.setAttribute('height', img.height)

                    image.setAttribute('alt', img.alt)

                    this.refreshCarousel()
                })

                this.carousel.append(img)
            })
    }

    #createWrapper() {
        const div = document.createElement('div')

        div.classList.add(
            'hidden',
            'fixed',
            'top-0',
            'left-0',
            'w-screen',
            'h-screen',
            'z-50',
            'bg-black/75'
        )

        return div
    }

    #createCarousel() {
        const div = document.createElement('div')

        div.classList.add(
            'h-[calc(100px-2rem)]',
            'flex',
            'items-center',
            'gap-4',
            'm-4',
            'relative',
            'z-50'
        )

        return div
    }

    #createFrameImage() {
        const img = document.createElement('img')

        img.classList.add('object-contain', 'mx-auto', 'w-full', 'h-full')

        img.setAttribute('loading', 'auto')

        img.setAttribute('decoding', 'async')

        return img
    }

    #createCloseButton() {
        const button = document.createElement('button')

        button.classList.add(
            'absolute',
            'top-4',
            'right-4',
            'ring-0',
            'focus:ring-0',
            'outline-none',
            'text-white',
            'hover:scale-110',
            'z-50'
        )

        button.setAttribute('type', 'button')

        button.setAttribute('title', 'Закрыть')

        button.addEventListener('click', () => {
            this.hide()
        })

        button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>`

        return button
    }

    #createBlur() {
        const div = document.createElement('div')

        div.classList.add(
            'w-full',
            'h-full',
            'absolute',
            'top-0',
            'left-0',
            'z-50',
            'backdrop-blur-sm'
        )

        return div
    }

    #createNextButton() {
        const button = document.createElement('button')

        button.classList.add(
            'absolute',
            'top-[calc(50%-(3.5rem))]',
            'right-0',
            'ring-0',
            'w-14',
            'h-14',
            'm-0',
            'flex',
            'items-center',
            'justify-center',
            'focus:ring-0',
            'hover:scale-110',
            'outline-none',
            'text-white',
            'z-50'
        )

        button.setAttribute('type', 'button')

        button.setAttribute('title', 'Следующее изображение')

        button.setAttribute('value', 'next')

        button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>`

        button.addEventListener('click', (event) => {
            this.carousel
                .querySelector('[data-active]')
                ?.nextElementSibling?.click()
        })

        return button
    }

    #createPrevButton() {
        const button = document.createElement('button')

        button.classList.add(
            'absolute',
            'top-[calc(50%-(3.5rem))]',
            'left-0',
            'ring-0',
            'w-14',
            'h-14',
            'm-0',
            'flex',
            'items-center',
            'justify-center',
            'focus:ring-0',
            'hover:scale-110',
            'outline-none',
            'text-white',
            'z-50'
        )

        button.setAttribute('type', 'button')

        button.setAttribute('title', 'Предыдущее изображение')

        button.setAttribute('value', 'prev')

        button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>`

        button.addEventListener('click', (event) => {
            this.carousel
                .querySelector('[data-active]')
                ?.previousElementSibling?.click()
        })

        return button
    }

    #createFrame() {
        const div = document.createElement('div')

        div.classList.add(
            'h-[calc(100vh-(100px+3.5rem))]',
            'flex',
            'items-center',
            'justify-center',
            'm-14',
            'mb-0',
            'relative',
            'z-50'
        )

        return div
    }
}
