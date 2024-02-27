<?php

use App\Models\{Article, Category};

assert($article instanceof Article);

$category = $article->getCategory();

?>
<main class="md:container mx-8 md:mx-auto">
    <form id="article-form">
        <div class="mb-8 relative">
            <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_title}}</label>
            <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="title" id="title" name="title" value="<?= $article->title ?>" required>
        </div>
        <div class="mb-8 relative">
            <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_category}}</label>
            <input value="<?= $category->id ?>" name="category_id" class="hidden" required />
            <div class="px-3 py-2 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" data-select><?= $category->title ?></div>
            <ul class="absolute select-none mt-1 hidden bg-white border-2 border-gray-200 rounded-lg shadow-lg max-h-[50vh] overflow-y-auto z-20" data-options>
                <?php

                $query = Category::query()
                    ->where('parent_id', null)
                    ->order('locale', 'ASC')
                    ->order('title', 'ASC');

                foreach (Category::all($query) as $cat) {
                    assert($cat instanceof Category);

                    $class = $cat->id === $category->id ? 'bg-sky-600 text-white' : '';

                    echo <<<HTML
                        <li data-value="{$cat->id}" class="px-4 py-3 cursor-pointer hover:bg-sky-600 hover:text-white {$class}">{$cat->title}</li>
                    HTML;

                    $cat->children = $cat->fetchChildren();

                    foreach ($cat->children as $child) {
                        assert($child instanceof Category);

                        $class = $child->id === $category->id ? 'bg-sky-600 text-white' : '';

                        echo <<<HTML
                            <li data-value="{$child->id}" class="px-4 py-3 pl-8 cursor-pointer bg-gray-100 hover:bg-sky-600 hover:text-white {$class}">{$child->title}</li>
                        HTML;

                        $child->children = $child->fetchChildren();

                        foreach ($child->children as $grandchild) {
                            assert($grandchild instanceof Category);

                            $class = $grandchild->id === $category->id ? 'bg-sky-600 text-white' : '';

                            echo <<<HTML
                                <li data-value="{$grandchild->id}" class="px-4 py-3 pl-12 cursor-pointer bg-gray-200 hover:bg-sky-600 hover:text-white {$class}">{$grandchild->title}</li>
                            HTML;
                        }
                    }
                }

                ?>
            </ul>
        </div>
        <div class="mb-8 grid grid-cols-1 xl:grid-cols-4 gap-8">
            <div class="relative order-2 xl:order-1">
                <div class="mb-8 relative">
                    <label class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_gallery}}</label>
                    <input type="hidden" name="gallery" value="<?= implode(";", $article->getGalleryImages()) ?>">
                    <div class="rounded-lg border-2 border-gray-200">
                        <div class="rounded-lg border-[16px] border-white">
                            <div class="add-gallery-image-button mb-4 w-full p-3 rounded-lg bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                            <ul class="gallery-container grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-2 gap-4">
                                <?php
                                foreach ($article->getGalleryImages() as $image) {
                                    require "article_gallery_item.php";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_video}}</label>
                    <input type="hidden" name="videos" value="<?= implode(";", $article->getVideos()) ?>">
                    <div class="rounded-lg border-2 border-gray-200">
                        <div class="rounded-lg border-[16px] border-white">
                            <div class="mb-4 add-video-button w-full p-3 rounded-lg bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                            <ul class="videos-container grid grid-cols-1 gap-4">
                                <?php
                                foreach ($article->getVideos() as $video) {
                                    require "article_video_item.php";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative order-3">
                <div class="mb-8 relative">
                    <label for="attachement" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_attachement}}</label>
                    <div class="border-2 border-gray-200 rounded-lg p-4 transition">
                        <div class="mb-4  w-full p-3 rounded-lg bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <ul class="sortable-list flex flex-col gap-4" aria-label="attachement" name="attachement">
                            <?php

                            foreach ($article->getAttachements() as $attachement) {
                                echo <<<HTML
                                <li class="flex flex-nowrap gap-2 items-center justify-between text-gray-800 w-full hover:bg-gray-100 rounded-md">
                                    <a href="{$attachement['url']}" class="text-ellipsis overflow-hidden whitespace-nowrap grow px-3 py-2" target="_blank" title="{{lang=delete}}">
                                        {$attachement['name']}
                                    </a>
                                    <div class="delete-attachement-button text-gray-500 cursor-pointer hover:text-red-600 pr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </div>
                                </li>
                                HTML;
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="mb-8 relative">
                    <label for="tags" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_tags}}</label>
                    <textarea class="block flex-grow w-full min-h-48 outline-none border-2 border-gray-200 rounded-t-lg p-4 focus:border-sky-600 transition" aria-label="tags" name="tags"><?= implode(", ", $article->tags) ?></textarea>
                    <div class="flex flex-nowrap">
                        <input class="block w-16 py-2 px-3 outline-none border-2 border-gray-200 border-t-0 rounded-bl-lg focus:border-sky-600 transition" aria-label="tags-limit" id="tags-limit" name="tags-limit" type="number" min="1" max="20" step="1" value="5">
                        <div id="generate-tags" class="grow flex items-center justify-center border-2 border-gray-200 border-t-0 border-l-0 px-3 py-2 w-full bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-br-lg" title="{{lang=article_generate_tags}}">{{lang=article_generate_tags}}</div>
                    </div>

                </div>
                <div class="mb-8 relative">
                    <label for="views" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_views}}</label>
                    <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="views" id="views" name="views" type="number" min="0" step="1" value="<?= $article->views ?>">
                </div>
                <div class="mb-8 relative">
                    <label for="rating" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_rating}}</label>
                    <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="rating" id="rating" name="rating" type="number" min="0" max="5" step="0.1" value="<?= $article->rating ?>">
                </div>
                <div class="mb-8 relative">
                    <label for="created_at" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_created_at}}</label>
                    <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="created_at" id="created_at" name="created_at" type="datetime-local" value="<?= $article->created_at->format("Y-m-d H:i") ?>">
                </div>
                <div class="flex flex-col gap-4">
                    <button id="delete-article" class="flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=delete}}">{{lang=delete}}</button>
                    <button id="save-article" class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=save}}">{{lang=save}}</button>
                </div>
            </div>
            <div class="order-1 xl:order-2 xl:col-span-2">
                <div class="mb-8 relative">
                    <div class="rounded-lg border-2 border-gray-200">
                        <div class="rounded-lg border-[16px] border-white">
                            <input type="hidden" name="image" value="<?= $article->getImage() ?>">
                            <img id="article-image" src="/images/articles/<?= $article->id ?>/image/<?= $article->getImage() ?>" alt="<?= $article->title ?>" width="320" height="180" class="bg-gray-200 rounded-t-md w-full" decoding="async" loading="lazy">
                            <div class="flex items-center justify-center flex-col select-none">
                                <button id="delete-image" type="button" class="flex items-center justify-center px-3 py-2 w-full bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg" title="{{lang=delete}}">{{lang=delete}}</button>
                                <button id="upload-image" type="button" class="flex items-center justify-center px-3 py-2 w-full rounded-b-md bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg" title="{{lang=upload}}">{{lang=upload}}</button>
                            </div>
                        </div>
                    </div>
                    <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_image}}</label>
                </div>
                <div class="mb-8 flex flex-col relative">
                    <label for="short_text" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_short_text}}</label>
                    <textarea class="flex-grow w-full min-h-48 outline-none border-2 border-gray-200 rounded-lg p-4 focus:border-sky-600 transition" name="short_text"><?= $article->short_text ?></textarea>
                </div>
                <div class="flex flex-col relative">
                    <label for="text" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_text}}</label>
                    <div class="flex-grow w-full min-h-96 outline-none border-2 border-gray-200 rounded-lg p-4 focus:border-sky-600 transition">
                        <input type="hidden" name="text" id="text" value="<?= htmlentities($article->text) ?>">
                        <div id="quill-editor"><?= $article->text ?></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>
