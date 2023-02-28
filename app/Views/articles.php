<?php include __DIR__ . "/header.php" ?>
<main class="md:container md:mx-auto">
    <div class="m-4">
        <?php include __DIR__ . "/breadcrumbs.php" ?>
    </div>
    <div class="flex items-center gap-4 m-4 justify-end">
        <?php include __DIR__ . "/sort.php" ?>
    </div>
    <div class="intersection-loader-container flex flex-col gap-4 m-4">
        <?php

        use Tischmann\Atlantis\Template;

        foreach ($articles as $article) {
            Template::echo('articles-item', ['article' => $article]);
        }

        Template::echo(
            'intersection-loader-target',
            [
                'pagination' => $pagination,
                'url' => "/fetch/category/{$category->slug}"
            ]
        );
        ?>
    </div>
</main>