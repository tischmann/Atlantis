<?php include __DIR__ . "/../header.php" ?>
<main class="md:container md:mx-auto text-black dark:text-white">
    <form method="post" class="p-4">
        {{csrf}}
        <div class="grid grid-cols-1 lg:grid-cols-2 md:gap-4">
            <div class="flex flex-col">
                <div class="mb-4">
                    <input class="mt-[0.3rem] mr-2 h-3.5 w-8 appearance-none rounded-[0.4375rem] bg-[rgba(0,0,0,0.25)] outline-none before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1875rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-white after:shadow-[0_0px_3px_0_rgb(0_0_0_/_7%),_0_2px_2px_0_rgb(0_0_0_/_4%)] after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-primary checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ml-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-primary checked:after:shadow-[0_3px_1px_-2px_rgba(0,0,0,0.2),_0_2px_2px_0_rgba(0,0,0,0.14),_0_1px_5px_0_rgba(0,0,0,0.12)] checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[3px_-1px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-primary checked:focus:bg-primary checked:focus:before:ml-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s]" type="checkbox" role="switch" name="visible" id="articleVisible" <?= $article->visible ? 'checked' : '' ?> />
                    <label class="inline-block pl-[0.15rem] hover:cursor-pointer" for="articleVisible">{{lang=article_visible}}</label>
                </div>
                <div class="mb-4">
                    <select data-te-select-init name="locale" id="articleLocaleInput">
                        <?php

                        use App\Models\{Category};

                        use Tischmann\Atlantis\{Locale};

                        foreach (Locale::available() as $locale) {
                            $selected = $locale === $article->locale ? 'selected' : '';

                            $label = Locale::get('locale_' . $locale);

                            echo <<<HTML
                            <option value="{$locale}" {$selected} title="{$label}">{$label}</option>
                            HTML;
                        }
                        ?>
                    </select>
                    <label for="articleLocaleInput" data-te-select-label-ref>{{lang=article_locale}}</label>
                </div>
                <div class="mb-4">
                    <select data-te-select-init name="category_id" id="articleCategoryInput">
                        <?php
                        $categories = Category::fill(Category::query());

                        foreach ([new Category(), ...$categories] as $category) {
                            assert($category instanceof Category);

                            $value = $category->id ? $category->id : '';

                            $selected = $category->id === $article->category_id ? 'selected' : '';

                            echo <<<HTML
                            <option value="{$value}" {$selected} title="{$category->title}">{$category->title}</option>
                            HTML;
                        }
                        ?>
                    </select>
                    <label for="articleCategoryInput" data-te-select-label-ref>{{lang=article_category}}</label>
                </div>
                <div class="relative mb-4" data-te-input-wrapper-init>
                    <input type="text" class="peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0" id="articleTitleInput" placeholder="{{lang=article_title}}" value="<?= $article->title ?>" autocomplete="off" name="title" required />
                    <label for="articleTitleInput" class="pointer-events-none absolute top-0 left-3 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-neutral-200 ">{{lang=article_title}}</label>
                </div>
                <div class="mb-4 relative flex flex-grow" data-te-input-wrapper-init>
                    <textarea class="peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0 flex-grow" placeholder="{{lang=article_short_text}}" id="articleShortTextInput" rows="6" name="short_text"><?= $article->short_text ?></textarea>
                    <label for="articleShortTextInput" class="pointer-events-none absolute top-0 left-3 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-neutral-200">{{lang=article_short_text}}</label>
                </div>
            </div>
            <div class="mb-4">
                <div class="mb-4">
                    <select data-te-select-init id="articleImageSelect">
                        <option value="1920|1080">1920x1080 (16:9)</option>
                        <option value="1280|720">1280x720 (16:9)</option>
                        <option value="1024|576">1024x576 (16:9)</option>
                        <option value="800|450">800x450 (16:9)</option>
                        <option value="1920|1080">1920x1440 (4:3)</option>
                        <option value="1280|960">1280x960 (4:3)</option>
                        <option value="1024|768">1024x768 (4:3)</option>
                        <option value="800|600" selected>800x600 (4:3)</option>
                        <option value="1920|1920">1920x1920 (1:1)</option>
                        <option value="1280|1280">1280x1280 (1:1)</option>
                        <option value="1024|1024">1024x1024 (1:1)</option>
                        <option value="800|800">800x800 (1:1)</option>
                    </select>
                    <label for="articleImageSelect" data-te-select-label-ref>{{lang=article_image_dimensions}}</label>
                </div>
                <input type="hidden" value="<?= $article->image ?>" name="image" id="articleImageInput">
                <input type='file' id="articleImageFile" class="hidden" aria-label="{{lang=article_image}}">
                <img src="<?= $article->image_url ?>" id="articleImage" width="800" height="600" alt="<?= $article->title ?>" class="rounded w-full object-cover">
                <button type="button" data-te-ripple-init data-te-ripple-color="light" id="imageDeleteButton" class="w-full hidden mt-4 rounded bg-danger px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#dc4c64] transition duration-150 ease-in-out hover:bg-danger-600 hover:shadow-[0_8px_9px_-4px_rgba(220,76,100,0.3),0_4px_18px_0_rgba(220,76,100,0.2)] focus:bg-danger-600 focus:shadow-[0_8px_9px_-4px_rgba(220,76,100,0.3),0_4px_18px_0_rgba(220,76,100,0.2)] focus:outline-none focus:ring-0 active:bg-danger-700 active:shadow-[0_8px_9px_-4px_rgba(220,76,100,0.3),0_4px_18px_0_rgba(220,76,100,0.2)] cursor-pointer">
                    {{lang=delete_image}}
                </button>
            </div>
        </div>
        <div class="mb-4">
            <label for="articleFullTextInput" class="form-label inline-block mb-1">{{lang=article_full_text}}</label>
            <textarea class="tinymce-editor peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0" id="articleFullTextInput" name="full_text"><?= $article->full_text ?></textarea>
        </div>
        <div class="mb-4 relative flex flex-grow" data-te-input-wrapper-init>
            <textarea class="peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0 flex-grow" placeholder="{{lang=article_short_text}}" id="articleTagsInput" name="tags"><?= implode(", ", $article->tags) ?></textarea>
            <label for="articleTagsInput" class="pointer-events-none absolute top-0 left-3 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-neutral-200">{{lang=article_tags}}</label>
        </div>
        <div class="flex gap-4 justify-end flex-wrap mb-4">
            <?php
            if ($article->id) {
                echo <<<HTML
                <button type="button" data-te-ripple-init data-te-ripple-color="light" id="deleteArticleButton" aria-label="{{lang=delete}}" class="inline-block rounded bg-danger px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#dc4c64] transition duration-150 ease-in-out hover:bg-danger-600 hover:shadow-[0_8px_9px_-4px_rgba(220,76,100,0.3),0_4px_18px_0_rgba(220,76,100,0.2)] focus:bg-danger-600 focus:shadow-[0_8px_9px_-4px_rgba(220,76,100,0.3),0_4px_18px_0_rgba(220,76,100,0.2)] focus:outline-none focus:ring-0 active:bg-danger-700 active:shadow-[0_8px_9px_-4px_rgba(220,76,100,0.3),0_4px_18px_0_rgba(220,76,100,0.2)] flex-grow md:flex-grow-0 text-center">{{lang=delete}}</button>
                <script nonce="{{nonce}}">
                    const dialog = new Dialog({
                        title: `{{lang=warning}}!`,
                        message: `{{lang=article_delete_confirm}}?`,
                        buttons: [
                            {
                                text: `{{lang=no}}`,
                                callback: () => {}
                            },
                            {
                                text: `{{lang=yes}}`,
                                callback: () => {
                                    fetch(`/article/delete/{$article->id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-Csrf-Token': `{{csrf-token}}`,
                                            'Accept': 'application/json',                    
                                        },
                                    }).then(response => response.json().then(data => {
                                        if (data?.status) {
                                            window.location.href = `/{{env=APP_LOCALE}}/admin/articles`
                                        } else {
                                            alert(data.message)
                                            console.error(data.message)
                                        }
                                    }).catch(error => {
                                        alert(error)
                                        console.error(error)
                                    })).catch(error => {
                                        alert(error)
                                        console.error(error)
                                    })
                                }
                            },
                        ]
                    })

                    document.getElementById('deleteArticleButton')
                        .addEventListener('click', () => dialog.show())
                </script>
                HTML;
            }
            ?>
            <a data-te-ripple-init data-te-ripple-color="light" href="/admin/articles" aria-label="{{lang=cancel}}" class="inline-block rounded bg-primary-100 px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-primary-700 transition duration-150 ease-in-out hover:bg-primary-accent-100 focus:bg-primary-accent-100 focus:outline-none focus:ring-0 active:bg-primary-accent-200 flex-grow md:flex-grow-0 text-center">{{lang=cancel}}</a>
            <button type="submit" data-te-ripple-init data-te-ripple-color="light" class="inline-block rounded bg-primary px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] flex-grow md:flex-grow-0 text-center">{{lang=save}}</button>
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

                if (!json || typeof json.location != 'string') {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                csrf = json.csrf

                resolve(json.location);
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

        const dimensions = document.getElementById('articleImageSelect')

        const imageDeleteButton = document.getElementById('imageDeleteButton')

        const loadImage = (file, width, height) => {
            if (!file || !width || !height) return

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
                .then(response => response.json())
                .then(data => {
                    input.value = data.image
                    img.src = data.location
                    csrf = data.csrf
                    imageDeleteButton.classList.remove('hidden')
                })
                .catch(error => {
                    console.error('Error:', error)
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

        dimensions.addEventListener('change', function(event) {
            const [width, height] = event.target.value.split('|')
            img.setAttribute('width', width)
            img.setAttribute('height', height)
        })


        imageDeleteButton.addEventListener('click', () => {
            img.setAttribute('src', '/images/placeholder.svg')
            input.value = ''
            imageDeleteButton.classList.add('hidden')
        })
    </script>
</main>