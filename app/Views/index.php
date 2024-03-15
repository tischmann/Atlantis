<main class="md:container mx-4 md:mx-auto mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <?php

        foreach ($articles as $article) {
            include 'article_main.php';
        }

        ?>
    </div>
    <div class="my-4"><?php include 'pagination.php'; ?></div>
</main>