<?php

use App\Models\{Article};

use Tischmann\Atlantis\{App};

assert($article instanceof Article);

$user = App::getCurrentUser();

?>
<main class="mx-8 lg:mx-auto lg:max-w-screen-lg">
    <article class="full-article">
        <h2 class="mb-1 font-bold text-2xl flex items-center w-full line-clamp-1"><?= $article->title ?>
            <?php

            if ($user->canAuthor($article)) {
                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/edit/article/{$article->id}" title="{{lang=edit}}" class="mx-4 hover:text-sky-800">
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
        </div>
        <div class="mb-4">
            <?= $article->short_text ?>
        </div>
        <div class="mb-4">
            <?php
            $image = $article->getImage();

            $gallery = $article->getGalleryImages();

            if ($gallery) {
                list($image_width, $image_height) = $article->getImageSizes();

                echo <<<HTML
                <div class="gallery-swiper mb-2 relative overflow-hidden select-none">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="/images/articles/{$article->id}/image/{$image}" alt="{$article->title}" width="{$image_width}" height="{$image_height}" class="w-full rounded-xl mr-4 shadow-lg" decoding="async" loading="auto">
                        </div>
                HTML;

                foreach ($gallery as $filename) {
                    list($image_width, $image_height) = $article->getImageSizes(
                        file: getenv('APP_ROOT') . "/public/images/articles/{$article->id}/gallery/{$filename}"
                    );
                    echo <<<HTML
                    <div class="swiper-slide">
                        <img src="/images/articles/{$article->id}/gallery/{$filename}" width="{$image_width}" height="{$image_height}" alt="{$article->title}" decoding="async" loading="auto" class="w-full rounded-xl">
                    </div>
                    HTML;
                }

                list($image_width, $image_height) = $article->getImageSizes(true);

                echo <<<HTML
                    </div>
                </div>
                <div thumbsSlider="" class="thumb-gallery-swiper relative overflow-hidden select-none">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide cursor-pointer">
                            <img src="/images/articles/{$article->id}/image/thumb_{$image}" width="{$image_width}" height="{$image_height}" alt="{$article->title}" decoding="async" loading="auto" class="rounded-xl w-full">
                        </div>
                HTML;

                foreach ($gallery as $filename) {
                    list($image_width, $image_height) = $article->getImageSizes(
                        file: getenv('APP_ROOT') . "/public/images/articles/{$article->id}/gallery/thumb_{$filename}"
                    );

                    echo <<<HTML
                    <div class="swiper-slide cursor-pointer">
                        <img src="/images/articles/{$article->id}/gallery/thumb_{$filename}" width="{$image_width}" height="{$image_height}" alt="{$article->title}" decoding="async" loading="auto" class="rounded-xl w-full">
                    </div>
                    HTML;
                }
                echo <<<HTML
                    </div>
                </div>
                HTML;
            } else {
                list($image_width, $image_height) = $article->getImageSizes();

                $src = $image
                    ? "/images/articles/{$article->id}/image/{$image}"
                    : '/images/placeholder_16_9.svg';

                echo <<<HTML
                <img src="{$src}" alt="{$article->title}" width="{$image_width}" height="{$image_height}" class="w-full rounded-xl mr-4 shadow-lg" decoding="async" loading="auto">
                HTML;
            }
            ?>
        </div>
        <div class="mb-4">
            <?= html_entity_decode($article->text) ?>
        </div>
        <?php
        $article_attachements = $article->getAttachements();

        if ($article_attachements) {
            echo <<<HTML
            <div class="mb-8 flex flex-col sm:flex-row gap-4">
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
            <div class="mb-4 grid grid-cols-1 gap-4">
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

        ?>
    </article>
</main>
<link rel="stylesheet" href="/css/swiper-bundle.min.css" />
<script src="/js/swiper-bundle.min.js" nonce="{{nonce}}"></script>
<script src="/js/full.article.min.js" nonce="{{nonce}}"></script>