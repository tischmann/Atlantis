<?php

use App\Models\{Category};

assert($category instanceof Category);

$category->children = $category->fetchChildren();

?>
<li class="block grow transition bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600" data-id="<?= $category->id ?>">
    <article class="grid grid-cols-1 sm:grid-cols-6 gap-2 p-2">
        <h2 class="m-0 flex flex-nowrap items-center gap-2 col-span-1 sm:col-span-2 font-semibold text-base line-clamp-1">
            <span class="block px-0 sm:px-2"><?= $category->title ?></span>
        </h2>
        <h3 class="m-0 flex flex-nowrap items-center line-clamp-1 text-sm"><?= $category->slug ?>
        </h3>
        <div class="col-span-1 sm:col-span-2 text-sm font-medium flex flex-wrap items-center gap-2">
            <?php

            foreach ($category->children as $child) {
                assert($child instanceof Category);

                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/edit/category/{$child->id}" title="{{lang=edit}}" class="flex items-center justify-center whitespace-nowrap bg-gray-300 text-gray-800 hover:underline dark:bg-gray-500 dark:text-white px-4 py-2 rounded-md">$child->title</a>
                HTML;
            }
            ?>
        </div>
        <div class="flex flex-nowrap items-center justify-start sm:justify-end gap-2">
            <a href="/{{env=APP_LOCALE}}/edit/category/<?= $category->id ?>" title="{{lang=edit}}" class="grow sm:grow-0 flex items-center justify-center bg-sky-600 rounded-md p-2 cursor-pointer text-white hover:bg-sky-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </a>
        </div>
    </article>
</li>