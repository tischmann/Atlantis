<?php include __DIR__ . "/header.php" ?>
<main class="container mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 font-medium mx-4 mb-4">
        <?php

        use Tischmann\Atlantis\{Controller, Template};

        foreach ($items as $locale => $categories) {
            Template::echo(
                'admin/category-locale',
                [
                    'locale' => $locale,
                    'categories' => $categories,
                ]
            );
        }
        ?>
    </div>
    <a href="/{{env=APP_LOCALE}}/category/add" aria-label="{{lang=add}}" class="h-12 w-12 fixed flex 
    items-center justify-center bottom-4 right-4 text-white text-xl
    rounded-full bg-pink-600 hover:bg-pink-700 hover:shadow-lg 
    active:bg-pink-700 focus:bg-pink-700 transition-all ease-in-out"><i class="fas fa-plus"></i></a>
    <?php include __DIR__ . "/sortable-categories-script.php" ?>
</main>