<?php

use App\Models\{Article};

use Tischmann\Atlantis\{App, DateTime, Locale, Template};

assert($article instanceof Article);

$user = App::getUser();

if (!$article->exists()) {
    $article->created_at = new DateTime();
    $article->locale = getenv('APP_LOCALE');
    $article->category_id = 0;
}

$category = $article->getCategory();

list($image_width, $image_height) = $article->getImageSizes();

?>
<link rel="stylesheet" href="/css/jquery-ui.min.css" media="screen">
<script src="/js/jquery.min.js" nonce="{{nonce}}"></script>
<script src="/js/jquery-ui.min.js" nonce="{{nonce}}"></script>
<style>
    .ui-state-highlight {
        min-height: 2.5rem;
        border-radius: .375rem;
    }
</style>
<main class="md:container mx-8 md:mx-auto">
    <a href="/{{env=APP_LOCALE}}/articles/<?= $article->url ?>.html" class="w-full mt-4 flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg mb-8 font-medium" title="{{lang=show}}">{{lang=show}}</a>
    <form data-article>
        <div class="mb-8 grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="col-span-1 xl:col-span-2">
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'admin/input_field',
                        args: [
                            'type' => 'text',
                            'name' => 'title',
                            'label' => get_str('article_title'),
                            'value' => $article->title,
                            'required' => true,
                            'autocomplete' => 'off'
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <select id="locale-select" name="locale" title="{{lang=locale}}">
                        <?php
                        foreach (Locale::available() as $value) {
                            $selected = $article->locale === $value
                                ? 'selected'
                                : '';
                            echo <<<HTML
                            <option value="{$value}" {$selected} title="" data-level="0">{{lang=locale_{$value}}}</option>
                            HTML;
                        }

                        ?>
                    </select>
                </div>
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'admin/select_field',
                        args: [
                            'name' => 'category_id',
                            'title' => get_str('article_category'),
                            'options' => $category_options
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php
                    $src = $article->getImage() ? "/images/articles/{$article->id}/image/{$article->getImage()} " : "/images/placeholder.svg";

                    $content = <<<HTML
                    <input type="hidden" id="image" name="image" value="{$article->getImage()}">
                        <img id="article-image" src="{$src}" alt="{$article->title}" width="{$image_width}" height="{$image_height}" class="bg-gray-200 rounded-lg w-full" decoding="async" loading="auto">                    
                        <div id="upload-image-container" class="mt-4 flex flex-nowrap gap-4">
                            <div class="w-40">
                    HTML;

                    $content .= Template::html(
                        template: 'admin/select_field',
                        args: [
                            'name' => 'image_size',
                            'title' => get_str('article_image_size'),
                            'options' => $image_sizes_options
                        ]
                    );

                    $content .= <<<HTML
                        </div>
                        <div id="upload-image" class="grow w-full flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg font-medium" title="{{lang=upload}}">{{lang=upload}}</div>
                    </div>
                    <div id="delete-image" class="absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md rounded-tl-none rounded-br-none hover:bg-red-500 cursor-pointer transition drop-shadow" title="{{lang=delete}}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </div>
                    HTML;

                    Template::echo(
                        template: 'fields/container_field',
                        args: [
                            'label' => get_str('article_image'),
                            'content' => $content,
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'admin/textarea_field',
                        args: [
                            'name' => 'short_text',
                            'label' => get_str('article_short_text'),
                            'value' => $article->short_text,
                            'rows' => 4
                        ]
                    );
                    ?>
                </div>
                <div>
                    <?php

                    $article_text = htmlentities(strval($article->text));

                    Template::echo(
                        template: 'fields/container_field',
                        args: [
                            'label' => get_str('article_text'),
                            'content' => <<<HTML
                            <textarea name="text" id="text" id="text" class="bg-white text-gray-800 dark:bg-gray-800 dark:text-white">{$article_text}</textarea>
                            HTML,
                        ]
                    );
                    ?>
                </div>
            </div>
            <div class="relative">
                <div class="mb-8">
                    <?php

                    $gallery_images = implode(";", $article->getGalleryImages());

                    $content = <<<HTML
                    <input type="hidden" name="gallery" id="gallery" value="{$gallery_images}">
                    <div id="upload-gallery-container" class="mb-4 flex flex-nowrap gap-4">
                        <div class="w-40">
                    HTML;

                    $content .= Template::html(
                        template: 'admin/select_field',
                        args: [
                            'name' => 'gallery_image_size',
                            'title' => get_str('article_image_size'),
                            'options' => $image_sizes_options
                        ]
                    );

                    $content .= <<<HTML
                        </div>
                        <div id="upload-gallery" class="grow w-full flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg font-medium" title="{{lang=upload}}">{{lang=upload}}</div>
                    </div>
                    <ul id="gallery-container" class="grid grid-cols-2 gap-4">
                    HTML;

                    foreach ($article->getGalleryImages() as $src) {
                        $gallery_thumb_width = 320;

                        $gallery_thumb_height = 180;

                        if ($article->id) {
                            list($gallery_thumb_width, $gallery_thumb_height) = $article->getImageSizes(
                                file: getenv('APP_ROOT') . "/public/images/articles/{$article->id}/gallery/thumb_{$src}"
                            );
                        }

                        $content .= Template::html(
                            template: 'admin/article_gallery_item',
                            args: [
                                'gallery_thumb_width' => $gallery_thumb_width,
                                'gallery_thumb_height' => $gallery_thumb_height,
                                'gallery_thumb_src' => "/images/articles/{$article->id}/gallery/thumb_{$src}"
                            ]
                        );
                    }

                    $content .= <<<HTML
                    </ul>
                    HTML;

                    Template::echo(
                        template: 'fields/container_field',
                        args: [
                            'label' => get_str('article_gallery'),
                            'content' => $content,
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php

                    $article_videos = implode(";", $article->getVideos());

                    $content = <<<HTML
                    <input type="hidden" id="title" name="videos" value="{$article_videos}">
                    <div id="upload-video" class="mb-4 w-full p-3 rounded-lg bg-sky-600 text-white flex items-center justify-center hover:bg-sky-500 transition cursor-pointer shadow hover:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <ul id="videos-container" class="grid grid-cols-2 gap-4">
                    HTML;

                    foreach ($article->getVideos() as $video) {
                        $content .= Template::html(
                            template: 'admin/article_video_item',
                            args: [
                                'gallery_video_src' => "/uploads/articles/{$article->id}/video/{$video}"
                            ]
                        );
                    }

                    $content .= <<<HTML
                    </ul>
                    HTML;

                    Template::echo(
                        template: 'fields/container_field',
                        args: [
                            'label' => get_str('article_video'),
                            'content' => $content,
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php

                    $article_attachements = implode(";", $article->getAttachements());

                    $content = <<<HTML
                    <input type="hidden" id="attachement" name="attachements" value="{$article_attachements}">
                    <div id="upload-attachement" class="mb-4  w-full p-3 rounded-lg bg-sky-600 text-white flex items-center justify-center hover:bg-sky-500 transition cursor-pointer shadow hover:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <ul id="attachements-container" class="grid grid-cols-1 gap-4" aria-label="attachement" name="attachement">
                    HTML;

                    foreach ($article->getAttachements() as $attachement) {
                        $content .= Template::html(
                            template: 'admin/article_attachements_item',
                            args: [
                                'attachement_label' => $attachement,
                                'attachement_href' => "/uploads/articles/{$article->id}/attachements/{$attachement}"
                            ]
                        );
                    }

                    $content .= <<<HTML
                    </ul>
                    HTML;

                    Template::echo(
                        template: 'fields/container_field',
                        args: [
                            'label' => get_str('article_attachement'),
                            'content' => $content,
                        ]
                    );

                    ?>
                </div>
                <div class="mb-8 relative">
                    <label for="tags" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 px-1 rounded-md">{{lang=article_tags}}</label>
                    <textarea class="block flex-grow min-h-48 py-2 px-3 outline-none border-2 border-gray-200 dark:border-gray-600 rounded-lg w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:border-sky-600 transition rounded-t-lg rounded-b-none" aria-label="tags" id="tags" name="tags"><?= implode(", ", $article->tags) ?></textarea>
                    <div class="flex flex-nowrap">
                        <input class="block w-16 py-2 px-3 outline-none border-2 border-gray-200 dark:border-gray-600 border-t-0 rounded-bl-lg focus:border-sky-600 text-gray-800 dark:text-white bg-white dark:bg-gray-800 font-medium transition" aria-label="tags-limit" id="tags-limit" name="tags-limit" type="number" min="1" max="20" step="1" value="5">
                        <div id="generate-tags" class="grow flex items-center justify-center border-2 border-gray-200 dark:border-gray-600 border-t-0 border-l-0 px-3 py-2 w-full bg-sky-600 hover:bg-sky-500 text-white cursor-pointer font-medium transition shadow hover:shadow-lg rounded-br-lg" title="{{lang=article_generate_tags}}">{{lang=article_generate_tags}}</div>
                    </div>
                </div>
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'admin/input_field',
                        args: [
                            'type' => 'datetime-local',
                            'name' => 'created_at',
                            'label' => get_str('article_created_at'),
                            'value' => $article->created_at?->format("Y-m-d H:i"),
                            'required' => true,
                            'autocomplete' => 'off'
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'admin/radio_field',
                        args: [
                            'name' => 'visible',
                            'label' => get_str('article_visible'),
                            'value' => intval($article->visible),
                            'options' => [
                                ['value' => 0, 'label' => get_str('article_visible_invisible')],
                                ['value' => 1, 'label' => get_str('article_visible_visible')]
                            ]
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'admin/radio_field',
                        args: [
                            'name' => 'fixed',
                            'label' => get_str('article_fixed'),
                            'value' => intval($article->fixed),
                            'options' => [
                                ['value' => 0, 'label' => get_str('article_fixed_off')],
                                ['value' => 1, 'label' => get_str('article_fixed_on')]
                            ]
                        ]
                    );
                    ?>
                </div>
                <?php
                if ($user->canModerate()) {
                    echo <<<HTML
                    <div class="mb-8">
                    HTML;

                    Template::echo(
                        template: 'admin/radio_field',
                        args: [
                            'name' => 'moderated',
                            'label' => get_str('article_moderated'),
                            'value' => intval($article->moderated),
                            'options' => [
                                ['value' => 0, 'label' => get_str('article_moderated_no')],
                                ['value' => 1, 'label' => get_str('article_moderated_yes')]
                            ]
                        ]
                    );

                    echo <<<HTML
                    </div>
                    HTML;
                }
                ?>
                <div class="flex flex-col gap-4">
                    <?php
                    if ($article->exists()) {
                        echo <<<HTML
                            <button class="flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full font-medium" type="button" title="{{lang=delete}}" data-delete>{{lang=delete}}</button>
                            <button class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full font-medium" type="button" title="{{lang=save}}" data-save>{{lang=save}}</button>
                            HTML;
                    } else {
                        echo <<<HTML
                            <button class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full font-medium" type="button" title="{{lang=add}}" data-add>{{lang=add}}</button>
                            HTML;
                    }
                    ?>
                </div>
            </div>

        </div>
    </form>
</main>
<script src="/tinymce/tinymce.min.js" nonce="{{nonce}}"></script>
<script nonce="{{nonce}}" type="module">
    import Progress from '/js/atlantis.progress.min.js'
    import Select from '/js/atlantis.select.min.js'

    (function() {
        const form = document.querySelector('[data-article]')

        const articleImageElement = document.getElementById('article-image')

        const articleImageInput = form.querySelector('input[name="image"]')

        const tagsLimitElement = document.getElementById('tags-limit')

        const galleryContainer = document.getElementById('gallery-container')

        const articleGalleryInput = form.querySelector('input[name="gallery"]')

        const videosContainer = document.getElementById('videos-container')

        const articleVideosInput = form.querySelector('input[name="videos"]')

        const attachementsContainer = document.getElementById('attachements-container')

        const articleAttachementsInput = form.querySelector('input[name="attachements"]')

        const categorySelect = new Select(form.querySelector(`select[name="category_id"]`))

        const articleTagsElement = form.querySelector('textarea[name="tags"]')

        const articleTextElement = form.querySelector('textarea[name="text"]')

        const darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches

        tinymce.init({
            language: 'ru',
            target: articleTextElement,
            paste_as_text: true,
            autosave_ask_before_unload: false,
            plugins: 'preview importcss searchreplace autolink directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            editimage_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample',
            height: 800,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_class: 'mceNonEditable',
            toolbar_mode: 'sliding',
            contextmenu: 'link image table',
            image_caption: true,
            skin: darkMode ? 'oxide-dark' : 'oxide',
            content_css: darkMode ? ['/app.min.css', '/dark.css'] : '/app.min.css',
            font_css: 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap',
            image_advtab: true,
            image_class_list: [{
                    title: '{{lang=none}}',
                    value: 'gallery-item'
                },
                {
                    title: 'rounded',
                    value: 'rounded gallery-item'
                },
                {
                    title: 'rounded-md',
                    value: 'rounded-md gallery-item'
                },
                {
                    title: 'rounded-lg',
                    value: 'rounded-lg gallery-item'
                },
                {
                    title: 'rounded-xl',
                    value: 'rounded-xl gallery-item'
                }
            ],
            relative_urls: false,
            images_upload_handler: (blobInfo, progress) => {
                return new Promise((resolve, reject) => {
                    const formData = new FormData()

                    formData.append(
                        'image',
                        blobInfo.blob(),
                        blobInfo.filename()
                    )

                    const xhr = new XMLHttpRequest()

                    xhr.withCredentials = true

                    xhr.open('POST', `/article/images`)

                    xhr.setRequestHeader('Accept', 'application/json')

                    xhr.upload.onprogress = (e) => {
                        progress((e.loaded / e.total) * 100)
                    }

                    xhr.onload = () => {
                        if (xhr.status === 403) {
                            return reject({
                                message: '{{lang=error}}: ' + xhr.status,
                                remove: true
                            })
                        }

                        if (xhr.status < 200 || xhr.status >= 300) {
                            return reject('{{lang=error}}: ' + xhr.status)
                        }

                        const json = JSON.parse(xhr.responseText)

                        if (!json?.src) {
                            return reject(
                                '{{lang=incorrect_response}}: ' + xhr.responseText
                            )
                        }

                        resolve(json.src)
                    }

                    xhr.onerror = () => {
                        reject(`{{lang=error}}: ${xhr.status}`)
                    }

                    xhr.send(formData)
                })
            }
        })

        function upload({
            url = '/',
            data = null,
            progress = function() {},
            success = function() {},
            failure = function() {},
            method = 'POST'
        } = {}) {
            new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest()

                    xhr.open(method.toUpperCase(), url)

                    xhr.upload.addEventListener('progress', (event) => {
                        if (event.lengthComputable) {
                            const percent = (event.loaded / event.total) * 100
                            progress(percent)
                        }
                    })

                    xhr.onload = () => {
                        const json = JSON.parse(xhr.response)

                        if (xhr.status === 200) {
                            resolve(json)
                        } else {
                            alert(json.message)
                            reject(json)
                        }
                    }

                    xhr.onerror = () => {
                        reject(new Error('{{lang=error}}'))
                    }

                    xhr.send(data)
                })
                .then(success)
                .catch(failure)
        }

        const localeSelect = new Select(
            form.querySelector('select[name="locale"]'), {
                onchange: (value, changed) => {
                    if (!changed) return

                    fetch(`/locale/categories/${value}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then((response) => response.json())
                        .then(({
                            items
                        }) => {
                            categorySelect.update(items)
                        })
                }
            })

        function getProportionalHeight(width, proportion = '16_9') {
            width = parseInt(width)

            switch (proportion) {
                case '16_9':
                    return Math.round((width / 16) * 9)
                case '4_3':
                    return Math.round((width / 4) * 3)
            }

            return width
        }

        const imageSizeSelect = new Select(
            form.querySelector(`select[name="image_size"]`), {
                onchange: (proportion, changed) => {
                    if (!changed) return
                    const height = getProportionalHeight(articleImageElement.getAttribute('width'), proportion)
                    articleImageElement.src = `/images/placeholder_${proportion}.svg`
                    articleImageElement.setAttribute('height', height)
                }
            }
        )

        document.getElementById('upload-image').addEventListener('click', () => {
            uploadImageHandler()
        })

        function uploadImageHandler({
            image = null,
            size = null,
            accept = '.jpg,.jpeg,.png,.webp,.gif,.bmp',
            multiple = false,
            success = function() {}
        } = {}) {
            if (image !== null) {
                return uploadImage({
                    image,
                    size,
                    success
                })
            }

            const input = document.createElement('input')

            input.hidden = true

            input.type = 'file'

            input.accept = accept

            input.multiple = multiple

            size = size || imageSizeSelect.getValue()

            input.addEventListener('change', (event) => {
                articleImageElement.src = `/images/placeholder_${size}.svg`

                uploadImage({
                    image: event.target.files[0],
                    size,
                    success: ({
                        image
                    }) => {
                        articleImageElement.src = `/images/articles/temp/${image}`

                        articleImageInput.setAttribute('value', image)

                        changeImageProportions(size)

                        input.remove()
                    }
                })
            }, {
                once: true
            })

            input.click()
        }

        function uploadImage({
            image,
            size = '16_9',
            success = function() {}
        } = {}) {
            const data = new FormData()

            data.append('image', image)

            data.append('size', size)

            upload({
                url: '/article/image',
                data,
                success
            })
        }

        function deleteImage() {
            articleImageElement.src = `/images/placeholder_16_9.svg`
            articleImageInput.setAttribute('value', '')
            changeImageProportions('16_9')
        }

        function changeImageProportions(size = '16_9') {
            articleImageElement.setAttribute('height', getProportionalHeight(articleImageElement.getAttribute('width'), size))
        }

        document.getElementById('delete-image').addEventListener('click', () => {
            deleteImage()
        })

        const gallerySizeSelect = new Select(form.querySelector(`select[name="gallery_image_size"]`))

        function initGalleryItem(li) {
            li.querySelector('[data-delete]').addEventListener('click', () => {
                li.classList.add('transition', 'scale-0')
                setTimeout(() => {
                    li.remove()
                    updateGalleryInput()
                }, 200)
            }, {
                once: true
            })
        }

        function createGalleryItem({
            src,
            width,
            height
        }) {
            const wrapper = document.createElement('div')

            wrapper.innerHTML = `<?= Template::html(template: 'admin/article_gallery_item') ?>`

            const li = wrapper.querySelector('li')

            const img = li.querySelector('img')

            img.src = `/images/articles/temp/thumb_${src}`

            img.setAttribute('width', width)

            img.setAttribute('height', height)

            initGalleryItem(li)

            return li
        }

        function updateGalleryInput() {
            const values = []

            galleryContainer.querySelectorAll('li:not(.ui-state-highlight)').forEach((li) => {
                values.push(li.querySelector('img').src.split('/').pop().replace('thumb_', ''))
            })

            articleGalleryInput.setAttribute('value', values.join(';'))
        }

        function uploadGalleryImage({
            image,
            size = '16_9',
            success = function() {},
            progress = function() {}
        } = {}) {
            return new Promise((resolve, reject) => {
                const data = new FormData()

                data.append('image[]', image)

                data.append('size', size)

                upload({
                    url: '/article/gallery',
                    data,
                    progress,
                    success
                })

                resolve()
            })
        }

        function uploadGalleryImageHandler({
            image = null,
            size = null,
            accept = '.jpg,.jpeg,.png,.webp,.gif,.bmp',
            multiple = true,
            success = function() {},
            progress = function() {}
        } = {}) {
            if (image !== null) {
                return uploadGalleryImage({
                    image,
                    size,
                    success,
                    progress
                })
            }

            size = size || gallerySizeSelect.getValue()

            const input = document.createElement('input')

            input.hidden = true

            input.type = 'file'

            input.accept = accept

            input.multiple = multiple

            const width = 320

            const height = getProportionalHeight(width, size)

            input.addEventListener('change', (event) => {
                Array.from(event.target.files).forEach((file) => {
                    const progress = new Progress(galleryContainer)

                    uploadGalleryImage({
                        image: file,
                        size,
                        progress: (value) => {
                            progress.update(value)
                        },
                        success: ({
                            images
                        }) => {
                            progress.destroy()

                            images.forEach((src) => {
                                galleryContainer.append(
                                    createGalleryItem({
                                        src,
                                        width,
                                        height
                                    })
                                )

                            })

                            updateGalleryInput()
                        }
                    })
                })

                input.remove()
            })

            input.click()
        }

        document.getElementById('upload-gallery').addEventListener('click', () => {
            uploadGalleryImageHandler()
        })

        function updateVideosInput() {
            articleVideosInput.setAttribute(
                'value',
                Array.from(videosContainer.querySelectorAll('li > video'))
                .map((video) => video.src.split('/').pop())
                .filter((src) => src !== '')
                .join(';')
            )
        }

        function initVideosItem(li) {
            li.querySelector('[data-delete]')?.addEventListener('click', () => {
                li.classList.add('transition', 'scale-0')
                setTimeout(() => {
                    li.remove()
                    updateVideosInput()
                }, 200)
            }, {
                once: true
            })
        }

        function createVideoItem({
            src
        }) {
            const wrapper = document.createElement('div')

            wrapper.innerHTML = `<?= Template::html(template: 'admin/article_video_item') ?>`

            const li = wrapper.querySelector('li')

            const video = li.querySelector('video')

            video.src = `/uploads/articles/temp/${src}`

            initVideosItem(li)

            return li
        }

        function uploadVideoHandler({
            file = null,
            accept = 'video/*',
            multiple = true,
            progress = function() {},
            success = function() {}
        } = {}) {
            if (file !== null) {
                return uploadVideo({
                    file,
                    progress,
                    success
                })
            }

            const input = document.createElement('input')

            input.hidden = true

            input.type = 'file'

            input.accept = accept

            input.multiple = multiple

            input.addEventListener('change', (event) => {
                Array.from(event.target.files).forEach((file) => {
                    const progress = new Progress(videosContainer)

                    uploadVideo({
                        file: file,
                        progress: function(value) {
                            progress.update(value)
                        },
                        success: ({
                            videos
                        }) => {
                            progress.destroy()

                            videos.forEach((src) => {
                                videosContainer.append(createVideoItem({
                                    src
                                }))
                            })

                            updateVideosInput()
                        }
                    })
                })

                input.remove()
            })

            input.click()
        }

        function uploadVideo({
            file = null,
            progress = function() {},
            success = function() {}
        }) {
            new Promise((resolve, reject) => {
                const data = new FormData()

                data.append('video[]', file)

                upload({
                    url: '/article/videos',
                    data,
                    progress,
                    success
                })

                resolve()
            })
        }

        document.getElementById('upload-video').addEventListener('click', () => {
            uploadVideoHandler()
        })

        function updateAttachementsInput() {
            articleAttachementsInput.setAttribute(
                'value',
                Array.from(
                    attachementsContainer.querySelectorAll('li > a')
                )
                .map((a) => a.getAttribute('href').split('/').pop())
                .filter((src) => src !== '')
                .join(';')
            )
        }

        function createAttachementItem({
            file
        }) {
            const wrapper = document.createElement('div')

            wrapper.innerHTML = `<?= Template::html(template: 'admin/article_attachements_item') ?>`

            const li = wrapper.querySelector('li')

            const a = li.querySelector('a')

            a.setAttribute('href', `/uploads/articles/temp/${file}`)

            a.textContent = file

            initAttachementItem(li)

            return li
        }

        function initAttachementItem(li) {
            li.querySelector('[data-delete]').addEventListener('click',
                () => {
                    li.classList.add('transition', 'scale-0')
                    setTimeout(() => {
                        li.remove()
                        updateAttachementsInput()
                    }, 200)
                }, {
                    once: true
                }
            )
        }

        function uploadAttachement({
            file = null,
            progress = function() {},
            success = function() {}
        }) {
            new Promise((resolve, reject) => {
                const data = new FormData()

                data.append('file[]', file)

                upload({
                    url: '/article/attachements',
                    data,
                    progress,
                    success
                })

                resolve()
            })
        }

        function uploadAttachementHandler({
            file = null,
            accept = '*',
            multiple = true,
            progress = function() {},
            success = function() {}
        } = {}) {
            if (file !== null) {
                return uploadAttachement({
                    file,
                    progress,
                    success
                })
            }

            const input = document.createElement('input')

            input.hidden = true

            input.type = 'file'

            input.accept = accept

            input.multiple = multiple

            input.addEventListener('change', (event) => {
                Array.from(event.target.files).forEach((file) => {
                    const progress = new Progress(attachementsContainer)

                    uploadAttachement({
                        file: file,
                        progress: function(value) {
                            progress.update(value)
                        },
                        success: ({
                            files
                        }) => {
                            progress.destroy()

                            files.forEach((file) => {
                                attachementsContainer.append(createAttachementItem({
                                    file
                                }))
                            })

                            updateAttachementsInput()
                        }
                    })
                })

                input.remove()
            })

            input.click()
        }

        document.getElementById('upload-attachement').addEventListener('click', () => {
            uploadAttachementHandler()
        })

        document.getElementById('generate-tags').addEventListener('click', () => {
            const threshold = 3

            let limit = parseInt(tagsLimitElement.value)

            limit = limit > 20 ? 20 : limit

            limit = limit < 1 ? 1 : limit

            const tags = {}

            let text = tinymce.activeEditor.getContent()

            const wrapper = document.createElement('div')

            wrapper.innerHTML = text

            text = wrapper.textContent || wrapper.innerText || ''

            text.split(/[\s,]+/)
                .forEach((tag, index) => {
                    tag = tag.toLowerCase()
                    if (tag.length < threshold) return
                    if (tags[tag] === undefined) tags[tag] = 1
                    else tags[tag]++
                })

            if (!Object.entries(tags).length) return ''

            articleTagsElement.value = Object.entries(tags)
                .sort((a, b) => b[1] - a[1])
                .slice(0, limit)
                .map((tag) => tag[0])
                .join(', ')
        })

        form.querySelector('button[data-save]')?.addEventListener('click', () => {
            articleTextElement.innerHTML = tinymce.activeEditor.getContent()

            fetch(`/article/<?= $article->id ?>`, {
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

        form.querySelector('button[data-add]')?.addEventListener('click', () => {
            articleTextElement.innerHTML = tinymce.activeEditor.getContent()

            fetch(`/article`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            }).then((response) => {
                response.json().then((json) => {
                    alert(json.message)

                    if (json.id) {
                        window.location.href = `/edit/article/${json.id}`
                    } else {
                        window.location.reload()
                    }
                })
            })
        })

        form.querySelector('button[data-delete]')?.addEventListener('click', (event) => {
            if (!confirm(`{{lang=confirm_delete}}`)) return

            fetch(`/article/<?= $article->id ?>`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body
            }).then((response) => {
                response.json().then((json) => {
                    alert(json.message)
                    window.location.href = '/edit/articles'
                })
            })
        })

        galleryContainer.querySelectorAll(`li`).forEach((li) => {
            initGalleryItem(li)
        })

        videosContainer.querySelectorAll(`li`).forEach((li) => {
            initVideosItem(li)
        })

        attachementsContainer.querySelectorAll(`li`).forEach((li) => {
            initAttachementItem(li)
        })

        $(galleryContainer).sortable({
            placeholder: 'ui-state-highlight',
            update: () => {
                updateGalleryInput()
            }
        })

        $(galleryContainer).disableSelection()

        $(attachementsContainer).sortable({
            placeholder: 'ui-state-highlight',
            handle: '.handle',
            update: () => {
                updateAttachementsInput()
            }
        })

        $(attachementsContainer).disableSelection()

        $(videosContainer).sortable({
            placeholder: 'ui-state-highlight',
            update: () => {
                updateVideosInput()
            }
        })

        $(videosContainer).disableSelection()
    })()
</script>