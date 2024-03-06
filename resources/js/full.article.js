import Gallery from './atlantis.gallery.min.js'

const thumbsSwiper = new Swiper('.thumb-gallery-swiper', {
    spaceBetween: 8,
    slidesPerView: getTabAmount(),
    freeMode: true,
    watchSlidesProgress: true
})

new Swiper('.gallery-swiper', {
    autoplay: {
        delay: 2500,
        disableOnInteraction: true
    },
    spaceBetween: 8,
    thumbs: {
        swiper: thumbsSwiper
    },
    effect: 'fade'
})

function getTabAmount() {
    if (window.innerWidth <= 768) return 4
    if (window.innerWidth <= 1280) return 6
    return 8
}

window.addEventListener('resize', () => {
    thumbsSwiper.params.slidesPerView = getTabAmount()
    thumbsSwiper.update()
})

document.querySelectorAll('.gallery-container').forEach((container) => {
    new Gallery(container)
})
