<?php

use App\Models\{Article, Category, User};

include __DIR__ . "/../header.php"

?>
<main class="container mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 m-4 text-xl font-bold text-gray-700">
        <div class="bg-white shadow-md rounded-lg p-4 border-t-4 border-t-red-600 flex justify-between items-center uppercase">
            <span>{{lang=dashboard_total_users}}</span><span><?= User::query()->count() ?></span>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4 border-t-4 border-t-sky-600 flex justify-between items-center uppercase">
            <span>{{lang=dashboard_total_categories}}</span><span><?= Category::query()->count() ?></span>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4 border-t-4 border-t-green-600 flex justify-between items-center uppercase">
            <span>{{lang=dashboard_total_articles}}</span><span><?= Article::query()->count() ?></span>
        </div>
    </div>
</main>