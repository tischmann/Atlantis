<?php

use App\Models\{Article};

use Tischmann\Atlantis\{App};

assert($article instanceof Article);

?>
<main class="md:container mx-8 md:mx-auto">
    <article>
        <h2 class="mb-1 font-bold text-2xl flex items-center w-full line-clamp-1"><?= $article->title ?>
            <?php

            if (App::getCurrentUser()->isAdmin()) {
                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/edit/article/{$article->id}" class="mx-4 hover:text-sky-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                </a>
                HTML;
            }

            ?>
        </h2>
        <div class="mb-4 text-gray-600 text-sm flex items-center flex-nowrap gap-4">
            <h3><?= $article->getCategory()->title ?></h3>
            <div class="flex flex-row flex-nowrap gap-2 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
                <span><?= $article->created_at->format('d.m.Y') ?></span>
            </div>
            <div class="flex flex-row flex-nowrap gap-4">
                <div class="flex flex-row flex-nowrap gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span><?= $article->views ?></span>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <?= $article->short_text ?>
        </div>
        <div class="mb-4">
            <?php
            $image = $article->getImage();

            $gallery = $article->getGalleryImages();

            if ($gallery) {
                echo <<<HTML
                <div class="gallery-swiper mb-2 relative overflow-hidden select-none">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="/images/articles/{$article->id}/image/{$image}" alt="{$article->title}" width="720" height="405" class="w-full rounded-xl mr-4 shadow-lg" decoding="async" loading="lazy">
                        </div>
                HTML;

                foreach ($gallery as $filename) {
                    echo <<<HTML
                    <div class="swiper-slide">
                        <img src="/images/articles/{$article->id}/gallery/{$filename}" width="720" height="405" alt="{$article->title}" decoding="async" loading="lazy" class="w-full rounded-xl">
                    </div>
                    HTML;
                }

                echo <<<HTML
                    </div>
                </div>
                <div thumbsSlider="" class="thumb-gallery-swiper relative overflow-hidden select-none">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide cursor-pointer">
                            <img src="/images/articles/{$article->id}/image/thumb_{$image}" width="320" height="180" alt="{$article->title}" decoding="async" loading="lazy" class="rounded-xl w-full">
                        </div>
                HTML;

                foreach ($gallery as $filename) {
                    echo <<<HTML
                    <div class="swiper-slide cursor-pointer">
                        <img src="/images/articles/{$article->id}/gallery/thumb_{$filename}" width="320" height="180" alt="{$article->title}" decoding="async" loading="lazy" class="rounded-xl w-full">
                    </div>
                    HTML;
                }
                echo <<<HTML
                    </div>
                </div>
                HTML;
            } else {
                echo <<<HTML
                <img src="/images/articles/{$article->id}/image/{$image}" alt="{$article->title}" width="720" height="405" class="w-full rounded-xl mr-4 shadow-lg" decoding="async" loading="lazy">
                HTML;
            }
            ?>
        </div>
        <div class="mb-4 ql-editor">
            <?= html_entity_decode($article->text) ?>
        </div>
        <div class="mb-8 flex flex-col sm:flex-row gap-4">
            <?php
            foreach ($article->getAttachements() as $file) {
                echo <<<HTML
                <a href="/uploads/articles/{$article->id}/attachements/{$file}" class="grow sm:grow-0 flex flex-nowrap gap-2 w-full sm:w-auto rounded-xl bg-gray-200 p-4 hover:bg-gray-300 hover:underline" download>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    {$file}
                </a>
                HTML;
            }
            ?>
        </div>
        <div class="mb-4 grid grid-cols-1 gap-4">
            <?php
            foreach ($article->getVideos() as $video) {
                echo <<<HTML
                <video src="/uploads/articles/{$article->id}/video/{$video}" class="block w-full rounded-xl" controls ></video>
                HTML;
            }
            ?>
        </div>
    </article>
</main>
<script nonce="{{nonce}}">
    window.addEventListener('load', () => {
        const thumbs = new Swiper('.thumb-gallery-swiper', {
            spaceBetween: 8,
            slidesPerView: getTabAmount(),
            freeMode: true,
            watchSlidesProgress: true,
        })

        new Swiper('.gallery-swiper', {
            autoplay: {
                delay: 2500,
                disableOnInteraction: true,
            },
            spaceBetween: 8,
            thumbs: {
                swiper: thumbs
            },
            effect: 'fade',
        })

        function getTabAmount() {
            if (window.innerWidth <= 768) return 4
            if (window.innerWidth <= 1280) return 6
            return 8
        }

        window.addEventListener('resize', () => {
            thumbs.params.slidesPerView = getTabAmount()
            thumbs.update()
        })
    })
</script>