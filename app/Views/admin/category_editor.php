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
                template: 'fields/input_field',
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
                template: 'fields/input_field',
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
                template: 'fields/select_field',
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
                template: 'fields/select_field',
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
                template: 'fields/container_field',
                args: [
                    'label' => get_str('category_children'),
                    'content' => Template::html(
                        template: 'admin/categories_list_list',
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
                <button class="flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=delete}}" data-delete>{{lang=delete}}</button>
                <button class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=save}}" data-save>{{lang=save}}</button>
                HTML;
            } else {
                echo <<<HTML
                <button class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=add}}" data-add>{{lang=add}}</button>
                HTML;
            }
            ?>
        </div>
    </form>
</main>
<script nonce="{{nonce}}" type="module">
    import Select from '/js/atlantis.select.min.js'

    const form = document.querySelector('form[data-category]')

    ;
    ['locale', 'parent_id'].forEach((name) => {
        new Select(document.querySelector(`select[name="${name}"]`))
    })

    document
        .querySelector('button[data-save]')
        ?.addEventListener('click', () => {
            fetch(`/category/${form.dataset.category}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            }).then((response) => {
                response.json().then((json) => {
                    alert(json.message)
                    window.location.reload()
                })
            })
        })

    document
        .querySelector('button[data-add]')
        ?.addEventListener('click', () => {
            fetch(`/category`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            }).then((response) => {
                response.json().then((json) => {
                    alert(json.message)
                    window.location.href = `/edit/category/${json.id}`
                })
            })
        })

    document
        .querySelector('button[data-delete]')
        ?.addEventListener('click', (event) => {
            if (!confirm(`{{lang=confirm_delete_category}}`)) return

            fetch(`/category/${form.dataset.category}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            }).then((response) => {
                response.json().then((json) => {
                    alert(json.message)
                    window.location.href = '/edit/categories'
                })
            })
        })
</script>