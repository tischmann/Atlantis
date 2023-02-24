<div class="flex gap-2 flex-wrap mb-4">
    <?php

    use App\Models\Article;

    assert($article instanceof Article);

    foreach ($article->tags as $tag) {
        echo <<<HTML
        <a href="/{{env=APP_LOCALE}}/tags/{$tag}" class="inline-block bg-gray-700 rounded-lg px-3 py-1 text-sm font-semibold hover:text-sky-500 hover:shadow-lg shadow-md transition-all ease-in-out">#{$tag}</a>
        HTML;
    }

    ?>
</div>