<script src="/quill.min.js" nonce="{{nonce}}"></script>
<script nonce="{{nonce}}">
    const textElement = document.querySelector('input[name="text"]')

    const galleryContainer = document.querySelector('.gallery-container')

    const galleryInput = document.querySelector('input[name="gallery"]')

    const videosContainer = document.querySelector('.videos-container')

    const videosInput = document.querySelector('input[name="videos"]')

    const deleteImageButton = document.getElementById('delete-image')

    const articleImage = document.getElementById('article-image')

    const articleImageInput = document.querySelector('input[name="image"]')

    const tagsElement = document.querySelector('textarea[name="tags"]')

    // Text

    const quill = new Quill('#quill-editor', {
        modules: {
            toolbar: [
                [{
                    'header': 1
                }, {
                    'header': 2
                }],
                [{
                    'align': []
                }],
                ['bold', 'italic', 'underline', 'strike'],
                [{
                    'color': []
                }, {
                    'background': []
                }],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                [{
                    'script': 'sub'
                }, {
                    'script': 'super'
                }],
                [{
                    'indent': '-1'
                }, {
                    'indent': '+1'
                }],
                ['link', 'video', 'code-block'],
                ['clean']
            ],
        },
        theme: 'snow',
    })

    quill.on('text-change', () => {
        textElement.setAttribute('value', quill.root.innerHTML)
    })

    // Selects

    document.select('[data-select]', '[data-options]')

    // Image

    deleteImageButton.addEventListener('click', function() {
        articleImage.src = '/images/placeholder.svg'
        articleImageInput.setAttribute('value', '')
    })

    document.getElementById('upload-image').addEventListener('click', function() {
        const image = document.getElementById('article-image')

        const file = document.createElement('input')

        file.hidden = true
        file.type = 'file'
        file.accept = '.jpg,.jpeg,.png,.webp,.gif,.bmp'
        file.multiple = false

        file.addEventListener('change', (event) => {
            const formData = new FormData();

            formData.append('image', event.target.files[0]);

            fetch('/article/image', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return document.dialog({
                            text: response.message
                        })
                    }

                    response.json().then(json => {
                        image.src = `/images/articles/temp/${json.images[0]}`
                        document.querySelector('input[name="image"]').setAttribute('value', json.images[0])
                    })
                })
        })

        file.click()
    })

    // Sortable lists

    document.querySelectorAll(`.sortable-list`).forEach(el => {
        document.sortable(el)
    })

    document.sortable(galleryContainer, {
        ondragend: () => {
            const value = []

            galleryContainer.querySelectorAll('li > img').forEach(img => {
                value.push(img.src.split('/').pop())
            })

            galleryInput.setAttribute('value', value.join(';'))
        }
    })

    document.sortable(videosContainer, {
        ondragend: () => {
            const value = []

            videosContainer.querySelectorAll('li > video').forEach(video => {
                value.push(video.src.split('/').pop())
            })

            videosInput.setAttribute('value', value.join(';'))
        }
    })

    // Gallery

    function getGalleryInputValues() {
        const value = galleryInput.value.split(';')

        value.map((v, i) => {
            if (v === '') value.splice(i, 1)
        })

        return value
    }

    function computeGalleryInputValues() {
        const values = []

        galleryContainer.querySelectorAll('li').forEach(li => {
            values.push(li.querySelector('img').src.split('/').pop().replace('thumb_', ''))
        })

        return values
    }

    function initGalleryItem(element) {
        element.addEventListener('click', function() {
            const li = this.closest('li')
            li.classList.add('transition', 'scale-0')
            setTimeout(() => {
                li.remove()
                galleryInput.setAttribute('value', computeGalleryInputValues().join(';'))
            }, 200)
        }, {
            once: true
        })
    }

    function processGalleryResponse(response) {
        const values = getGalleryInputValues()

        response.images.forEach(src => {
            const div = document.createElement('div')

            div.insertAdjacentHTML('beforeend', `<?= require "article_gallery_item.php" ?>`)

            const li = div.querySelector('li')

            const image = li.querySelector('img')

            li.querySelectorAll(`.delete-gallery-image-button`).forEach(initGalleryItem)

            image.src = `/images/articles/temp/thumb_${src}`

            galleryContainer.append(li)

            values.push(src)

            galleryInput.setAttribute('value', values.join(';'))
        })
    }

    document.querySelectorAll('.add-gallery-image-button').forEach(button => {
        button.addEventListener('click', () => {
            const file = document.createElement('input')

            file.hidden = true
            file.type = 'file'
            file.accept = '.jpg,.jpeg,.png,.webp,.gif,.bmp'
            file.multiple = true

            file.addEventListener('change', (event) => {
                Array.from(event.target.files).forEach(file => {
                    new Promise((resolve, reject) => {
                        const data = new FormData()

                        data.append('image[]', file)

                        const progress = document.progress(0, galleryContainer)

                        document.upload(
                            '/article/gallery',
                            data,
                            function(percent) {
                                progress.update(percent)
                            }
                        ).then((response) => {
                            progress.destroy()
                            processGalleryResponse(response)
                        })

                        resolve()
                    })
                })
            })

            file.click()
        })
    })

    document.querySelectorAll(`.delete-gallery-image-button`)
        .forEach(initGalleryItem)

    // Videos

    function getVideosInputValues() {
        const value = videosInput.value.split(';')

        value.map((v, i) => {
            if (v === '') value.splice(i, 1)
        })

        return value
    }

    function computeVideosInputValues() {
        const values = []

        videosContainer.querySelectorAll('li').forEach(li => {
            values.push(li.querySelector('video').src.split('/').pop())
        })

        return values
    }

    function initVideosItem(element) {
        element.addEventListener('click', function() {
            const li = this.closest('li')
            li.classList.add('transition', 'scale-0')
            setTimeout(() => {
                li.remove()
                videosInput.setAttribute('value', computeVideosInputValues().join(';'))
            }, 200)
        }, {
            once: true
        })
    }

    function processVideosResponse(response) {
        const values = getVideosInputValues()

        response.videos.forEach(src => {
            const div = document.createElement('div')

            div.insertAdjacentHTML('beforeend', `<?= require "article_video_item.php" ?>`)

            const li = div.querySelector('li')

            const video = li.querySelector('video')

            li.querySelectorAll(`.delete-videos-button`)
                .forEach(initVideosItem)

            video.src = `/uploads/articles/temp/${src}`

            videosContainer.append(li)

            values.push(src)

            videosInput.setAttribute('value', values.join(';'))
        })
    }

    document.querySelectorAll('.add-video-button').forEach(el => {
        el.addEventListener('click', () => {
            const file = document.createElement('input')

            file.hidden = true
            file.type = 'file'
            file.accept = 'video/*'
            file.multiple = true

            file.addEventListener('change', (event) => {
                Array.from(event.target.files).forEach(file => {
                    new Promise((resolve, reject) => {
                        const data = new FormData()

                        data.append('video[]', file)

                        const progress = document.progress(0, videosContainer)

                        document.upload(
                            '/article/videos',
                            data,
                            function(percent) {
                                progress.update(percent)
                            }
                        ).then((response) => {
                            progress.destroy()
                            processVideosResponse(response)
                        })

                        resolve()
                    })
                })
            })

            file.click()
        })
    })

    document.querySelectorAll(`.delete-videos-button`)
        .forEach(initVideosItem)

    // Attachements

    document.querySelectorAll(`.delete-attachement-button`).forEach(el => {
        el.addEventListener('click', () => {
            const li = el.closest('li')
            li.classList.add('transition', 'scale-0')
            setTimeout(() => {
                li.remove()
            }, 200)
        }, {
            once: true
        })
    })

    // Tags

    document.getElementById('generate-tags').addEventListener('click', function() {
        tagsElement.value = document.tags(
            textElement.value,
            document.getElementById('tags-limit').value
        )
    })
</script>

<?php require "article_update_script.php" ?>