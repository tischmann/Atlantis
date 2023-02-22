<?php include __DIR__ . "/header.php" ?>
<main class="md:container md:mx-auto">
    <div class="intersection-loader-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 mx-4 mb-4">
        <?php

        use Tischmann\Atlantis\Template;

        foreach ($articles as $article) {
            Template::echo('articles-item', ['article' => $article]);
        }
        ?>
        <div class="intersection-loader-target flex justify-center items-center" data-url="/fetch/articles/{$category->id}" data-page="{{pagination_page}}" data-limit="{{pagination_limit}}" data-search="{{search_value}}" data-sort="{{sort_type}}" data-order="{{sort_order}}">
            <div class="spinner-grow inline-block w-8 h-8 bg-sky-500 rounded-full opacity-0" role="status"></div>
        </div>
    </div>
</main>