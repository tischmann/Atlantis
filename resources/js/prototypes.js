HTMLSelectElement.prototype.select = function ({
    onchange = function () {}
} = {}) {
    function createOption({
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

    const options = new Set()

    const uuid = self.crypto.randomUUID()

    const wrapper = document.createElement('div')

    wrapper.classList.add('relative', 'grow')

    const label = document.createElement('label')

    label.classList.add(
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

    label.textContent = this.title

    label.style.maxWidth = '-webkit-fill-available'

    const input = document.createElement('input')

    input.setAttribute('value', this.value)

    input.setAttribute('type', 'hidden')

    input.setAttribute('name', this.name)

    input.setAttribute('required', '')

    const button = document.createElement('button')

    button.setAttribute('type', 'button')

    button.id = `button-${uuid}`

    button.classList.add(
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

    button.setAttribute('data-atlantis-select', '')

    const ul = document.createElement('ul')

    ul.id = `ul-${uuid}`

    ul.classList.add(
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

    ul.setAttribute('data-atlantis-options', '')

    this.querySelectorAll('option').forEach((option) => {
        const li = createOption({
            value: option.value,
            text: option.textContent,
            level: option.dataset.level,
            selected: option.selected,
            disabled: option.disabled
        })

        ul.append(li)

        options.add(li)

        if (option.selected) {
            button.textContent = option.textContent
            input.setAttribute('value', option.value)
        }
    })

    wrapper.append(label, input, button, ul)

    function getValue() {
        return input.value
    }

    function setValue(value, text) {
        button.textContent = text
        input.setAttribute('value', value)
    }

    function selectClickHandler(event) {
        event.stopPropagation()

        const target = event.target

        document
            .querySelectorAll(`[data-atlantis-select]:not(#button-${uuid})`)
            .forEach((el) => el.classList.remove('border-sky-600'))

        document
            .querySelectorAll(`[data-atlantis-options]:not(#ul-${uuid})`)
            .forEach((el) => el.classList.add('hidden'))

        target.classList.toggle('border-sky-600')

        ul.classList.toggle('hidden')

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

    function optionClickHandler(event) {
        const target = event.target.closest('li')

        const value = target.dataset.value

        setValue(value, target.textContent)

        options.forEach((el) => {
            if (el === target) el.classList.add('bg-sky-600', 'text-white')
            else el.classList.remove('bg-sky-600', 'text-white')
        })

        onchange(value)
    }

    function update(items) {
        options.forEach((el) => el.remove())

        options.clear()

        items.forEach(
            ({
                value = '',
                text = '',
                level = 0,
                selected = false,
                disabled = false
            } = {}) => {
                const li = createOption({
                    value,
                    text,
                    level,
                    selected,
                    disabled
                })

                ul.append(li)

                options.add(li)

                if (selected) setValue(value, text)
            }
        )
    }

    wrapper.addEventListener('click', function (event) {
        const target = event.target

        if (target.closest('button') === button) {
            return selectClickHandler(event)
        } else if (options.has(target.closest('li'))) {
            return optionClickHandler(event)
        }
    })

    this.parentElement.replaceChild(wrapper, this)

    return {
        getValue,
        setValue,
        update
    }
}
