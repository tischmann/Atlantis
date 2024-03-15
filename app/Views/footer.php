<footer class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white w-full block">
    <div class="no-print w-full py-8 px-4 border-b-2 border-b-gray-600">

    </div>
    <div class="no-print hidden sm:block sm:container mx-auto py-8">
        <ul class="flex items-start justify-evenly flex-row flex-wrap gap-8">
            <?php

            use App\Models\Category;

            $query = Category::query()
                ->where('parent_id', null)
                ->order('position', 'ASC');

            foreach (Category::all($query) as $category) {
                assert($category instanceof Category);

                echo <<<HTML
                <li class="flex flex-col gap-2 text-sm">
                    <a href="/{{env=APP_LOCALE}}/category/{$category->slug}" title="{$category->title}" class="font-semibold hover:underline uppercase">{$category->title}</a>
                HTML;

                $category->children = $category->fetchChildren();

                if ($category->children) {
                    echo <<<HTML
                    <ul class="flex flex-col gap-2">
                    HTML;

                    foreach ($category->children as $child) {
                        assert($child instanceof Category);

                        echo <<<HTML
                        <li class="flex flex-col gap-2 text-xs">
                            <a href="/{{env=APP_LOCALE}}/category/{$child->slug}" title="{$child->title}" class="hover:underline">{$child->title}</a>
                        </li>
                        HTML;
                    }

                    echo <<<HTML
                    </ul>
                    HTML;
                }

                echo <<<HTML
                </li>
                HTML;
            }
            ?>
        </ul>
    </div>
    <div class="p-4 text-sm bg-gray-200 dark:bg-gray-600 flex items-center justify-center">© <?= date("Y") ?> Copyright:<span class="font-medium mx-1">{{env=APP_TITLE}}</span></div>
</footer>