import { Fancybox } from './fancybox.min.js'
import { Carousel } from './carousel.min.js'
import { Thumbs } from './carousel.thumbs.min.js'

document.querySelectorAll('[data-carousel]').forEach((element) => {
    new Carousel(
        element,
        {
            on: {
                ready: function () {
                    Fancybox.bind('[data-fancybox="carousel"]', {
                        Thumbs: {
                            type: 'classic'
                        }
                    })
                }
            },
            Dots: false,
            Thumbs: {
                type: 'classic'
            }
        },
        { Thumbs }
    )
})
