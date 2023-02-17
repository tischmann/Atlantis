<nav class="w-full flex flex-wrap gap-2 items-center">
    <?php

    use App\Models\User;

    use Tischmann\Atlantis\Breadcrumb;

    if (User::current()->isAdmin()) {
        echo <<<HTML
        <button class="bg-gray-200 text-black px-3 py-2 rounded-lg hover:bg-sky-600 hover:text-white transition-all ease-in-out text-xs font-medium" type="button" data-bs-toggle="offcanvas" data-bs-target="#dashboardMenu" aria-controls="dashboardMenu">
            <i class="fas fa-bars"></i>
        </button>
        HTML;

        include __DIR__ . "/admin/sidebar.php";
    }
    ?>
    <a href="/" aria-label="{{lang=home}}" class="bg-gray-200 text-black px-3 
    py-2 rounded-lg hover:bg-sky-600 hover:text-white 
    transition-all ease-in-out text-xs font-medium">{{lang=home}}</a>
    <?php
    foreach ($breadcrumbs as $breadcrumb) {
        assert($breadcrumb instanceof Breadcrumb);

        if ($breadcrumb->url) {
            echo <<<HTML
            <a href="{$breadcrumb->url}" aria-label="{$breadcrumb->label}" class="bg-gray-200 text-black px-3 py-2 rounded-lg hover:bg-sky-600 hover:text-white transition-all ease-in-out text-xs font-medium">{$breadcrumb->label}</a>
            HTML;
        } else {
            echo <<<HTML
            <span class="bg-gray-200 text-black px-3 py-2 rounded-lg cursor-default text-xs font-medium">{$breadcrumb->label}</span>
            HTML;
        }
    }
    ?>
</nav>