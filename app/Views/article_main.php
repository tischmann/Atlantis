<?php

use App\Models\Article;

assert($article instanceof Article);

?>

<a href="/{{env=APP_LOCALE}}/article/<?= $article->id ?>" title="<?= $article->title ?>" class="group/item block p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300">
    <article>
        <h2 class="mb-1 font-semibold text-lg line-clamp-1"><?= $article->title ?></h2>
        <h3 class="line-clamp-1 text-gray-600 text-sm mb-2"><?= $article->getCategory()->title ?></h3>
        <div class="relative">
            <div class="group-hover/item:opacity-100 absolute opacity-0 inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center transition-opacity">
                <div class="bg-gray-100 text-gray-800 py-2 px-3 rounded-lg flex items-center flex-nowrap gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    {{lang=read}}
                </div>
            </div>
            <img src="<?= $article->getImage() ? "/images/articles/{$article->id}/image/thumb_{$article->getImage()} " : "/images/placeholder.svg" ?>" alt="<?= $article->title ?>" width="400" height="300" class="bg-gray-200 w-full rounded-lg mb-4 shadow-lg" decoding="async" loading="lazy">
        </div>
        <div class="mb-4 text-gray-600 text-sm flex justify-between items-center flex-nowrap">
            <div class="flex flex-row flex-nowrap gap-2 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
                <span><?= $article->created_at->getElapsedTime(getenv('APP_LOCALE')) ?></span>
            </div>
        </div>
        <p class="line-clamp-3"><?= mb_substr($article->short_text, 0, 500) ?></p>
    </article>
</a>