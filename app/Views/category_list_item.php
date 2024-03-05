<?php

use App\Models\{Category};

assert($category instanceof Category);

$category->children = $category->fetchChildren();

?>
<li class="block grow transition border-2 border-gray-200 rounded-lg hover:border-gray-300" data-id="<?= $category->id ?>">
    <article class="grid grid-cols-1 sm:grid-cols-6 gap-2 p-2">
        <h2 class="flex flex-nowrap items-center gap-2 col-span-1 sm:col-span-2 font-semibold text-base line-clamp-1">
            <span class="block px-0 sm:px-2"><?= $category->title ?></span>
        </h2>
        <h3 class="flex flex-nowrap items-center line-clamp-1 text-sm"><?= $category->slug ?>
        </h3>
        <div class="col-span-1 sm:col-span-2 text-gray-800 text-sm flex flex-wrap items-center gap-2">
            <?php
            foreach ($category->children as $child) {
                assert($child instanceof Category);

                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/edit/category/{$child->id}" title="{{lang=edit}}" class="flex items-center justify-center whitespace-nowrap bg-gray-100 px-4 py-2 rounded-md hover:bg-gray-200">$child->title</a>
                HTML;
            }
            ?>
        </div>
        <div class="flex flex-nowrap items-center justify-start sm:justify-end gap-2">
            <div class="bg-gray-100 rounded-md p-2 cursor-pointer hover:bg-gray-200 transition" title="{{lang=sort_up}}" data-sort="up">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                </svg>
            </div>
            <div class="bg-gray-100 rounded-md p-2 cursor-pointer hover:bg-gray-200 transition" title="{{lang=sort_down}}" data-sort="down">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
            <a href="/{{env=APP_LOCALE}}/edit/category/<?= $category->id ?>" title="{{lang=edit}}" class="bg-gray-100 rounded-md p-2 cursor-pointer hover:bg-gray-200 transition ml-auto sm:ml-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                </svg>
            </a>
        </div>
    </article>
</li>