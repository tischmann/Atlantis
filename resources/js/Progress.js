export default class Progress {
    value = 0
    completed = false
    constructor(
        container,
        { value = 0, start = function () {}, complete = function () {} } = {}
    ) {
        this.container = container

        this.value = value

        this.start = start

        this.complete = complete

        this.wrapper = document.createElement('div')

        this.wrapper.classList.add(
            'relative',
            'w-full',
            'h-8',
            'rounded-lg',
            'bg-gray-200',
            'dark:bg-gray-700'
        )

        this.bar = document.createElement('div')

        this.bar.classList.add(
            'h-full',
            'rounded-lg',
            'bg-sky-600',
            'transition-all'
        )

        this.bar.style.width = '0%'

        this.percent = document.createElement('div')

        this.percent.classList.add(
            'absolute',
            'top-1/2',
            'left-1/2',
            '-translate-x-1/2',
            '-translate-y-1/2',
            'text-gray-800',
            'dark:text-white',
            'font-bold',
            'text-base',
            'text-center',
            'shadow-sm'
        )

        this.percent.innerText = '0%'

        this.wrapper.append(this.bar, this.percent)

        this.container.append(this.wrapper)

        this.start()

        this.update(value)
    }

    update(value) {
        if (this.completed) return

        value = parseInt(value)

        if (value < 0) value = 0
        else if (value > 100) value = 100

        this.bar.style.width = `${value}%`

        if (value >= 50) {
            this.percent.classList.add('text-white')
        }

        this.percent.innerText = `${value}%`

        if (value === 100) {
            this.complete()
            this.completed = true
        }
    }

    destroy() {
        this.wrapper.remove()
    }
}
