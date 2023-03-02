<?php

use App\Models\Article;

use Tischmann\Atlantis\Date;

?>
<div class="block w-full rounded-lg <?= $article->visible ? 'bg-sky-800' : 'bg-gray-600' ?> text-white shadow-lg">
    <div class="relative text-sky-800 text-xs">
        <img class="rounded-t-lg <?= !$article->visible ? 'grayscale' : '' ?> w-full" data-src="<?= $article->image_url ?>" width="<?= Article::THUMB_WIDTH ?>" height="<?= Article::THUMB_HEIGHT ?>" alt="<?= $article->title ?>" src="/images/placeholder.svg" data-atlantis-lazy-image />
        <div class="absolute flex top-0 inset-x-0 gap-4 p-4 flex-wrap">
            <span class="block px-3 py-2 bg-white uppercase rounded-md shadow-md outline-none ring-0 transition duration-150 ease-in-out font-semibold"><?= $article->category->title ?></span>
            <span class="block px-3 py-2 bg-white uppercase rounded-md shadow-md outline-none ring-0 transition duration-150 ease-in-out font-semibold"><?= Date::localeFormat($article->created_at, getenv('APP_LOCALE'), 'd MMMM y, kk:mm')  ?></span>
        </div>
        <div class="absolute flex bottom-0 inset-x-0 gap-4 p-4 justify-end">
            <span class="px-3 py-2 rounded-md bg-white shadow-md font-semibold"><i class="fas fa-eye mr-2"></i><?= $article->views ?></span>
            <span class="px-3 py-2 rounded-md bg-white shadow-md font-semibold"><i class="fas fa-star mr-2"></i><?= $article->rating ?></span>
        </div>
    </div>
    <div class="p-6">
        <h5 class="mb-3 text-xl font-semibold leading-tight truncate"><?= $article->title ?></h5>
        <p class="mb-4 text-sm line-clamp-3"><?= $article->short_text ?></p>
        <div class="flex gap-6 flex-wrap">
            <a href="/{{env=APP_LOCALE}}/article/<?= $article->id ?>" aria-label="{{lang=show}}" class="inline-block flex-grow text-center px-3 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded-md shadow-md hover:bg-pink-500 hover:shadow-lg focus:bg-pink-500 active:bg-pink-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out" data-te-ripple-init data-te-ripple-color="light" target="_blank">{{lang=show}}</a>
            <a href="/{{env=APP_LOCALE}}/edit/article/<?= $article->id ?>" aria-label="{{lang=edit}}" class="inline-block flex-grow text-center px-3 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded-md shadow-md hover:bg-pink-500 hover:shadow-lg focus:bg-pink-500 active:bg-pink-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out" data-te-ripple-init data-te-ripple-color="light">{{lang=edit}}</a>
        </div>
    </div>
</div>