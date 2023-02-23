<?php include __DIR__ . "/header.php" ?>
<main class="md:container md:mx-auto">
    <div class="intersection-loader-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 mx-4 mb-4">
        <?php

        use Tischmann\Atlantis\Template;

        foreach ($articles as $article) {
            Template::echo('articles-item', ['article' => $article]);
        }

        Template::echo(
            'intersection-loader-target',
            [
                'url' => "/fetch/articles/{$category->id}"
            ]
        );
        ?>
    </div>
</main>