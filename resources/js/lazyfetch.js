class LazyFetch {
    constructor(container, { lazyload, callback = function () {} } = {}) {
        this.container = container

        this.lazyload = lazyload

        this.callback = callback

        if (!container) {
            console.warn('LazyFetch: container not found')
            return false
        }

        this.target = this.container.querySelector(
            '.intersection-loader-target'
        )

        if (!this.target) {
            console.warn('LazyFetch: target not found')
            return false
        }

        if (!this.target.dataset.hasOwnProperty('url')) {
            console.warn('LazyFetch: url not found')
            return false
        }

        if (!this.target.dataset.hasOwnProperty('search')) {
            console.warn('LazyFetch: data-search not found in target')
            return false
        }

        if (!this.target.dataset.hasOwnProperty('page')) {
            console.warn('LazyFetch: data-page not found in target')
            return false
        }

        if (!this.target.dataset.hasOwnProperty('next')) {
            console.warn('LazyFetch: data-next not found in target')
            return false
        }

        if (!this.target.dataset.hasOwnProperty('last')) {
            console.warn('LazyFetch: data-last not found in target')
            return false
        }

        if (!this.target.dataset.hasOwnProperty('limit')) {
            console.warn('LazyFetch: data-limit not found in target')
            return false
        }

        if (!this.target.dataset.hasOwnProperty('sort')) {
            console.warn('LazyFetch: data-sort not found in target')
            return false
        }

        if (!this.target.dataset.hasOwnProperty('order')) {
            console.warn('LazyFetch: data-order not found in target')
            return false
        }

        this.url = this.target.dataset.url

        this.search = this.target.dataset.search

        this.page = this.target.dataset.page

        this.next = this.target.dataset.next

        this.last = this.target.dataset.last

        this.limit = this.target.dataset.limit

        this.sort = this.target.dataset.sort

        this.order = this.target.dataset.order

        this.observer = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        this.fetch(entry.target)
                    }
                })
            },
            {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            }
        )

        this.observer.observe(this.target)
    }

    fetch(target) {
        if (this.page == this.last) return target.remove()

        const data = JSON.stringify({
            search: this.search,
            page: this.page,
            next: this.next,
            last: this.last,
            limit: this.limit,
            sort: this.sort,
            order: this.order
        })

        fetch(this.url, {
            method: `POST`,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'Content-length': data.length
            },
            body: data
        })
            .then((response) =>
                response
                    .json()
                    .then((json) => {
                        if (json?.status) {
                            if (json?.html) {
                                target.insertAdjacentHTML(
                                    `beforebegin`,
                                    json.html
                                )

                                this.page = target.dataset.page = json.page

                                this.next = target.dataset.next = json.next

                                this.last = target.dataset.last = json.last

                                this.container.appendChild(target)

                                this.lazyload.update()
                            }

                            this.callback()
                        } else {
                            alert(error)
                            console.error('LazyFetch:', json?.message)
                        }
                    })
                    .catch((error) => {
                        alert(error)
                        console.error('LazyFetch:', error)
                    })
            )
            .catch((error) => {
                alert(error)
                console.error('LazyFetch:', error)
            })
    }
}
