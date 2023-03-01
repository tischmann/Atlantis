<?php

use App\Models\Article;

use Tischmann\Atlantis\Date;

assert($article instanceof Article);

$date = Date::getElapsed($article->created_at);

?>
<a href="/{{env=APP_LOCALE}}/article/<?= $article->id ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 rounded-lg bg-white shadow-lg dark:bg-gray-700 mb-4 relative cursor-pointer">
    <div class="h-72 md:h-auto w-full rounded-t-lg md:rounded-none md:rounded-l-lg bg-cover bg-center bg-[url(/images/placeholder.svg)]" data-bg="<?= $article->image_url ?>" data-atlantis-lazy-image></div>
    <div class="p-6 lg:col-span-2 xl:col-span-3">
        <h5 class="mb-2 text-xl font-medium "><?= $article->title ?></h5>
        <p class="mb-4 text-base line-clamp-3 md:line-clamp-4 lg:line-clamp-5 xl:line-clamp-6"><?= $article->short_text ?></p>
        <p class="text-xs"><?= $date ?></p>
    </div>
</a>