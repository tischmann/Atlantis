<?php

use Tischmann\Atlantis\{Locale, Template};

include __DIR__ . "/../header.php"

?>
<main class="md:container md:mx-auto">
    <form method="post" class="m-4">
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
                    ['locale' => $category->locale ?: getenv('APP_LOCALE')]
                ),
            ]
        );

        Template::echo(
            'admin/select-field',
            [
                'label' => Locale::get('category_parent'),
                'name' => 'parent_id',
                'id' => 'categoryParent',
                'options' => Template::html(
                    'admin/category-options',
                    [
                        'locale' => $category->locale ?: getenv('APP_LOCALE'),
                        'category' => $category
                    ]
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
            <div class="mb-4 order-container">
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
            <script nonce="{{nonce}}" type="module">
                import Atlantis, { Sortable } from '/js/atlantis.js'

                const $ = new Atlantis()

                document
                    .querySelectorAll('.order-container [data-atlantis-categories]')
                    .forEach((container) => {
                        new Sortable(container, {
                            ondragend: () => {
                                const children = []

                                container
                                    .querySelectorAll('li[data-id]')
                                    .forEach((el, index) => children.push(el.dataset.id))

                                $.fetch(`/categories/order`, {
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: {
                                        children
                                    }
                                })
                            }
                        })
                    })
            </script>
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
    <script nonce="{{nonce}}" type="module">
        import Atlantis from '/js/atlantis.js'

        const $ = new Atlantis()

        $.on(document.getElementById('categoryLocale'), 'change', function() {
            $.fetch(`/admin/fetch/parent/categories`, {
                headers: {
                    'Content-Type': 'application/json'
                },
                body: {
                    locale: this.value,
                    category: <?= $category->id ?>
                },
                success: ({
                    html
                }) => {
                    document.getElementById(`categoryParent`).innerHTML = html
                }
            })
        })
    </script>
</main>