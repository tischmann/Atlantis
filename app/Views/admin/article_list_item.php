<?php

use App\Models\Article;

assert($article instanceof Article);

$image = $article->getImage();

$src_low = $image ? "/images/articles/{$article->id}/image/thumb_{$image} " : "/images/placeholder.svg";

$src = $image ? "/images/articles/{$article->id}/image/{$image} " : "/images/placeholder.svg";

list($image_width, $image_height) = $article->getImageSizes(true);

?>

<a href="/{{env=APP_LOCALE}}/edit/article/<?= $article->id ?>" title="<?= $article->title ?>" class="group/item block shadow-md hover:shadow-xl">
    <article>
        <div class="relative">
            <div class="group-hover/item:opacity-100 absolute opacity-0 inset-0 bg-black bg-opacity-50 rounded-t-xl flex items-center justify-center transition-opacity">
                <div class="bg-gray-100 text-gray-800 py-2 px-3 rounded-lg flex items-center flex-nowrap gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    {{lang=edit}}
                </div>
            </div>
            <img srcset="<?= $src_low ?> 320w, <?= $src ?> 1280w" sizes="(max-width: 600px) 1280px, 320px" alt="<?= $article->title ?>" width="<?= $image_width ?>" height="<?= $image_height ?>" class="bg-gray-200 w-full rounded-t-xl" decoding="async" loading="auto">
        </div>
        <div class="px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-xl rounded-t-none">
            <h2 class="m-0 mb-1 font-medium text-sm line-clamp-1"><?= $article->title ?></h2>
            <h3 class="m-0 mb-1 line-clamp-1 text-gray-600 dark:text-gray-400 text-xs"><?= $article->getCategory()->title ?></h3>
            <div class="mb-2 text-gray-600 dark:text-gray-400 text-xs flex justify-between items-center flex-nowrap gap-2">
                <span class="grow"><?= $article->created_at->getElapsedTime(getenv('APP_LOCALE')) ?></span>
                <?php
                if ($article->fixed) {
                    echo <<<HTML
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                    </div>
                    HTML;
                }

                if (!$article->moderated) {
                    echo <<<HTML
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                        </svg>
                    </div>
                    HTML;
                }

                if (!$article->visible) {
                    echo <<<HTML
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </div>
                    HTML;
                }
                ?>
            </div>
            <p class="m-0 line-clamp-3 text-xs"><?= mb_substr($article->short_text, 0, 500) ?></p>
        </div>
    </article>
</a>