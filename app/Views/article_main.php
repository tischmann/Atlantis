<?php

use App\Models\{Article, Category};

$article ??= new Article();

assert($article instanceof Article);

$image = $article->getImage();

$src_low = $image ? "/images/articles/{$article->id}/image/thumb_{$image} " : "/images/placeholder.svg";

$src = $image ? "/images/articles/{$article->id}/image/{$image} " : "/images/placeholder.svg";

list($image_width, $image_height) = $article->getImageSizes(true);

$category = $article->getCategory();

$category ??= new Category();

$is_liked = $article->isLikedByUuid(strval(cookies_get('uuid')));

$is_viewed = $article->isViewedByUuid(strval(cookies_get('uuid')));

$url = $article->url ? "/{{env=APP_LOCALE}}/articles/{$article->url}.html" : "#";

?>
<article class="relative group/item group/label">
    <a href="<?= $url ?>" title="<?= $article->title ?>" class="absolute inset-0 z-10"></a>
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
    <div class="relative px-3 py-3 border-2 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-xl rounded-t-none border-t-0 group-hover/label:border-gray-300 dark:group-hover/label:border-gray-600 transition">
        <h2 class="font-medium text-base line-clamp-1 my-0"><?= $article->title ?></h2>
        <a href="/{{env=APP_LOCALE}}/category/<?= $category->slug ?>" class="category-link relative inline-block line-clamp-1 text-gray-600 dark:text-gray-400 text-xs font-medium z-20 hover:underline" title="<?= $category->title ?>"><?= $category->title ?></a>
        <div class="mb-2 text-gray-600 dark:text-gray-400 text-xs flex justify-between items-center flex-nowrap gap-4">
            <span class="grow block created-at-label"><?= $article->created_at?->getElapsedTime(getenv('APP_LOCALE')) ?></span>
            <div class="flex gap-4">
                <div class="flex flex-nowrap items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?= $is_liked ? "hidden" : "" ?>">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 <?= $is_liked ? "" : "hidden" ?>">
                        <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                    </svg>
                    <span class="article-likes"><?= $article->getLikes() ?></span>
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
                    <span class="article-views"><?= $article->getViews() ?></span>
                </div>
            </div>
        </div>
        <p class="line-clamp-3 text-sm my-0 mb-2 short-text"><?= mb_substr(strval($article->short_text), 0, 500) ?></p>
        <?php
        if ($article->tags) {
            echo <<<HTML
            <div class="flex flex-wrap overflow-hidden gap-2 text-xs w-full h-6">
            HTML;

            $i = 1;

            $max = 5;

            foreach ($article->tags as $tag) {
                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/tags/{$tag}" class="rounded-md bg-gray-200 dark:bg-gray-700 px-2 py-1 hover:bg-gray-300 dark:hover:bg-gray-600 hover:underline z-20" title="{$tag}">#{$tag}</a>
                HTML;

                if ($i++ >= $max) break;
            }

            echo <<<HTML
            </div>
            HTML;
        }
        ?>
    </div>
</article>