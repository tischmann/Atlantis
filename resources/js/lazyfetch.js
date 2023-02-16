class LazyFetch {
    constructor(container, callback = function () {}) {
        this.container = container

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
        const data = JSON.stringify({
            search: this.search,
            page: this.page,
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
            .then((response) => response.json())
            .then((response) => {
                if (response?.status) {
                    if (response?.html) {
                        target.insertAdjacentHTML(`beforebegin`, response.html)

                        this.page = response.data.page

                        this.container.appendChild(target)
                    }

                    if (response?.last) target.remove()

                    this.callback()
                } else {
                    console.error('LazyFetch:', response?.message)
                }
            })
            .catch((error) => {
                console.error('LazyFetch:', error)
            })
    }
}
