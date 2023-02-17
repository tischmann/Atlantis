<main class="container mx-auto ">
    <div class="p-4 flex sticky-top bg-white">
        <?php include __DIR__ . "/../breadcrumbs.php" ?>
    </div>
    <div class="mx-4 mb-4 text-gray-500 font-semibold flex items-center gap-4 uppercase">
        <h1>{{lang=articles}}</h1>
        <div class="h-0.5 w-full rounded-full bg-gray-500"></div>
    </div>
    <div class="accordion mx-4" id="accordionArticles">
        <?php

        use Tischmann\Atlantis\Template;

        foreach ($items as $articles) {
            $category = reset($articles)->category;

            echo <<<HTML
            <div class="accordion-item rounded-none bg-white border border-gray-200">
                <h2 class="accordion-header mb-0" id="heading-{$category->id}">
                    <button class="accordion-button collapsed relative flex items-center
                                w-full py-4 px-5 text-base text-gray-800 text-left 
                                bg-white border-0 rounded-none transition
                                focus:outline-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-{$category->id}" aria-expanded="false"
                        aria-controls="collapse-{$category->id}">{$category->title}</button>
                </h2>
                <div id="collapse-{$category->id}" class="accordion-collapse border-0 collapse"
                    aria-labelledby="heading-{$category->id}" data-bs-parent="#accordionArticles">
                    <div class="accordion-body py-4 px-5">
                        <div class="intersection-loader-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            HTML;

            foreach ($articles as $article) {
                Template::echo('admin/articles-item', ['article' => $article]);
            }

            echo <<<HTML
                            <div class="intersection-loader-target" data-url="/fetch/admin/articles/{$category->id}"
                                data-page="{{pagination_page}}" data-limit="{{pagination_limit}}" data-search="{{search_value}}"
                                data-sort="{{sort_type}}" data-order="{{sort_order}}"></div>
                        </div>
                    </div>
                </div>
            </div>
            HTML;
        }
        ?>
    </div>
    <a href="/{{env=APP_LOCALE}}/add/article" aria-label="{{lang=add}}" class="h-16 w-16 fixed flex 
    items-center justify-center bottom-4 right-4 text-white text-xl
    rounded-full bg-pink-600 hover:bg-pink-700 hover:shadow-lg 
    active:bg-pink-700 focus:bg-pink-700 transition-all ease-in-out"><i class="fas fa-plus"></i></a>
    <div class="h-24"></div>
</main>