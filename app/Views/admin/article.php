<?php

use Tischmann\Atlantis\{Locale, Template};

include __DIR__ . "/../header.php";

?>
<main class="md:container md:mx-auto">
    <form method="post" class="m-4">
        {{csrf}}
        <div class="grid grid-cols-1 lg:grid-cols-3 md:gap-4">
            <div class="flex flex-col lg:col-span-2">
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
                        'selected' => $locale === $article->locale ?: getenv('APP_LOCALE')
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

                Template::echo(
                    'admin/select-field',
                    [
                        'label' => Locale::get('article_category'),
                        'name' => 'category_id',
                        'id' => 'articleCategory',
                        'options' => Template::html('admin/category-options', [
                            'locale' => $article->locale,
                            'article' => $article
                        ]),
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
            <textarea class="tinymce-editor" id="articleFullText" name="full_text" data-tinymce-textarea data-locale="{{env=APP_LOCALE}}" data-id="<?= $article->id ?>"><?= $article->full_text ?></textarea>
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
    <script src="/tinymce/tinymce.min.js" nonce="{{nonce}}"></script>
    <script nonce="{{nonce}}" type="module">
        import Atlantis from '/js/atlantis.js'

        const $ = new Atlantis()

        $.on(document.getElementById('articleLocale'), 'change', function() {
            $.fetch(`/admin/fetch/parent/categories`, {
                headers: {
                    'Content-Type': 'application/json'
                },
                body: {
                    locale: this.value,
                    article: <?= $article->id ?>
                },
                success: ({
                    html
                }) => {
                    document.getElementById(`articleCategory`).innerHTML = html
                }
            })
        })

        const textareaElement = document.getElementById(`articleFullText`)

        const useDarkMode = window.matchMedia(
            '(prefers-color-scheme: dark)'
        ).matches

        tinymce.init({
            language: textareaElement.dataset.locale,
            target: textareaElement,
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            editimage_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            height: 600,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_class: 'mceNonEditable',
            toolbar_mode: 'floating',
            contextmenu: 'link image table',
            image_caption: true,
            skin: useDarkMode ? 'oxide-dark' : 'oxide',
            content_css: useDarkMode ? 'dark' : 'default',
            image_advtab: true,
            image_class_list: [{
                    title: 'None',
                    value: 'm-4'
                },
                {
                    title: 'Rounded',
                    value: 'rounded-lg m-4'
                }
            ],
            images_upload_handler: (blobInfo, progress) =>
                new Promise((resolve, reject) => {
                    const formData = new FormData()

                    formData.append('file', blobInfo.blob(), blobInfo.filename())

                    const xhr = new XMLHttpRequest()

                    xhr.withCredentials = true

                    xhr.open(
                        'POST',
                        `/upload/article/image/${textareaElement.dataset.id}`
                    )

                    xhr.setRequestHeader('Accept', 'application/json')

                    xhr.upload.onprogress = (e) => {
                        progress((e.loaded / e.total) * 100)
                    }

                    xhr.onload = () => {
                        if (xhr.status === 403) {
                            reject({
                                message: 'HTTP Error: ' + xhr.status,
                                remove: true
                            })
                            return
                        }

                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP Error: ' + xhr.status)
                            return
                        }

                        const json = JSON.parse(xhr.responseText)

                        if (!json || typeof json.thumb_location != 'string') {
                            reject('Invalid JSON: ' + xhr.responseText)
                            return
                        }

                        resolve(json.thumb_location)
                    }

                    xhr.onerror = () => {
                        reject(
                            'Image upload failed due to a XHR Transport error. Code: ' +
                            xhr.status
                        )
                    }

                    xhr.send(formData)
                })
        })
    </script>
</main>