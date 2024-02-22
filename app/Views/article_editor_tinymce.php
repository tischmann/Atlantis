<?php

use App\Models\{Article, Category};

assert($article instanceof Article);

$category = $article->getCategory();

?>
<main class="md:container mx-8 md:mx-auto">
    <h1 class="text-xl font-bold mb-4 select-none bg-gray-200 text-gray-800 rounded-xl px-4 py-3">{{lang=article_edit}}</h1>
    <form>
        {{csrf}}
        <input class="mb-4 font-semibold text-xl py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="title" name="title" value="<?= $article->title ?>" required>
        <div class="mb-4 relative">
            <div class="px-3 py-2 outline-none border-2 border-gray-200 rounded-lg w-full font-semibold focus:border-sky-600 transition" data-select><?= $category->title ?></div>
            <input value="<?= $category->id ?>" name="category" class="hidden" required />
            <div class="absolute select-none mt-1 hidden bg-white rounded-lg shadow-lg max-h-[50vh] overflow-y-auto" data-options>
                <?php

                $query = Category::query()
                    ->where('parent_id', null)
                    ->order('locale', 'ASC')
                    ->order('title', 'ASC');

                foreach (Category::all($query) as $cat) {
                    assert($cat instanceof Category);

                    $class = $cat->id === $category->id ? 'bg-sky-600 text-white' : '';

                    echo <<<HTML
                        <div data-value="{$cat->id}" class="px-4 py-3 cursor-pointer hover:bg-sky-600 hover:text-white transition {$class}">{$cat->title}</div>
                    HTML;

                    $cat->children = $cat->fetchChildren();

                    foreach ($cat->children as $child) {
                        assert($child instanceof Category);

                        $class = $child->id === $category->id ? 'bg-sky-600 text-white' : '';

                        echo <<<HTML
                            <div data-value="{$child->id}" class="px-4 py-3 pl-8 cursor-pointer bg-gray-100 hover:bg-sky-600 hover:text-white transition {$class}">{$child->title}</div>
                        HTML;

                        $child->children = $child->fetchChildren();

                        foreach ($child->children as $grandchild) {
                            assert($grandchild instanceof Category);

                            $class = $grandchild->id === $category->id ? 'bg-sky-600 text-white' : '';

                            echo <<<HTML
                                <div data-value="{$grandchild->id}" class="px-4 py-3 pl-12 cursor-pointer bg-gray-200 hover:bg-sky-600 hover:text-white transition {$class}">{$grandchild->title}</div>
                            HTML;
                        }
                    }
                }

                ?>
            </div>
        </div>
        <div class="mb-4">
            <img src=" <?= $article->getImage() ?>" alt="<?= $article->title ?>" width="400" height="300" class="bg-gray-200 w-auto rounded-lg border-2 border-gray-200" decoding="async" loading="lazy">
        </div>
        <div class="mb-4">
            <textarea class="w-full min-h-96 outline-none border-2 border-gray-200 rounded-lg p-4 focus:border-sky-600 transition" aria-label="text" name="text"><?= $article->text ?></textarea>
        </div>
        <div class="mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-4">
                <?php
                foreach ($article->getGalleryImages() as $image) {
                    echo <<<HTML
                        <img src="{$image['thumb']}" width="400" height="300" alt="{$article->title}" decoding="async" loading="lazy" class="w-full rounded-lg">
                    HTML;
                }
                ?>
            </div>
        </div>
    </form>
    <script src="/tinymce/tinymce.min.js" nonce="{{nonce}}"></script>
    <script nonce="{{nonce}}">
        document.querySelectorAll('[data-select]').forEach(el => {
            el.addEventListener('click', function(event) {
                const optionsElement = this.parentElement.querySelector('[data-options]')

                optionsElement.classList.toggle('hidden')

                event.stopPropagation()

                document.addEventListener('click', () => {
                    optionsElement.classList.add('hidden')
                }, {
                    once: true
                })
            })
        })

        document.querySelectorAll('[data-options] > div').forEach(el => {
            el.addEventListener('click', function(event) {
                const optionsElement = this.parentElement

                const parent = optionsElement.parentElement

                parent.querySelector('input').setAttribute('value', this.dataset.value)

                parent.querySelector('[data-select]').textContent = this.textContent

                optionsElement.querySelectorAll('div').forEach(el => {
                    el.classList.remove('bg-sky-600', 'text-white')

                    if (el.dataset.value === this.dataset.value) {
                        el.classList.add('bg-sky-600', 'text-white')
                    }
                })
            })
        })

        // tinymce.init({
        //     language: `{{env=APP_LOCALE}}`,
        //     target: document.querySelector('textarea[name="text"]'),
        //     plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
        //     editimage_cors_hosts: ['picsum.photos'],
        //     menubar: 'file edit view insert format tools table help',
        //     toolbar: 'undo redo | bold italic underline strikethrough | fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
        //     height: 600,
        //     quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
        //     noneditable_class: 'mceNonEditable',
        //     toolbar_mode: 'floating',
        //     contextmenu: 'link image table',
        //     image_caption: true,
        //     skin: 'oxide',
        //     content_css: 'default',
        //     image_advtab: true,
        //     paste_as_text: true,
        //     images_upload_handler: (blobInfo, progress) =>
        //         new Promise((resolve, reject) => {
        //             const formData = new FormData()

        //             formData.append('file', blobInfo.blob(), blobInfo.filename())

        //             const xhr = new XMLHttpRequest()

        //             xhr.withCredentials = true

        //             xhr.open(
        //                 'POST',
        //                 `/upload/article/images/${<?= $article->id ?>}`
        //             )

        //             xhr.setRequestHeader('Accept', 'application/json')

        //             xhr.upload.onprogress = (e) => {
        //                 progress((e.loaded / e.total) * 100)
        //             }

        //             xhr.onload = () => {
        //                 if (xhr.status === 403) {
        //                     reject({
        //                         message: 'HTTP Error: ' + xhr.status,
        //                         remove: true
        //                     })
        //                     return
        //                 }

        //                 if (xhr.status < 200 || xhr.status >= 300) {
        //                     reject('HTTP Error: ' + xhr.status)
        //                     return
        //                 }

        //                 let json = {}

        //                 try {
        //                     json = JSON.parse(xhr.responseText)
        //                 } catch (e) {
        //                     reject('Invalid JSON: ' + xhr.responseText)
        //                     return
        //                 }

        //                 if (!json) {
        //                     reject('Invalid JSON: ' + xhr.responseText)
        //                     return
        //                 }

        //                 json.images = json?.images || []

        //                 if (!json.images.length) {
        //                     reject('No image uploaded')
        //                     return
        //                 }

        //                 json.images.forEach(image => {
        //                     return resolve(`/images/articles/<?= $article->id ?>/thumb_${image}`)
        //                 })
        //             }

        //             xhr.onerror = () => {
        //                 reject(
        //                     'Image upload failed due to a XHR Transport error. Code: ' +
        //                     xhr.status
        //                 )
        //             }

        //             xhr.send(formData)
        //         })
        // })
    </script>
</main>