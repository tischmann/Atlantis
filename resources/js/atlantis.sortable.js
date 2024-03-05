export default class Sortable {
    dragged = null
    dragStarted = false

    constructor(
        container,
        { ondragstart = function () {}, ondragend = function () {} } = {}
    ) {
        this.container = container

        if (!this.container) return

        this.ondragstart = ondragstart

        this.ondragend = ondragend
        ;[].slice
            .call(this.container.querySelectorAll('li'))
            .forEach(function (el) {
                el.draggable = true
            })

        this.container.addEventListener('dragstart', this, false)

        this.container.addEventListener('dragover', this, false)

        this.container.addEventListener('dragend', this, false)
    }

    handleEvent(event) {
        switch (event.type) {
            case 'dragstart':
                this.onDragStartHandler(event)
                break
            case 'dragover':
                this.onDragOverHandler(event)
                break
            case 'dragend':
                this.onDragEndHandler(event)
                break
        }
    }

    onDragStartHandler(event) {
        this.dragStarted = true

        this.dragged = event.target.closest('li')

        event.dataTransfer.effectAllowed = 'move'

        event.dataTransfer.setData('Text', this.dragged.textContent)

        setTimeout(() => {
            this.dragged.classList.add('opacity-50')
        }, 0)

        this.ondragstart(this, event)
    }

    onDragEndHandler(event) {
        if (!this.dragStarted) return

        this.dragStarted = false

        event.preventDefault()

        this.dragged.classList.remove('opacity-50')

        this.ondragend(this, event)
    }

    onDragOverHandler(event) {
        if (!this.dragStarted) return

        event.preventDefault()

        event.dataTransfer.dropEffect = 'move'

        const target = event.target.closest('li')

        if (target && target !== this.dragged && target.nodeName === 'LI') {
            const offset = this.getMouseOffset(event)

            const middleY = this.getElementVerticalCenter(event.target)

            if (offset.y > middleY) {
                this.container.insertBefore(this.dragged, target.nextSibling)
            } else {
                this.container.insertBefore(this.dragged, target)
            }
        }
    }

    getElementVerticalCenter(element) {
        const rect = element.getBoundingClientRect()
        return (rect.bottom - rect.top) / 2
    }

    getMouseOffset(event) {
        const rect = event.target.getBoundingClientRect()

        return {
            x: event.pageX - rect.left,
            y: event.pageY - rect.top
        }
    }

    destroy() {
        ;[].slice
            .call(this.container.querySelectorAll('li'))
            .forEach(function (el) {
                el.draggable = false
            })
        this.container.removeEventListener('dragstart', this, false)
        this.container.removeEventListener('dragover', this, false)
        this.container.removeEventListener('dragend', this, false)
    }
}
