function lightbox(container, thumbPrefix = 'thumb_') {
    const close = this.tag('button', {
        classList: [
            'absolute',
            'top-4',
            'right-4',
            'ring-0',
            'focus:ring-0',
            'outline-none',
            'text-gray-400',
            'hover:text-gray-300',
            'z-[1080]'
        ],
        attr: { value: 'cancel' },
        append: [
            this.tag('i', {
                classList: ['fas', 'fa-times', 'text-2xl']
            })
        ],
        on: {
            click: hide
        }
    })

    const blur = this.tag('div', {
        classList: [
            'w-full',
            'h-full',
            'absolute',
            'top-0',
            'left-0',
            'z-[1040]',
            'backdrop-blur-sm'
        ]
    })

    const next = this.tag('button', {
        classList: [
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
            'outline-none',
            'text-gray-400',
            'hover:text-gray-300',
            'z-[1080]',
            'hover:bg-gray-800'
        ],
        attr: { value: 'next' },
        append: [
            this.tag('i', {
                classList: ['fas', 'fa-chevron-right', 'text-2xl']
            })
        ],
        on: {
            click: () => {
                const active = carousel.querySelector('[data-active]')

                const next = active?.nextElementSibling

                if (next) next.click()
            }
        }
    })

    const prev = this.tag('button', {
        classList: [
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
            'outline-none',
            'text-gray-400',
            'hover:text-gray-300',
            'z-[1080]',
            'hover:bg-gray-800'
        ],
        attr: { value: 'prev' },
        append: [
            this.tag('i', {
                classList: ['fas', 'fa-chevron-left', 'text-2xl']
            })
        ],
        on: {
            click: () => {
                const active = carousel.querySelector('[data-active]')

                const prev = active?.previousElementSibling

                if (prev) prev.click()
            }
        }
    })

    const frame = this.tag('div', {
        classList: [
            'h-[calc(100vh-(100px+3.5rem))]',
            'flex',
            'items-center',
            'justify-center',
            'm-14',
            'mb-0',
            'relative',
            'z-[1080]'
        ]
    })

    const carousel = this.tag('div', {
        classList: [
            'h-[calc(100px-2rem)]',
            'flex',
            'items-center',
            'gap-4',
            'm-4',
            'relative',
            'z-[1080]'
        ]
    })

    const wrapper = this.tag('div', {
        classList: [
            'hidden',
            'fixed',
            'top-0',
            'left-0',
            'w-screen',
            'h-screen',
            'z-[1040]',
            'bg-black/75',
            'transition-all',
            'ease-in-out'
        ],
        append: [blur, close, next, prev, frame, carousel]
    })

    document.body.append(wrapper)

    const makeActive = (element) => {
        element.dataset.active = true
        element.classList.remove('brightness-50')
    }

    const makeInactive = (element) => {
        delete element.dataset.active
        element.classList.add('brightness-50')
    }

    const refreshCarousel = () => {
        const currentSrc = sanitizeSrc(frame.querySelector('img').src)

        carousel.querySelectorAll('img').forEach((el) => {
            if (sanitizeSrc(el.src) == currentSrc) {
                makeActive(el)
            } else {
                makeInactive(el)
            }
        })
    }

    const populateCarousel = () => {
        container.querySelectorAll(`img`).forEach((element) => {
            const image = this.tag('img', {
                classList: [
                    'object-cover',
                    'w-auto',
                    'h-full',
                    'cursor-pointer'
                ],
                attr: {
                    width: element.width,
                    height: element.height,
                    src: element.src
                },
                on: {
                    click: function () {
                        const img = frame.querySelector('img')

                        img.src = sanitizeSrc(this.src)

                        img.width = this.width

                        img.height = this.height

                        refreshCarousel()
                    }
                }
            })

            carousel.append(image)
        })
    }

    function sanitizeSrc(src) {
        return src.replace(new RegExp(thumbPrefix), '')
    }

    function show() {
        document.body.classList.add('overflow-hidden')

        wrapper.classList.remove('hidden')

        if (carousel.scrollWidth > carousel.clientWidth) {
            carousel.classList.remove('justify-center')
            carousel.classList.add('overflow-x-auto', 'justify-start')
        } else {
            carousel.classList.add('justify-center')
        }
    }

    function hide(event) {
        wrapper.classList.add('hidden')
        document.body.classList.remove('overflow-hidden')
    }

    const onClick = (event) => {
        const element = event.target

        frame.innerHTML = ''

        const image = this.tag('img', {
            classList: ['object-contain', 'mx-auto', 'w-full', 'h-full'],
            attr: {
                width: element.width,
                height: element.height,
                src: sanitizeSrc(element.src)
            }
        })

        frame.appendChild(image)

        if (carousel.children.length == 0) populateCarousel()

        refreshCarousel()

        show()
    }

    container.querySelectorAll(`img`).forEach((element) => {
        this.on(element, 'click', onClick)
    })
}
