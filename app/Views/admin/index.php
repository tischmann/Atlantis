<?php

use App\Models\{Article, Category, User};

include __DIR__ . "/../header.php"

?>
<main class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 my-4 text-xl font-bold text-gray-700">
        <a href="/{{env=APP_LOCALE}}/admin/users" title="{{lang=categories}}" aria-label="{{lang=users}}" class="bg-gray-100 shadow-md rounded-lg p-4 flex justify-between items-center uppercase hover:shadow-xl hover:text-sky-600">
            <span class="truncate"><i class="fas fa-users mr-2"></i>{{lang=users}}</span>
            <span><?= User::query()->count() ?></span>
        </a>
        <a href="/{{env=APP_LOCALE}}/admin/categories" aria-label="{{lang=categories}}" title="{{lang=categories}}" class="bg-gray-100 shadow-md rounded-lg p-4 flex justify-between items-center uppercase hover:shadow-xl hover:text-sky-600">
            <span class="truncate"><i class="fas fa-sitemap mr-2"></i>{{lang=categories}}</span>
            <span><?= Category::query()->count() ?></span>
        </a>
        <a href="/{{env=APP_LOCALE}}/admin/articles" title="{{lang=articles}}" aria-label="{{lang=articles}}" class="bg-gray-100 shadow-md rounded-lg p-4 flex justify-between items-center uppercase hover:shadow-xl hover:text-sky-600">
            <span class="truncate"><i class="fas fa-newspaper mr-2"></i>{{lang=articles}}</span>
            <span><?= Article::query()->count() ?></span>
        </a>
    </div>
</main>