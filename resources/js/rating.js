class Rating {
    constructor(form, uuid) {
        this.form = form

        this.uuid = uuid

        this.id = this.form.dataset.id

        this.rating = this.form.dataset.rating

        this.uniqueid = self.crypto.randomUUID()

        for (let i = 5; i >= 1; i--) {
            const input = document.createElement('input')

            const label = document.createElement('label')

            input.type = 'radio'

            input.id = `star-${i}-${this.uniqueid}`

            label.setAttribute('for', input.id)

            input.name = `rating`

            input.value = i

            if (this.rating == i) input.checked = true

            input.addEventListener('change', this)

            this.form.append(input, label)
        }
    }

    handleEvent(event) {
        switch (event.type) {
            case 'change':
                this.change(event)
                break
        }
    }

    change(event) {
        this.rating = event.target.value

        let csrf = `{{csrf-token}}`

        const body = JSON.stringify({ uuid: this.uuid })

        fetch(`/rating/${this.id}/${this.rating}`, {
            method: `POST`,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Csrf-Token': csrf,
                'Content-Length': body.length.toString()
            },
            body
        })
            .then((response) =>
                response
                    .json()
                    .then((json) => {
                        if (json?.status) {
                            csrf = json.csrf
                        } else {
                            alert(json?.message)
                            console.error('Rating:', json?.message)
                        }
                    })
                    .catch((error) => {
                        alert(error)
                        console.error('Rating:', error)
                    })
            )
            .catch((error) => {
                alert(error)
                console.error('Rating:', error)
            })
    }
}
