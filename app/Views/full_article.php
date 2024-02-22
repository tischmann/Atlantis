<?php

use App\Models\{Article};

use Tischmann\Atlantis\{App};

assert($article instanceof Article);

?>
<main class="md:container mx-8 md:mx-auto">
    <article>
        <h2 class="mb-1 font-semibold text-xl flex items-center w-full line-clamp-1"><?= $article->title ?>
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
                <div class="flex flex-row flex-nowrap gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                    </svg>
                    <span><?= $article->rating ?></span>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <img src=" <?= $article->getImage() ?>" alt="<?= $article->title ?>" width="400" height="300" class="bg-gray-200 w-auto rounded-lg mr-4 shadow-lg float-left" decoding="async" loading="lazy">
            <?php
            $paragraphs = explode("\n", $article->text);

            foreach ($paragraphs as $paragraph) {
                echo "<p class='mb-4'>{$paragraph}</p>";
            }
            ?>
        </div>
        <div class="mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-4">
                <?php
                foreach ($article->getGalleryImages() as $image) {
                    echo <<<HTML
                        <img src="{$image['thumb']}" width="400" height="300" alt="{$article->title}" decoding="async" loading="lazy" class="w-full rounded-lg">
                    HTML;
                }
                ?>
            </div>
        </div>
    </article>
</main>