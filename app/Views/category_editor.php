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
<link rel="stylesheet" href="/css/jquery-ui.min.css" media="screen">
<style>
    .ui-state-highlight {
        min-height: 3.5rem;
        border-radius: .5rem;
    }
</style>
<script src="/js/jquery.min.js" nonce="{{nonce}}"></script>
<script src="/js/jquery-ui.min.js" nonce="{{nonce}}"></script>
<main class="md:container mx-8 md:mx-auto">
    <form class="mb-8" data-category="<?= $category->id ?>">
        <div class="mb-8">
            <?php
            Template::echo(
                template: 'input_field',
                args: [
                    'type' => 'text',
                    'name' => 'title',
                    'label' => get_str('category_title'),
                    'value' => $category->title,
                    'required' => true,
                    'autocomplete' => 'off'
                ]
            );
            ?>
        </div>
        <div class="mb-8">
            <?php
            Template::echo(
                template: 'input_field',
                args: [
                    'type' => 'text',
                    'name' => 'slug',
                    'label' => get_str('category_slug'),
                    'value' => $category->slug,
                    'required' => true,
                    'autocomplete' => 'off'
                ]
            );
            ?>
        </div>
        <div class="mb-8">
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
        <div class="mb-8">
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
            <div class="mb-8">
            HTML;

            Template::echo(
                template: 'container_field',
                args: [
                    'label' => get_str('category_children'),
                    'content' => Template::html(
                        template: 'categories_list_list',
                        args: [
                            'categories' => $category->children
                        ]
                    ),
                ]
            );

            echo <<<HTML
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
<script nonce="{{nonce}}" type="module">
    import Category from '/js/atlantis.category.min.js'
    new Category()
</script>