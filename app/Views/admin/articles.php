<?php include __DIR__ . "/../header.php" ?>
<main class="md:container md:mx-auto">
    <div class="flex items-center gap-4 px-4 justify-end">
        <?php include __DIR__ . "/../sort.php" ?>
    </div>
    <div class="intersection-loader-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 p-4">
        <?php

        use Tischmann\Atlantis\Template;

        foreach ($articles as $article) {
            Template::echo('admin/articles-item', ['article' => $article]);
        }

        Template::echo(
            'intersection-loader-target',
            [
                'pagination' => $pagination,
                'url' => "/fetch/admin/articles"
            ]
        );

        ?>
    </div>
    <div class="fixed bottom-4 right-4 ">
        <a href="/{{env=APP_LOCALE}}/add/article" aria-label="{{lang=add}}" data-te-ripple-init data-te-ripple-color="light" class="flex items-center justify-center rounded-full text-lg bg-primary h-12 w-12 uppercase leading-normal text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]">
            <i class="fas fa-plus"></i>
        </a>
    </div>
</main>