<main class="md:container mx-4 md:mx-auto mb-4">
    <ul class="flex flex-col gap-4 text-sm">
        <?php


        use App\Models\Category;

        $query = Category::query()
            ->where('parent_id', null)
            ->where('locale', getenv('APP_LOCALE'))
            ->order('position', 'ASC');

        foreach (Category::all($query) as $category) {
            assert($category instanceof Category);

            echo <<<HTML
            <li class="flex">
                <a href="/{{env=APP_LOCALE}}/category/{$category->slug}" title="{$category->title}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 hover:bg-gray-300 hover:px-4 transition-all">{$category->title}</a>
            </li>
            HTML;

            $category->children = $category->fetchChildren();

            if ($category->children) {
                echo <<<HTML
                <ul class="flex flex-col pl-8 gap-4">
                HTML;

                foreach ($category->children as $child) {
                    assert($child instanceof Category);

                    echo <<<HTML
                    <li class="flex">
                        <a href="/{{env=APP_LOCALE}}/category/{$child->slug}" title="{$child->title}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 hover:bg-gray-300 hover:px-4 transition-all">{$child->title}</a>
                    </li>
                    HTML;
                }

                echo <<<HTML
                </ul>
                HTML;
            }
        }

        ?>
    </ul>
</main>