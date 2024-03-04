<?php

use App\Models\Article;

assert($article instanceof Article);

$image = $article->getImage();

$src_low = $image ? "/images/articles/{$article->id}/image/thumb_{$image} " : "/images/placeholder.svg";

$src = $image ? "/images/articles/{$article->id}/image/{$image} " : "/images/placeholder.svg";

list($image_width, $image_height) = $article->getImageSizes(true);

?>

<a href="/{{env=APP_LOCALE}}/edit/article/<?= $article->id ?>" title="<?= $article->title ?>" class="group/item group/label block">
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
        <div class="px-4 py-3 border-2 border-gray-200 rounded-xl rounded-t-none border-t-0 group-hover/label:border-gray-300 transition">
            <h2 class="font-semibold text-base line-clamp-1"><?= $article->title ?></h2>
            <h3 class="line-clamp-1 text-gray-600 text-xs mb-1"><?= $article->getCategory()->title ?></h3>
            <div class="mb-2 text-gray-600 text-xs flex justify-between items-center flex-nowrap gap-2">
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
            <p class="line-clamp-3 text-sm"><?= mb_substr($article->short_text, 0, 500) ?></p>
        </div>
    </article>
</a>