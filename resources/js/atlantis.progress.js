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

        this.wrapper.style.position = 'relative'

        this.wrapper.style.width = '100%'

        this.wrapper.style.height = '2rem'

        this.wrapper.style.borderRadius = '0.5rem'

        this.wrapper.style.backgroundColor = '#f3f4f6'

        this.bar = document.createElement('div')

        this.bar.style.width = '0%'

        this.bar.style.height = '100%'

        this.bar.style.borderRadius = '0.5rem'

        this.bar.style.backgroundColor = '#0284c7'

        this.bar.style.transition = 'all 0.2s ease-in-out'

        this.percent = document.createElement('div')

        this.percent.style.position = 'absolute'

        this.percent.style.top = '50%'

        this.percent.style.left = '50%'

        this.percent.style.transform = 'translate(-50%, -50%)'

        this.percent.style.color = '#1f2937'

        this.percent.style.fontWeight = 'bold'

        this.percent.style.fontSize = '1rem'

        this.percent.style.lineHeight = '1.5rem'

        this.percent.style.textAlign = 'center'

        this.percent.style.textShadow = '0 0 0.5rem rgba(0, 0, 0, 0.5)'

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

        if (value >= 50) this.percent.style.color = '#fff'

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
