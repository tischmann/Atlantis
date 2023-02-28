<?php

use Tischmann\Atlantis\Template;

include __DIR__ . "/../header.php";

?>
<main class="md:container md:mx-auto">
    <div class="m-4">
        <?php include __DIR__ . "/../breadcrumbs.php" ?>
    </div>
    <div class="flex items-center gap-4 m-4 justify-end">
        <?php include __DIR__ . "/../sort.php" ?>
    </div>
    <?php

    $lazyload = Template::html(
        'lazyload',
        [
            'pagination' => $pagination,
            'url' => "/fetch/admin/articles"
        ]
    );

    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 m-4" <?= $lazyload ?>>
        <?php

        foreach ($articles as $article) {
            Template::echo('admin/articles-item', ['article' => $article]);
        }

        ?>
    </div>
    <?= Template::html('admin/add-button', ['href' => '/{{env=APP_LOCALE}}/add/article']) ?>
</main>