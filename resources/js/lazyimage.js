;(function () {
    window.addEventListener('load', function () {
        const observer = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return

                const target = entry.target

                const image = new Image()

                if (target.dataset.hasOwnProperty('lazyBg')) {
                    image.onload = function () {
                        target.style.backgroundImage = `url(${this.src})`

                        delete target.dataset.lazyBg

                        target.classList.remove(
                            `bg-[url('/images/placeholder.svg')]`
                        )

                        observer.unobserve(target)
                    }

                    image.src = target.dataset.lazyBg
                } else if (target.dataset.hasOwnProperty('lazySrc')) {
                    image.onload = function () {
                        target.src = this.src

                        delete target.dataset.lazySrc

                        observer.unobserve(target)
                    }

                    image.src = target.dataset.lazySrc
                }
            })
        })

        document
            .querySelectorAll('[data-lazy-src],[data-lazy-bg]')
            .forEach((target) => {
                if (target.dataset.hasOwnProperty('lazyBg')) {
                    target.classList.add(`bg-[url('/images/placeholder.svg')]`)
                }

                observer.observe(target)
            })
    })
})()
