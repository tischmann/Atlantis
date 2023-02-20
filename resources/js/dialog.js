class Dialog {
    constructor({ title, message, buttons = [] } = {}) {
        this.title = title

        this.message = message

        this.buttons = buttons

        this.id = 'dialog-' + (Math.random() + 1).toString(36).substring(16)

        this.dialog = this.create()

        document.body.appendChild(this.dialog)
    }

    show() {
        this.dialog.showModal()
    }

    create() {
        const dialog = document.createElement('dialog')

        dialog.id = this.id

        dialog.classList.add('rounded', 'shadow-xl', 'fixed', 'w-96')

        const form = document.createElement('form')

        form.method = 'dialog'

        const title = document.createElement('h5')

        title.classList.add(
            'block',
            'text-xl',
            'font-medium',
            'leading-normal',
            'text-gray-800',
            'pr-12',
            'mb-4',
            'truncate'
        )

        title.innerText = this.title

        const message = document.createElement('div')

        message.classList.add('mb-4')

        message.innerText = this.message

        const buttons = document.createElement('div')

        buttons.classList.add('flex', 'items-center', 'gap-4')

        const closeButton = document.createElement('button')

        closeButton.classList.add(
            'absolute',
            'top-4',
            'right-4',
            'ring-0',
            'focus:ring-0',
            'outline-none',
            'text-gray-500'
        )

        const icon = document.createElement('i')

        icon.classList.add('fas', 'fa-times', 'text-xl')

        closeButton.value = 'cancel'

        closeButton.appendChild(icon)

        this.buttons.forEach((button) => {
            const buttonElement = document.createElement('button')

            buttonElement.classList.add(
                'inline-block',
                'w-full',
                'px-6',
                'py-2.5',
                'bg-sky-600',
                'text-white',
                'font-medium',
                'text-xs',
                'leading-tight',
                'uppercase',
                'rounded',
                'shadow-md',
                'hover:bg-pink-700',
                'hover:shadow-lg',
                'focus:bg-pink-700',
                'focus:shadow-lg',
                'focus:outline-none',
                'focus:ring-0',
                'active:bg-pink-800',
                'active:shadow-lg',
                'transition',
                'duration-150',
                'ease-in-out'
            )

            buttonElement.innerText = button?.text || 'Button'

            buttonElement.addEventListener('click', () => {
                button?.callback()
                this.dialog.close()
            })

            buttons.appendChild(buttonElement)
        })

        form.append(closeButton, title, message, buttons)

        dialog.appendChild(form)

        return dialog
    }
}
