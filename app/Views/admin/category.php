<main class="container mx-auto">
    <div class="p-4 flex sticky-top bg-white">
        <?php include __DIR__ . "/../breadcrumbs.php" ?>
    </div>
    <form method="post" class="mx-4">
        {{csrf}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="lg:mb-4">
                <div class="mb-4">
                    <label for="categoryLocale" class="form-label inline-block mb-1 
            text-gray-500">{{lang=category_locale}}</label>
                    <select class="form-select appearance-none block w-full px-3 py-1.5
                text-base font-normal text-gray-700 bg-white bg-clip-padding 
                bg-no-repeat border border-solid border-gray-300 rounded
                transition ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                focus:border-blue-600 focus:outline-none" id="categoryLocale" name="locale" aria-label="{{lang=category_locale}}">
                        <?php

                        use App\Models\Category;

                        use Tischmann\Atlantis\{Locale, Template};

                        foreach (Locale::available() as $locale) {
                            $selected = $locale === $category->locale ? 'selected' : '';

                            $label = Locale::get('locale_' . $locale);

                            echo <<<HTML
                    <option value="{$locale}" {$selected} title="{$label}">{$label}</option>
                    HTML;
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="categoryTitle" class="form-label inline-block mb-1 text-gray-500">{{lang=category_title}}</label>
                    <input type="text" class="form-control block w-full px-3 py-1.5
                text-base font-normal text-gray-700 bg-white bg-clip-padding
                border border-solid border-gray-300 rounded transition
                ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                focus:border-blue-600 focus:outline-none" id="categoryTitle" name="title" placeholder="{{lang=category_title}}" value="<?= $category->title ?>" required />
                </div>
                <div class="mb-4">
                    <label for="categoryParent" class="form-label inline-block mb-1 text-gray-500">{{lang=category_parent}}</label>
                    <select class="form-select appearance-none block w-full px-3 py-1.5
                text-base font-normal text-gray-700 bg-white bg-clip-padding 
                bg-no-repeat border border-solid border-gray-300 rounded
                transition ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                focus:border-blue-600 focus:outline-none" id="categoryParent" name="parent_id" aria-label="{{lang=category_parent}}">
                        <?php
                        $childrenID = array_keys($category->getAllChildren());

                        $parents = Category::fill(
                            Category::query()
                                ->where('id', '!()', [
                                    $category->id,
                                    ...$childrenID
                                ])
                                ->order('title', 'ASC')
                        );

                        foreach ([new Category(), ...$parents] as $parent) {
                            assert($parent instanceof Category);

                            $value = $parent->id ? $parent->id : '';

                            $selected = $parent->id === $category->parent_id ? 'selected' : '';

                            echo <<<HTML
                    <option value="{$value}" {$selected} title="{$parent->title}">{$parent->title}</option>
                    HTML;
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="categorySlug" class="form-label inline-block mb-1 text-gray-500">{{lang=category_slug}}</label>
                    <input type="text" class="form-control block w-full px-3 py-1.5
                        text-base font-normal text-gray-700 bg-white bg-clip-padding
                        border border-solid border-gray-300 rounded transition
                        ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                        focus:border-blue-600 focus:outline-none" id="categorySlug" name="slug" placeholder="{{lang=category_slug}}" value="<?= $category->slug ?>" required />
                </div>
            </div>
            <?php
            if ($category->children) {
                echo <<<HTML
            <div class="mb-4">
                <div class="h-full flex flex-col">
                    <div class="form-label inline-block mb-1 text-gray-500">{{lang=category_children}}</div>
                    <div class="bg-sky-600 rounded text-sky-800 flex-grow p-4">
            HTML;

                Template::echo(
                    'admin/category-children',
                    [
                        'category' => $category
                    ]
                );

                echo <<<HTML
                    </div>
                </div>
            </div>
            HTML;
            }
            ?>
        </div>

        <div class="mb-4 flex gap-4 flex-wrap justify-evenly md:justify-end items-center">
            <?php
            if ($category->id) {
                echo <<<HTML
                <button type="button" id="deleteCategoryButton" aria-label="{{lang=delete}}" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=delete}}</button>
                HTML;
            }
            ?>
            <a href="/admin/categories" aria-label="{{lang=cancel}}" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-gray-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-700 hover:shadow-lg focus:bg-gray-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-800 active:shadow-lg transition duration-150 ease-in-out text-center">{{lang=cancel}}</a>
            <button type="submit" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-sky-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=save}}</button>
        </div>
    </form>
    <?php
    if ($category->id) {
        echo <<<HTML
        <dialog id="deleteDialod" class="rounded shadow-xl relative w-96">
            <form method="dialog">
                <button value="cancel" class="absolute top-4 right-4 ring-0 focus:ring-0 outline-none text-gray-500"><i
                        class="fas fa-times text-xl"></i></button>
                <h5 class="block text-xl font-medium leading-normal text-gray-800 pr-12 mb-4 truncate" id="exampleModalLabel">{{lang=warning}}!</h5>
                <div class="mb-4">{{lang=category_delete_confirm}}?
        HTML;

        if ($category->children) echo " {{lang=category_children_will_be_deleted}}!";

        echo <<<HTML
                </div>
                <div class="flex items-center gap-4">
                    <button value="cancel"
                    class="inline-block w-full px-6 py-2.5 bg-gray-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-700 hover:shadow-lg focus:bg-gray-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=no}}</button>
                <button value="default"
                    class="inline-block w-full px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-pink-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=yes}}</button>
                </div>                        
            </form>
        </dialog>
        <script nonce="{{nonce}}">
            const deleteDialog = document.getElementById('deleteDialod')

            deleteDialog.addEventListener('close', () => {
                if (deleteDialog.returnValue == `cancel`) return

                fetch(`/category/delete/{$category->id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': `XMLHttpRequest`,
                        'X-Csrf-Token': `{{csrf-token}}`,
                        'Accept': 'application/json',                    
                    },
                }).then(response => response.json().then(data => {
                    if (data?.status) {
                        window.location.href = `/{{env=APP_LOCALE}}/admin/categories`
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
            })

            document.getElementById('deleteCategoryButton')
                .addEventListener('click', () => deleteDialog.showModal())
        </script>
        HTML;
    }

    include __DIR__ . "/sortable-categories-script.php"

    ?>
</main>