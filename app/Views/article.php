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
        <div class="text-gray-500 flex items-center gap-4 text-sm">
            <span><?= Date::getElapsed($article->created_at)  ?></span>
            <div><i class="fas fa-eye mr-2"></i><?= $article->views ?></div>
            <div>
                <form class="atlantis-rating" data-id="<?= $article->id ?>" data-rating="<?= $article->rating ?>"></form>
            </div>
        </div>
        <div class="mt-4 fslightbox-gallery">
            <img class="lazy w-full md:max-w-lg md:float-left mb-4 md:mr-8 rounded-xl shadow-md" data-src="<?= $article->image_url ?>" src="/images/placeholder.svg" width="<?= Article::THUMB_WIDTH ?>" height="<?= Article::THUMB_HEIGHT ?>" alt="<?= $article->title ?>">
            <?= html_entity_decode($article->full_text) ?>
        </div>
        <?php require __DIR__ . "/tags.php" ?>
    </article>
    <script nonce="{{nonce}}" type="module">
        import Atlantis from '/js/atlantis.js'

        const atlantis = new Atlantis()

        document.querySelectorAll(`form.atlantis-rating[data-id][data-rating]`)
            .forEach(form => {
                const article_id = form.dataset?.id

                const rating = atlantis.toInt(form.dataset?.rating)

                const uniqueid = atlantis.uniqueid()

                const onchange = function(event) {
                    const url = `/rating/${article_id}/${event.target.value}`

                    atlantis.fetch(url, {
                        body: {
                            uuid: atlantis.getUUID()
                        }
                    })
                }

                for (let i = 5; i >= 1; i--) {
                    const id = `atlantis-rating-${i}-${uniqueid}`

                    const label = atlantis.tag('label', {
                        attr: {
                            for: id
                        }
                    })

                    const input = atlantis.tag('input', {
                        attr: {
                            name: `rating`,
                            type: `radio`,
                            value: i,
                            id,
                        },
                        data: {
                            id: article_id
                        },
                        on: {
                            change: onchange
                        }
                    })

                    if (rating == i) input.checked = true

                    form.append(input, label)
                }
            })

        document.querySelectorAll(`.fslightbox-gallery img`).forEach(img => {
            const a = document.createElement("a")
            a.setAttribute("data-fslightbox", "gallery")
            let src = img.dataset?.src || img.getAttribute("src")
            src = src.replace(/thumb_/g, '')
            a.setAttribute("href", src)
            img.before(a)
            a.append(img)
        })

        refreshFsLightbox()
    </script>
</main>