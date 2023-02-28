<?php

use Tischmann\Atlantis\{CSRF, Locale, Template};

include __DIR__ . "/../header.php"

?>
<main class="md:container md:mx-auto">
    <form method="post" class="my-4 px-4">
        {{csrf}}
        <?php

        Template::echo(
            'admin/select-field',
            [
                'label' => Locale::get('category_locale'),
                'name' => 'locale',
                'id' => 'categoryLocale',
                'options' => Template::html(
                    'admin/locales-options',
                    ['locale' => $category->locale]
                ),
                'attr' => [
                    'data-token' => CSRF::generateToken(),
                    'data-id' => $category->id,
                ]
            ]
        );

        Template::echo(
            'admin/select-field',
            [
                'label' => Locale::get('category_parent'),
                'name' => 'parent_id',
                'id' => 'categoryParent',
                'options' => Template::html(
                    'admin/parent-category-options',
                    ['category' => $category]
                )
            ]
        );

        Template::echo(
            'admin/input-field',
            [
                'type' => 'text',
                'label' => Locale::get('category_title'),
                'name' => 'title',
                'value' => $category->title,
                'required' => true,
                'autocomplete' => false,
                'id' => 'categoryTitle',
            ]
        );

        Template::echo(
            'admin/input-field',
            [
                'type' => 'text',
                'label' => Locale::get('category_slug'),
                'name' => 'slug',
                'value' => $category->slug,
                'required' => true,
                'autocomplete' => false,
                'id' => 'categorySlug',
            ]
        );

        Template::echo(
            'admin/switch-field',
            [
                'label' => Locale::get('category_visible'),
                'name' => 'visible',
                'checked' => $category->visible,
                'id' => 'categoryVisible',
            ]
        );

        if ($category->children) {
            echo <<<HTML
            <div class="mb-4">
                <div class="h-full flex flex-col">
                    <div class="form-label inline-block mb-1">{{lang=category_children}}</div>
                    <div class="bg-sky-800 rounded-xl flex-grow p-4">
            HTML;

            Template::echo(
                'admin/category-children',
                [
                    'category' => $category
                ]
            );

            echo <<<HTML
                    </div>
                </div>
            </div>
            HTML;
        }
        ?>
        <div class="mb-4 flex gap-4 flex-wrap justify-evenly md:justify-end items-center">
            <?php
            if ($category->id) {
                $locale = getenv('APP_LOCALE');

                Template::echo(
                    'admin/delete-button',
                    [
                        'id' => "delete-category-{$category->id}",
                        'title' => Locale::get('warning'),
                        'message' => Locale::get('category_delete_confirm') . "? "
                            . Locale::get('category_delete_confirm_children') . "!",
                        'url' => "/{$locale}/category/delete/{$category->id}",
                        'redirect' => "/{$locale}/admin/categories",
                    ]
                );
            }
            ?>
            <?= Template::html('admin/cancel-button', ['href' => '/{{env=APP_LOCALE}}/admin/categories']) ?>
            <?= Template::html('admin/save-button') ?>
        </div>
    </form>
    <script src="/js/category.js" nonce="{{nonce}}" type="module" async></script>
    <script src="/js/orderCategories.js" nonce="{{nonce}}" type="module" async></script>
</main>