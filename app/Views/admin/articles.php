<?php include __DIR__ . "/../header.php" ?>
<main class="container mx-auto">
    <div class="intersection-loader-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 mx-4 mb-4">
        <?php

        use Tischmann\Atlantis\Template;

        foreach ($articles as $article) {
            Template::echo('admin/articles-item', ['article' => $article]);
        }
        ?>
        <div class="intersection-loader-target flex justify-center items-center" data-url="/fetch/admin/articles/{$category->id}" data-page="{{pagination_page}}" data-limit="{{pagination_limit}}" data-search="{{search_value}}" data-sort="{{sort_type}}" data-order="{{sort_order}}">
            <div class="spinner-grow inline-block w-8 h-8 bg-sky-600 rounded-full opacity-0" role="status"></div>
        </div>
    </div>
    <a href="/{{env=APP_LOCALE}}/add/article" aria-label="{{lang=add}}" class="h-12 w-12 fixed flex 
    items-center justify-center bottom-4 right-4 text-white text-xl
    rounded-full bg-pink-600 hover:bg-pink-700 hover:shadow-lg 
    active:bg-pink-700 focus:bg-pink-700 transition-all ease-in-out"><i class="fas fa-plus"></i></a>
</main>