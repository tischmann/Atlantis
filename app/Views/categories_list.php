<?php

use App\Models\{Category};

use Tischmann\Atlantis\{Template};
?>
<link rel="stylesheet" href="/css/jquery-ui.min.css" media="screen">
<style>
    .ui-state-highlight {
        min-height: 3.75rem;
        border-radius: .5rem;
    }
</style>
<script src="/js/jquery.min.js" nonce="{{nonce}}"></script>
<script src="/js/jquery-ui.min.js" nonce="{{nonce}}"></script>
<main class="md:container mx-4 md:mx-auto mb-4">
    <div class="mb-4 flex flex-col sm:flex-row gap-4">
        <?php
        Template::echo(
            template: 'select_field',
            args: [
                'name' => 'locale',
                'title' => get_str('article_locale'),
                'options' => $locale_options
            ]
        );
        ?>
    </div>
    <a href="/{{env=APP_LOCALE}}/new/category" title="{{lang=category_new}}" class="mb-4 flex items-center justify-center p-3 rounded-lg bg-sky-600 hover:bg-sky-500 text-white shadow hover:shadow-lg transition">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </a>
    <div class="hidden sm:grid grid-cols-1 sm:grid-cols-6 gap-2 px-4 py-3 text-white dark:text-gray-800 uppercase bg-gray-500 dark:bg-gray-400 rounded-lg transition mb-2 text-sm font-semibold">
        <div class="col-span-1 sm:col-span-2 text-ellipsis overflow-hidden">{{lang=category_title}}</div>
        <div class="text-ellipsis overflow-hidden">{{lang=category_slug}}</div>
        <div class="col-span-1 sm:col-span-2 text-ellipsis overflow-hidden">{{lang=category_children}}</div>
        <div class="text-ellipsis overflow-hidden text-right">{{lang=category_actions}}</div>
    </div>
    <ul id="categories-list" class="grid grid-cols-1 gap-2">
        <?php

        foreach ($categories as $category) {
            assert($category instanceof Category);

            Template::echo(
                template: 'category_list_item',
                args: [
                    'category' => $category
                ]
            );
        }

        ?>
    </ul>
</main>
<script src="/js/atlantis.categories.min.js" nonce="{{nonce}}" type="module"></script>
<script src="/js/categories.list.min.js" nonce="{{nonce}}" type="module"></script>