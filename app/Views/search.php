<?php

use Tischmann\Atlantis\Template;

include __DIR__ . "/header.php"

?>
<main class="md:container md:mx-auto">
    <div class="m-4">
        <?php include __DIR__ . "/breadcrumbs.php" ?>
    </div>
    <div class="flex items-center gap-4 m-4 justify-end">
        <?php include __DIR__ . "/sort.php" ?>
    </div>
    <?php

    $lazyload = Template::html(
        'lazyload',
        [
            'pagination' => $pagination,
            'url' => "/fetch/search"
        ]
    );

    ?>
    <div class="m-4" <?= $lazyload ?>>
        <?php

        if (!$articles) Template::echo('search-empty');

        foreach ($articles as $article) {
            Template::echo('search-article-item', [
                'article' => $article
            ]);
        }

        ?>
    </div>
</main>