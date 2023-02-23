<?php include __DIR__ . "/../header.php" ?>
<main class="md:container md:mx-auto">
    <div class="flex flex-wrap gap-4 my-4 px-4">
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
                <div class="flex-grow w-full bg-sky-700 rounded-lg text-white px-4 py-2 
                whitespace-nowrap uppercase text-center font-bold shadow">
                    {{lang=locale_{$locale}}}
                </div>
                <ul class="flex gap-4 flex-wrap">
                    {$children}
                </ul>
            </div>
            HTML;
        }
        ?>
    </div>
    <?= Template::html('admin/add-button', ['href' => '/{{env=APP_LOCALE}}/add/category']) ?>
    <?php include __DIR__ . "/sortable-categories-script.php" ?>
</main>