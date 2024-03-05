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
            <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=category_title}}</label>
            <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="title" id="title" name="title" value="<?= $category->title ?>" required>
        </div>
        <div class="mb-8 relative">
            <label for="slug" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=category_slug}}</label>
            <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="slug" id="slug" name="slug" value="<?= $category->slug ?>" required>
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
                <div class="rounded-lg border-2 border-gray-200 select-none">
                    <div class="rounded-lg border-[16px] border-white relative">
                            <div class="hidden sm:grid grid-cols-1 sm:grid-cols-6 gap-2 px-4 py-3 text-gray-600 bg-gray-100 rounded-lg transition mb-4 text-sm font-semibold">
                                <div class="col-span-1 sm:col-span-2 text-ellipsis overflow-hidden">{{lang=category_title}}</div>
                                <div class="text-ellipsis overflow-hidden">{{lang=category_slug}}</div>
                                <div class="col-span-1 sm:col-span-2 text-ellipsis overflow-hidden">{{lang=category_children}}</div>
                                <div class="text-ellipsis overflow-hidden text-right">{{lang=category_actions}}</div>
                            </div>
                            <ul id="categories-list" class="flex flex-wrap gap-4 font-semibold text-gray-600">
            HTML;

            foreach ($category->children as $child) {
                assert($child instanceof Category);

                Template::echo(
                    template: 'category_list_item',
                    args: [
                        'category' => $child
                    ]
                );
            }

            echo <<<HTML
                        </ul>
                    </div>
                </div>
                <label class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=category_children}}</label>
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
<script src="/js/atlantis.categories.min.js" nonce="{{nonce}}" type="module"></script>
<script src="/js/category.editor.min.js" nonce="{{nonce}}" type="module"></script>