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

$image_thumb_src = $image
    ? "/images/articles/{$article->id}/image/thumb_{$image}"
    : '/images/placeholder_16_9.svg';

$is_liked = $article->isLikedByUuid(strval(cookies_get('uuid')));

$is_viewed = $article->isViewedByUuid(strval(cookies_get('uuid')));

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
        <h2 class="mb-1 font-semibold text-2xl flex items-center w-full line-clamp-1"><?= $article->title ?>
            <?php

            if ($user->canAuthor($article)) {
                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/edit/article/{$article->id}" title="{{lang=edit}}" class="no-print mx-4 hover:text-sky-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </a>
                HTML;
            }

            ?>
        </h2>
        <div class="text-gray-600 dark:text-gray-400 text-sm flex sm:items-center flex-col sm:flex-row gap-4">
            <h3 class="text-sm my-0 hover:underline"><a href="/{{env=APP_LOCALE}}/category/<?= $category->slug ?>"><?= $category->title ?></a></h3>
            <div class="flex flex-nowrap items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
                <span><?= $article->created_at?->getElapsedTime(getenv('APP_LOCALE')) ?></span>
            </div>
            <?php
            if ($article->updated_at) {
                echo <<<HTML
                <span class="opacity-50 lowercase">{{lang=updated_at}} {$article->updated_at->getElapsedTime(getenv('APP_LOCALE'))}</span>
                HTML;
            }
            ?>
            <div class="flex items-center gap-4">
                <div class="flex flex-nowrap items-center gap-1">
                    <svg id="liked-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?= $is_liked ? "hidden" : "" ?>">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                    <svg id="like-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 <?= $is_liked ? "" : "hidden" ?>">
                        <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                    </svg>
                    <span id="likes-counter"><?= $article->getLikes() ?></span>
                </div>
                <div class="flex flex-nowrap items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?= $is_viewed ? "hidden" : "" ?>">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 <?= $is_viewed ? "" : "hidden" ?>">
                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                    </svg>
                    <span><?= $article->getViews() ?></span>
                </div>
            </div>
            <div id="article-liked-label" class="flex no-print justify-normal sm:justify-end text-gray-600 dark:text-gray-400 whitespace-nowrap text-sm <?= $is_liked ? "" : "hidden" ?>">{{lang=alticle_liked}}</div>
        </div>
        <div class="my-4 flex flex-wrap sm:flex-nowrap justify-normal sm:justify-between gap-4">
            <div class="flex flex-nowrap items-center gap-2 no-print cursor-pointer font-semibold hover:underline text-gray-600 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 <?= $is_liked ? "" : "hidden" ?>">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 <?= $is_liked ? "hidden" : "" ?>">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                </svg>
                <span id="like-article" class="whitespace-nowrap" data-liked="<?= intval($is_liked) ?>">{{lang=<?= $is_liked ? "dislike" : "like" ?>}}</span>
            </div>
            <div class="flex flex-nowrap items-center gap-2 print-page no-print cursor-pointer font-semibold hover:underline text-gray-600 dark:text-gray-400" title="{{lang=print_page}}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
                <span class="whitespace-nowrap">{{lang=print_page}}</span>
            </div>
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
                <img src="{$image_src}" alt="{$article->title}" width="{$image_width}" height="{$image_height}" class="w-full rounded-md shadow-lg" decoding="async" loading="auto" data-fancybox="carousel" data-src="{$image_src}" data-thumb-src="{$image_thumb_src}">
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
                <a href="/uploads/articles/{$article->id}/attachements/{$file}" class="grow sm:grow-0 flex items-center flex-nowrap gap-2 w-full sm:w-auto rounded-xl bg-gray-100 dark:bg-gray-700 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-600 hover:underline" download>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
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
            <div class="mb-4 flex flex-wrap gap-2 text-sm no-print">
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
                Dots: false,
                Thumbs: {
                    type: 'classic'
                }
            }, {
                Thumbs
            }
        )
    })

    Fancybox.bind('[data-fancybox="carousel"]', {
        Thumbs: {
            type: 'classic'
        }
    })

    document.querySelectorAll('.print-page').forEach((el) => {
        el.addEventListener('click', (event) => {
            window.print()
        })
    })

    document.getElementById('like-article').addEventListener('click', function() {
        const liked = !!parseInt(this.dataset?.liked || 0)

        const likedIcon = document.getElementById('liked-icon')

        const likeIcon = document.getElementById('like-icon')

        const likedArticleLabel = document.getElementById('article-liked-label')

        const likesCounter = document.getElementById('likes-counter')

        this.dataset.liked = liked ? "0" : "1"

        fetch('/like/article/<?= $article->id ?>', {
            method: liked ? 'DELETE' : 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        }).then((response) => {
            if (response.ok) {
                if (liked) {
                    likeIcon.classList.add('hidden')
                    likedIcon.classList.remove('hidden')
                    likedArticleLabel.classList.add('hidden')
                    this.innerText = '{{lang=like}}'
                } else {
                    likeIcon.classList.remove('hidden')
                    likedIcon.classList.add('hidden')
                    likedArticleLabel.classList.remove('hidden')
                    this.innerText = '{{lang=dislike}}'
                }

                return response.json().then(({
                    likes
                }) => {
                    likesCounter.innerText = likes
                })
            }

            response.json().then(({
                message
            }) => {
                alert(message)
            }).catch(() => {
                alert('{{lang=incorrect_response}}')
            })
        })
    })
</script>