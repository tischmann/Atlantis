<main class="md:container mx-4 md:mx-auto mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <?php

        use App\Models\Article;

        use Tischmann\Atlantis\Pagination;

        $query = Article::query()
            ->order('fixed', 'DESC')
            ->order('created_at', 'DESC');

        $pagination = new Pagination(query: $query, limit: 10);

        foreach (Article::all($query) as $article) {
            assert($article instanceof Article);
            include  'article_main.php';
        }

        ?>
    </div>
    <div class="my-4"><?php include 'pagination.php'; ?></div>
</main>