<?php include __DIR__ . "/header.php" ?>
<main class="md:container md:mx-auto">
    <div class="m-4">
        <?php include __DIR__ . "/breadcrumbs.php" ?>
    </div>
    <article class="m-4 article">
        <div class="text-3xl font-bold flex items-center gap-4 mb-2"><?= $article->title ?>
            <?php

            use App\Models\Article;

            use App\Models\User;

            use Tischmann\Atlantis\Date;

            if (User::current()->isAdmin()) {
                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/edit/article/{$article->id}" aria-label="{{lang=edit}}" class="text-lg text-sky-500 hover:text-pink-600">
                    <i class="fas fa-pencil"></i>
                </a>
                HTML;
            }
            ?>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <span><?= Date::getElapsed($article->created_at)  ?></span>
            <div><i class="fas fa-eye mr-2"></i><?= $article->views ?></div>
            <div>
                <form class="atlantis-rating" data-id="<?= $article->id ?>" data-rating="<?= $article->rating ?>" data-token="{{csrf-token}}" data-atlantis-article-rating></form>
            </div>
        </div>
        <div class="mt-4" data-atlantis-lightbox data-atlantis-lazy-image-container>
            <img class="w-full md:max-w-lg md:float-left mb-4 md:mr-8 rounded-xl shadow-md" data-src="<?= $article->image_url ?>" src="/placeholder.svg" width="<?= Article::THUMB_WIDTH ?>" height="<?= Article::THUMB_HEIGHT ?>" alt="<?= $article->title ?>" data-atlantis-lazy-image>
            <?= html_entity_decode($article->full_text) ?>
        </div>
        <?php require __DIR__ . "/tags.php" ?>
    </article>
    <script src="/js/articleRating.js" nonce="{{nonce}}" type="module" async></script>
</main>