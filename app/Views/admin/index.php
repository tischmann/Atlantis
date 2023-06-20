<?php

use App\Models\{Article, Category, User};

use Tischmann\Atlantis\{Locale};

include __DIR__ . "/../header.php"

?>
<main class="md:container md:mx-auto">
    <div class="m-4">
        <div class="flex flex-wrap gap-4">
            <?php

            $buttons = [
                [
                    'icon' => 'fas fa-users',
                    'label' => '{{lang=users}}',
                    'count' => User::query()->count(),
                    'href' => '/{{env=APP_LOCALE}}/admin/users'
                ],
                [
                    'icon' => 'fas fa-earth-americas',
                    'label' => '{{lang=locales}}',
                    'count' => count(Locale::available()),
                    'href' => '/{{env=APP_LOCALE}}/admin/locales'
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
                <a href="{$button['href']}" title="{$button['label']}" aria-label="{$button['label']}" class=" bg-sky-800 text-white shadow rounded-lg px-4 p-4 flex justify-between items-center gap-4 hover:bg-sky-700 text-xl font-medium transition-all ease-in-out w-full md:w-auto">
                    <span class="truncate drop-shadow flex items-center gap-4"><i class="{$button['icon']} text-[64px]"></i>{$button['label']}</span>
                    <span class="text-[32px] font-bold bg-sky-900 rounded-lg p-4 min-w-[60px] text-center">{$button['count']}</span>
                </a>
                HTML;
            }


            ?>
        </div>
    </div>
</main>