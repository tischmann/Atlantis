<main class="md:container mx-4 md:mx-auto mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <?php

        use App\Models\Article;

        foreach ($articles as $article) {
            assert($article instanceof Article);
            include 'article_in_category.php';
        }

        ?>
    </div>
</main>