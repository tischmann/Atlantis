<?php include __DIR__ . "/../header.php" ?>
<main class="md:container md:mx-auto">
    <div class="flex flex-wrap gap-4 m-4 order-container">
        <?php

        use App\Models\{Category};

        use Tischmann\Atlantis\{Template};

        foreach ($items as $locale => $categories) {
            $category = new Category();

            $category->children = $categories;

            $children = Template::html(
                'admin/category-children',
                [
                    'category' => $category
                ]
            );

            echo <<<HTML
            <div class="flex flex-wrap rounded-xl gap-4 bg-sky-800 p-4 shadow-lg">
                <div class="flex-grow w-full bg-sky-700 rounded-lg text-white px-4 py-2 whitespace-nowrap uppercase text-center font-bold shadow">{{lang=locale_{$locale}}}</div>
                {$children}
            </div>
            HTML;
        }

        ?>
        <script nonce="{{nonce}}" type="module">
            import Atlantis, {
                Sortable
            } from '/js/atlantis.js'

            const $ = new Atlantis()

            document
                .querySelectorAll('.order-container [data-atlantis-categories]')
                .forEach((container) => {
                    new Sortable(container, {
                        ondragend: () => {
                            const children = []

                            container
                                .querySelectorAll('li[data-id]')
                                .forEach((el, index) => children.push(el.dataset.id))

                            $.fetch(`/categories/order`, {
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: {
                                    children
                                }
                            })
                        }
                    })
                })
        </script>
    </div>
    <?= Template::html('admin/add-button', ['href' => '/{{env=APP_LOCALE}}/add/category']) ?>
</main>