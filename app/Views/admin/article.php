<?php

use App\Models\{Article, Category};

use Tischmann\Atlantis\{Locale, Template};

include __DIR__ . "/../header.php";

?>
<main class="md:container md:mx-auto">
    <div class="m-4 mb-8">
        <?php include __DIR__ . "/../breadcrumbs.php" ?>
    </div>
    <form method="post" class="m-4">
        {{csrf}}
        <div class="grid grid-cols-1 lg:grid-cols-2 md:gap-4">
            <div class="flex flex-col">
                <?php

                Template::echo(
                    'admin/switch-field',
                    [
                        'label' => Locale::get('article_visible'),
                        'name' => 'visible',
                        'checked' => $article->visible,
                        'id' => 'articleVisible',
                    ]
                );

                $options = '';

                foreach (Locale::available() as $locale) {
                    $label = Locale::get('locale_' . $locale);

                    $options .= Template::html('admin/option', [
                        'value' => $locale,
                        'label' => $label,
                        'title' => $label,
                        'selected' => $locale === $article->locale
                    ]);
                }

                Template::echo(
                    'admin/select-field',
                    [
                        'label' => Locale::get('article_locale'),
                        'name' => 'locale',
                        'id' => 'articleLocale',
                        'options' => $options
                    ]
                );

                $options = '';


                $categories = Category::fill(Category::query());

                foreach ([new Category(), ...$categories] as $category) {
                    assert($category instanceof Category);

                    $options .= Template::html('admin/option', [
                        'value' => $category->id ? $category->id : '',
                        'label' => $category->title,
                        'title' => $category->title,
                        'selected' => $category->id === $article->category_id
                    ]);
                }

                Template::echo(
                    'admin/select-field',
                    [
                        'label' => Locale::get('article_category'),
                        'name' => 'category_id',
                        'id' => 'articleCategory',
                        'options' => $options
                    ]
                );

                Template::echo(
                    'admin/input-field',
                    [
                        'type' => 'text',
                        'label' => Locale::get('article_title'),
                        'name' => 'title',
                        'value' => $article->title,
                        'required' => true,
                        'autocomplete' => false,
                        'id' => 'articleTitle',
                    ]
                );

                Template::echo(
                    'admin/textarea-field',
                    [
                        'label' => Locale::get('article_short_text'),
                        'name' => 'short_text',
                        'id' => 'articleShortText',
                        'flex' => true,
                        'rows' => 3,
                        'value' => $article->short_text
                    ]
                );

                ?>
            </div>
            <?php
            Template::echo(
                'admin/load-image',
                [
                    'value' => $article->image,
                    'name' => 'image',
                    'label' => Locale::get('article_image'),
                    'src' => $article->id ? "/images/articles/{$article->id}/{$article->image}" : "/placeholder.svg",
                    'width' => '',
                    'height' => '',
                    'url' => "/upload/article/image/{$article->id}"
                ]
            );
            ?>
        </div>
        <div class="mb-4">
            <label for="articleFullText" class="form-label inline-block mb-1">{{lang=article_full_text}}</label>
            <textarea class="tinymce-editor" id="articleFullText" name="full_text" data-tinymce-textarea data-token="{{csrf-token}}" data-locale="{{env=APP_LOCALE}}" data-id="<?= $article->id ?>"><?= $article->full_text ?></textarea>
        </div>
        <?php

        Template::echo(
            'admin/textarea-field',
            [
                'label' => Locale::get('article_tags'),
                'name' => 'tags',
                'id' => 'articleTags',
                'flex' => true,
                'rows' => 3,
                'value' => implode(", ", $article->tags)
            ]
        );

        ?>
        <div class="mb-4 flex gap-4 flex-wrap justify-evenly md:justify-end items-center">
            <?php
            if ($article->id) {
                $locale = getenv('APP_LOCALE');

                Template::echo(
                    'admin/delete-button',
                    [
                        'id' => "delete-article-{$article->id}",
                        'title' => Locale::get('warning'),
                        'message' => Locale::get('article_delete_confirm') . "?",
                        'url' => "/{$locale}/article/delete/{$article->id}",
                        'redirect' => "/{$locale}/admin/articles",
                    ]
                );
            }
            ?>
            <?= Template::html('admin/cancel-button', ['href' => '/{{env=APP_LOCALE}}/admin/articles']) ?>
            <?= Template::html('admin/save-button') ?>
        </div>
    </form>
    <script src="/tinymce/tinymce.min.js" nonce="{{nonce}}" async></script>
    <script src="/js/articleAdmin.js" nonce="{{nonce}}" type="module" async></script>
    <script src="/js/imageUpload.js" nonce="{{nonce}}" type="module" async></script>
</main>