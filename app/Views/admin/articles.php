<?php

use Tischmann\Atlantis\Template;

?>
<main class="md:container md:mx-auto">
    <?php

    include __DIR__ . "/../sort.php";

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