<?php

use Tischmann\Atlantis\Template;

?>
<main class="md:container md:mx-auto">
    <?php include __DIR__ . "/sort.php";
    ?>
    <div class="flex flex-col gap-4 m-4">
        <?php

        foreach ($articles as $article) {
            Template::echo('articles-item', ['article' => $article]);
        }

        ?>
    </div>
</main>