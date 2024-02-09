<?php

use Tischmann\Atlantis\Template;

?>
<main class="md:container md:mx-auto">
    <?php

    include __DIR__ . "/sort.php";

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