<?php

use App\Models\{Article};

use Tischmann\Atlantis\{Template};

?>
<main class="md:container mx-4 md:mx-auto mb-4">
    <div class="mb-4 flex flex-col sm:flex-row gap-4">
        <?php
        Template::echo(
            template: 'select_field',
            args: [
                'name' => 'order',
                'title' => get_str('order'),
                'options' => $order_options
            ]
        );

        Template::echo(
            template: 'select_field',
            args: [
                'name' => 'direction',
                'title' => get_str('direction'),
                'options' => $direction_options
            ]
        );
        ?>
    </div>
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

        Template::echo(
            template: 'select_field',
            args: [
                'name' => 'category_id',
                'title' => get_str('article_category'),
                'options' => $category_options
            ]
        );

        Template::echo(
            template: 'select_field',
            args: [
                'name' => 'visible',
                'title' => get_str('article_visible'),
                'options' => $visible_options
            ]
        );

        Template::echo(
            template: 'select_field',
            args: [
                'name' => 'fixed',
                'title' => get_str('article_fixed'),
                'options' => $fixed_options
            ]
        );

        Template::echo(
            template: 'select_field',
            args: [
                'name' => 'moderated',
                'title' => get_str('article_moderated'),
                'options' => $moderated_options
            ]
        );
        ?>
    </div>
    <?php

    if ($articles) {
        echo <<<HTML
        <a href="/{{env=APP_LOCALE}}/new/article" title="{{lang=article_new}}" class="mb-4 flex items-center justify-center p-3 rounded-lg bg-sky-600 hover:bg-sky-500 shadow hover:shadow-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </a>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4">
        HTML;

        foreach ($articles as $article) {
            assert($article instanceof Article);
            include  'article_list_item.php';
        }

        echo <<<HTML
        </div>
        HTML;
    } else {
        include "articles_not_found.php";
    }

    ?>
    <div class="my-4"><?php include 'pagination.php'; ?></div>
</main>
<script nonce="{{nonce}}" type="module">
    (function() {
        [
            'category_id',
            'visible',
            'locale',
            'fixed',
            'order',
            'direction',
            'moderated'
        ].forEach((field) => {
            document.querySelector(`select[name="${field}"]`).select({
                onchange: (value) => {
                    const url = new URL(window.location.href)
                    url.searchParams.set(field, value)
                    window.location.href = url.toString()
                }
            })
        })
    })()
</script>