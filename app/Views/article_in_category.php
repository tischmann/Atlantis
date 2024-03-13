<?php

use App\Models\Article;

assert($article instanceof Article);

$image = $article->getImage();

$src_low = $image ? "/images/articles/{$article->id}/image/thumb_{$image} " : "/images/placeholder.svg";

$src = $image ? "/images/articles/{$article->id}/image/{$image} " : "/images/placeholder.svg";

list($image_width, $image_height) = $article->getImageSizes(true);

?>

<a href="/{{env=APP_LOCALE}}/articles/<?= $article->url ?>.html" title="<?= $article->title ?>" class="group/item group/label block">
    <article>
        <div class="relative">
            <div class="group-hover/item:opacity-100 absolute opacity-0 inset-0 bg-black bg-opacity-50 rounded-t-xl flex items-center justify-center transition-opacity">
                <div class="bg-gray-100 text-gray-800 py-2 px-3 rounded-lg flex items-center flex-nowrap gap-2 shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    {{lang=read}}
                </div>
            </div>
            <img srcset="<?= $src_low ?> 320w, <?= $src ?> 1280w" sizes="(max-width: 600px) 1280px, 320px" alt="<?= $article->title ?>" width="<?= $image_width ?>" height="<?= $image_height ?>" class="bg-gray-200 w-full rounded-t-xl" decoding="async" loading="auto">
        </div>
        <div class="px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl rounded-t-none border-t-0 group-hover/label:border-gray-300 dark:group-hover/label:border-gray-600 transition">
            <h2 class="font-semibold text-base line-clamp-1 my-0"><?= $article->title ?></h2>
            <h3 class="line-clamp-1 text-gray-600 dark:text-gray-400 text-xs  my-0"><?= $article->getCategory()->title ?></h3>
            <div class="my-2 text-gray-600 dark:text-gray-400 text-xs flex justify-between items-center flex-nowrap gap-4">
                <span class="grow block"><?= $article->created_at?->getElapsedTime(getenv('APP_LOCALE')) ?></span>
                <div class="flex flex-nowrap items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span><?= $article->getViews() ?></span>
                </div>
            </div>
            <p class="line-clamp-3 text-sm my-0"><?= mb_substr($article->short_text, 0, 500) ?></p>
        </div>
    </article>
</a>