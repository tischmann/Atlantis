<main class="md:container md:mx-auto">
    <section class="flex flex-col">
        <?php

        use Tischmann\Atlantis\{Route, Router};

        foreach (Router::$routes as $method => $routes) {
            foreach ($routes as $route) {
                assert($route instanceof Route);

                $controller = $route->controller::class;

                $args = json_encode($route->args);

                echo <<<HTML
            <div class="grid grid-cols-8 even:bg-gray-100 rounded-lg">
                <div class="text-ellipsis overflow-hidden p-2" title="{$route->method}">{$route->method}</div>
                <div class="text-ellipsis overflow-hidden p-2 col-span-2" title="{$route->path}">{$route->path}</div>
                <div class="text-ellipsis overflow-hidden p-2 col-span-2" title="{$controller}">{$controller}</div>
                <div class="text-ellipsis overflow-hidden p-2" title="{$route->action}">{$route->action}</div>
                <div class="text-ellipsis overflow-hidden p-2" title="{$route->title}">{$route->title}</div>
                <div class="text-ellipsis overflow-hidden p-2" title="{$args}">{$args}</div>
            </div>
            HTML;
            }
        }
        ?>
    </section>
</main>