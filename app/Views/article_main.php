<?php

use App\Models\Article;

assert($article instanceof Article);

$image = $article->getImage();

?>

<a href="/{{env=APP_LOCALE}}/article/<?= $article->id ?>" title="<?= $article->title ?>" class="group/item group/label block">
    <article>
        <div class="relative">
            <div class="group-hover/item:opacity-100 absolute opacity-0 inset-0 bg-black bg-opacity-50 rounded-t-xl flex items-center justify-center transition-opacity">
                <div class="bg-gray-100 text-gray-800 py-2 px-3 rounded-lg flex items-center flex-nowrap gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    {{lang=read}}
                </div>
            </div>
            <img src="<?= $image ? "/images/articles/{$article->id}/image/thumb_{$image} " : "/images/placeholder.svg" ?>" alt="<?= $article->title ?>" width="400" height="300" class="bg-gray-200 w-full rounded-t-xl" decoding="async" loading="lazy">
        </div>
        <div class="px-4 py-3 border-2 border-gray-200 rounded-xl rounded-t-none border-t-0 group-hover/label:border-gray-300 transition">
            <h2 class="font-semibold text-base line-clamp-1"><?= $article->title ?></h2>
            <h3 class="line-clamp-1 text-gray-600 text-xs mb-1"><?= $article->getCategory()->title ?></h3>
            <div class="mb-2 text-gray-600 text-xs flex justify-between items-center flex-nowrap gap-2">
                <span class="grow"><?= $article->created_at->getElapsedTime(getenv('APP_LOCALE')) ?></span>
            </div>
            <p class="line-clamp-3 text-sm"><?= mb_substr($article->short_text, 0, 500) ?></p>
        </div>
    </article>
</a>