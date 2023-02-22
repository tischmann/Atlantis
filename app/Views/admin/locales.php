<?php include __DIR__ . "/../header.php" ?>
<main class="md:container md:mx-auto px-4">
    <div class="flex flex-wrap gap-4 my-4">
        <?php

        use Tischmann\Atlantis\{Locale, Template};

        foreach ($locales as $locale) {
            $count = count(Locale::getLocale($locale));

            echo <<<HTML
            <a href="/{{env=APP_LOCALE}}/locale/edit/{$locale}" title="{{lang=locale_{$locale}}}" aria-label="{{lang=locale_{$locale}}}" class="bg-sky-800 text-white shadow rounded-lg px-4 py-3 flex justify-between items-center gap-4 hover:bg-sky-700 text-xl font-medium transition-all ease-in-out">
                <span class="truncate drop-shadow">{{lang=locale_{$locale}}}</span>
                <span class="font-bold countup bg-sky-900 rounded-lg px-2">{$count}</span>
            </a>
            HTML;
        }

        ?>
    </div>
    <?= Template::html('admin/add-button', ['href' => '/{{env=APP_LOCALE}}/add/locale']) ?>
</main>