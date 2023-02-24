<?php

use App\Models\{Article, Category};

use Tischmann\Atlantis\{Locale, Template};

include __DIR__ . "/../header.php";

?>
<main class="md:container md:mx-auto text-black dark:text-white">
    <form method="post" class="p-4">
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
            <div class="mb-4">
                <input type="hidden" value="<?= $article->image ?>" name="image" id="articleImageInput">
                <input type='file' id="articleImageFile" class="hidden" aria-label="{{lang=article_image}}">
                <img src="/images/articles/<?= $article->id ?>/<?= $article->image ?>" id="articleImage" width="" height="" alt="<?= $article->title ?>" class="rounded w-full object-cover">
                <button type="button" data-te-ripple-init data-te-ripple-color="light" id="imageDeleteButton" class="mt-4 w-full block text-center flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out">
                    {{lang=delete_image}}
                </button>
            </div>
        </div>
        <div class="mb-4">
            <label for="articleFullText" class="form-label inline-block mb-1">{{lang=article_full_text}}</label>
            <textarea class="tinymce-editor" id="articleFullText" name="full_text"><?= $article->full_text ?></textarea>
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
    <script nonce="{{nonce}}">
        let csrf = `{{csrf-token}}`

        const imageUploadHandler = (blobInfo, progress) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();

            xhr.withCredentials = true;

            xhr.open('POST', `/upload/article/image/<?= $article->id ?>`);

            xhr.setRequestHeader('Accept', 'application/json');

            xhr.setRequestHeader('X-Csrf-Token', csrf);

            xhr.upload.onprogress = (e) => {
                progress(e.loaded / e.total * 100);
            };

            xhr.onload = () => {
                if (xhr.status === 403) {
                    reject({
                        message: 'HTTP Error: ' + xhr.status,
                        remove: true
                    });
                    return;
                }

                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }

                console.log(xhr.responseText)

                const json = JSON.parse(xhr.responseText);

                if (!json || typeof json.thumb_location != 'string') {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                csrf = json.csrf

                resolve(json.thumb_location);
            };

            xhr.onerror = () => {
                reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };

            const formData = new FormData();

            formData.append('file', blobInfo.blob(), blobInfo.filename());

            xhr.send(formData);
        });

        const useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

        const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;

        tinymce.init({
            language: '<?= getenv('APP_LOCALE') ?>',
            selector: 'textarea.tinymce-editor',
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            editimage_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            height: 800,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_class: 'mceNonEditable',
            toolbar_mode: 'floating',
            contextmenu: 'link image table',
            image_caption: true,
            skin: useDarkMode ? 'oxide-dark' : 'oxide',
            content_css: useDarkMode ? 'dark' : 'default',
            image_advtab: true,
            images_upload_handler: imageUploadHandler
        });

        const img = document.getElementById('articleImage')

        const file = document.getElementById('articleImageFile')

        const input = document.getElementById('articleImageInput')

        const imageDeleteButton = document.getElementById('imageDeleteButton')

        const errorHandler = (message) => {
            new Dialog({
                title: `{{lang=warning}}`,
                message: message,
                buttons: [{
                    text: `{{lang=yes}}`,
                    class: `bg-pink-600 text-white hover:bg-pink-500 focus:bg-pink-500 active:bg-pink-500`,
                }, ],
                onclose: () => window.location.reload()
            }).show()
        }

        const loadImage = (file, width, height) => {
            if (!file) return

            const formData = new FormData();

            formData.append('width', width);

            formData.append('height', height);

            formData.append('file', file, file.name);

            fetch(`/upload/article/image/<?= $article->id ?>`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Csrf-Token': csrf
                    },
                    body: formData
                })
                .then(response => {
                    if (response.status !== 200) {
                        response.text().then(text => {
                            return errorHandler(text)
                        })
                    }

                    response.json()
                        .then(json => {
                            input.value = json.image
                            img.src = json.location
                            csrf = json.csrf
                        })
                        .catch(error => {
                            errorHandler(error)
                        })
                }).catch(error => {
                    errorHandler(error)
                })
        }

        file.addEventListener('change', function(event) {
            loadImage(event.target.files[0],
                img.getAttribute('width'),
                img.getAttribute('height'))
        })

        img.addEventListener('click', function(event) {
            file.dispatchEvent(new MouseEvent('click'));
        })

        imageDeleteButton.addEventListener('click', () => {
            img.setAttribute('src', '/images/placeholder.svg')
            input.value = ''
        })
    </script>
</main>