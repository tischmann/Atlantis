<ul class="sortable-categories-container list-none flex flex-wrap gap-4 text-sky-800">
    <?php

    use App\Models\{Category};

    foreach ($category->children as $child) {
        assert($child instanceof Category);

        echo <<<HTML
        <li class="bg-white rounded-lg px-4 py-2 whitespace-nowrap flex items-center shadow font-semibold" data-id="{$child->id}">
            <i class="handle fas fa-arrows mr-4 hover:text-pink-600 cursor-grab"></i>
            <div>
                {$child->title}
                <a href="/{{env=APP_LOCALE}}/category/edit/{$child->id}" aria-label="{{lang=edit}}"><i class="fas fa-pencil-alt ml-4 hover:text-pink-600"></i></a>
            </div>
        </li>
        HTML;
    }
    ?>
</ul>