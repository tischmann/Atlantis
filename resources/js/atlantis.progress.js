export default class Progress {
    constructor(container, { value = 0 } = {}) {
        this.value = value

        this.container = container

        this.wrapper = document.createElement('div')

        this.wrapper.classList.add('w-full', 'h-8', 'rounded-lg', 'bg-gray-200')

        this.bar = document.createElement('div')

        this.bar.classList.add(
            'h-8',
            'rounded-lg',
            'bg-sky-600',
            'transition-all'
        )

        this.wrapper.append(this.bar)

        this.container.append(this.wrapper)

        this.update(this.value)
    }

    update(value) {
        this.bar.style.width = `${value}%`
    }

    destroy() {
        this.wrapper?.remove()
    }
}
