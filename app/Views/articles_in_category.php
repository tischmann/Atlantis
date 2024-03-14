<main class="md:container mx-4 md:mx-auto mb-4">
    <h1 class="text-3xl my-4 font-semibold">{{lang=articles_in_category}} "<?= $category ?? "" ?>"</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <?php

        use App\Models\Article;

        foreach ($articles as $article) {
            assert($article instanceof Article);
            include 'article_main.php';
        }

        ?>
    </div>
    <div class="my-4"><?php include 'pagination.php'; ?></div>
</main>