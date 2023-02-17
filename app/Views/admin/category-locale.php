<div class="flex flex-wrap rounded-xl gap-4 bg-sky-600 p-4 shadow-lg text-sky-800">
    <div class="flex-grow w-full bg-sky-400 rounded-lg text-white px-4 py-2 
    whitespace-nowrap uppercase text-center font-bold">
        {{lang=locale_<?= $locale ?>}}
    </div>
    <ul id="sortCategories" class="flex gap-4 flex-wrap">
        <?php

        use App\Models\Category;

        use Tischmann\Atlantis\Template;

        $category = new Category();

        $category->children = $categories;

        Template::echo('admin/category-children', ['category' => $category]);

        ?>
    </ul>
</div>