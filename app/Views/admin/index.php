<?php

use App\Models\{Article, Category, User};

include __DIR__ . "/../header.php"

?>
<main class="md:container md:mx-auto px-4">
    <div class="flex flex-wrap items-center gap-4 my-4">
        <?php

        $buttons = [
            [
                'icon' => 'fas fa-users',
                'label' => '{{lang=users}}',
                'count' => User::query()->count(),
                'href' => '/{{env=APP_LOCALE}}/admin/users'
            ],
            [
                'icon' => 'fas fa-sitemap',
                'label' => '{{lang=categories}}',
                'count' => Category::query()->count(),
                'href' => '/{{env=APP_LOCALE}}/admin/categories'
            ],
            [
                'icon' => 'fas fa-newspaper',
                'label' => '{{lang=articles}}',
                'count' => Article::query()->count(),
                'href' => '/{{env=APP_LOCALE}}/admin/articles'
            ]
        ];

        foreach ($buttons as $button) {
            echo <<<HTML
            <a href="{$button['href']}" title="{$button['label']}" aria-label="{$button['label']}" class=" bg-sky-800 text-white shadow rounded-lg px-4 py-3 flex justify-between items-center gap-4 hover:bg-sky-700 text-xl font-medium transition-all ease-in-out">
                <span class="truncate drop-shadow"><i class="{$button['icon']} mr-2"></i>{$button['label']}</span>
                <span class="font-bold countup bg-sky-900 rounded-lg px-2">{$button['count']}</span>
            </a>
            HTML;
        }


        ?>
    </div>
</main>