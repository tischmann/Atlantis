<main class="container mx-auto">
    <div class="p-4 flex sticky-top bg-white">
        <?php include __DIR__ . "/../breadcrumbs.php" ?>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 mx-4">
        <?php
        foreach ($items as $item => $args) {
            echo <<<HTML
            <a href="{$args['url']}"
                class="flex flex-col items-center justify-center relative gap-2
                text-white hover:bg-pink-600 bg-sky-600 p-4 rounded-xl"
                title="{$args['title']}" aria-label="{$args['title']}">
                <i class="{$args['icon']} text-[80px]"></i>
                <span class="uppercase">{$args['label']}</span>
            </a>
            HTML;
        }
        ?>
    </div>
</main>