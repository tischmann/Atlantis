<div class="hidden sm:grid grid-cols-1 sm:grid-cols-6 gap-2 px-4 py-3 text-white dark:text-gray-800 uppercase bg-gray-500 dark:bg-gray-400 rounded-lg transition mb-2 text-sm font-semibold">
    <div class="col-span-1 sm:col-span-2 text-ellipsis overflow-hidden">{{lang=category_title}}</div>
    <div class="text-ellipsis overflow-hidden">{{lang=category_slug}}</div>
    <div class="col-span-1 sm:col-span-2 text-ellipsis overflow-hidden">{{lang=category_children}}</div>
    <div class="text-ellipsis overflow-hidden text-right">{{lang=category_actions}}</div>
</div>
<ul id="categories-list" class="grid grid-cols-1 gap-2">
    <?php

    use App\Models\{Category};

    use Tischmann\Atlantis\{Template};

    foreach ($categories as $category) {
        assert($category instanceof Category);

        Template::echo(
            template: 'category_list_item',
            args: [
                'category' => $category
            ]
        );
    }

    ?>
</ul>