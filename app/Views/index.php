<main class="md:container mx-4 md:mx-auto mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <?php

        use App\Models\Article;

        $query = Article::query()->order('id', 'ASC');

        foreach (Article::all($query) as $article) {
            assert($article instanceof Article);
            include __DIR__ . '/article_main.php';
        }

        ?>
    </div>
</main>