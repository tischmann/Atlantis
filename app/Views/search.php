<?php

use Tischmann\Atlantis\Template;

?>
<main class="md:container md:mx-auto">
    <?php

    include __DIR__ . "/sort.php";

    ?>
    <div class="m-4">
        <?php

        if (!$articles) Template::echo('search-empty');

        foreach ($articles as $article) {
            Template::echo('search-article-item', [
                'article' => $article
            ]);
        }

        include __DIR__ . "/pagination.php";
        ?>
    </div>
</main>