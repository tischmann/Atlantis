<?php

use App\Models\User;

use Tischmann\Atlantis\Date;

?>
<div class="rounded-lg shadow-lg bg-white dark:bg-sky-800 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
    <div class="text-sky-800 text-xs h-56 md:h-full w-full rounded-t-lg md:rounded-none md:rounded-l-lg bg-cover bg-center bg-[url('/placeholder.svg')] min-h-[180px]" data-bg="<?= $article->image_url ?>" data-atlantis-lazy-image>
        <!-- <div class="absolute flex top-0 inset-x-0 gap-4 p-4 flex-wrap">
            <span class="block px-3 py-2 bg-white uppercase rounded-md shadow-md outline-none ring-0 transition duration-150 ease-in-out font-semibold"><?= $article->category->title ?></span>
            <span class="block px-3 py-2 bg-white uppercase rounded-md shadow-md outline-none ring-0 transition duration-150 ease-in-out font-semibold"><?= Date::getElapsed($article->created_at)  ?></span>
        </div>
        <div class="absolute flex bottom-0 inset-x-0 gap-4 p-4 justify-end">
            <span class="px-3 py-2 rounded-lg bg-white shadow-md font-semibold"><i class="fas fa-eye mr-2"></i><?= $article->views ?></span>
            <span class="px-3 py-2 rounded-lg bg-white shadow-md font-semibold"><i class="fas fa-star mr-2"></i><?= $article->rating ?></span>
        </div> -->
    </div>
    <div class="p-6 flex-grow md:col-span-2 lg:col-span-3 xl:col-span-4 2xl:col-span-5">
        <h5 class="text-xl font-medium mb-2 truncate"><?= $article->title ?></h5>
        <div class="text-sm mb-4"><?= $article->short_text ?></div>
        <div class="flex gap-6 flex-wrap">
            <a href="/{{env=APP_LOCALE}}/article/<?= $article->id ?>" aria-label="{{lang=show}}" class="inline-block flex-grow text-center px-3 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded-md shadow-md hover:bg-pink-500 hover:shadow-lg focus:bg-pink-500 active:bg-pink-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out" data-te-ripple-init data-te-ripple-color="light" target="_blank">{{lang=show}}</a>
            <?php

            if (User::current()->isAdmin()) {
                echo <<<HTML
            <a href="/{{env=APP_LOCALE}}/edit/article/<?= $article->id ?>" aria-label="{{lang=edit}}" class="inline-block flex-grow text-center px-3 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded-md shadow-md hover:bg-pink-500 hover:shadow-lg focus:bg-pink-500 active:bg-pink-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out" data-te-ripple-init data-te-ripple-color="light">{{lang=edit}}</a>
            HTML;
            }

            ?>
        </div>
    </div>
</div>