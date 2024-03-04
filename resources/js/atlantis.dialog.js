export default class Dialog {
    constructor({
        title = 'Title',
        text = 'Text',
        html = null,
        show = true,
        onopen = function () {},
        onclose = function () {}
    } = {}) {
        this.title = title

        this.text = text

        this.html = html

        this.show = show

        this.onopen = onopen

        this.onclose = onclose

        this.wrapper = document.createElement('div')

        this.wrapper.classList.add('overflow-auto')

        this.setText(this.text)

        if (this.html) this.setHtml(this.html)

        this.dialogElement = document.createElement('dialog')

        this.dialogElement.setAttribute('id', crypto.randomUUID())

        this.dialogElement.classList.add(
            'relative',
            'p-8',
            'rounded-xl',
            'shadow',
            'mx-4',
            'bg-white',
            'md:mx-auto',
            'max-w-[90vw]',
            'scale-0',
            'transition-all'
        )

        this.closeButton = document.createElement('button')

        this.titleElement = document.createElement('h2')

        this.setTitle(this.title)

        this.titleElement.classList.add(
            'text-xl',
            'font-semibold',
            'mb-4',
            'select-none',
            'pr-4'
        )

        this.closeButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>`

        this.closeButton.classList.add(
            'absolute',
            'text-gray-400',
            'hover:text-red-600',
            'top-4',
            'right-4',
            'outline-none',
            'transition',
            'select-none'
        )

        this.closeButton.addEventListener(
            'click',
            () => {
                this.close()
            },
            { once: true }
        )

        this.dialogElement.append(
            this.titleElement,
            this.wrapper,
            this.closeButton
        )

        document.body.append(this.dialogElement)

        this.dialogElement.classList.remove('scale-0')

        if (this.show) this.open()
    }

    setTitle(title) {
        this.titleElement.textContent = title
    }

    setText(text) {
        this.wrapper.innerHTML = text
    }

    setHtml(html) {
        this.wrapper.innerHTML = html
    }

    open() {
        this.dialogElement.showModal()
        this.onopen(this)
    }

    close() {
        this.dialogElement.close()
        this.onclose(this)
    }
}
