<?php include __DIR__ . "/header.php" ?>
<main class="md:container md:mx-auto">
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
                <form id="atlantis-rating-{{uniqid}}" class="atlantis-rating">
                    <?php

                    $uniqid = uniqid();

                    for ($i = 5; $i >= 1; $i--) {
                        $checked = $article->rating == $i ? 'checked' : '';

                        echo <<<HTML
                        <input type="radio" name="rating" value="{$i}" id="atlantis-rating-{$i}-{$uniqid}" {$checked}/>
                        <label for="atlantis-rating-{$i}-{$uniqid}"></label>
                        HTML;
                    }
                    ?>
                </form>
            </div>
        </div>
        <div class="mt-4" data-atlantis-lightbox data-atlantis-lazy-image-container>
            <img class="w-full md:max-w-lg md:float-left mb-4 md:mr-8 rounded-xl shadow-md" data-src="<?= $article->image_url ?>" src="/placeholder.svg" width="<?= Article::THUMB_WIDTH ?>" height="<?= Article::THUMB_HEIGHT ?>" alt="<?= $article->title ?>" data-atlantis-lazy-image>
            <?= html_entity_decode($article->full_text) ?>
        </div>
        <?php require __DIR__ . "/tags.php" ?>
    </article>
    <script nonce="{{nonce}}" type="module">
        import Atlantis from '/js/atlantis.js'

        const $ = new Atlantis()

        const uuid = $.getUUID()

        document
            .getElementById(`atlantis-rating-{{uniqid}}`)
            .querySelectorAll('input[type="radio"]')
            .forEach((input) => {
                $.on(input, 'change', function() {
                    $.fetch(
                        `/rating/<?= $article->id ?>/${this.value}`, {
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: {
                                uuid
                            }
                        }
                    )
                })
            })
    </script>
</main>