<?php

use App\Models\{Article, Category};

assert($article instanceof Article);

$category = $article->getCategory();

?>
<main class="md:container mx-8 md:mx-auto">
    <h1 class="mb-8 text-xl font-bold select-none bg-gray-200 text-gray-800 rounded-xl px-4 py-3">{{lang=article_edit}}</h1>
    <form>
        {{csrf}}
        <div class="mb-4 relative">
            <label for="title" class="absolute -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_title}}</label>
            <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="title" id="title" name="title" value="<?= $article->title ?>" required>
        </div>
        <div class="mb-4 relative">
            <label for="title" class="absolute -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_category}}</label>
            <div class="px-3 py-2 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" data-select><?= $category->title ?></div>
            <input value="<?= $category->id ?>" name="category" class="hidden" required />
            <div class="absolute select-none mt-1 hidden bg-white rounded-lg shadow-lg max-h-[50vh] overflow-y-auto z-20" data-options>
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
        <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="mb-4 relative">
                    <div class="rounded-lg border-2 border-gray-200">
                        <div id="article-image-container" class="rounded-lg border-[16px] border-white relative">
                            <div id="image-actions-container" class="absolute inset-0 flex items-center justify-center flex-col gap-4 backdrop-blur rounded-lg hidden transition-all">
                                <div class="rounded-md bg-white flex items-center justify-center p-4 text-gray-800 cursor-pointer hover:bg-gray-300 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div class="rounded-md bg-white flex items-center justify-center p-4 text-gray-800 cursor-pointer hover:bg-gray-300 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                </div>
                            </div>
                            <img src=" <?= $article->getImage() ?>" alt="<?= $article->title ?>" width="400" height="300" class="bg-gray-200 rounded-md shadow-lg w-full" decoding="async" loading="lazy">
                        </div>
                    </div>
                    <label for="title" class="absolute -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_image}}</label>
                </div>
                <div class="relative">
                    <label for="title" class="absolute -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_gallery}}</label>
                    <div class="rounded-lg border-2 border-gray-200">
                        <div class="rounded-lg border-[16px] border-white">
                            <div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                                <div alt="{$article->title}" decoding="async" loading="lazy" class="w-full rounded-lg bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </div>
                                <?php
                                foreach ($article->getGalleryImages() as $image) {
                                    echo <<<HTML
                                        <img src="{$image['thumb']}" width="400" height="300" alt="{$article->title}" decoding="async" loading="lazy" class="w-full rounded-lg">
                                    HTML;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative md:col-span-2 flex flex-col">
                <label for="title" class="absolute -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_text}}</label>
                <textarea class="flex-grow w-full min-h-96 outline-none border-2 border-gray-200 rounded-lg p-4 focus:border-sky-600 transition" aria-label="text" name="text"><?= $article->text ?></textarea>
            </div>
        </div>
    </form>
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

        document.getElementById('article-image-container').addEventListener('mouseenter', function() {
            document.getElementById('image-actions-container').classList.remove('hidden')
        })

        document.getElementById('article-image-container').addEventListener('mouseleave', function() {
            document.getElementById('image-actions-container').classList.add('hidden')
        })
    </script>
</main>