<nav class="flex flex-wrap gap-2 items-center font-semibold text-xs">
    <a href="/" aria-label="{{lang=home}}" class="bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg hover:text-sky-600 transition-all ease-in-out"><i class="fas fa-home"></i></a>
    <?php

    use Tischmann\Atlantis\Breadcrumb;

    foreach ($breadcrumbs as $breadcrumb) {
        assert($breadcrumb instanceof Breadcrumb);

        if ($breadcrumb->url) {
            echo <<<HTML
            <a href="{$breadcrumb->url}" aria-label="{$breadcrumb->label}" class="bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg hover:text-sky-600 transition-all ease-in-out">{$breadcrumb->label}</a>
            HTML;
        } else {
            echo <<<HTML
            <span class="bg-gray-100 dark:bg-gray-700 text-sky-600 px-3 py-2 rounded-lg cursor-default truncate">{$breadcrumb->label}</span>
            HTML;
        }
    }
    ?>
</nav>