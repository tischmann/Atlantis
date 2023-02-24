<?php

use Tischmann\Atlantis\Breadcrumb;

if (isset($breadcrumbs) && is_array($breadcrumbs)) {

    echo <<<HTML
    <nav class="flex flex-wrap gap-2 items-center font-semibold text-xs">
        <a href="/{{env=APP_LOCALE}}" aria-label="{{lang=home}}" class="bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg hover:text-sky-700 dark:hover:text-sky-500 transition-all ease-in-out"><i class="fas fa-home"></i></a>
    HTML;

    foreach ($breadcrumbs as $breadcrumb) {
        if (!assert($breadcrumb instanceof Breadcrumb)) {
            $message = 'Invalid breadcrumb type: ' . gettype($breadcrumb);
            throw new Exception($message);
        };

        if ($breadcrumb->url) {
            echo <<<HTML
            <a href="{$breadcrumb->url}" aria-label="{$breadcrumb->label}" class="bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg hover:text-sky-700 dark:hover:text-sky-500 transition-all ease-in-out">{$breadcrumb->label}</a>
            HTML;
        } else {
            echo <<<HTML
            <span class="bg-gray-100 dark:bg-gray-700 text-sky-700 dark:text-sky-400 px-3 py-2 rounded-lg cursor-default truncate">{$breadcrumb->label}</span>
            HTML;
        }
    }
}

echo "</nav>";
