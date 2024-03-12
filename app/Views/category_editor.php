<?php

use App\Models\{Category};

use Tischmann\Atlantis\{DateTime, Template};

assert($category instanceof Category);

if (!$category->exists()) {
    $category->created_at = new DateTime();
    $category->locale = getenv('APP_LOCALE');
}

$category->children = $category->fetchChildren();

?>
<main class="md:container mx-8 md:mx-auto">
    <form class="mb-8" data-category="<?= $category->id ?>">
        <div class="mb-8 relative">
            <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 px-1">{{lang=category_title}}</label>
            <input class="py-2 px-3 outline-none border-2 border-gray-200 dark:border-gray-600 rounded-lg w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:border-sky-600 transition" aria-label="title" id="title" name="title" value="<?= $category->title ?>" required>
        </div>
        <div class="mb-8 relative">
            <label for="slug" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 px-1">{{lang=category_slug}}</label>
            <input class="py-2 px-3 outline-none border-2 border-gray-200 dark:border-gray-600 rounded-lg w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:border-sky-600 transition" aria-label="slug" id="slug" name="slug" value="<?= $category->slug ?>" required>
        </div>
        <div class="mb-8 relative">
            <?php
            Template::echo(
                template: 'select_field',
                args: [
                    'name' => 'locale',
                    'title' => get_str('category_locale'),
                    'options' => $locale_options
                ]
            );
            ?>
        </div>
        <div class="mb-8 relative">
            <?php
            Template::echo(
                template: 'select_field',
                args: [
                    'name' => 'parent_id',
                    'title' => get_str('category_parent_id'),
                    'options' => $parent_options
                ]
            );
            ?>
        </div>

        <?php
        if ($category->children) {
            echo <<<HTML
            <div class="mb-8 relative">
                <div class="rounded-lg border-2 border-gray-200 dark:border-gray-600 select-none">
                    <div class="rounded-lg border-[16px] border-white dark:border-gray-800 relative">
            HTML;

            $categories = $category->children;

            include 'categories_list_list.php';

            echo <<<HTML
                    </div>
                </div>
                <label class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 px-1">{{lang=category_children}}</label>
            </div>
            HTML;
        }

        ?>
        <div class="flex flex-col gap-4">
            <?php
            if ($category->exists()) {
                echo <<<HTML
                <button id="delete-category" class="flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=delete}}" data-confirm="{{lang=confirm_delete_category}}">{{lang=delete}}</button>
                <button id="save-category" class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=save}}">{{lang=save}}</button>
                HTML;
            } else {
                echo <<<HTML
                <button id="add-category" class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=add}}">{{lang=add}}</button>
                HTML;
            }
            ?>
        </div>
    </form>
</main>
<link rel="stylesheet" href="/css/jquery-ui.min.css" media="screen">
<script src="/js/jquery.min.js" nonce="{{nonce}}"></script>
<script src="/js/jquery-ui.min.js" nonce="{{nonce}}"></script>
<script src="/js/atlantis.categories.min.js" nonce="{{nonce}}" type="module"></script>
<script src="/js/category.editor.min.js" nonce="{{nonce}}" type="module"></script>