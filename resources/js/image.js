class IMage {
    constructor(base64) {
        this.base64 = base64
        this.loaded = false
    }

    load() {
        return new Promise((resolve) => {
            if (this.loaded) resolve(this.image)

            this.image = new Image()

            this.image.src = this.base64

            this.image.onload = () => {
                this.loaded = true
                resolve(this.image)
            }
        })
    }

    square(size) {
        return this.rect(size, size)
    }

    rect(width, height) {
        width = parseInt(width, 10)

        height = parseInt(height, 10)

        return new Promise((resolve) => {
            this.load().then((image) => {
                let sWidth = image.width

                let sHeight = image.height

                const aspectRatioInput = sWidth / sHeight

                const aspectRatioOutput = width / height

                let dWidth = width

                let dHeight = height

                let sX = 0

                let sY = 0

                let dX = 0

                let dY = 0

                if (aspectRatioInput > 1 && aspectRatioOutput > 1) {
                    if (aspectRatioInput > aspectRatioOutput) {
                        // Input wider than output
                        sWidth = sHeight * aspectRatioOutput

                        sX = (image.width - sWidth) / 2

                        if (sX < 0) sX = 0
                    } else {
                        // Input taller than output or equal
                        sHeight = sWidth / aspectRatioOutput

                        sY = (image.height - sHeight) / 2

                        if (sY < 0) sY = 0
                    }
                } else if (aspectRatioInput < 1 && aspectRatioOutput < 1) {
                    if (aspectRatioInput > aspectRatioOutput) {
                        // Input wider than output
                        sWidth = sHeight * aspectRatioOutput

                        sX = (image.width - sWidth) / 2

                        if (sX < 0) sX = 0
                    } else {
                        // Input taller than output or equal
                        sHeight = sWidth / aspectRatioOutput

                        sY = (image.height - sHeight) / 2

                        if (sY < 0) sY = 0
                    }
                } else if (aspectRatioInput > 1 && aspectRatioOutput < 1) {
                    // Input wider than output

                    sWidth = sHeight * aspectRatioOutput

                    sX = (image.width - sWidth) / 2

                    if (sX < 0) sX = 0
                } else {
                    // Input taller than output or equal

                    sHeight = sWidth / aspectRatioOutput

                    sY = (image.height - sHeight) / 2

                    if (sY < 0) sY = 0
                }

                this.render(
                    sX,
                    sY,
                    sWidth,
                    sHeight,
                    dX,
                    dY,
                    dWidth,
                    dHeight
                ).then((base64) => {
                    resolve(base64)
                })
            })
        })
    }

    #draw(image, sX, sY, sWidth, sHeight, dX, dY, dWidth, dHeight) {
        const canvas = document.createElement('canvas')

        canvas.width = dWidth

        canvas.height = dHeight

        const ctx = canvas.getContext('2d')

        ctx.drawImage(image, sX, sY, sWidth, sHeight, dX, dY, dWidth, dHeight)

        return canvas.toDataURL()
    }

    render(sX, sY, sWidth, sHeight, dX, dY, dWidth, dHeight) {
        return new Promise((resolve) => {
            this.load().then((image) => {
                resolve(
                    this.#draw(
                        image,
                        parseInt(sX, 10),
                        parseInt(sY, 10),
                        parseInt(sWidth, 10),
                        parseInt(sHeight, 10),
                        parseInt(dX, 10),
                        parseInt(dY, 10),
                        parseInt(dWidth, 10),
                        parseInt(dHeight, 10)
                    )
                )
            })
        })
    }
}
