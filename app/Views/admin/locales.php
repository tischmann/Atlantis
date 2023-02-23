<?php include __DIR__ . "/../header.php" ?>
<main class="md:container md:mx-auto px-4">
    <div class="flex flex-col md:flex-row md:flex-wrap gap-4 my-4">
        <?php

        use Tischmann\Atlantis\{Locale, Template};

        foreach ($locales as $locale) {
            $count = count(Locale::getLocale($locale));

            echo <<<HTML
            <a href="/{{env=APP_LOCALE}}/locale/edit/{$locale}" title="{{lang=locale_{$locale}}}" aria-label="{{lang=locale_{$locale}}}" class="bg-sky-800 text-white shadow rounded-lg px-4 py-3 flex justify-between items-center gap-4 hover:bg-sky-700 text-xl font-medium transition-all ease-in-out w-full md:w-auto">
                <div class="flex items-center gap-4">
                    <img src="/images/flags/1x1/{$locale}.svg" width="64" height="64" class="rounded-md shadow-md" alt="{{lang=locale_{$locale}}}"/>
                    <div class="flex flex-col gap-2">
                        <span class="truncate drop-shadow">{{lang=locale_{$locale}}}</span>                
            HTML;

            if ($locale === getenv('APP_LOCALE')) {
                echo <<<HTML
                        <span class="text-sm drop-shadow">{{lang=locale_current}}</span>
                HTML;
            }

            echo <<<HTML
                    </div>   
                </div>             
                <span class="text-[32px] font-bold countup bg-sky-900 rounded-lg p-4 min-w-[60px] text-center">{$count}</span>
            </a>
            HTML;
        }

        ?>
    </div>
    <?= Template::html('admin/add-button', ['href' => '/{{env=APP_LOCALE}}/add/locale']) ?>
</main>