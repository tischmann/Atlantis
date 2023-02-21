<?php include __DIR__ . "/header.php" ?>
<main class="container mx-auto">
    <div class="m-4">
        <div class="text-3xl font-bold flex items-center gap-4 mb-2"><?= $article->title ?>
            <?php

            use App\Models\User;

            if (User::current()->isAdmin()) {
                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/edit/article/{$article->id}" aria-label="{{lang=edit}}" class="text-lg text-sky-600 hover:text-pink-600">
                    <i class="fas fa-pencil"></i>
                </a>
                HTML;
            }
            ?>
        </div>
        <div class="text-gray-500 flex items-center gap-4 text-sm">
            <span><?= $article->updated_at->format('Y-m-d H:i') ?></span>
            <div><i class="fas fa-eye mr-2"></i><?= $article->views ?></div>
            <div>
                <form class="rating" data-id="<?= $article->id ?>" data-rating="<?= $article->rating ?>"></form>
            </div>
        </div>
        <div class="mt-4">
            <img class="lazy w-full md:max-w-lg md:float-left mb-4 md:mr-8 rounded-xl shadow-md" data-src="<?= $article->image_url ?>" src="/images/placeholder.svg" width="400" height="300" alt="<?= $article->title ?>">
            <?= html_entity_decode($article->full_text) ?>
        </div>
    </div>
    <script src="/js/rating.js" nonce="{{nonce}}"></script>
    <script nonce="{{nonce}}">
        new Rating(document.querySelector(`.rating`), new UUID().get())
    </script>
</main>