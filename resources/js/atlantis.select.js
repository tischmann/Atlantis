export default class Select {
    options = new Set()

    constructor(element, { onchange = function () {} } = {}) {
        this.element = element

        this.onchange = onchange

        this.uuid = self.crypto.randomUUID()

        this.wrapper = document.createElement('div')

        this.wrapper.classList.add('relative', 'grow')

        this.label = document.createElement('label')

        this.label.classList.add(
            'absolute',
            'select-none',
            '-top-3',
            'left-2',
            'mb-2',
            'text-sm',
            'text-gray-600',
            'bg-white',
            'dark:bg-gray-800',
            'dark:text-gray-400',
            'px-1',
            'text-ellipsis',
            'whitespace-nowrap',
            'overflow-hidden'
        )

        this.label.textContent = this.element.getAttribute('title')

        this.label.style.maxWidth = '-webkit-fill-available'

        this.input = document.createElement('input')

        this.input.setAttribute('value', this.element.value)

        this.input.setAttribute('type', 'hidden')

        this.input.setAttribute('name', this.element.name)

        this.input.setAttribute('required', '')

        this.button = document.createElement('button')

        this.button.setAttribute('type', 'button')

        this.button.id = `button-${this.uuid}`

        this.button.classList.add(
            'flex',
            'items-center',
            'justify-start',
            'px-3',
            'py-2',
            'outline-none',
            'border-2',
            'border-gray-200',
            'dark:border-gray-600',
            'rounded-lg',
            'w-full',
            'focus:border-sky-600',
            'transition',
            'min-h-11',
            'z-10'
        )

        this.button.setAttribute('data-atlantis-select', '')

        this.ul = document.createElement('ul')

        this.ul.id = `ul-${this.uuid}`

        this.ul.classList.add(
            'absolute',
            'select-none',
            'mt-1',
            'hidden',
            'bg-white',
            'dark:bg-gray-800',
            'rounded-lg',
            'border-2',
            'border-sky-600',
            'shadow-xl',
            'max-h-[50vh]',
            'overflow-y-auto',
            'z-20'
        )

        this.ul.setAttribute('data-atlantis-options', '')

        this.element.querySelectorAll('option').forEach((option) => {
            const li = this.createOption({
                value: option.value,
                text: option.textContent,
                level: option.dataset.level,
                selected: option.selected,
                disabled: option.disabled
            })

            this.ul.append(li)

            this.options.add(li)

            if (option.selected) {
                this.button.textContent = option.textContent
                this.input.setAttribute('value', option.value)
            }
        })

        this.wrapper.append(this.label, this.input, this.button, this.ul)

        this.element.parentElement.replaceChild(this.wrapper, this.element)

        this.wrapper.addEventListener('click', this)
    }

    handleEvent(event) {
        const target = event.target

        switch (event.type) {
            case 'click':
                if (target.closest('button') === this.button) {
                    return this.#selectClickHandler(event)
                } else if (this.options.has(target.closest('li'))) {
                    return this.#optionClickHandler(event)
                }
                break
        }
    }

    #selectClickHandler(event) {
        event.stopPropagation()

        const target = event.target

        document
            .querySelectorAll(
                `[data-atlantis-select]:not(#button-${this.uuid})`
            )
            .forEach((el) => el.classList.remove('border-sky-600'))

        document
            .querySelectorAll(`[data-atlantis-options]:not(#ul-${this.uuid})`)
            .forEach((el) => el.classList.add('hidden'))

        target.classList.toggle('border-sky-600')

        this.ul.classList.toggle('hidden')

        document.addEventListener(
            'click',
            function () {
                document
                    .querySelectorAll('[data-atlantis-select]')
                    .forEach((el) => {
                        el.classList.remove('border-sky-600')
                    })
                document
                    .querySelectorAll('[data-atlantis-options]')
                    .forEach((el) => {
                        el.classList.add('hidden')
                    })
            },
            {
                once: true
            }
        )
    }

    #optionClickHandler(event) {
        const target = event.target

        const value = target.dataset.value

        this.setValue(value, target.textContent)

        this.options.forEach((el) => {
            if (el === target) el.classList.add('bg-sky-600', 'text-white')
            else el.classList.remove('bg-sky-600', 'text-white')
        })

        this.onchange(value)
    }

    createOption({
        value = '',
        text = '',
        level = 0,
        selected = false,
        disabled = false
    }) {
        const li = document.createElement('li')

        li.dataset.value = value

        li.dataset.level = level

        li.innerText = text

        li.classList.add(
            'px-4',
            'py-3',
            'cursor-pointer',
            'whitespace-nowrap',
            'min-h-12',
            'hover:bg-sky-600',
            'hover:text-white'
        )

        if (selected) {
            li.classList.add('bg-sky-600', 'text-white')
        }

        if (disabled) {
            li.classList.add('opacity-50', 'cursor-not-allowed')
        }

        switch (`${level}`) {
            case '1':
                li.classList.add('pl-8')
                break
            case '2':
                li.classList.add('pl-12')
                break
            case '3':
                li.classList.add('pl-16')
                break
            case '4':
                li.classList.add('pl-20')
                break
            case '5':
                li.classList.add('pl-24')
                break
            case '6':
                li.classList.add('pl-28')
                break
            case '7':
                li.classList.add('pl-32')
                break
            case '8':
                li.classList.add('pl-36')
                break
            case '9':
                li.classList.add('pl-40')
                break
            case '10':
                li.classList.add('pl-44')
                break
        }

        return li
    }

    getValue() {
        return this.input.value
    }

    setValue(value, text) {
        this.button.textContent = text
        this.input.setAttribute('value', value)
    }

    update(items) {
        this.options.forEach((el) => el.remove())

        this.options.clear()

        items.forEach(
            ({
                value = '',
                text = '',
                level = 0,
                selected = false,
                disabled = false
            } = {}) => {
                const li = this.createOption({
                    value,
                    text,
                    level,
                    selected,
                    disabled
                })

                this.ul.append(li)

                this.options.add(li)

                if (selected) this.setValue(value, text)
            }
        )
    }
}
