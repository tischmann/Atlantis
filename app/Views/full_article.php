<?php

use App\Models\{Article};

use Tischmann\Atlantis\{App, Request};

assert($article instanceof Article);

$user = App::getCurrentUser();

$request = Request::instance();

$article->setView(strval(cookies_get('uuid')));

$category = $article->getCategory();

$image = $article->getImage();

$gallery = $article->getGalleryImages();

list($image_width, $image_height) = $article->getImageSizes();

$image_src = $image
    ? "/images/articles/{$article->id}/image/{$image}"
    : '/images/placeholder_16_9.svg';

?>
<link rel="stylesheet" href="/css/fancybox.min.css" />
<link rel="stylesheet" href="/css/carousel.min.css" />
<style>
    .f-carousel {
        --f-carousel-spacing: 0.5rem;
        --f-button-color: #fff;
    }

    .f-carousel__thumbs {
        --f-thumb-border-radius: 0.5rem;
    }
</style>
<main class="mx-8 lg:mx-auto lg:max-w-screen-lg">
    <article class="full-article gallery-container">
        <h2 class="mb-1 font-bold text-2xl flex items-center w-full line-clamp-1"><?= $article->title ?>
            <?php

            if ($user->canAuthor($article)) {
                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/edit/article/{$article->id}" title="{{lang=edit}}" class="no-print mx-4 hover:text-sky-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                </a>
                HTML;
            }

            ?>
        </h2>
        <div class="mb-4 text-gray-600 dark:text-gray-400 text-sm flex items-center flex-nowrap gap-4">
            <h3 class="text-sm my-0 hover:underline"><a href="/{{env=APP_LOCALE}}/category/<?= $category->slug ?>"><?= $category->title ?></a></h3>
            <div class="flex flex-row flex-nowrap gap-4 items-center">
                <div class="flex flex-nowrap items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>
                    <span><?= $article->created_at?->getElapsedTime(getenv('APP_LOCALE')) ?></span>
                    <?php
                    if ($article->updated_at) {
                        echo <<<HTML
                            <span class="ml-2 opacity-50 lowercase">{{lang=updated_at}} {$article->updated_at->getElapsedTime(getenv('APP_LOCALE'))}</span>
                            HTML;
                    }
                    ?>
                </div>
                <div class="flex flex-nowrap items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span><?= $article->getViews() ?></span>
                </div>
            </div>
        </div>
        <div class="my-4 flex flex-nowrap items-center gap-2 print-page no-print cursor-pointer font-semibold hover:underline text-gray-600 dark:text-gray-400" title="{{lang=print_page}}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
            </svg>
            <span>{{lang=print_page}}</span>
        </div>
        <div class="mb-4">
            <?= $article->short_text ?>
        </div>
        <div class="mb-4">
            <?php

            if ($gallery) {
                echo <<<HTML
                <img src="{$image_src}" alt="{$article->title}" width="{$image_width}" height="{$image_height}" class="hidden print w-full rounded-md shadow-lg" decoding="async" loading="lazy">
                HTML;

                echo <<<HTML
                <div class="select-none no-print">
                    <div class="f-carousel" data-carousel>
                        <div class="f-carousel__slide" data-fancybox="carousel" data-src="/images/articles/{$article->id}/image/{$image}" data-thumb-src="/images/articles/{$article->id}/image/thumb_{$image}">
                            <img src="/images/articles/{$article->id}/image/{$image}" alt="{$article->title}" width="{$image_width}" height="{$image_height}" class="rounded-md" decoding="async" loading="auto">
                        </div>
                HTML;

                foreach ($gallery as $filename) {
                    list($image_width, $image_height) = $article->getImageSizes(
                        file: getenv('APP_ROOT') . "/public/images/articles/{$article->id}/gallery/{$filename}"
                    );

                    echo <<<HTML
                     <div class="f-carousel__slide" data-fancybox="carousel" data-src="/images/articles/{$article->id}/gallery/{$filename}" data-thumb-src="/images/articles/{$article->id}/gallery/thumb_{$filename}">
                        <img src="/images/articles/{$article->id}/gallery/{$filename}" alt="{$article->title}" width="{$image_width}" height="{$image_height}" class="rounded-md" decoding="async" loading="lazy">
                    </div>
                    HTML;
                }

                echo <<<HTML
                    </div>
                </div>
                HTML;
            } else {
                echo <<<HTML
                <img src="{$image_src}" alt="{$article->title}" width="{$image_width}" height="{$image_height}" class="w-full rounded-md shadow-lg" decoding="async" loading="auto" data-fancybox="carousel" data-src="{$image_src}">
                HTML;
            }
            ?>
        </div>
        <div class="mb-4">
            <?= html_entity_decode(strval($article->text)) ?>
        </div>
        <?php
        $article_attachements = $article->getAttachements();

        if ($article_attachements) {
            echo <<<HTML
            <div class="mb-8 flex flex-col sm:flex-row gap-4 no-print">
            HTML;

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

            echo <<<HTML
            </div>
            HTML;
        }

        $article_videos = $article->getVideos();

        if ($article_videos) {
            echo <<<HTML
            <div class="mb-4 grid grid-cols-1 gap-4 no-print">
            HTML;

            foreach ($article_videos as $video) {
                echo <<<HTML
                <video src="/uploads/articles/{$article->id}/video/{$video}" class="block w-full rounded-xl" controls ></video>
                HTML;
            }

            echo <<<HTML
            </div>
            HTML;
        }

        if ($article->tags) {
            echo <<<HTML
            <div class="mb-4 flex flex-wrap gap-2 text-sm">
            HTML;

            foreach ($article->tags as $tag) {
                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/tags/{$tag}" class="rounded-xl bg-gray-200 dark:bg-gray-700 px-3 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 hover:underline" title="{$tag}">#{$tag}</a>
                HTML;
            }

            echo <<<HTML
            </div>
            HTML;
        }

        ?>
    </article>
</main>
<script nonce="{{nonce}}" type="module">
    import {
        Fancybox
    } from '/js/fancybox.min.js'
    import {
        Carousel
    } from '/js/carousel.min.js'
    import {
        Thumbs
    } from '/js/carousel.thumbs.min.js'

    document.querySelectorAll('[data-carousel]').forEach((element) => {
        new Carousel(
            element, {
                on: {
                    ready: function() {
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
            }, {
                Thumbs
            }
        )
    })

    document.querySelectorAll('.print-page').forEach((el) => {
        el.addEventListener('click', (event) => {
            window.print()
        })
    })
</script>