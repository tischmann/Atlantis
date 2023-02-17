<nav class="w-full flex flex-wrap gap-2 items-center text-xs uppercase font-medium">
    <a href="/" aria-label="{{lang=home}}" class="bg-gray-200 text-black px-3 
    py-2 rounded-lg hover:bg-sky-600 hover:px-5 hover:text-white 
    transition-all ease-in-out">{{lang=home}}</a>
    <?php

    use Tischmann\Atlantis\Breadcrumb;

    foreach ($breadcrumbs as $breadcrumb) {
        assert($breadcrumb instanceof Breadcrumb);

        if ($breadcrumb->url) {
            echo <<<HTML
            <a href="{$breadcrumb->url}" aria-label="{$breadcrumb->label}" class="bg-gray-200 text-black px-3 py-2 rounded-lg hover:px-5 hover:bg-sky-600 hover:text-white transition-all ease-in-out">{$breadcrumb->label}</a>
            HTML;
        } else {
            echo <<<HTML
            <span class="bg-gray-200 text-black px-3 py-2 rounded-lg cursor-default">{$breadcrumb->label}</span>
            HTML;
        }
    }
    ?>
</nav>